<?php

include_once 'data.php';
include '../domain/cliente.php';

class ClienteData extends Data {

    public function insertarTBCliente($cliente) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbcliente (tbclientecarnet, tbclientenombre, tbclientefechanacimiento, tbclientetelefono, tbclientecorreo, tbclientedireccion, tbclientegenero, tbclienteinscripcion, tbclienteestado, tbclientecontrasena, tbclienteimagenid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
";
        
        $stmt = mysqli_prepare($conn, $queryInsert);
        $carnet = $cliente->getCarnet();
        $nombre = $cliente->getNombre();
        $fechaNacimiento = $cliente->getFechaNacimiento();
        $telefono = $cliente->getTelefono();
        $correo = $cliente->getCorreo();
        $direccion = $cliente->getDireccion();
        $genero = $cliente->getGenero();
        $inscripcion = $cliente->getInscripcion();
        $estado = $cliente->getEstado();
        $contrasena = $cliente->getContrasena();
        $imagenid = $cliente->getTbclienteImagenId();

        mysqli_stmt_bind_param($stmt, 'sssssssssis', $carnet, $nombre, $fechaNacimiento, $telefono, $correo, $direccion, $genero, $inscripcion, $estado, $contrasena, $imagenid);

        $result = mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_close($conn);
        
        if($result){
            return $id;
        }else{
            return false;
        }
    }

    public function actualizarTBCliente($cliente) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbcliente SET tbclientecarnet=?, tbclientenombre=?, tbclientefechanacimiento=?, tbclientetelefono=?, tbclientecorreo=?, tbclientedireccion=?, tbclientegenero=?, tbclienteinscripcion=?, tbclienteestado=?, tbclientecontrasena=?, tbclienteimagenid=? WHERE tbclienteid=?;";

        $stmt = mysqli_prepare($conn, $queryUpdate);
        $carnet = $cliente->getCarnet();
        $nombre = $cliente->getNombre();
        $fechaNacimiento = $cliente->getFechaNacimiento();
        $telefono = $cliente->getTelefono();
        $correo = $cliente->getCorreo();
        $direccion = $cliente->getDireccion();
        $genero = $cliente->getGenero();
        $inscripcion = $cliente->getInscripcion();
        $estado = $cliente->getEstado();
        $contrasena = $cliente->getContrasena();
        $imagenid = $cliente->getTbclienteImagenId();
        $id = $cliente->getId();

        mysqli_stmt_bind_param($stmt, 'sssssssssisi', $carnet, $nombre, $fechaNacimiento, $telefono, $correo, $direccion, $genero, $inscripcion, $estado, $contrasena, $imagenid, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBCliente($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbcliente WHERE tbclienteid=?;";
        $stmt = mysqli_prepare($conn, $queryDelete);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getAllTBCliente() {
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
                $row['tbclienteestado'],
                isset($row['tbclientecontrasena']) ? $row['tbclientecontrasena'] : '',
                isset($row['tbclienteimagenid']) ? $row['tbclienteimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $clientes;
    }

    public function existeClientePorCarnet($carnet) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT tbclientecarnet FROM tbcliente WHERE tbclientecarnet=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $carnet);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }

    public function autenticarCliente($correo, $contrasena) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbcliente WHERE tbclientecorreo=? AND tbclientecontrasena=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $correo, $contrasena);
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
                $row['tbclienteestado'],
                $row['tbclientecontrasena'],
                isset($row['tbclienteimagenid']) ? $row['tbclienteimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $cliente;
    }

    public function getClientePorId($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbcliente WHERE tbclienteid=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
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
                $row['tbclienteestado'],
                $row['tbclientecontrasena'],
                isset($row['tbclienteimagenid']) ? $row['tbclienteimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $cliente;
    }
}
?>