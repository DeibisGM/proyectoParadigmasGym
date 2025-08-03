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
            tbclientesestado
        ) VALUES (
            '" . $cliente->getCarnet() . "',
            '" . $cliente->getNombre() . "',
            '" . $cliente->getFechaNacimiento() . "',
            '" . $cliente->getTelefono() . "',
            '" . $cliente->getCorreo() . "',
            '" . $cliente->getDireccion() . "',
            '" . $cliente->getGenero() . "',
            '" . $cliente->getInscripcion() . "',
            '" . $cliente->getEstado() . "'
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
            tbclientesestado='" . $cliente->getEstado() . "'
            WHERE tbclientesid=" . $cliente->getId() . ";";

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
                $row['tbclientesid'],
                $row['tbclientesid'],
                $row['tbclientesnombre'],
                $row['tbclientesfechanacimiento'],
                $row['tbclientestelefono'],
                $row['tbclientescorreo'],
                $row['tbclientesdireccion'],
                $row['tbclientesgenero'],
                $row['tbclientesinscripcion'],
                $row['tbclientesestado']
            );
        }

        mysqli_close($conn);
        return $clientes;
    }

    public function existeClientePorCarnet($carnet) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT tbclientecarnet FROM tbcliente WHERE tbclientecarnet='" . $carnet . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }
}
?>
