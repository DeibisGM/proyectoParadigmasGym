<?php
include_once 'data.php';
include_once '../domain/salaReserva.php';

class SalaReservasData extends Data
{
    public function verificarDisponibilidad($salas, $fecha, $horaInicio, $horaFin, $eventoIdExcluir = null)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) return ["error" => "Error de conexión a la base de datos."];
        $conn->set_charset('utf8');

        $query = "SELECT tbsalaid, tbeventoid FROM tbreservasala WHERE tbreservafecha = ? AND (? < tbreservahorafin AND ? > tbreservahorainicio)";
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
            return ["conflictos" => $nombresSalasConflicto];
        }

        mysqli_close($conn);
        return ["disponible" => true];
    }

    public function getAllReservasDeSalas()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT rs.tbreservafecha, rs.tbreservahorainicio, rs.tbreservahorafin, rs.tbsalaid, e.tbeventonombre
                  FROM tbreservasala rs
                  JOIN tbevento e ON rs.tbeventoid = e.tbeventoid
                  ORDER BY rs.tbreservafecha, rs.tbreservahorainicio";

        $result = mysqli_query($conn, $query);
        $reservas = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $reservas;
    }

    public function insertarReservaDeSala($salaReserva, $conn)
    {
        $query = "INSERT INTO tbreservasala (tbsalaid, tbeventoid, tbreservafecha, tbreservahorainicio, tbreservahorafin) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        $salasStr = is_array($salaReserva->getSalaId()) ? implode('$', $salaReserva->getSalaId()) : $salaReserva->getSalaId();
        mysqli_stmt_bind_param(
            $stmt, "sisss",
            $salasStr,
            $salaReserva->getEventoId(),
            $salaReserva->getFecha(),
            $salaReserva->getHoraInicio(),
            $salaReserva->getHoraFin()
        );
        return mysqli_stmt_execute($stmt);
    }

    public function actualizarReservaDeSala($salaReserva, $conn)
    {
        $query = "UPDATE tbreservasala SET tbsalaid=?, tbreservafecha=?, tbreservahorainicio=?, tbreservahorafin=? WHERE tbeventoid=?";
        $stmt = mysqli_prepare($conn, $query);
        $salasStr = is_array($salaReserva->getSalaId()) ? implode('$', $salaReserva->getSalaId()) : $salaReserva->getSalaId();
        mysqli_stmt_bind_param(
            $stmt, "ssssi",
            $salasStr,
            $salaReserva->getFecha(),
            $salaReserva->getHoraInicio(),
            $salaReserva->getHoraFin(),
            $salaReserva->getEventoId()
        );
        return mysqli_stmt_execute($stmt);
    }

    public function eliminarReservaDeSalaPorEvento($eventoId, $conn)
    {
        $query = "DELETE FROM tbreservasala WHERE tbeventoid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $eventoId);
        return mysqli_stmt_execute($stmt);
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

        return $row ? explode('$', $row['tbsalaid']) : [];
    }

    public function eliminarReservaDeSalaPorReserva($reservaId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "DELETE FROM tbreservasala WHERE tbreservaid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $reservaId);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }
}
?>