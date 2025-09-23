<?php
include_once 'data.php';
include_once __DIR__ . '/../logic/ImageManager.php';

class PadecimientoDictamenData extends Data
{

    public function insertarTBPadecimientoDictamen($padecimientodictamen)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("No se pudo conectar a la base de datos.");
            return null;
        }
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbpadecimientodictamen (
            tbpadecimientodictamenfechaemision,
            tbpadecimientodictamenentidademision,
            tbpadecimientodictamenimagenid
        ) VALUES (?, ?, ?);";

        $stmt = mysqli_prepare($conn, $queryInsert);
        $fechaemision = $padecimientodictamen->getPadecimientodictamenfechaemision();
        $entidademision = $padecimientodictamen->getPadecimientodictamenentidademision();
        $imagenid = $padecimientodictamen->getPadecimientodictamenimagenid();

        mysqli_stmt_bind_param(
            $stmt,
            'sss',
            $fechaemision,
            $entidademision,
            $imagenid
        );

        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $id;
        } else {
            error_log("Error SQL: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return null;
        }
    }

    public function actualizarTBPadecimientoDictamen($padecimientodictamen)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return false;
        }
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbpadecimientodictamen SET
            tbpadecimientodictamenfechaemision = ?,
            tbpadecimientodictamenentidademision = ?,
            tbpadecimientodictamenimagenid = ?
            WHERE tbpadecimientodictamenid = ?;";

        $stmt = mysqli_prepare($conn, $queryUpdate);
        $fechaemision = $padecimientodictamen->getPadecimientodictamenfechaemision();
        $entidademision = $padecimientodictamen->getPadecimientodictamenentidademision();
        $imagenid = $padecimientodictamen->getPadecimientodictamenimagenid();
        $id = $padecimientodictamen->getPadecimientodictamenid();

        mysqli_stmt_bind_param(
            $stmt,
            'sssi',
            $fechaemision,
            $entidademision,
            $imagenid,
            $id
        );

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBPadecimientoDictamen($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return false;
        }
        $conn->set_charset('utf8');

        // Primero obtenemos la información del registro para saber qué imagen eliminar
        $querySelect = "SELECT tbpadecimientodictamenimagenid FROM tbpadecimientodictamen WHERE tbpadecimientodictamenid=?";
        $stmtSelect = mysqli_prepare($conn, $querySelect);
        mysqli_stmt_bind_param($stmtSelect, 'i', $id);
        mysqli_stmt_execute($stmtSelect);
        $result = mysqli_stmt_get_result($stmtSelect);

        $imagenId = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $imagenId = $row['tbpadecimientodictamenimagenid'];
        }
        mysqli_stmt_close($stmtSelect);

        // Si hay una imagen asociada, la eliminamos usando ImageManager
        if (!empty($imagenId)) {
            $imageManager = new ImageManager();

            // Si el imagenId es una cadena con múltiples IDs separados por '$'
            if (strpos($imagenId, '$') !== false) {
                $imageManager->deleteImagesFromString($imagenId);
            } else {
                // Si es un solo ID
                $imageManager->deleteImage($imagenId);
            }
        }

        // Ahora eliminamos el registro de padecimientodictamen
        $queryDelete = "DELETE FROM tbpadecimientodictamen WHERE tbpadecimientodictamenid=?";
        $stmt = mysqli_prepare($conn, $queryDelete);

        mysqli_stmt_bind_param($stmt, 'i', $id);
        $result = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
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

        $query = "SELECT tbpadecimientodictamenentidademision FROM tbpadecimientodictamen WHERE tbpadecimientodictamenentidademision=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $entidad);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $existe = mysqli_stmt_num_rows($stmt) > 0;

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $existe;
    }

    public function getPadecimientoDictamenPorId($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbpadecimientodictamen WHERE tbpadecimientodictamenid=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $padecimientodictamen = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $padecimientodictamen = new PadecimientoDictamen(
                $row['tbpadecimientodictamenid'],
                $row['tbpadecimientodictamenfechaemision'],
                $row['tbpadecimientodictamenentidademision'],
                isset($row['tbpadecimientodictamenimagenid']) ? $row['tbpadecimientodictamenimagenid'] : ''
            );
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $padecimientodictamen;
    }

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

    public function getClientePorCarnet($carnet)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT tbclienteid, tbclientecarnet, tbclientenombre FROM tbcliente WHERE tbclientecarnet=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $carnet);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $cliente = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $cliente = $row;
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $cliente;
    }

    public function getListaIdsPadecimientosPorClienteId($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT tbpadecimientodictamenid FROM tbclientepadecimiento WHERE tbclienteid = ?;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $listaIds = '';
        if ($row = mysqli_fetch_assoc($result)) {
            $listaIds = $row['tbpadecimientodictamenid'] ?? '';
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $listaIds;
    }

    public function actualizarListaPadecimientosPorClienteId($clienteId, $nuevaLista)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("No se pudo conectar a la base de datos.");
            return false;
        }
        $conn->set_charset('utf8');

        $queryExiste = "SELECT tbclienteid FROM tbclientepadecimiento WHERE tbclienteid = ?;";
        $stmtExiste = mysqli_prepare($conn, $queryExiste);
        mysqli_stmt_bind_param($stmtExiste, 'i', $clienteId);
        mysqli_stmt_execute($stmtExiste);
        $resultExiste = mysqli_stmt_get_result($stmtExiste);

        if (mysqli_num_rows($resultExiste) > 0) {
            $query = "UPDATE tbclientepadecimiento SET tbpadecimientodictamenid = ? WHERE tbclienteid = ?;";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'si', $nuevaLista, $clienteId);
        } else {
            $query = "INSERT INTO tbclientepadecimiento (tbclienteid, tbpadecimientodictamenid) VALUES (?, ?);";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'is', $clienteId, $nuevaLista);
        }

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getPadecimientosDictamenPorCliente($clienteId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

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

        return $this->getAllTBPadecimientoDictamenPorId($padecimientosIds);
    }
