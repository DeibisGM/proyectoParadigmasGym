<?php
include_once 'data.php';
include_once '../domain/reservaLibre.php';

class ReservaLibreData extends Data
{
    public function insertarReservaLibre($reserva)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "INSERT INTO tbreservalibre (tbreservalibreclienteid, tbreservalibrehorariolibreid, tbreservalibrefecha, tbreservalibrehora, tbreservalibreestado) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iisss",
            $reserva->getClienteId(), $reserva->getHorarioLibreId(), $reserva->getFecha(),
            $reserva->getHora(), $reserva->getEstado()
        );
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getReservasLibrePorCliente($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbreservalibre WHERE tbreservalibreclienteid = ? ORDER BY tbreservalibrefecha DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reservas[] = new ReservaLibre($row['tbreservalibreid'], $row['tbreservalibreclienteid'], $row['tbreservalibrehorariolibreid'], $row['tbreservalibrefecha'], $row['tbreservalibrehora'], $row['tbreservalibreestado']);
        }
        mysqli_close($conn);
        return $reservas;
    }
}
?>