<?php

include_once 'data.php';
include '../domain/sala.php';

class SalaData extends Data{

    public function insertarTbsala($sala){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbsala (
            tbsalanombre,
            tbsalacapacidad,
            tbsalaactivo

        ) VALUES (
            '" . $sala->getTbsalanombre() . "',
            '" . $sala->getTbsalacapacidad() . "',
            '" . $sala->getTbsalaestado() . "'
        );";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;

    }

    public function actualizarTbsala($sala){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbsala SET
            tbsalanombre='" . $sala->getTbsalanombre() . "',
            tbsalacapacidad='" . $sala->getTbsalacapacidad() . "',
            tbsalaactivo='" . $sala->getTbsalaestado() . "'
            WHERE tbsalaid=" . $sala->getTbsalaid() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTbsala($id){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbsala WHERE tbsalaid=" . $id . ";";
        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    public function obtenerTbsala(){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbsala;";
        $result = mysqli_query($conn, $querySelect);

        $salas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $salas[] = new Sala(
                $row['tbsalaid'],
                $row['tbsalanombre'],
                $row['tbsalacapacidad'],
                $row['tbsalaactivo']
            );
        }

        mysqli_close($conn);
        return $salas;
    }


}
?>