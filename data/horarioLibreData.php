<?php
include_once 'data.php';
include_once '../domain/horarioLibre.php';

class HorarioLibreData extends Data
{
    public function insertarHorarioLibre($horario)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryCheck = "SELECT tbhorariolibreid FROM tbhorariolibre WHERE tbhorariolibrefecha = ? AND tbhorariolibrehora = ? AND tbhorariolibresalaid = ?";
        $stmtCheck = mysqli_prepare($conn, $queryCheck);
        mysqli_stmt_bind_param($stmtCheck, "ssi", $horario->getFecha(), $horario->getHora(), $horario->getSalaId());
        mysqli_stmt_execute($stmtCheck);
        $resultCheck = mysqli_stmt_get_result($stmtCheck);
        if (mysqli_num_rows($resultCheck) > 0) {
            mysqli_close($conn);
            return false;
        }

        $query = "INSERT INTO tbhorariolibre (tbhorariolibrefecha, tbhorariolibrehora, tbhorariolibresalaid, tbhorariolibreinstructorid, tbhorariolibrecupos) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssisi",
            $horario->getFecha(), $horario->getHora(), $horario->getSalaId(),
            $horario->getInstructorId(), $horario->getCupos()
        );
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getHorariosPorRangoDeFechas($fechaInicio, $fechaFin)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbhorariolibre WHERE tbhorariolibrefecha BETWEEN ? AND ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $fechaInicio, $fechaFin);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $horarios = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $horarios[] = new HorarioLibre($row['tbhorariolibreid'], $row['tbhorariolibrefecha'], $row['tbhorariolibrehora'], $row['tbhorariolibresalaid'], $row['tbhorariolibreinstructorid'], $row['tbhorariolibrecupos'], $row['tbhorariolibrematriculados'], $row['tbhorariolibreactivo']);
        }
        mysqli_close($conn);
        return $horarios;
    }

    public function eliminarHorarioLibre($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        mysqli_autocommit($conn, false);
        try {
            $queryDeleteReservas = "DELETE FROM tbreservalibre WHERE tbreservalibrehorariolibreid = ?";
            $stmtReservas = mysqli_prepare($conn, $queryDeleteReservas);
            mysqli_stmt_bind_param($stmtReservas, "i", $id);
            mysqli_stmt_execute($stmtReservas);

            $queryDeleteHorario = "DELETE FROM tbhorariolibre WHERE tbhorariolibreid = ?";
            $stmtHorario = mysqli_prepare($conn, $queryDeleteHorario);
            mysqli_stmt_bind_param($stmtHorario, "i", $id);
            mysqli_stmt_execute($stmtHorario);

            mysqli_commit($conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            return false;
        } finally {
            mysqli_close($conn);
        }
    }

    public function getHorarioLibrePorId($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbhorariolibre WHERE tbhorariolibreid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        if ($row) {
            return new HorarioLibre($row['tbhorariolibreid'], $row['tbhorariolibrefecha'], $row['tbhorariolibrehora'], $row['tbhorariolibresalaid'], $row['tbhorariolibreinstructorid'], $row['tbhorariolibrecupos'], $row['tbhorariolibrematriculados'], $row['tbhorariolibreactivo']);
        }
        return null;
    }

    public function incrementarMatriculados($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "UPDATE tbhorariolibre SET tbhorariolibrematriculados = tbhorariolibrematriculados + 1 WHERE tbhorariolibreid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function decrementarMatriculados($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "UPDATE tbhorariolibre SET tbhorariolibrematriculados = tbhorariolibrematriculados - 1 WHERE tbhorariolibreid = ? AND tbhorariolibrematriculados > 0";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }
}
?>