<?php

include_once 'data.php';
include '../domain/cliente.php';
include_once 'clientePadecimientoData.php';

class ClienteData extends Data
{

    public function insertarTBCliente($cliente)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "INSERT INTO tbcliente (tbclientecarnet, tbclientenombre, tbclientefechanacimiento, tbclientetelefono, tbclientecorreo, tbclientedireccion, tbclientegenero, tbclienteinscripcion, tbclienteactivo, tbclientecontrasena) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        $carnet = $cliente->getCarnet();
        $nombre = $cliente->getNombre();
        $fechaNacimiento = $cliente->getFechaNacimiento();
        $telefono = $cliente->getTelefono();
        $correo = $cliente->getCorreo();
        $direccion = $cliente->getDireccion();
        $genero = $cliente->getGenero();
        $inscripcion = $cliente->getInscripcion();
        $activo = $cliente->getActivo();
        $contrasena = $cliente->getContrasena();

        mysqli_stmt_bind_param($stmt, "sssissssss", $carnet, $nombre, $fechaNacimiento, $telefono, $correo, $direccion, $genero, $inscripcion, $activo, $contrasena);

        $result = mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_close($conn);
        return $result ? $id : 0;
    }

    public function existeclientePorCorreo($correo)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $correo = mysqli_real_escape_string($conn, $correo);
        $query = "SELECT tbclienteid FROM tbcliente WHERE tbclientecorreo='" . $correo . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }

    public function actualizarTBCliente($cliente)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "UPDATE tbcliente SET tbclientenombre=?, tbclientefechanacimiento=?, tbclientetelefono=?, tbclientecorreo=?, tbclientedireccion=?, tbclientegenero=?, tbclienteinscripcion=?, tbclienteactivo=?, tbclientecontrasena=?, tbclienteimagenid=? WHERE tbclienteid=?";
        $stmt = mysqli_prepare($conn, $query);

        $nombre = $cliente->getNombre();
        $fechaNacimiento = $cliente->getFechaNacimiento();
        $telefono = $cliente->getTelefono();
        $correo = $cliente->getCorreo();
        $direccion = $cliente->getDireccion();
        $genero = $cliente->getGenero();
        $inscripcion = $cliente->getInscripcion();
        $activo = $cliente->getActivo();
        $contrasena = $cliente->getContrasena();
        $imagenId = $cliente->getTbclienteImagenId();
        $id = $cliente->getId();

        mysqli_stmt_bind_param($stmt, "ssisssssisi", $nombre, $fechaNacimiento, $telefono, $correo, $direccion, $genero, $inscripcion, $activo, $contrasena, $imagenId, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBCliente($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return false;
        }
        $conn->set_charset('utf8');

// Iniciar transacción
        mysqli_autocommit($conn, false);

        try {
// 1. Primero eliminar datos clínicos del cliente
            $clientePadecimientoData = new ClientePadecimientoData();
            $resultClientePadecimiento = $clientePadecimientoData->eliminarTBClientePadecimientoPorCliente($id);

// 2. Luego eliminar el cliente
            $queryDelete = "DELETE FROM tbcliente WHERE tbclienteid=?";
            $stmt = mysqli_prepare($conn, $queryDelete);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                $resultCliente = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparando consulta de cliente");
            }

// Si ambas operaciones fueron exitosas, confirmar
            if ($resultClientePadecimiento && $resultCliente) {
                mysqli_commit($conn);
                $result = true;
            } else {
                mysqli_rollback($conn);
                $result = false;
            }

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $result = false;
        }

        mysqli_close($conn);
        return $result;
    }

    public function getAllTBCliente()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbcliente;";
        $result = mysqli_query($conn, $querySelect);

        $clientes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $clientes[] = new Cliente(
                $row['tbclienteid'],
                $row['tbclientecarnet'],
                $row['tbclientenombre'],
                $row['tbclientefechanacimiento'],
                $row['tbclientetelefono'],
                $row['tbclientecorreo'],
                $row['tbclientedireccion'],
                $row['tbclientegenero'],
                $row['tbclienteinscripcion'],
                $row['tbclienteactivo'],
                $row['tbclientecontrasena'],
                $row['tbclienteimagenid']
            );
        }

        mysqli_close($conn);
        return $clientes;
    }

// MODIFICADO: Devuelve el ID del cliente si existe
    public function existeClientePorCarnet($carnet)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT tbclienteid FROM tbcliente WHERE tbclientecarnet=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $carnet);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $clienteId = null;
        if($row = mysqli_fetch_assoc($result)){
            $clienteId = $row['tbclienteid'];
        }

        mysqli_close($conn);
        return $clienteId;
    }

    public function autenticarCliente($correo, $contrasena)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbcliente WHERE tbclientecorreo=? AND tbclientecontrasena=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $correo, $contrasena);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $cliente = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $cliente = new Cliente(
                $row['tbclienteid'],
                $row['tbclientecarnet'],
                $row['tbclientenombre'],
                $row['tbclientefechanacimiento'],
                $row['tbclientetelefono'],
                $row['tbclientecorreo'],
                $row['tbclientedireccion'],
                $row['tbclientegenero'],
                $row['tbclienteinscripcion'],
                $row['tbclienteactivo'],
                $row['tbclientecontrasena'],
                isset($row['tbclienteimagenid']) ? $row['tbclienteimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $cliente;
    }

    public function getClientePorId($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbcliente WHERE tbclienteid=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $cliente = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $cliente = new Cliente(
                $row['tbclienteid'],
                $row['tbclientecarnet'],
                $row['tbclientenombre'],
                $row['tbclientefechanacimiento'],
                $row['tbclientetelefono'],
                $row['tbclientecorreo'],
                $row['tbclientedireccion'],
                $row['tbclientegenero'],
                $row['tbclienteinscripcion'],
                $row['tbclienteactivo'],
                $row['tbclientecontrasena'],
                isset($row['tbclienteimagenid']) ? $row['tbclienteimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $cliente;
    }
}
?>