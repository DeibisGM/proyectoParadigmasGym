<?php

include_once 'data.php';
include '../domain/sala.php';

class SalaData extends Data{

    public function insertarTbsala($sala){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "INSERT INTO tbsala (tbsalanombre, tbsalacapacidad, tbsalaestado) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sii",
            $sala->getTbsalanombre(),
            $sala->getTbsalacapacidad(),
            $sala->getTbsalaestado()
        );

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTbsala($sala){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "UPDATE tbsala SET tbsalanombre=?, tbsalacapacidad=?, tbsalaestado=? WHERE tbsalaid=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "siii",
            $sala->getTbsalanombre(),
            $sala->getTbsalacapacidad(),
            $sala->getTbsalaestado(),
            $sala->getTbsalaid()
        );

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTbsala($id){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "DELETE FROM tbsala WHERE tbsalaid=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getSalaById($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbsala WHERE tbsalaid = ?;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $sala = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $sala = new Sala(
                $row['tbsalaid'],
                $row['tbsalanombre'],
                $row['tbsalacapacidad'],
                $row['tbsalaestado']
            );
        }
        mysqli_close($conn);
        return $sala;
    }

    public function getAllSalas(){
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
                $row['tbsalaestado']
            );
        }

        mysqli_close($conn);
        return $salas;
    }


}
?>