<?php

include_once __DIR__ . '/data.php';
include_once __DIR__ . '/../domain/cuerpoZona.php';

class CuerpoZonaData extends Data
{

    public function existeCuerpoZonaNombre($nombreCuerpoZona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryCheck = "SELECT COUNT(*) as count FROM tbcuerpozona WHERE tbcuerpozonanombre = ?";
        $stmt = mysqli_prepare($conn, $queryCheck);
        mysqli_stmt_bind_param($stmt, "s", $nombreCuerpoZona);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return ($row['count'] > 0);
    }

    public function insertarTBCuerpoZona($cuerpoZona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryGetLastId = "SELECT MAX(tbcuerpozonaid) AS idcuerpozona FROM tbcuerpozona";
        $resultId = mysqli_query($conn, $queryGetLastId);
        $nextId = 1;
        if ($row = mysqli_fetch_row($resultId)) {
            if ($row[0] !== null) {
                $nextId = (int)$row[0] + 1;
            }
        }

        $queryInsert = "INSERT INTO tbcuerpozona (tbcuerpozonaid, tbcuerpozonanombre, tbcuerpozonadescripcion, tbcuerpozonaactivo, tbcuerpozonaimagenesids) VALUES (?, ?, ?, ?, '')";
        $stmt = mysqli_prepare($conn, $queryInsert);
        $nombre = $cuerpoZona->getNombreCuerpoZona();
        $descripcion = $cuerpoZona->getDescripcionCuerpoZona();
        $activo = $cuerpoZona->getActivoCuerpoZona();
        mysqli_stmt_bind_param($stmt, "issi", $nextId, $nombre, $descripcion, $activo);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);

        return $result ? $nextId : false;
    }

    public function actualizarTBCuerpoZona($cuerpoZona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryUpdate = "UPDATE tbcuerpozona SET tbcuerpozonanombre=?, tbcuerpozonadescripcion=?, tbcuerpozonaactivo=?, tbcuerpozonaimagenesids=? WHERE tbcuerpozonaid=?";
        $stmt = mysqli_prepare($conn, $queryUpdate);
        $nombre = $cuerpoZona->getNombreCuerpoZona();
        $descripcion = $cuerpoZona->getDescripcionCuerpoZona();
        $activo = $cuerpoZona->getActivoCuerpoZona();
        $imagenesIds = $cuerpoZona->getImagenesIds();
        $id = $cuerpoZona->getIdCuerpoZona();
        mysqli_stmt_bind_param($stmt, "ssisi", $nombre, $descripcion, $activo, $imagenesIds, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarEstadoTBCuerpoZona($idCuerpoZona, $estado)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryUpdate = "UPDATE tbcuerpozona SET tbcuerpozonaactivo=? WHERE tbcuerpozonaid=?";
        $stmt = mysqli_prepare($conn, $queryUpdate);
        mysqli_stmt_bind_param($stmt, "ii", $estado, $idCuerpoZona);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarSubZonaTBCuerpoZona($idCuerpoZona, $subZona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryUpdate = "UPDATE tbcuerpozona SET tbcuerpozonasubzonaid=? WHERE tbcuerpozonaid=?";
        $stmt = mysqli_prepare($conn, $queryUpdate);
        mysqli_stmt_bind_param($stmt, "si", $subZona, $idCuerpoZona);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getCuerpoZonaSubZonaId($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $querySelect = "SELECT * FROM tbcuerpozona WHERE tbcuerpozonaid = ?;";
        $stmt = mysqli_prepare($conn, $querySelect);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_close($conn);

        if ($row = mysqli_fetch_assoc($result)) {

            return $row['tbcuerpozonasubzonaid'];
        }

        return null;
    }

    public function getCuerpoZonaById($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $querySelect = "SELECT * FROM tbcuerpozona WHERE tbcuerpozonaid = ?;";
        $stmt = mysqli_prepare($conn, $querySelect);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_close($conn);

        if ($row = mysqli_fetch_assoc($result)) {
            return new CuerpoZona(
                $row['tbcuerpozonaid'],
                $row['tbcuerpozonanombre'],
                $row['tbcuerpozonadescripcion'],
                $row['tbcuerpozonaactivo'],
                $row['tbcuerpozonaimagenesids']
            );
        }
        return null;
    }

    public function getAllTBCuerpoZona()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $querySelect = "SELECT * FROM tbcuerpozona;";
        $result = mysqli_query($conn, $querySelect);
        mysqli_close($conn);
        $cuerpoZonas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $currentCuerpoZona = new CuerpoZona(
                $row['tbcuerpozonaid'],
                $row['tbcuerpozonanombre'],
                $row['tbcuerpozonadescripcion'],
                $row['tbcuerpozonaactivo'],
                $row['tbcuerpozonaimagenesids']
            );
            array_push($cuerpoZonas, $currentCuerpoZona);
        }
        return $cuerpoZonas;
    }

    public function getActiveTBCuerpoZona()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $querySelect = "SELECT * FROM tbcuerpozona WHERE tbcuerpozonaactivo = 1;";
        $result = mysqli_query($conn, $querySelect);
        mysqli_close($conn);
        $cuerpoZonas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $currentCuerpoZona = new CuerpoZona(
                $row['tbcuerpozonaid'],
                $row['tbcuerpozonanombre'],
                $row['tbcuerpozonadescripcion'],
                $row['tbcuerpozonaactivo'],
                $row['tbcuerpozonaimagenesids']
            );
            array_push($cuerpoZonas, $currentCuerpoZona);
        }
        return $cuerpoZonas;
    }

    public function eliminarTBCuerpoZona($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "DELETE FROM tbcuerpozona WHERE tbcuerpozonaid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }
}
?>