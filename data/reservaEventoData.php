<?php
include_once 'data.php';
include_once '../domain/reservaEvento.php';

class ReservaEventoData extends Data
{
    public function insertarReservaEvento($reserva)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "INSERT INTO tbreservaevento (tbreservaeventoclienteid, tbreservaeventoeventoid, tbreservaeventofecha, tbreservaeventohorainicio, tbreservaeventohorafin, tbreservaeventoactivo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iisssi",
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
        $query = "SELECT re.*, e.tbeventonombre, i.tbinstructornombre
                  FROM tbreservaevento re
                  JOIN tbevento e ON re.tbreservaeventoeventoid = e.tbeventoid
                  LEFT JOIN tbinstructor i ON e.tbinstructorid = i.tbinstructorid
                  WHERE re.tbreservaeventoclienteid = ? ORDER BY re.tbreservaeventofecha DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new ReservaEvento($row['tbreservaeventoid'], $row['tbreservaeventoclienteid'], $row['tbreservaeventoeventoid'], $row['tbreservaeventofecha'], $row['tbreservaeventohorainicio'], $row['tbreservaeventohorafin'], $row['tbreservaeventoactivo']);
            $reserva->setEventoNombre($row['tbeventonombre']);
            $reserva->setInstructorNombre($row['tbinstructornombre'] ?? 'N/A');
            $reservas[] = $reserva;
        }
        mysqli_close($conn);
        return $reservas;
    }

    public function getReservasPorEvento($eventoId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbreservaevento WHERE tbreservaeventoeventoid = ? AND tbreservaeventoactivo = 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $eventoId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reservas[] = new ReservaEvento($row['tbreservaeventoid'], $row['tbreservaeventoclienteid'], $row['tbreservaeventoeventoid'], $row['tbreservaeventofecha'], $row['tbreservaeventohorainicio'], $row['tbreservaeventohorafin'], $row['tbreservaeventoactivo']);
        }
        mysqli_close($conn);
        return $reservas;
    }

    public function getAllReservasEvento()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT
                    re.*,
                    e.tbeventonombre,
                    c.tbclientenombre,
                    i.tbinstructornombre
                  FROM tbreservaevento re
                  JOIN tbevento e ON re.tbreservaeventoeventoid = e.tbeventoid
                  JOIN tbcliente c ON re.tbreservaeventoclienteid = c.tbclienteid
                  LEFT JOIN tbinstructor i ON e.tbinstructorid = i.tbinstructorid
                  ORDER BY re.tbreservaeventofecha DESC, re.tbreservaeventohorainicio DESC";
        $result = mysqli_query($conn, $query);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new ReservaEvento(
                $row['tbreservaeventoid'], $row['tbreservaeventoclienteid'], $row['tbreservaeventoeventoid'], $row['tbreservaeventofecha'], $row['tbreservaeventohorainicio'], $row['tbreservaeventohorafin'], $row['tbreservaeventoactivo']
            );
            $reserva->setClienteNombre($row['tbclientenombre']);
            $reserva->setEventoNombre($row['tbeventonombre']);
            $reserva->setInstructorNombre($row['tbinstructornombre'] ?? 'N/A');
            $reservas[] = $reserva;
        }
        mysqli_close($conn);
        return $reservas;
    }
}