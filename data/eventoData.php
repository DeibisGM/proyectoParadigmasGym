<?php
include_once 'data.php';
include_once '../domain/evento.php';

class EventoData extends Data
{
    /**
     * Inserta un evento y una única entrada de reserva de sala con los IDs concatenados.
     * Nota: Este enfoque almacena múltiples IDs en un solo campo de texto, lo cual es una desnormalización de la base de datos.
     */
    public function insertarEvento($evento, $salas)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return "Error de conexión: " . mysqli_connect_error();
        }
        $conn->set_charset('utf8');

        mysqli_autocommit($conn, false);

        try {
            // 1. Obtener nuevo ID e insertar en tbevento
            $result = mysqli_query($conn, "SELECT MAX(tbeventoid) as max_id FROM tbevento");
            $row = mysqli_fetch_assoc($result);
            $nuevoEventoId = ($row['max_id'] ?? 0) + 1;
            $evento->setId($nuevoEventoId);

            $queryEvento = "INSERT INTO tbevento (tbeventoid, tbeventonombre, tbeventodescripcion, tbeventofecha, tbeventohorainicio, tbeventohorafin, tbeventoaforo, tbinstructorid, tbeventoestado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtEvento = mysqli_prepare($conn, $queryEvento);

            // CORRECCIÓN: El tipo de dato para aforo, instructorid y estado es entero (i), no string (s).
            mysqli_stmt_bind_param($stmtEvento, "isssssiii",
                $evento->getId(), $evento->getNombre(), $evento->getDescripcion(), $evento->getFecha(), $evento->getHoraInicio(),
                $evento->getHoraFin(), $evento->getAforo(), $evento->getInstructorId(), $evento->getEstado()
            );

            if (!mysqli_stmt_execute($stmtEvento)) {
                throw new Exception("Error al insertar el evento principal: " . mysqli_stmt_error($stmtEvento));
            }

            // 2. Insertar en tbreservasala
            $salasStr = implode('$', $salas); // Usar un delimitador claro
            $queryReserva = "INSERT INTO tbreservasala (tbreservasalaid, tbsalaid, tbeventoid, tbreservafecha, tbreservahorainicio, tbreservahorafin) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtReserva = mysqli_prepare($conn, $queryReserva);

            $resultReserva = mysqli_query($conn, "SELECT MAX(tbreservasalaid) as max_id FROM tbreservasala");
            $rowReserva = mysqli_fetch_assoc($resultReserva);
            $nextReservaSalaId = ($rowReserva['max_id'] ?? 0) + 1;

            // El campo 'tbsalaid' se trata como string para almacenar la cadena de IDs
            mysqli_stmt_bind_param($stmtReserva, "isisss",
                $nextReservaSalaId, $salasStr, $evento->getId(),
                $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin()
            );

            if (!mysqli_stmt_execute($stmtReserva)) {
                throw new Exception("Error al reservar la sala: " . mysqli_stmt_error($stmtReserva));
            }

            mysqli_commit($conn);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            return $e->getMessage();
        } finally {
            mysqli_close($conn);
        }
    }

    /**
     * Verifica la disponibilidad de salas buscando conflictos en los rangos de tiempo.
     */
    public function verificarDisponibilidadSalas($salas, $fecha, $horaInicio, $horaFin)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return ["Error de conexión: " . mysqli_connect_error()];
        }
        $conn->set_charset('utf8');

        $query = "SELECT tbsalaid FROM tbreservasala WHERE tbreservafecha = ? AND (? < tbreservahorafin AND ? > tbreservahorainicio)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $fecha, $horaInicio, $horaFin);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $salasOcupadas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $salasOcupadas = array_merge($salasOcupadas, explode('$', $row['tbsalaid']));
        }

        $salasOcupadas = array_unique($salasOcupadas);
        $conflictos = array_intersect($salas, $salasOcupadas);

        if (!empty($conflictos)) {
            $listaSalas = implode(',', array_map('intval', $conflictos));
            $queryNombres = "SELECT tbsalanombre FROM tbsala WHERE tbsalaid IN ($listaSalas)";
            $resultNombres = mysqli_query($conn, $queryNombres);
            $nombresSalas = [];
            while ($rowNombre = mysqli_fetch_assoc($resultNombres)) {
                $nombresSalas[] = $rowNombre['tbsalanombre'];
            }
            mysqli_close($conn);
            return $nombresSalas;
        }

        mysqli_close($conn);
        return [];
    }

    /**
     * Actualiza un evento existente.
     */
    public function actualizarEvento($evento)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return false;
        $conn->set_charset('utf8');

        $query = "UPDATE tbevento SET tbeventonombre=?, tbeventodescripcion=?, tbeventofecha=?, tbeventohorainicio=?, tbeventohorafin=?, tbeventoaforo=?, tbinstructorid=?, tbeventoestado=? WHERE tbeventoid=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssiiii",
            $evento->getNombre(), $evento->getDescripcion(), $evento->getFecha(), $evento->getHoraInicio(),
            $evento->getHoraFin(), $evento->getAforo(), $evento->getInstructorId(), $evento->getEstado(), $evento->getId()
        );

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    /**
     * Elimina un evento y su registro de reserva de sala asociado.
     */
    public function eliminarEvento($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return false;
        $conn->set_charset('utf8');

        mysqli_autocommit($conn, false);
        try {
            $queryReserva = "DELETE FROM tbreservasala WHERE tbeventoid = ?";
            $stmtReserva = mysqli_prepare($conn, $queryReserva);
            mysqli_stmt_bind_param($stmtReserva, "i", $id);
            if (!mysqli_stmt_execute($stmtReserva)) {
                throw new Exception("Error al eliminar las reservas de sala.");
            }

            $queryEvento = "DELETE FROM tbevento WHERE tbeventoid = ?";
            $stmtEvento = mysqli_prepare($conn, $queryEvento);
            mysqli_stmt_bind_param($stmtEvento, "i", $id);
            if (!mysqli_stmt_execute($stmtEvento)) {
                throw new Exception("Error al eliminar el evento.");
            }

            mysqli_commit($conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            return false;
        } finally {
            mysqli_close($conn);
        }
    }

    /**
     * Obtiene todos los eventos, recupera los IDs de sala asociados y luego mapea sus nombres.
     */
    public function getAllEventos()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return [];
        $conn->set_charset('utf8');

        $query = "SELECT e.*, i.tbinstructornombre, rs.tbsalaid 
                  FROM tbevento e 
                  LEFT JOIN tbinstructor i ON e.tbinstructorid = i.tbinstructorid
                  LEFT JOIN tbreservasala rs ON e.tbeventoid = rs.tbeventoid
                  ORDER BY e.tbeventofecha, e.tbeventohorainicio";

        $result = mysqli_query($conn, $query);
        $eventosData = [];
        $salaIds = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $eventosData[] = $row;
            if (!empty($row['tbsalaid'])) {
                // CORRECCIÓN: Se completó la función explode con el delimitador '$'.
                $salaIds = array_merge($salaIds, explode('$', $row['tbsalaid']));
            }
        }

        $salaIds = array_unique(array_filter($salaIds));
        $salaNombres = [];

        if (!empty($salaIds)) {
            $listaSalas = implode(',', array_map('intval', $salaIds));
            $queryNombres = "SELECT tbsalaid, tbsalanombre FROM tbsala WHERE tbsalaid IN ($listaSalas)";
            $resultNombres = mysqli_query($conn, $queryNombres);
            while ($rowNombre = mysqli_fetch_assoc($resultNombres)) {
                $salaNombres[$rowNombre['tbsalaid']] = $rowNombre['tbsalanombre'];
            }
        }

        $eventos = [];
        foreach ($eventosData as $row) {
            $evento = new Evento(
                $row['tbeventoid'], $row['tbeventonombre'], $row['tbeventodescripcion'], $row['tbeventofecha'],
                $row['tbeventohorainicio'], $row['tbeventohorafin'], $row['tbeventoaforo'],
                $row['tbinstructorid'], $row['tbeventoestado']
            );
            $evento->setInstructorNombre($row['tbinstructornombre'] ?? 'No asignado');

            $nombres = '';
            if (!empty($row['tbsalaid'])) {
                // CORRECCIÓN: Se completó la función explode con el delimitador '$'.
                $ids = explode('$', $row['tbsalaid']);
                $nombresArr = [];
                foreach ($ids as $id) {
                    if (isset($salaNombres[trim($id)])) { // Se añade trim por seguridad
                        $nombresArr[] = $salaNombres[trim($id)];
                    }
                }
                $nombres = implode(', ', $nombresArr);
            }
            $evento->setSalasNombre($nombres);
            $eventos[] = $evento;
        }

        mysqli_close($conn);
        return $eventos;
    }
}