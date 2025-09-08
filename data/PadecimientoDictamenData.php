<?php
include_once 'data.php';
include '../domain/PadecimientoDictamen.php';

class PadecimientoDictamenData extends Data
{

    public function insertarTBPadecimientoDictamen($padecimientodictamen)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbpadecimientodictamen (
            tbpadecimientodictamenfechaemision,
            tbpadecimientodictamenentidademision,
            tbpadecimientodictamenimagenid
        ) VALUES (
            '" . $padecimientodictamen->getPadecimientodictamenfechaemision() . "',
            '" . $padecimientodictamen->getPadecimientodictamenentidademision() . "',
            '" . $padecimientodictamen->getPadecimientodictamenimagenid() . "'
        );";

        $result = mysqli_query($conn, $queryInsert);

        if ($result) {
            $id = mysqli_insert_id($conn);
            mysqli_close($conn);
            return $id;
        } else {
            error_log("Error SQL: " . mysqli_error($conn));
            mysqli_close($conn);
            return null;
        }
    }

    public function actualizarTBPadecimientoDictamen($padecimientodictamen)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbpadecimientodictamen SET
            tbpadecimientodictamenfechaemision='" . $padecimientodictamen->getPadecimientodictamenfechaemision() . "',
            tbpadecimientodictamenentidademision='" . $padecimientodictamen->getPadecimientodictamenentidademision() . "',
            tbpadecimientodictamenimagenid='" . $padecimientodictamen->getPadecimientodictamenimagenid() . "'
            WHERE tbpadecimientodictamenid=" . $padecimientodictamen->getPadecimientodictamenid() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBPadecimientoDictamen($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbpadecimientodictamen WHERE tbpadecimientodictamenid=?;";
        $stmt = mysqli_prepare($conn, $queryDelete);

        mysqli_stmt_bind_param($stmt, 'i', $id);
        $result = mysqli_stmt_execute($stmt);

        mysqli_close($conn);
        return $result;
    }

    public function getAllTBPadecimientoDictamen()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbpadecimientodictamen ORDER BY tbpadecimientodictamenid DESC;";
        $result = mysqli_query($conn, $querySelect);

        $lista = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $lista[] = new PadecimientoDictamen(
                $row['tbpadecimientodictamenid'],
                $row['tbpadecimientodictamenfechaemision'],
                $row['tbpadecimientodictamenentidademision'],
                isset($row['tbpadecimientodictamenimagenid']) ? $row['tbpadecimientodictamenimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $lista;
    }

    public function getAllTBPadecimientoDictamenPorId($padecimientoLista)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $ids = explode('$', $padecimientoLista);
        $ids = array_filter($ids, function($id) {
            return is_numeric($id);
        });

        $idLista = implode(',', $ids);

        if (empty($idLista)) {
            return [];
        }

        $querySelect = "SELECT * FROM tbpadecimientodictamen WHERE tbpadecimientodictamenid IN ($idLista);";

        $result = mysqli_query($conn, $querySelect);

        $lista = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $lista[] = new PadecimientoDictamen(
                $row['tbpadecimientodictamenid'],
                $row['tbpadecimientodictamenfechaemision'],
                $row['tbpadecimientodictamenentidademision'],
                isset($row['tbpadecimientodictamenimagenid']) ? $row['tbpadecimientodictamenimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $lista;
    }

    public function existePadecimientoDictamenEntidad($entidad)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT tbpadecimientodictamenentidademision FROM tbpadecimientodictamen WHERE tbpadecimientodictamenentidademision='" . $entidad . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }

    public function getPadecimientoDictamenPorId($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $id = mysqli_real_escape_string($conn, $id);
        $query = "SELECT * FROM tbpadecimientodictamen WHERE tbpadecimientodictamenid='" . $id . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);

        $padecimientodictamen = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $padecimientodictamen = new PadecimientoDictamen(
                $row['tbpadecimientodictamenid'],
                $row['tbpadecimientodictamenfechaemision'],
                $row['tbpadecimientodictamenentidademision'],
                isset($row['tbpadecimientodictamenimagenid']) ? $row['tbpadecimientodictamenimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $padecimientodictamen;
    }

    // Método para obtener todos los clientes (para el selector)
    public function getAllClientes()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT tbclienteid, tbclientecarnet, tbclientenombre FROM tbcliente ORDER BY tbclientecarnet;";
        $result = mysqli_query($conn, $querySelect);

        $clientes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $clientes[] = $row;
        }

        mysqli_close($conn);
        return $clientes;
    }

    // Método para obtener cliente por carnet
    public function getClientePorCarnet($carnet)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $carnet = mysqli_real_escape_string($conn, $carnet);
        $query = "SELECT tbclienteid, tbclientecarnet, tbclientenombre FROM tbcliente WHERE tbclientecarnet='" . $carnet . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);

        $cliente = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $cliente = $row;
        }

        mysqli_close($conn);
        return $cliente;
    }

    // Métodos para manejar la relación cliente-padecimiento dictamen en tabla intermedia
    public function getPadecimientosDictamenPorCliente($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Obtener los IDs de padecimientos dictamen del cliente desde tbdatoclinico
        $query = "SELECT tbpadecimientodictamenid FROM tbclientepadecimiento WHERE tbclienteid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $padecimientosIds = '';
        if ($row = mysqli_fetch_assoc($result)) {
            $padecimientosIds = $row['tbpadecimientodictamenid'] ?? '';
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        if (empty($padecimientosIds)) {
            return [];
        }

        // Obtener los padecimientos dictamen usando los IDs
        return $this->getAllTBPadecimientoDictamenPorId($padecimientosIds);
    }
}
?>