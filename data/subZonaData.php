<?php
include_once __DIR__ . '/data.php';
include_once __DIR__ . '/../domain/subzona.php';

class subZonaData extends Data
{
    public function insertarTBSubZona($subzona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryInsert = "INSERT INTO tbsubzona (tbsubzonaimagenid, tbsubzonanombre, tbsubzonadescripcion) VALUES ('" . $subzona->getSubzonaimaenid() . "', '" . $subzona->getSubzonanombre() . "', '" . $subzona->getSubzonadescripcion() . "');";
        $result = mysqli_query($conn, $queryInsert);
        if ($result) {
            $id = mysqli_insert_id($conn);
            mysqli_close($conn);
            return $id;
        } else {
            mysqli_close($conn);
            return null;
        }
    }

    public function actualizarTBSubZona($partezona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryUpdate = "UPDATE tbsubzona SET tbsubzonaimagenid='" . $partezona->getSubzonaimaenid() . "', tbsubzonanombre='" . $partezona->getSubzonanombre() . "', tbsubzonadescripcion='" . $partezona->getSubzonadescripcion() . "', tbsubzonaactivo='" . $partezona->getSubzonaactivo() . "' WHERE tbsubzonaid=" . $partezona->getSubzonaid() . ";";
        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBSubZonaLogical($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryDelete = "UPDATE tbsubzona SET tbsubzonaactivo = 0 WHERE tbsubzonaid = ?;";
        $stmt = mysqli_prepare($conn, $queryDelete);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBSubZona($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryDelete = "DELETE FROM tbsubzona WHERE tbsubzonaid = ?;";
        $stmt = mysqli_prepare($conn, $queryDelete);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getAllTBSubZona()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $querySelect = "SELECT * FROM tbsubzona;";
        $result = mysqli_query($conn, $querySelect);
        $lista = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $lista[] = new subzona($row['tbsubzonaid'], isset($row['tbsubzonaimagenid']) ? $row['tbsubzonaimagenid'] : '', $row['tbsubzonanombre'], $row['tbsubzonadescripcion'], $row['tbsubzonaactivo']);
        }
        mysqli_close($conn);
        return $lista;
    }

    public function getAllTBSubZonaPorId($parteLista)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $ids = explode('$', $parteLista);
        $ids = array_filter($ids, function($id) { return is_numeric($id); });
        $idLista = implode(',', $ids);
        if (empty($idLista)) { return []; }
        $querySelect = "SELECT * FROM tbsubzona WHERE tbsubzonaid IN ($idLista);";
        $result = mysqli_query($conn, $querySelect);
        $lista = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $lista[] = new subzona($row['tbsubzonaid'], isset($row['tbsubzonaimagenid']) ? $row['tbsubzonaimagenid'] : '', $row['tbsubzonanombre'], $row['tbsubzonadescripcion'], $row['tbsubzonaactivo']);
        }
        mysqli_close($conn);
        return $lista;
    }

    public function existeSubZonaNombre($nombre)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT tbsubzonanombre FROM tbsubzona WHERE tbsubzonanombre='" . $nombre . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;
        mysqli_close($conn);
        return $existe;
    }

    public function getSubZonaPorId($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $id = mysqli_real_escape_string($conn, $id);
        $query = "SELECT * FROM tbsubzona WHERE tbsubzonaid='" . $id . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $subzona = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $subzona = new subzona($row['tbsubzonaid'], isset($row['tbsubzonaimagenid']) ? $row['tbsubzonaimagenid'] : '', $row['tbsubzonanombre'], $row['tbsubzonadescripcion'], $row['tbsubzonaactivo']);
        }
        mysqli_close($conn);
        return $subzona;
    }

    public function desactivarSubZonaLista($parteLista)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $ids = explode('$', $parteLista);
        $ids = array_filter($ids, function($id) { return is_numeric($id); });
        $idsStr = implode(',', $ids);
        if (empty($idsStr)) { return false; }
        $queryUpdate = "UPDATE tbsubzona SET tbsubzonaactivo = 0 WHERE tbsubzonaid IN ($idsStr);";
        $result = mysqli_query($conn, $queryUpdate);
        $filasAfectadas = mysqli_affected_rows($conn);
        mysqli_close($conn);
        return $filasAfectadas > 0;
    }
}