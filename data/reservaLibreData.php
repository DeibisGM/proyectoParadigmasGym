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

    public function existeReservaLibre($clienteId, $horarioLibreId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT tbreservalibreid FROM tbreservalibre WHERE tbreservalibreclienteid = ? AND tbreservalibrehorariolibreid = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $clienteId, $horarioLibreId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existe = mysqli_num_rows($result) > 0;
        mysqli_close($conn);
        return $existe;
    }

    public function getReservasLibrePorCliente($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT
                    rl.tbreservalibreid, rl.tbreservalibreclienteid, rl.tbreservalibrehorariolibreid, rl.tbreservalibreactivo,
                    hl.tbhorariolibrefecha, hl.tbhorariolibrehora,
                    s.tbsalanombre,
                    i.tbinstructornombre
                  FROM tbreservalibre rl
                  LEFT JOIN tbhorariolibre hl ON rl.tbreservalibrehorariolibreid = hl.tbhorariolibreid
                  LEFT JOIN tbsala s ON hl.tbhorariolibresalaid = s.tbsalaid
                  LEFT JOIN tbinstructor i ON hl.tbhorariolibreinstructorid = i.tbinstructorid
                  WHERE rl.tbreservalibreclienteid = ?
                  ORDER BY hl.tbhorariolibrefecha DESC, hl.tbhorariolibrehora DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new ReservaLibre(
                $row['tbreservalibreid'], $row['tbreservalibreclienteid'], $row['tbreservalibrehorariolibreid'], $row['tbreservalibreactivo']
            );
            $reserva->setFecha($row['tbhorariolibrefecha'] ?? 'N/A');
            $reserva->setHora($row['tbhorariolibrehora'] ?? 'N/A');
            $reserva->setSalaNombre($row['tbsalanombre'] ?? 'N/A');
            $reserva->setInstructorNombre($row['tbinstructornombre'] ?? 'N/A');
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
                    rl.tbreservalibreid, rl.tbreservalibreclienteid, rl.tbreservalibrehorariolibreid, rl.tbreservalibreactivo,
                    c.tbclientenombre,
                    hl.tbhorariolibrefecha, hl.tbhorariolibrehora,
                    s.tbsalanombre,
                    i.tbinstructornombre
                  FROM tbreservalibre rl
                  LEFT JOIN tbcliente c ON rl.tbreservalibreclienteid = c.tbclienteid
                  LEFT JOIN tbhorariolibre hl ON rl.tbreservalibrehorariolibreid = hl.tbhorariolibreid
                  LEFT JOIN tbsala s ON hl.tbhorariolibresalaid = s.tbsalaid
                  LEFT JOIN tbinstructor i ON hl.tbhorariolibreinstructorid = i.tbinstructorid
                  ORDER BY hl.tbhorariolibrefecha DESC, hl.tbhorariolibrehora DESC";
        $result = mysqli_query($conn, $query);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new ReservaLibre(
                $row['tbreservalibreid'], $row['tbreservalibreclienteid'], $row['tbreservalibrehorariolibreid'], $row['tbreservalibreactivo']
            );
            $reserva->setClienteNombre($row['tbclientenombre'] ?? 'Cliente Eliminado');
            $reserva->setFecha($row['tbhorariolibrefecha'] ?? 'N/A');
            $reserva->setHora($row['tbhorariolibrehora'] ?? 'N/A');
            $reserva->setSalaNombre($row['tbsalanombre'] ?? 'Sala Eliminada');
            $reserva->setInstructorNombre($row['tbinstructornombre'] ?? 'N/A');
            $reservas[] = $reserva;
        }
        mysqli_close($conn);
        return $reservas;
    }

    public function getReservaLibreById($reservaId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbreservalibre WHERE tbreservalibreid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $reservaId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);

        if ($row) {
            return new ReservaLibre(
                $row['tbreservalibreid'], $row['tbreservalibreclienteid'], $row['tbreservalibrehorariolibreid'], $row['tbreservalibreactivo']
            );
        }
        return null;
    }

    public function eliminarReservaLibre($reservaId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "DELETE FROM tbreservalibre WHERE tbreservalibreid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $reservaId);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }
}