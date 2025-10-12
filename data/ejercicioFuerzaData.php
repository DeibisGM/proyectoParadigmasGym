<?php

include_once 'data.php';
include_once '../domain/ejercicioFuerza.php';

class EjercicioFuerzaData extends Data{

    public function insertarTbejerciciofuerza($ejercicio){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbejerciciofuerza (
            tbejerciciofuerzanombre,
            tbejerciciofuerzadescripcion,
            tbejerciciofuerzarepeticion,
            tbejerciciofuerzaserie,
            tbejerciciofuerzapeso,
            tbejerciciofuerzadescanso
        ) VALUES (
            '" . $ejercicio->getTbejerciciofuerzanombre() . "',
            '" . $ejercicio->getTbejerciciofuerzadescripcion() . "',
            '" . $ejercicio->getTbejerciciofuerzarepeticion() . "',
            '" . $ejercicio->getTbejerciciofuerzaserie() . "',
            '" . $ejercicio->getTbejerciciofuerzapeso() . "',
            '" . $ejercicio->getTbejerciciofuerzadescanso() . "'
        );";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTbejerciciofuerza($ejercicio){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbejerciciofuerza SET
            tbejerciciofuerzanombre='" . $ejercicio->getTbejerciciofuerzanombre() . "',
            tbejerciciofuerzadescripcion='" . $ejercicio->getTbejerciciofuerzadescripcion() . "',
            tbejerciciofuerzarepeticion='" . $ejercicio->getTbejerciciofuerzarepeticion() . "',
            tbejerciciofuerzaserie='" . $ejercicio->getTbejerciciofuerzaserie() . "',
            tbejerciciofuerzapeso='" . $ejercicio->getTbejerciciofuerzapeso() . "',
            tbejerciciofuerzadescanso='" . $ejercicio->getTbejerciciofuerzadescanso() . "'
            WHERE tbejerciciofuerzaid=" . $ejercicio->getTbejerciciofuerzaid() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTbejerciciofuerza($id){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbejerciciofuerza WHERE tbejerciciofuerzaid=" . $id . ";";
        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    public function obtenerTbejerciciofuerza(){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbejerciciofuerza;";
        $result = mysqli_query($conn, $querySelect);

        $ejercicios = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ejercicios[] = new EjercicioFuerza(
                $row['tbejerciciofuerzaid'],
                $row['tbejerciciofuerzanombre'],
                $row['tbejerciciofuerzadescripcion'],
                $row['tbejerciciofuerzarepeticion'],
                $row['tbejerciciofuerzaserie'],
                $row['tbejerciciofuerzapeso'],
                $row['tbejerciciofuerzadescanso']
            );
        }

        mysqli_close($conn);
        return $ejercicios;
    }
}
?>