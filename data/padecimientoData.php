<?php
include_once 'data.php';
include_once '../domain/padecimiento.php';

class PadecimientoData extends Data {

    public function insertarTbpadecimiento($padecimiento) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryGetLastId = "SELECT MAX(tbpadecimientoid) AS tbpadecimientoid FROM tbpadecimiento";
        $resultId = mysqli_query($conn, $queryGetLastId);
        $nextId = 1;

        if ($row = mysqli_fetch_row($resultId)) {
            if ($row[0] !== null) {
                $nextId = (int)$row[0] + 1;
            }
        }

        $queryInsert = "INSERT INTO tbpadecimiento (
            tbpadecimientoid, tbpadecimientotipo, tbpadecimientonombre,
            tbpadecimientodescripcion, tbpadecimientoformadeactuar
        ) VALUES (
            " . $nextId . ",
            '" . mysqli_real_escape_string($conn, $padecimiento->getTbpadecimientotipo()) . "',
            '" . mysqli_real_escape_string($conn, $padecimiento->getTbpadecimientonombre()) . "',
            '" . mysqli_real_escape_string($conn, $padecimiento->getTbpadecimientodescripcion()) . "',
            '" . mysqli_real_escape_string($conn, $padecimiento->getTbpadecimientoformadeactuar()) . "'
        );";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTbpadecimiento($padecimiento) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbpadecimiento SET
            tbpadecimientotipo='" . mysqli_real_escape_string($conn, $padecimiento->getTbpadecimientotipo()) . "',
            tbpadecimientonombre='" . mysqli_real_escape_string($conn, $padecimiento->getTbpadecimientonombre()) . "',
            tbpadecimientodescripcion='" . mysqli_real_escape_string($conn, $padecimiento->getTbpadecimientodescripcion()) . "',
            tbpadecimientoformadeactuar='" . mysqli_real_escape_string($conn, $padecimiento->getTbpadecimientoformadeactuar()) . "'
            WHERE tbpadecimientoid=" . $padecimiento->getTbpadecimientoid() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTbpadecimiento($padecimientoid) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbpadecimiento WHERE tbpadecimientoid=" . $padecimientoid . ";";

        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    public function obtenerTbpadecimiento() {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbpadecimiento ORDER BY tbpadecimientonombre;";
        $result = mysqli_query($conn, $querySelect);

        $padecimientos = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $padecimientos[] = new Padecimiento(
                $row['tbpadecimientoid'],
                $row['tbpadecimientotipo'],
                $row['tbpadecimientonombre'],
                $row['tbpadecimientodescripcion'],
                $row['tbpadecimientoformadeactuar']
            );
        }

        mysqli_close($conn);
        return $padecimientos;
    }

    public function obtenerTbpadecimientoPorId($padecimientoid) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbpadecimiento WHERE tbpadecimientoid=" . intval($padecimientoid) . ";";
        $result = mysqli_query($conn, $querySelect);

        if ($row = mysqli_fetch_assoc($result)) {
            $padecimiento = new Padecimiento(
                $row['tbpadecimientoid'],
                $row['tbpadecimientotipo'],
                $row['tbpadecimientonombre'],
                $row['tbpadecimientodescripcion'],
                $row['tbpadecimientoformadeactuar']
            );
            mysqli_close($conn);
            return $padecimiento;
        }

        mysqli_close($conn);
        return null;
    }

    public function obtenerTbpadecimientoPorTipo($tipo) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbpadecimiento WHERE tbpadecimientotipo='" . mysqli_real_escape_string($conn, $tipo) . "' ORDER BY tbpadecimientonombre;";
        $result = mysqli_query($conn, $querySelect);

        $padecimientos = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $padecimientos[] = new Padecimiento(
                $row['tbpadecimientoid'],
                $row['tbpadecimientotipo'],
                $row['tbpadecimientonombre'],
                $row['tbpadecimientodescripcion'],
                $row['tbpadecimientoformadeactuar']
            );
        }

        mysqli_close($conn);
        return $padecimientos;
    }

    public function verificarNombreExistente($nombre, $idExcluir = null) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT COUNT(*) as total FROM tbpadecimiento WHERE tbpadecimientonombre='" . mysqli_real_escape_string($conn, $nombre) . "'";

        if ($idExcluir !== null) {
            $querySelect .= " AND tbpadecimientoid != " . intval($idExcluir);
        }

        $result = mysqli_query($conn, $querySelect);
        $row = mysqli_fetch_assoc($result);

        mysqli_close($conn);
        return $row['total'] > 0;
    }
}
?>