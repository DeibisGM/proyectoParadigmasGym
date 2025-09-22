<?php
include_once 'data.php';
include_once '../domain/reservaEvento.php';

class ReservaEventoData extends Data
{
    public function insertarReservaEvento($reserva)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "INSERT INTO tbreservaevento (tbreservaeventoclienteid, tbreservaeventoeventoid, tbreservaeventofecha, tbreservaeventohorainicio, tbreservaeventohorafin, tbreservaeventoestado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iissss",
            $reserva->getClienteId(), $reserva->getEventoId(), $reserva->getFecha(),
            $reserva->getHoraInicio(), $reserva->getHoraFin(), $reserva->getEstado()
        );
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getReservasEventoPorCliente($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT re.*, e.tbeventonombre 
                  FROM tbreservaevento re
                  JOIN tbevento e ON re.tbreservaeventoeventoid = e.tbeventoid
                  WHERE re.tbreservaeventoclienteid = ? ORDER BY re.tbreservaeventofecha DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new ReservaEvento($row['tbreservaeventoid'], $row['tbreservaeventoclienteid'], $row['tbreservaeventoeventoid'], $row['tbreservaeventofecha'], $row['tbreservaeventohorainicio'], $row['tbreservaeventohorafin'], $row['tbreservaeventoestado']);
            $reserva->setEventoNombre($row['tbeventonombre']);
            $reservas[] = $reserva;
        }
        mysqli_close($conn);
        return $reservas;
    }

    public function getReservasPorEvento($eventoId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbreservaevento WHERE tbreservaeventoeventoid = ? AND tbreservaeventoestado = 'activa'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $eventoId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reservas[] = new ReservaEvento($row['tbreservaeventoid'], $row['tbreservaeventoclienteid'], $row['tbreservaeventoeventoid'], $row['tbreservaeventofecha'], $row['tbreservaeventohorainicio'], $row['tbreservaeventohorafin'], $row['tbreservaeventoestado']);
        }
        mysqli_close($conn);
        return $reservas;
    }
}
?>