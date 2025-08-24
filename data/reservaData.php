<?php
include_once 'data.php';
include_once '../domain/reserva.php';

class ReservaData extends Data
{

    public function insertarReserva($reserva)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "INSERT INTO tbreserva (tbclienteid, tbeventoid, tbreservafecha, tbreservahorainicio, tbreservahorafin, tbreservaestado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iissss",
            $reserva->getClienteId(), $reserva->getEventoId(), $reserva->getFecha(),
            $reserva->getHoraInicio(), $reserva->getHoraFin(), $reserva->getEstado()
        );

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarEstadoReserva($id, $estado)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "UPDATE tbreserva SET tbreservaestado=? WHERE tbreservaid=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $estado, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getReservasPorFecha($fecha)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT r.*, c.tbclientenombre, e.tbeventonombre 
                  FROM tbreserva r
                  JOIN tbcliente c ON r.tbclienteid = c.tbclienteid
                  LEFT JOIN tbevento e ON r.tbeventoid = e.tbeventoid
                  WHERE r.tbreservafecha = ? AND r.tbreservaestado = 'activa'";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $fecha);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new Reserva(
                $row['tbreservaid'], $row['tbclienteid'], $row['tbeventoid'], $row['tbreservafecha'],
                $row['tbreservahorainicio'], $row['tbreservahorafin'], $row['tbreservaestado']
            );
            $reserva->setClienteNombre($row['tbclientenombre']);
            if ($row['tbeventonombre']) {
                $reserva->setEventoNombre($row['tbeventonombre']);
            }
            $reservas[] = $reserva;
        }
        mysqli_close($conn);
        return $reservas;
    }

    public function getReservasPorCliente($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT r.*, e.tbeventonombre 
                  FROM tbreserva r 
                  LEFT JOIN tbevento e ON r.tbeventoid = e.tbeventoid
                  WHERE r.tbclienteid = ? 
                  ORDER BY r.tbreservafecha DESC, r.tbreservahorainicio DESC";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new Reserva(
                $row['tbreservaid'], $row['tbclienteid'], $row['tbeventoid'], $row['tbreservafecha'],
                $row['tbreservahorainicio'], $row['tbreservahorafin'], $row['tbreservaestado']
            );
            if ($row['tbeventonombre']) {
                $reserva->setEventoNombre($row['tbeventonombre']);
            }
            $reservas[] = $reserva;
        }
        mysqli_close($conn);
        return $reservas;
    }

    /**
     * NUEVA FUNCIÓN: Busca una reserva cancelada específica para un cliente.
     * El operador <=> es NULL-safe, por lo que compara correctamente si eventoId es NULL.
     */
    public function findCanceledReserva($clienteId, $fecha, $horaInicio, $eventoId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbreserva 
                  WHERE tbclienteid = ? 
                  AND tbreservafecha = ? 
                  AND tbreservahorainicio = ? 
                  AND tbeventoid <=> ? 
                  AND tbreservaestado = 'cancelada'
                  LIMIT 1";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isss", $clienteId, $fecha, $horaInicio, $eventoId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $reserva = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $reserva = new Reserva(
                $row['tbreservaid'], $row['tbclienteid'], $row['tbeventoid'], $row['tbreservafecha'],
                $row['tbreservahorainicio'], $row['tbreservahorafin'], $row['tbreservaestado']
            );
        }

        mysqli_close($conn);
        return $reserva;
    }
}

?>