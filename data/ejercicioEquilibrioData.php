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
        ) VALUES (?, ?, ?, ?, ?, ?);";

        $stmt = mysqli_prepare($conn, $queryInsert);
        $nombre = $ejercicio->getTbejercicioequilibrionombre();
        $descripcion = $ejercicio->getTbejercicioequilibriodescripcion();
        $dificultad = $ejercicio->getTbejercicioequilibriodificultad();
        $duracion = $ejercicio->getTbejercicioequilibrioduracion();
        $materiales = $ejercicio->getTbejercicioequilibriomateriales();
        $postura = $ejercicio->getTbejercicioequilibriopostura();

        mysqli_stmt_bind_param($stmt, "sssiss", $nombre, $descripcion, $dificultad, $duracion, $materiales, $postura);

        $result = mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_close($conn);
        return $result ? $id : 0;
    }

    public function actualizarTbejercicioequilibrio($ejercicio){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbejercicioequilibrio SET
            tbejercicioequilibrionombre=?,
            tbejercicioequilibriodescripcion=?,
            tbejercicioequilibriodificultad=?,
            tbejercicioequilibrioduracion=?,
            tbejercicioequilibriomateriales=?,
            tbejercicioequilibriopostura=?
            WHERE tbejercicioequilibrioid=?;";

        $stmt = mysqli_prepare($conn, $queryUpdate);
        $nombre = $ejercicio->getTbejercicioequilibrionombre();
        $descripcion = $ejercicio->getTbejercicioequilibriodescripcion();
        $dificultad = $ejercicio->getTbejercicioequilibriodificultad();
        $duracion = $ejercicio->getTbejercicioequilibrioduracion();
        $materiales = $ejercicio->getTbejercicioequilibriomateriales();
        $postura = $ejercicio->getTbejercicioequilibriopostura();
        $id = $ejercicio->getTbejercicioequilibrioid();

        mysqli_stmt_bind_param($stmt, "sssissi", $nombre, $descripcion, $dificultad, $duracion, $materiales, $postura, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTbejercicioequilibrio($id){
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbejercicioequilibrio WHERE tbejercicioequilibrioid=?;";
        $stmt = mysqli_prepare($conn, $queryDelete);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $result = mysqli_stmt_execute($stmt);
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