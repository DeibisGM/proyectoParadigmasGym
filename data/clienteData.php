<?php

include_once 'data.php';
include '../domain/cliente.php';

class ClienteData extends Data {

    public function insertarTBCliente($cliente) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbcliente (
            tbclientescarnet,
            tbclientesnombre,
            tbclientesfechanacimiento,
            tbclientestelefono,
            tbclientescorreo,
            tbclientesdireccion,
            tbclientesgenero,
            tbclientesinscripcion,
            tbclientesestado,
            tbclientescontrasena
        ) VALUES (
            '" . $cliente->getCarnet() . "',
            '" . $cliente->getNombre() . "',
            '" . $cliente->getFechaNacimiento() . "',
            '" . $cliente->getTelefono() . "',
            '" . $cliente->getCorreo() . "',
            '" . $cliente->getDireccion() . "',
            '" . $cliente->getGenero() . "',
            '" . $cliente->getInscripcion() . "',
            '" . $cliente->getEstado() . "',
            '" . $cliente->getContrasena() . "'
        );";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTBCliente($cliente) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbcliente SET
            tbclientescarnet='" . $cliente->getCarnet() . "',
            tbclientesnombre='" . $cliente->getNombre() . "',
            tbclientesfechanacimiento='" . $cliente->getFechaNacimiento() . "',
            tbclientestelefono='" . $cliente->getTelefono() . "',
            tbclientescorreo='" . $cliente->getCorreo() . "',
            tbclientesdireccion='" . $cliente->getDireccion() . "',
            tbclientesgenero='" . $cliente->getGenero() . "',
            tbclientesinscripcion='" . $cliente->getInscripcion() . "',
            tbclientesestado='" . $cliente->getEstado() . "',
            tbclientescontrasena='" . $cliente->getContrasena() . "'
            WHERE tbclienteid=" . $cliente->getId() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBCliente($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbcliente WHERE tbclienteid=" . $id . ";";
        $result = mysqli_query($conn, $queryDelete);
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
                $row['tbclientescarnet'],
                $row['tbclientesnombre'],
                $row['tbclientesfechanacimiento'],
                $row['tbclientestelefono'],
                $row['tbclientescorreo'],
                $row['tbclientesdireccion'],
                $row['tbclientesgenero'],
                $row['tbclientesinscripcion'],
                $row['tbclientesestado'],
                isset($row['tbclientescontrasena']) ? $row['tbclientescontrasena'] : ''
            );
        }

        mysqli_close($conn);
        return $clientes;
    }

    public function existeClientePorCarnet($carnet) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT tbclientescarnet FROM tbcliente WHERE tbclientescarnet='" . $carnet . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }

    public function autenticarCliente($correo, $contrasena) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Escape strings to prevent SQL injection
        $correo = mysqli_real_escape_string($conn, $correo);
        $contrasena = mysqli_real_escape_string($conn, $contrasena);

        $query = "SELECT * FROM tbcliente WHERE tbclientescorreo='" . $correo . "' AND tbclientescontrasena='" . $contrasena . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);

        $cliente = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $cliente = new Cliente(
                $row['tbclienteid'],
                $row['tbclientescarnet'],
                $row['tbclientesnombre'],
                $row['tbclientesfechanacimiento'],
                $row['tbclientestelefono'],
                $row['tbclientescorreo'],
                $row['tbclientesdireccion'],
                $row['tbclientesgenero'],
                $row['tbclientesinscripcion'],
                $row['tbclientesestado'],
                $row['tbclientescontrasena']
            );
        }

        mysqli_close($conn);
        return $cliente;
    }

    public function getClientePorId($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $id = mysqli_real_escape_string($conn, $id);
        $query = "SELECT * FROM tbcliente WHERE tbclienteid='" . $id . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);

        $cliente = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $cliente = new Cliente(
                $row['tbclienteid'],
                $row['tbclientescarnet'],
                $row['tbclientesnombre'],
                $row['tbclientesfechanacimiento'],
                $row['tbclientestelefono'],
                $row['tbclientescorreo'],
                $row['tbclientesdireccion'],
                $row['tbclientesgenero'],
                $row['tbclientesinscripcion'],
                $row['tbclientesestado'],
                $row['tbclientescontrasena']
            );
        }

        mysqli_close($conn);
        return $cliente;
    }
}
?>