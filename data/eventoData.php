<?php
include_once 'data.php';
include_once '../domain/evento.php';

class EventoData extends Data
{
    public function insertarEvento($evento, $salas)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return "Error de conexión a la base de datos.";
        }
        $conn->set_charset('utf8');
        mysqli_autocommit($conn, false);

        try {
            // 1. Obtener el siguiente ID para el evento
            $resultId = mysqli_query($conn, "SELECT MAX(tbeventoid) as max_id FROM tbevento");
            $rowId = mysqli_fetch_assoc($resultId);
            $nuevoEventoId = ($rowId['max_id'] ?? 0) + 1;
            $evento->setId($nuevoEventoId);

            // 2. Insertar en tbevento
            $queryEvento = "INSERT INTO tbevento (tbeventoid, tbeventonombre, tbeventodescripcion, tbeventofecha, tbeventohorainicio, tbeventohorafin, tbeventoaforo, tbinstructorid, tbeventoestado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtEvento = mysqli_prepare($conn, $queryEvento);
            mysqli_stmt_bind_param(
                $stmtEvento, "isssssiii",
                $evento->getId(), $evento->getNombre(), $evento->getDescripcion(), $evento->getFecha(),
                $evento->getHoraInicio(), $evento->getHoraFin(), $evento->getAforo(),
                $evento->getInstructorId(), $evento->getEstado()
            );
            if (!mysqli_stmt_execute($stmtEvento)) {
                throw new Exception("Error al insertar el evento: " . mysqli_stmt_error($stmtEvento));
            }

            // 3. Insertar la reserva de la(s) sala(s)
            $salasStr = implode('$', $salas);
            $queryReserva = "INSERT INTO tbreservasala (tbsalaid, tbeventoid, tbreservafecha, tbreservahorainicio, tbreservahorafin) VALUES (?, ?, ?, ?, ?)";
            $stmtReserva = mysqli_prepare($conn, $queryReserva);
            mysqli_stmt_bind_param(
                $stmtReserva, "sisss",
                $salasStr, $evento->getId(), $evento->getFecha(),
                $evento->getHoraInicio(), $evento->getHoraFin()
            );
            if (!mysqli_stmt_execute($stmtReserva)) {
                throw new Exception("Error al reservar las salas: " . mysqli_stmt_error($stmtReserva));
            }

            mysqli_commit($conn);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            return $e->getMessage();
        } finally {
            if (isset($stmtEvento)) mysqli_stmt_close($stmtEvento);
            if (isset($stmtReserva)) mysqli_stmt_close($stmtReserva);
            mysqli_close($conn);
        }
    }

    public function actualizarEvento($evento, $salas)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return "Error de conexión a la base de datos.";
        $conn->set_charset('utf8');
        mysqli_autocommit($conn, false);

        try {
            // 1. Actualizar tbevento
            $queryEvento = "UPDATE tbevento SET tbeventonombre=?, tbeventodescripcion=?, tbeventofecha=?, tbeventohorainicio=?, tbeventohorafin=?, tbeventoaforo=?, tbinstructorid=?, tbeventoestado=? WHERE tbeventoid=?";
            $stmtEvento = mysqli_prepare($conn, $queryEvento);
            mysqli_stmt_bind_param(
                $stmtEvento, "sssssiiii",
                $evento->getNombre(), $evento->getDescripcion(), $evento->getFecha(), $evento->getHoraInicio(),
                $evento->getHoraFin(), $evento->getAforo(), $evento->getInstructorId(),
                $evento->getEstado(), $evento->getId()
            );
            if (!mysqli_stmt_execute($stmtEvento)) {
                throw new Exception("Error al actualizar el evento: " . mysqli_stmt_error($stmtEvento));
            }

            // 2. Actualizar tbreservasala (SOLO FECHA Y HORA, NO LAS SALAS)
            $salasStr = implode('$', $salas); // Usamos las salas originales
            $queryReserva = "UPDATE tbreservasala SET tbsalaid=?, tbreservafecha=?, tbreservahorainicio=?, tbreservahorafin=? WHERE tbeventoid=?";
            $stmtReserva = mysqli_prepare($conn, $queryReserva);
            mysqli_stmt_bind_param(
                $stmtReserva, "ssssi",
                $salasStr, $evento->getFecha(), $evento->getHoraInicio(),
                $evento->getHoraFin(), $evento->getId()
            );
            if (!mysqli_stmt_execute($stmtReserva)) {
                throw new Exception("Error al actualizar la reserva de salas: " . mysqli_stmt_error($stmtReserva));
            }

            mysqli_commit($conn);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            return $e->getMessage();
        } finally {
            if (isset($stmtEvento)) mysqli_stmt_close($stmtEvento);
            if (isset($stmtReserva)) mysqli_stmt_close($stmtReserva);
            mysqli_close($conn);
        }
    }

    public function eliminarEvento($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return false;
        $conn->set_charset('utf8');
        mysqli_autocommit($conn, false);

        try {
            // 1. Eliminar reservas de clientes para este evento
            $queryClientes = "DELETE FROM tbreserva WHERE tbeventoid = ?";
            $stmtClientes = mysqli_prepare($conn, $queryClientes);
            mysqli_stmt_bind_param($stmtClientes, "i", $id);
            if (!mysqli_stmt_execute($stmtClientes)) {
                throw new Exception("Error al eliminar las reservas de los clientes.");
            }

            // 2. Eliminar la reserva de la sala para este evento
            $queryReserva = "DELETE FROM tbreservasala WHERE tbeventoid = ?";
            $stmtReserva = mysqli_prepare($conn, $queryReserva);
            mysqli_stmt_bind_param($stmtReserva, "i", $id);
            if (!mysqli_stmt_execute($stmtReserva)) {
                throw new Exception("Error al eliminar la reserva de sala.");
            }

            // 3. Eliminar el evento principal
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
            if (isset($stmtClientes)) mysqli_stmt_close($stmtClientes);
            if (isset($stmtReserva)) mysqli_stmt_close($stmtReserva);
            if (isset($stmtEvento)) mysqli_stmt_close($stmtEvento);
            mysqli_close($conn);
        }
    }

    public function verificarDisponibilidadSalas($salas, $fecha, $horaInicio, $horaFin, $eventoIdExcluir = null)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return ["Error de conexión"];
        $conn->set_charset('utf8');

        $query = "SELECT tbsalaid FROM tbreservasala WHERE tbreservafecha = ? AND (? < tbreservahorafin AND ? > tbreservahorainicio)";
        if ($eventoIdExcluir) {
            $query .= " AND tbeventoid != ?";
        }
        $stmt = mysqli_prepare($conn, $query);
        if ($eventoIdExcluir) {
            mysqli_stmt_bind_param($stmt, "sssi", $fecha, $horaInicio, $horaFin, $eventoIdExcluir);
        } else {
            mysqli_stmt_bind_param($stmt, "sss", $fecha, $horaInicio, $horaFin);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $salasOcupadasIds = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $salasOcupadasIds = array_merge($salasOcupadasIds, explode('$', $row['tbsalaid']));
        }
        mysqli_stmt_close($stmt);

        $salasOcupadasIds = array_unique(array_filter($salasOcupadasIds));
        $conflictos = array_intersect($salas, $salasOcupadasIds);

        if (!empty($conflictos)) {
            $listaConflictos = implode(',', array_map('intval', $conflictos));
            $queryNombres = "SELECT tbsalanombre FROM tbsala WHERE tbsalaid IN ($listaConflictos)";
            $resultNombres = mysqli_query($conn, $queryNombres);
            $nombresSalasConflicto = [];
            while ($rowNombre = mysqli_fetch_assoc($resultNombres)) {
                $nombresSalasConflicto[] = $rowNombre['tbsalanombre'];
            }
            mysqli_close($conn);
            return $nombresSalasConflicto;
        }

        mysqli_close($conn);
        return [];
    }

    public function getSalaIdsPorEventoId($eventoId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return null;
        $conn->set_charset('utf8');

        $query = "SELECT tbsalaid FROM tbreservasala WHERE tbeventoid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $eventoId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return $row ? $row['tbsalaid'] : null;
    }

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
        $todosLosSalaIds = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $eventosData[] = $row;
            if (!empty($row['tbsalaid'])) {
                $todosLosSalaIds = array_merge($todosLosSalaIds, explode('$', $row['tbsalaid']));
            }
        }

        $todosLosSalaIds = array_unique(array_filter($todosLosSalaIds));
        $mapaNombresSalas = [];
        if (!empty($todosLosSalaIds)) {
            $listaIds = implode(',', array_map('intval', $todosLosSalaIds));
            $queryNombres = "SELECT tbsalaid, tbsalanombre FROM tbsala WHERE tbsalaid IN ($listaIds)";
            $resultNombres = mysqli_query($conn, $queryNombres);
            while ($rowNombre = mysqli_fetch_assoc($resultNombres)) {
                $mapaNombresSalas[$rowNombre['tbsalaid']] = $rowNombre['tbsalanombre'];
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

            $nombresConcatenados = '';
            if (!empty($row['tbsalaid'])) {
                $ids = explode('$', $row['tbsalaid']);
                $nombresArr = [];
                foreach ($ids as $id) {
                    if (isset($mapaNombresSalas[trim($id)])) {
                        $nombresArr[] = $mapaNombresSalas[trim($id)];
                    }
                }
                $nombresConcatenados = implode(', ', $nombresArr);
            }
            $evento->setSalasNombre($nombresConcatenados);
            $eventos[] = $evento;
        }

        mysqli_close($conn);
        return $eventos;
    }
}

?>