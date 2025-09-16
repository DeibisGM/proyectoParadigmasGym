<?php
include_once 'data.php';
include_once '../domain/evento.php';
include_once 'salaReservasData.php';

class EventoData extends Data
{
    private $salaReservasData;

    public function __construct()
    {
        parent::__construct();
        $this->salaReservasData = new SalaReservasData();
    }

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

            // 3. Insertar la reserva de la(s) sala(s) usando la clase SalaReservasData
            $salaReserva = new SalaReserva(0, $salas, $evento->getId(), $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin());
            if (!$this->salaReservasData->insertarReservaDeSala($salaReserva, $conn)) {
                throw new Exception("Error al reservar las salas.");
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

            // 2. Actualizar tbreservasala
            $salaReserva = new SalaReserva(0, $salas, $evento->getId(), $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin());
            if (!$this->salaReservasData->actualizarReservaDeSala($salaReserva, $conn)) {
                throw new Exception("Error al actualizar la reserva de salas.");
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
            if (!$this->salaReservasData->eliminarReservaDeSalaPorEvento($id, $conn)) {
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

    public function getAllEventos()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return [];
        $conn->set_charset('utf8');

        $query = "SELECT e.*, i.tbinstructornombre
                  FROM tbevento e
                  LEFT JOIN tbinstructor i ON e.tbinstructorid = i.tbinstructorid
                  ORDER BY e.tbeventofecha, e.tbeventohorainicio";
        $result = mysqli_query($conn, $query);

        $eventos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $evento = new Evento(
                $row['tbeventoid'], $row['tbeventonombre'], $row['tbeventodescripcion'], $row['tbeventofecha'],
                $row['tbeventohorainicio'], $row['tbeventohorafin'], $row['tbeventoaforo'],
                $row['tbinstructorid'], $row['tbeventoestado']
            );
            $evento->setInstructorNombre($row['tbinstructornombre'] ?? 'No asignado');

            // Obtener salas y sus nombres
            $salaIds = $this->salaReservasData->getSalaIdsPorEventoId($evento->getId());
            if (!empty($salaIds)) {
                $listaIds = implode(',', array_map('intval', $salaIds));
                $queryNombres = "SELECT tbsalanombre FROM tbsala WHERE tbsalaid IN ($listaIds)";
                $resultNombres = mysqli_query($conn, $queryNombres);
                $nombresArr = [];
                while ($rowNombre = mysqli_fetch_assoc($resultNombres)) {
                    $nombresArr[] = $rowNombre['tbsalanombre'];
                }
                $evento->setSalasNombre(implode(', ', $nombresArr));
            }

            $eventos[] = $evento;
        }

        mysqli_close($conn);
        return $eventos;
    }

    public function getEventoById($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return null;
        $conn->set_charset('utf8');

        $query = "SELECT e.*, i.tbinstructornombre
                  FROM tbevento e
                  LEFT JOIN tbinstructor i ON e.tbinstructorid = i.tbinstructorid
                  WHERE e.tbeventoid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if (!$row) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return null;
        }

        $evento = new Evento(
            $row['tbeventoid'], $row['tbeventonombre'], $row['tbeventodescripcion'], $row['tbeventofecha'],
            $row['tbeventohorainicio'], $row['tbeventohorafin'], $row['tbeventoaforo'],
            $row['tbinstructorid'], $row['tbeventoestado']
        );
        $evento->setInstructorNombre($row['tbinstructornombre'] ?? 'No asignado');

        // Obtener salas y sus nombres
        $salaIds = $this->salaReservasData->getSalaIdsPorEventoId($id);
        if (!empty($salaIds)) {
            $listaIds = implode(',', array_map('intval', $salaIds));
            $queryNombres = "SELECT tbsalanombre FROM tbsala WHERE tbsalaid IN ($listaIds)";
            $resultNombres = mysqli_query($conn, $queryNombres);
            $nombresArr = [];
            while ($rowNombre = mysqli_fetch_assoc($resultNombres)) {
                $nombresArr[] = $rowNombre['tbsalanombre'];
            }
            $evento->setSalasNombre(implode(', ', $nombresArr));
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $evento;
    }
}

?>