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
        if (!$conn) return "Error de conexión a la base de datos.";
        $conn->set_charset('utf8');
        mysqli_autocommit($conn, false);

        try {
            $nuevoEventoId = 1;
            $resultId = mysqli_query($conn, "SELECT MAX(tbeventoid) as max_id FROM tbevento");
            if ($rowId = mysqli_fetch_assoc($resultId)) {
                $nuevoEventoId = ($rowId['max_id'] ?? 0) + 1;
            }
            $evento->setId($nuevoEventoId);

            // MODIFICADO: Se añade tbeventotipo
            $queryEvento = "INSERT INTO tbevento (tbeventoid, tbinstructorid, tbeventotipo, tbeventonombre, tbeventodescripcion, tbeventofecha, tbeventohorainicio, tbeventohorafin, tbeventoaforo, tbeventoactivo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtEvento = mysqli_prepare($conn, $queryEvento);
            mysqli_stmt_bind_param(
                $stmtEvento, "isssssssii",
                $evento->getId(), $evento->getInstructorId(), $evento->getTipo(), $evento->getNombre(), $evento->getDescripcion(), $evento->getFecha(),
                $evento->getHoraInicio(), $evento->getHoraFin(), $evento->getAforo(), $evento->getActivo()
            );
            if (!mysqli_stmt_execute($stmtEvento)) throw new Exception("Error al insertar el evento: " . mysqli_stmt_error($stmtEvento));

            $salaReserva = new SalaReserva(0, $salas, $evento->getId(), $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin());
            if (!$this->salaReservasData->insertarReservaDeSala($salaReserva, $conn)) throw new Exception("Error al reservar las salas.");

            mysqli_commit($conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            return $e->getMessage();
        } finally {
            if (isset($stmtEvento)) mysqli_stmt_close($stmtEvento);
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
            // MODIFICADO: Se añade tbeventotipo
            $queryEvento = "UPDATE tbevento SET tbinstructorid=?, tbeventotipo=?, tbeventonombre=?, tbeventodescripcion=?, tbeventofecha=?, tbeventohorainicio=?, tbeventohorafin=?, tbeventoaforo=?, tbeventoactivo=? WHERE tbeventoid=?";
            $stmtEvento = mysqli_prepare($conn, $queryEvento);
            mysqli_stmt_bind_param(
                $stmtEvento, "issssssiii",
                $evento->getInstructorId(), $evento->getTipo(), $evento->getNombre(), $evento->getDescripcion(), $evento->getFecha(), $evento->getHoraInicio(),
                $evento->getHoraFin(), $evento->getAforo(), $evento->getActivo(), $evento->getId()
            );
            if (!mysqli_stmt_execute($stmtEvento)) throw new Exception("Error al actualizar el evento: " . mysqli_stmt_error($stmtEvento));

            $salaReserva = new SalaReserva(0, $salas, $evento->getId(), $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin());
            if (!$this->salaReservasData->actualizarReservaDeSala($salaReserva, $conn)) throw new Exception("Error al actualizar la reserva de salas.");

            mysqli_commit($conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            return $e->getMessage();
        } finally {
            if (isset($stmtEvento)) mysqli_stmt_close($stmtEvento);
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
            $queryClientes = "DELETE FROM tbreservaevento WHERE tbreservaeventoeventoid = ?";
            $stmtClientes = mysqli_prepare($conn, $queryClientes);
            mysqli_stmt_bind_param($stmtClientes, "i", $id);
            if (!mysqli_stmt_execute($stmtClientes)) throw new Exception("Error al eliminar las reservas de los clientes.");

            if (!$this->salaReservasData->eliminarReservaDeSalaPorEvento($id, $conn)) throw new Exception("Error al eliminar la reserva de sala.");

            $queryEvento = "DELETE FROM tbevento WHERE tbeventoid = ?";
            $stmtEvento = mysqli_prepare($conn, $queryEvento);
            mysqli_stmt_bind_param($stmtEvento, "i", $id);
            if (!mysqli_stmt_execute($stmtEvento)) throw new Exception("Error al eliminar el evento.");

            mysqli_commit($conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            // Puedes loguear el error: error_log($e->getMessage());
            return false;
        } finally {
            if (isset($stmtClientes)) mysqli_stmt_close($stmtClientes);
            if (isset($stmtEvento)) mysqli_stmt_close($stmtEvento);
            mysqli_close($conn);
        }
    }

    public function getAllEventos()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return [];
        $conn->set_charset('utf8');
        $query = "SELECT e.*, i.tbinstructornombre FROM tbevento e LEFT JOIN tbinstructor i ON e.tbinstructorid = i.tbinstructorid ORDER BY e.tbeventofecha, e.tbeventohorainicio";
        $result = mysqli_query($conn, $query);
        $eventos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // MODIFICADO: Se añade tbeventotipo al constructor
            $evento = new Evento($row['tbeventoid'],  $row['tbinstructorid'], $row['tbeventotipo'], $row['tbeventonombre'], $row['tbeventodescripcion'], $row['tbeventofecha'], $row['tbeventohorainicio'], $row['tbeventohorafin'], $row['tbeventoaforo'], $row['tbeventoactivo']);
            $evento->setInstructorNombre($row['tbinstructornombre'] ?? 'No asignado');
            $salaIds = $this->salaReservasData->getSalaIdsPorEventoId($evento->getId());
            if (!empty($salaIds)) {
                $listaIds = implode(',', array_map('intval', $salaIds));
                $queryNombres = "SELECT tbsalanombre FROM tbsala WHERE tbsalaid IN ($listaIds)";
                $resultNombres = mysqli_query($conn, $queryNombres);
                $nombresArr = [];
                while ($rowNombre = mysqli_fetch_assoc($resultNombres)) $nombresArr[] = $rowNombre['tbsalanombre'];
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
        $query = "SELECT e.*, i.tbinstructornombre FROM tbevento e LEFT JOIN tbinstructor i ON e.tbinstructorid = i.tbinstructorid WHERE e.tbeventoid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        if (!$row) {
            mysqli_close($conn);
            return null;
        }
        // MODIFICADO: Se añade tbeventotipo al constructor
        $evento = new Evento($row['tbeventoid'],  $row['tbinstructorid'], $row['tbeventotipo'], $row['tbeventonombre'], $row['tbeventodescripcion'], $row['tbeventofecha'], $row['tbeventohorainicio'], $row['tbeventohorafin'], $row['tbeventoaforo'], $row['tbeventoactivo']);
        $evento->setInstructorNombre($row['tbinstructornombre'] ?? 'No asignado');
        $salaIds = $this->salaReservasData->getSalaIdsPorEventoId($id);
        if (!empty($salaIds)) {
            $listaIds = implode(',', array_map('intval', $salaIds));
            $queryNombres = "SELECT tbsalanombre FROM tbsala WHERE tbsalaid IN ($listaIds)";
            $resultNombres = mysqli_query($conn, $queryNombres);
            $nombresArr = [];
            while ($rowNombre = mysqli_fetch_assoc($resultNombres)) $nombresArr[] = $rowNombre['tbsalanombre'];
            $evento->setSalasNombre(implode(', ', $nombresArr));
        }
        mysqli_close($conn);
        return $evento;
    }

    public function getAllEventosActivos()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return [];
        $conn->set_charset('utf8');
        $query = "SELECT e.*, i.tbinstructornombre,
                         (SELECT COUNT(*) FROM tbreservaevento WHERE tbreservaeventoeventoid = e.tbeventoid) as reservas_count
                  FROM tbevento e
                  LEFT JOIN tbinstructor i ON e.tbinstructorid = i.tbinstructorid
                  WHERE e.tbeventoactivo = 1 AND e.tbeventofecha >= CURDATE()
                  ORDER BY e.tbeventofecha, e.tbeventohorainicio";
        $result = mysqli_query($conn, $query);
        $eventos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // MODIFICADO: Se añade tbeventotipo al constructor
            $evento = new Evento($row['tbeventoid'], $row['tbinstructorid'], $row['tbeventotipo'], $row['tbeventonombre'], $row['tbeventodescripcion'], $row['tbeventofecha'], $row['tbeventohorainicio'], $row['tbeventohorafin'], $row['tbeventoaforo'],  $row['tbeventoactivo']);
            $evento->setInstructorNombre($row['tbinstructornombre'] ?? 'No asignado');
            $evento->setReservasCount($row['reservas_count']);
            $eventos[] = $evento;
        }
        mysqli_close($conn);
        return $eventos;
    }
}
?>