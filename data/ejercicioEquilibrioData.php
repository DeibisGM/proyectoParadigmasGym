<?php

include_once 'data.php';
include_once '../domain/ejercicioEquilibrio.php';

class EjercicioEquilibrioData extends Data{

    public function insertarTbejercicioequilibrio($ejercicio){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbejercicioequilibrio (
            tbejercicioequilibrionombre,
            tbejercicioequilibriodescripcion,
            tbejercicioequilibriodificultad,
            tbejercicioequilibrioduracion,
            tbejercicioequilibriomateriales,
            tbejercicioequilibriopostura
        ) VALUES (
            '" . $ejercicio->getTbejercicioequilibrionombre() . "',
            '" . $ejercicio->getTbejercicioequilibriodescripcion() . "',
            '" . $ejercicio->getTbejercicioequilibriodificultad() . "',
            '" . $ejercicio->getTbejercicioequilibrioduracion() . "',
            '" . $ejercicio->getTbejercicioequilibriomateriales() . "',
            '" . $ejercicio->getTbejercicioequilibriopostura() . "'
        );";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTbejercicioequilibrio($ejercicio){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbejercicioequilibrio SET
            tbejercicioequilibrionombre='" . $ejercicio->getTbejercicioequilibrionombre() . "',
            tbejercicioequilibriodescripcion='" . $ejercicio->getTbejercicioequilibriodescripcion() . "',
            tbejercicioequilibriodificultad='" . $ejercicio->getTbejercicioequilibriodificultad() . "',
            tbejercicioequilibrioduracion='" . $ejercicio->getTbejercicioequilibrioduracion() . "',
            tbejercicioequilibriomateriales='" . $ejercicio->getTbejercicioequilibriomateriales() . "',
            tbejercicioequilibriopostura='" . $ejercicio->getTbejercicioequilibriopostura() . "'
            WHERE tbejercicioequilibrioid=" . $ejercicio->getTbejercicioequilibrioid() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTbejercicioequilibrio($id){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbejercicioequilibrio WHERE tbejercicioequilibrioid=" . $id . ";";
        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    public function obtenerTbejercicioequilibrio(){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbejercicioequilibrio;";
        $result = mysqli_query($conn, $querySelect);

        $ejercicios = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ejercicios[] = new EjercicioEquilibrio(
                $row['tbejercicioequilibrioid'],
                $row['tbejercicioequilibrionombre'],
                $row['tbejercicioequilibriodescripcion'],
                $row['tbejercicioequilibriodificultad'],
                $row['tbejercicioequilibrioduracion'],
                $row['tbejercicioequilibriomateriales'],
                $row['tbejercicioequilibriopostura']
            );
        }

        mysqli_close($conn);
        return $ejercicios;
    }
}
?>