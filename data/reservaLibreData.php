<?php
include_once 'data.php';
include_once '../domain/reservaLibre.php';

class ReservaLibreData extends Data
{
    public function insertarReservaLibre($reserva)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        // MODIFICADO: Añadida la nueva columna
        $query = "INSERT INTO tbreservalibre (tbreservalibreclienteid, tbreservalibrehorariolibreid, tbreservalibreclienteresponsableid, tbreservalibreactivo) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iiii",
            $reserva->getClienteId(),
            $reserva->getHorarioLibreId(),
            $reserva->getClienteResponsableId(), // NUEVO
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
        // MODIFICADO: Se obtiene el nombre del responsable
        $query = "SELECT
                    rl.*,
                    hl.tbhorariolibrefecha, hl.tbhorariolibrehora,
                    s.tbsalanombre,
                    i.tbinstructornombre,
                    resp.tbclientenombre AS nombreresponsable
                  FROM tbreservalibre rl
                  LEFT JOIN tbhorariolibre hl ON rl.tbreservalibrehorariolibreid = hl.tbhorariolibreid
                  LEFT JOIN tbsala s ON hl.tbhorariolibresalaid = s.tbsalaid
                  LEFT JOIN tbinstructor i ON hl.tbhorariolibreinstructorid = i.tbinstructorid
                  LEFT JOIN tbcliente resp ON rl.tbreservalibreclienteresponsableid = resp.tbclienteid
                  WHERE rl.tbreservalibreclienteid = ?
                  ORDER BY hl.tbhorariolibrefecha DESC, hl.tbhorariolibrehora DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new ReservaLibre(
                $row['tbreservalibreid'], $row['tbreservalibreclienteid'], $row['tbreservalibrehorariolibreid'], $row['tbreservalibreclienteresponsableid'], $row['tbreservalibreactivo']
            );
            $reserva->setFecha($row['tbhorariolibrefecha'] ?? 'N/A');
            $reserva->setHora($row['tbhorariolibrehora'] ?? 'N/A');
            $reserva->setSalaNombre($row['tbsalanombre'] ?? 'N/A');
            $reserva->setInstructorNombre($row['tbinstructornombre'] ?? 'N/A');
            $reserva->setClienteResponsableNombre($row['nombreresponsable'] ?? 'N/A'); // NUEVO
            $reservas[] = $reserva;
        }
        mysqli_close($conn);
        return $reservas;
    }

    public function getAllReservasLibre()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        // MODIFICADO: Se obtiene el nombre del responsable
        $query = "SELECT
                    rl.*,
                    c.tbclientenombre,
                    hl.tbhorariolibrefecha, hl.tbhorariolibrehora,
                    s.tbsalanombre,
                    i.tbinstructornombre,
                    resp.tbclientenombre AS nombreresponsable
                  FROM tbreservalibre rl
                  LEFT JOIN tbcliente c ON rl.tbreservalibreclienteid = c.tbclienteid
                  LEFT JOIN tbhorariolibre hl ON rl.tbreservalibrehorariolibreid = hl.tbhorariolibreid
                  LEFT JOIN tbsala s ON hl.tbhorariolibresalaid = s.tbsalaid
                  LEFT JOIN tbinstructor i ON hl.tbhorariolibreinstructorid = i.tbinstructorid
                  LEFT JOIN tbcliente resp ON rl.tbreservalibreclienteresponsableid = resp.tbclienteid
                  ORDER BY hl.tbhorariolibrefecha DESC, hl.tbhorariolibrehora DESC";
        $result = mysqli_query($conn, $query);
        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new ReservaLibre(
                $row['tbreservalibreid'], $row['tbreservalibreclienteid'], $row['tbreservalibrehorariolibreid'], $row['tbreservalibreclienteresponsableid'], $row['tbreservalibreactivo']
            );
            $reserva->setClienteNombre($row['tbclientenombre'] ?? 'Cliente Eliminado');
            $reserva->setFecha($row['tbhorariolibrefecha'] ?? 'N/A');
            $reserva->setHora($row['tbhorariolibrehora'] ?? 'N/A');
            $reserva->setSalaNombre($row['tbsalanombre'] ?? 'Sala Eliminada');
            $reserva->setInstructorNombre($row['tbinstructornombre'] ?? 'N/A');
            $reserva->setClienteResponsableNombre($row['nombreresponsable'] ?? 'N/A'); // NUEVO
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
                $row['tbreservalibreid'], $row['tbreservalibreclienteid'], $row['tbreservalibrehorariolibreid'], $row['tbreservalibreclienteresponsableid'], $row['tbreservalibreactivo']
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