public function eliminarRelacionPorDictamenId($dictamenId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return false;
        }
        $conn->set_charset('utf8');

        $querySelect = "SELECT tbclientepadecimientoid, tbpadecimientodictamenid FROM tbclientepadecimiento WHERE tbpadecimientodictamenid LIKE ?";
        $searchPattern = "%$dictamenId%";
        $stmt = mysqli_prepare($conn, $querySelect);
        mysqli_stmt_bind_param($stmt, "s", $searchPattern);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $registroId = $row['tbclientepadecimientoid'];
            $dictamenesActuales = $row['tbpadecimientodictamenid'];

            $dictamenesArray = explode('$', $dictamenesActuales);
            $nuevosDictamenes = array_filter($dictamenesArray, function($id) use ($dictamenId) {
                return $id != $dictamenId;
            });

            $nuevaCadena = implode('$', $nuevosDictamenes);

            if (empty($nuevaCadena)) {
                $queryUpdate = "UPDATE tbclientepadecimiento SET tbpadecimientodictamenid = NULL WHERE tbclientepadecimientoid = ?";
                $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
                mysqli_stmt_bind_param($stmtUpdate, "i", $registroId);
            } else {
                $queryUpdate = "UPDATE tbclientepadecimiento SET tbpadecimientodictamenid = ? WHERE tbclientepadecimientoid = ?";
                $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
                mysqli_stmt_bind_param($stmtUpdate, "si", $nuevaCadena, $registroId);
            }

            mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return true;
    }

    public function obtenerClienteIdPorDictamenId($dictamenId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return null;
        }
        $conn->set_charset('utf8');

        $querySelect = "SELECT tbclienteid FROM tbclientepadecimiento WHERE tbpadecimientodictamenid LIKE ?";
        $searchPattern = "%$dictamenId%";
        $stmt = mysqli_prepare($conn, $querySelect);
        mysqli_stmt_bind_param($stmt, "s", $searchPattern);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $clienteId = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $clienteId = $row['tbclienteid'];
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $clienteId;
    }

    public function asociarDictamenACliente($clienteId, $dictamenId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return false;
        }
        $conn->set_charset('utf8');

        $queryExiste = "SELECT tbclientepadecimientoid, tbpadecimientodictamenid FROM tbclientepadecimiento WHERE tbclienteid = ?";
        $stmtExiste = mysqli_prepare($conn, $queryExiste);
        mysqli_stmt_bind_param($stmtExiste, "i", $clienteId);
        mysqli_stmt_execute($stmtExiste);
        $resultExiste = mysqli_stmt_get_result($stmtExiste);

        if ($rowExiste = mysqli_fetch_assoc($resultExiste)) {
            $registroId = $rowExiste['tbclientepadecimientoid'];
            $dictamenesActuales = $rowExiste['tbpadecimientodictamenid'] ?? '';

            $dictamenesArray = empty($dictamenesActuales) ? [] : explode('$', $dictamenesActuales);
            if (!in_array($dictamenId, $dictamenesArray)) {
                $dictamenesArray[] = $dictamenId;
            }
            $nuevaCadena = implode('$', array_filter($dictamenesArray));

            $queryUpdate = "UPDATE tbclientepadecimiento SET tbpadecimientodictamenid = ? WHERE tbclientepadecimientoid = ?";
            $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
            mysqli_stmt_bind_param($stmtUpdate, "si", $nuevaCadena, $registroId);
            $result = mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);
        } else {
            $queryGetLastId = "SELECT MAX(tbclientepadecimientoid) AS tbclientepadecimientoid FROM tbclientepadecimiento";
            $resultId = mysqli_query($conn, $queryGetLastId);
            $nextId = 1;

            if ($row = mysqli_fetch_row($resultId)) {
                if ($row[0] !== null) {
                    $nextId = (int)$row[0] + 1;
                }
            }

            $queryInsert = "INSERT INTO tbclientepadecimiento (tbclientepadecimientoid, tbclienteid, tbpadecimientoid, tbpadecimientodictamenid) VALUES (?, ?, NULL, ?)";
            $stmtInsert = mysqli_prepare($conn, $queryInsert);
            mysqli_stmt_bind_param($stmtInsert, "iis", $nextId, $clienteId, $dictamenId);
            $result = mysqli_stmt_execute($stmtInsert);
            mysqli_stmt_close($stmtInsert);
        }

        mysqli_stmt_close($stmtExiste);
        mysqli_close($conn);
        return $result ?? false;
    }
}
?>