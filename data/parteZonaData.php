<?php
include_once 'data.php';
include '../domain/partezona.php';

class parteZonaData extends Data
{

    public function insertarTBParteZona($partezona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbpartezona (
        tbpartezonaimagenid,
        tbpartezonanombre,
        tbpartezonadescripcion
    ) VALUES (
        '" . $partezona->getPartezonaimaenid() . "',
        '" . $partezona->getPartezonanombre() . "',
        '" . $partezona->getPartezonadescripcion() . "'
    );";

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

    public function actualizarTBParteZona($partezona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbpartezona SET
            tbpartezonaimagenid='" . $partezona->getPartezonaimaenid() . "',
            tbpartezonanombre='" . $partezona->getPartezonanombre() . "',
            tbpartezonadescripcion='" . $partezona->getPartezonadescripcion() . "',
            tbpartezonaactivo='" . $partezona->getPartezonaactivo() . "'
            WHERE tbpartezonaid=" . $partezona->getPartezonaid() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBParteZona2($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "UPDATE tbpartezona SET tbpartezonaactivo = 0 WHERE tbpartezonaid = ?;";
        $stmt = mysqli_prepare($conn, $queryDelete);

        mysqli_stmt_bind_param($stmt, 'i', $id);
        $result = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return $result;
    }

    public function eliminarTBParteZona($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbpartezona WHERE tbpartezonaid = ?;";
        $stmt = mysqli_prepare($conn, $queryDelete);

        mysqli_stmt_bind_param($stmt, 'i', $id);
        $result = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return $result;
    }

    public function getAllTBParteZona()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbpartezona;";
        $result = mysqli_query($conn, $querySelect);

        $lista = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $lista[] = new partezona(
                $row['tbpartezonaid'],
                isset($row['tbpartezonaimagenid']) ? $row['tbpartezonaimagenid'] : '',
                $row['tbpartezonanombre'],
                $row['tbpartezonadescripcion'],
                $row['tbpartezonaactivo']
            );
        }

        mysqli_close($conn);
        return $lista;
    }

    public function getAllTBParteZonaPorId($parteLista)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $ids = explode('$', $parteLista);

        $ids = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        $idLista = implode(',', $ids);

        if (empty($idLista)) {
            return [];
        }

        $querySelect = "SELECT * FROM tbpartezona WHERE tbpartezonaid IN ($idLista);";

        $result = mysqli_query($conn, $querySelect);

        $lista = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $lista[] = new partezona(
                $row['tbpartezonaid'],
                isset($row['tbpartezonaimagenid']) ? $row['tbpartezonaimagenid'] : '',
                $row['tbpartezonanombre'],
                $row['tbpartezonadescripcion'],
                $row['tbpartezonaactivo']
            );
        }

        mysqli_close($conn);
        return $lista;
    }


    public function existeParteZonaNombre($nombre)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT tbpartezonanombre FROM tbpartezona WHERE tbpartezonanombre='" . $nombre . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }

    public function getParteZonaPorId($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $id = mysqli_real_escape_string($conn, $id);
        $query = "SELECT * FROM tbpartezona WHERE tbpartezonaid='" . $id . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);

        $partezona = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $partezona = new partezona(
                $row['tbpartezonaid'],
                isset($row['tbpartezonaimagenid']) ? $row['tbpartezonaimagenid'] : '',
                $row['tbpartezonanombre'],
                $row['tbpartezonadescripcion'],
                $row['tbpartezonaactivo']
            );
        }

        mysqli_close($conn);
        return $partezona;
    }

    public function desactivarParteZonaLista($parteLista)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $ids = explode('$', $parteLista);

        $ids = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        $idsStr = implode(',', $ids);

        if (empty($idsStr)) {
            return false;
        }

        $queryUpdate = "UPDATE tbpartezona SET tbpartezonaactivo = 0 WHERE tbpartezonaid IN ($idsStr);";
        $result = mysqli_query($conn, $queryUpdate);

        $filasAfectadas = mysqli_affected_rows($conn);

        mysqli_close($conn);

        return $filasAfectadas > 0;
    }

}