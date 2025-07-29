<?php

include_once 'data.php';
include '../domain/zonaCuerpo.php';

class ZonaCuerpoData extends Data {

    public function insertarTBZonaCuerpo($zonaCuerpo) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db);
        $conn->set_charset('utf8');

        $queryGetLastId = "SELECT MAX(idzonacuerpo) AS idzonacuerpo FROM tbzonacuerpo";
        $resultId = mysqli_query($conn, $queryGetLastId);
        $nextId = 1;

        if ($row = mysqli_fetch_row($resultId)) {
            if ($row[0] !== null) {
                $nextId = (int)$row[0] + 1;
            }
        }

        $queryInsert = "INSERT INTO tbzonacuerpo VALUES (" . $nextId . ",'" .
                $zonaCuerpo->getNombreZonaCuerpo() . "','" .
                $zonaCuerpo->getDescripcionZonaCuerpo() . "'," .
                $zonaCuerpo->getActivoZonaCuerpo() . ");";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTBZonaCuerpo($zonaCuerpo) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbzonacuerpo SET nombrezonacuerpo='" . $zonaCuerpo->getNombreZonaCuerpo() .
                "', descripcionzonacuerpo='" . $zonaCuerpo->getDescripcionZonaCuerpo() .
                "', activozonacuerpo=" . $zonaCuerpo->getActivoZonaCuerpo() .
                " WHERE idzonacuerpo=" . $zonaCuerpo->getIdZonaCuerpo() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBZonaCuerpo($idZonaCuerpo) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db);
        $conn->set_charset('utf8');
        $queryDelete = "DELETE from tbzonacuerpo where idzonacuerpo=" . $idZonaCuerpo . ";";
        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    public function getAllTBZonaCuerpo() {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db);
        $conn->set_charset('utf8');
        $querySelect = "SELECT * FROM tbzonacuerpo;";
        $result = mysqli_query($conn, $querySelect);
        mysqli_close($conn);
        $zonasCuerpo = [];
        while ($row = mysqli_fetch_array($result)) {
            $currentZonaCuerpo = new ZonaCuerpo($row['idzonacuerpo'], $row['nombrezonacuerpo'], $row['descripcionzonacuerpo'], $row['activozonacuerpo']);
            array_push($zonasCuerpo, $currentZonaCuerpo);
        }
        return $zonasCuerpo;
    }
}
?>