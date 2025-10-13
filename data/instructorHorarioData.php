<?php
include_once 'data.php';
include '../domain/instructorHorario.php';

class InstructorHorarioData extends Data
{
    public function insertarTBInstructorHorario($instructorHorario)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbinstructorhorario (
            tbinstructorhorarioid,
            tbinstructorid,
            tbinstructorhorariodia,
            tbinstructorhorariohorainicio,
            tbinstructorhorariohorafin,
            tbinstructorhorarioactivo
        ) VALUES (
            '" . mysqli_real_escape_string($conn, $instructorHorario->getId()) . "',
            '" . mysqli_real_escape_string($conn, $instructorHorario->getInstructorId()) . "',
            '" . mysqli_real_escape_string($conn, $instructorHorario->getDia()) . "',
            '" . mysqli_real_escape_string($conn, $instructorHorario->getHoraInicio()) . "',
            '" . mysqli_real_escape_string($conn, $instructorHorario->getHoraFin()) . "',
            " . $instructorHorario->getActivo() . "
        )";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTBInstructorHorario($instructorHorario)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbinstructorhorario SET
            tbinstructorid = '" . mysqli_real_escape_string($conn, $instructorHorario->getInstructorId()) . "',
            tbinstructorhorariodia = '" . mysqli_real_escape_string($conn, $instructorHorario->getDia()) . "',
            tbinstructorhorariohorainicio = '" . mysqli_real_escape_string($conn, $instructorHorario->getHoraInicio()) . "',
            tbinstructorhorariohorafin = '" . mysqli_real_escape_string($conn, $instructorHorario->getHoraFin()) . "',
            tbinstructorhorarioactivo = " . $instructorHorario->getActivo() . "
            WHERE tbinstructorhorarioid = '" . mysqli_real_escape_string($conn, $instructorHorario->getId()) . "'";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBInstructorHorario($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "UPDATE tbinstructorhorario SET tbinstructorhorarioactivo = 0
                       WHERE tbinstructorhorarioid = '" . mysqli_real_escape_string($conn, $id) . "'";
        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    public function getAllTBInstructorHorario($esAdmin = false)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT ih.*, i.tbinstructornombre
                       FROM tbinstructorhorario ih
                       INNER JOIN tbinstructor i ON ih.tbinstructorid = i.tbinstructorid";

        if (!$esAdmin) {
            $querySelect .= " WHERE ih.tbinstructorhorarioactivo = 1 AND i.tbinstructoractivo = 1";
        }

        $querySelect .= " ORDER BY ih.tbinstructorhorariodia, ih.tbinstructorhorariohorainicio";

        $result = mysqli_query($conn, $querySelect);
        $horarios = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $horario = new InstructorHorario(
                $row['tbinstructorhorarioid'],
                $row['tbinstructorid'],
                $row['tbinstructorhorariodia'],
                $row['tbinstructorhorariohorainicio'],
                $row['tbinstructorhorariohorafin'],
                $row['tbinstructorhorarioactivo']
            );
            $horarios[] = $horario;
        }

        mysqli_close($conn);
        return $horarios;
    }

    public function getHorariosPorInstructor($instructorId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbinstructorhorario
                 WHERE tbinstructorid = '" . mysqli_real_escape_string($conn, $instructorId) . "'
                 AND tbinstructorhorarioactivo = 1
                 ORDER BY tbinstructorhorariodia, tbinstructorhorariohorainicio";

        $result = mysqli_query($conn, $query);
        $horarios = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $horarios[] = new InstructorHorario(
                $row['tbinstructorhorarioid'],
                $row['tbinstructorid'],
                $row['tbinstructorhorariodia'],
                $row['tbinstructorhorariohorainicio'],
                $row['tbinstructorhorariohorafin'],
                $row['tbinstructorhorarioactivo']
            );
        }

        mysqli_close($conn);
        return $horarios;
    }

    public function getHorarioPorId($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbinstructorhorario
                 WHERE tbinstructorhorarioid = '" . mysqli_real_escape_string($conn, $id) . "'";
        $result = mysqli_query($conn, $query);

        $horario = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $horario = new InstructorHorario(
                $row['tbinstructorhorarioid'],
                $row['tbinstructorid'],
                $row['tbinstructorhorariodia'],
                $row['tbinstructorhorariohorainicio'],
                $row['tbinstructorhorariohorafin'],
                $row['tbinstructorhorarioactivo']
            );
        }

        mysqli_close($conn);
        return $horario;
    }

    public function existeHorarioSuperpuesto($instructorId, $dia, $horaInicio, $horaFin, $excluirId = null)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT COUNT(*) as count FROM tbinstructorhorario
                 WHERE tbinstructorid = '" . mysqli_real_escape_string($conn, $instructorId) . "'
                 AND tbinstructorhorariodia = '" . mysqli_real_escape_string($conn, $dia) . "'
                 AND tbinstructorhorarioactivo = 1
                 AND (
                     (tbinstructorhorariohorainicio <= '" . $horaInicio . "' AND tbinstructorhorariohorafin > '" . $horaInicio . "') OR
                     (tbinstructorhorariohorainicio < '" . $horaFin . "' AND tbinstructorhorariohorafin >= '" . $horaFin . "') OR
                     (tbinstructorhorariohorainicio >= '" . $horaInicio . "' AND tbinstructorhorariohorafin <= '" . $horaFin . "')
                 )";

        if ($excluirId) {
            $query .= " AND tbinstructorhorarioid != '" . mysqli_real_escape_string($conn, $excluirId) . "'";
        }

        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);

        return $row['count'] > 0;
    }

    public function getNextHorarioId()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT MAX(CAST(SUBSTRING(tbinstructorhorarioid, 3) AS UNSIGNED)) as max_id
                 FROM tbinstructorhorario
                 WHERE tbinstructorhorarioid LIKE 'IH%'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);

        $nextId = ($row['max_id'] ? intval($row['max_id']) + 1 : 1);
        return 'IH' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
?>