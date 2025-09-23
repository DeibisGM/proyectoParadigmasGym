<?php

include_once 'data.php';
include_once '../domain/horario.php';

class HorarioData extends Data
{

    public function getAllHorarios()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbhorario ORDER BY tbhorarioid ASC;";
        $result = mysqli_query($conn, $querySelect);
        mysqli_close($conn);

        $horarios = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $horarios[] = new Horario(
                $row['tbhorarioid'],
                $row['tbhorariodia'],
                $row['tbhorarioactivo'],
                $row['tbhorarioapertura'],
                $row['tbhorariocierre'],
                $row['tbhorariobloqueo']
            );
        }
        return $horarios;
    }

    public function updateHorario($horario)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbhorario SET tbhorarioactivo=?, tbhorarioapertura=?, tbhorariocierre=?, tbhorariobloqueo=? WHERE tbhorarioid=?;";

        $stmt = mysqli_prepare($conn, $queryUpdate);

        $id = $horario->getId();
        $activo = $horario->isActivo();
        $apertura = $horario->getApertura();
        $cierre = $horario->getCierre();
        $bloqueos = $horario->getBloqueosAsString();

        if ($activo == 0) {
            $apertura = null;
            $cierre = null;
            $bloqueos = '';
        }

        mysqli_stmt_bind_param($stmt, 'isssi', $activo, $apertura, $cierre, $bloqueos, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getHorarioDelDia($diaId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $querySelect = "SELECT * FROM tbhorario WHERE tbhorarioid = ?;";
        $stmt = mysqli_prepare($conn, $querySelect);
        mysqli_stmt_bind_param($stmt, "i", $diaId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);

        if ($row) {
            return new Horario(
                $row['tbhorarioid'],
                $row['tbhorariodia'],
                $row['tbhorarioactivo'],
                $row['tbhorarioapertura'],
                $row['tbhorariocierre'],
                $row['tbhorariobloqueo']
            );
        }
        return null;
    }
}
