<?php
include_once 'data.php';
include_once '../domain/reservaLibre.php';

class ReservaLibreData extends Data
{
    public function insertarReservaLibre($reserva)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "INSERT INTO tbreservalibre (tbreservalibreclienteid, tbreservalibrehorariolibreid, tbreservalibreactivo) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iii",
            $reserva->getClienteId(),
            $reserva->getHorarioLibreId(),
            $reserva->isActivo()
        );
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getReservasLibrePorCliente($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT
                    rl.tbreservalibreid,
                    rl.tbreservalibreclienteid,
                    rl.tbreservalibrehorariolibreid,
                    rl.tbreservalibreactivo,
                    hl.tbhorariolibrefecha,
                    hl.tbhorariolibrehora,
                    s.tbsalanombre
                  FROM tbreservalibre rl
                  JOIN tbhorariolibre hl ON rl.tbreservalibrehorariolibreid = hl.tbhorariolibreid
                  JOIN tbsala s ON hl.tbhorariolibresalaid = s.tbsalaid
                  WHERE rl.tbreservalibreclienteid = ?
                  ORDER BY hl.tbhorariolibrefecha DESC, hl.tbhorariolibrehora DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new ReservaLibre(
                $row['tbreservalibreid'],
                $row['tbreservalibreclienteid'],
                $row['tbreservalibrehorariolibreid'],
                $row['tbreservalibreactivo']
            );
            $reserva->setFecha($row['tbhorariolibrefecha']);
            $reserva->setHora($row['tbhorariolibrehora']);
            $reserva->setSalaNombre($row['tbsalanombre']);
            $reservas[] = $reserva;
        }
        mysqli_close($conn);
        return $reservas;
    }

    public function getAllReservasLibre()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT
                    rl.tbreservalibreid,
                    rl.tbreservalibreclienteid,
                    rl.tbreservalibrehorariolibreid,
                    rl.tbreservalibreactivo,
                    c.tbclientenombre,
                    hl.tbhorariolibrefecha,
                    hl.tbhorariolibrehora,
                    s.tbsalanombre
                  FROM tbreservalibre rl
                  JOIN tbcliente c ON rl.tbreservalibreclienteid = c.tbclienteid
                  JOIN tbhorariolibre hl ON rl.tbreservalibrehorariolibreid = hl.tbhorariolibreid
                  JOIN tbsala s ON hl.tbhorariolibresalaid = s.tbsalaid
                  ORDER BY hl.tbhorariolibrefecha DESC, hl.tbhorariolibrehora DESC";
        $result = mysqli_query($conn, $query);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new ReservaLibre(
                $row['tbreservalibreid'],
                $row['tbreservalibreclienteid'],
                $row['tbreservalibrehorariolibreid'],
                $row['tbreservalibreactivo']
            );
            $reserva->setClienteNombre($row['tbclientenombre']);
            $reserva->setFecha($row['tbhorariolibrefecha']);
            $reserva->setHora($row['tbhorariolibrehora']);
            $reserva->setSalaNombre($row['tbsalanombre']);
            $reservas[] = $reserva;
        }
        mysqli_close($conn);
        return $reservas;
    }
}
?>