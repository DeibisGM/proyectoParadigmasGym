<?php

include_once 'data.php';
include '../domain/zonaCuerpo.php';

class ZonaCuerpoData extends Data {

    /**
     * Verifica si ya existe una zona del cuerpo con el nombre especificado.
     */
    public function existeZonaCuerpoNombre($nombreZonaCuerpo) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Consulta para verificar si existe una zona con el mismo nombre
        $queryCheck = "SELECT COUNT(*) as count FROM tbzonacuerpo WHERE tbzonacuerponombre = '" . $nombreZonaCuerpo . "';";
        $result = mysqli_query($conn, $queryCheck);
        $row = mysqli_fetch_assoc($result);
        
        mysqli_close($conn);
        return ($row['count'] > 0);
    }

    /**
     * Inserta una nueva zona del cuerpo en la base de datos.
     */
    public function insertarTBZonaCuerpo($zonaCuerpo) {

        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Obtener el siguiente ID disponible
        $queryGetLastId = "SELECT MAX(tbzonacuerpoid) AS idzonacuerpo FROM tbzonacuerpo";
        $resultId = mysqli_query($conn, $queryGetLastId);
        $nextId = 1;

        if ($row = mysqli_fetch_row($resultId)) {
            if ($row[0] !== null) {
                $nextId = (int)$row[0] + 1;
            }
        }

        // Consulta de inserción
        $queryInsert = "INSERT INTO tbzonacuerpo VALUES (" . $nextId . ",'" .
                $zonaCuerpo->getNombreZonaCuerpo() . "','" .
                $zonaCuerpo->getDescripcionZonaCuerpo() . "'," .
                $zonaCuerpo->getActivoZonaCuerpo() . ");";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);

        if ($result) {
            return $nextId; // Devuelve el ID en lugar de un booleano
        } else {
            return false; // O un valor que indique error
        }
    }

    /**
     * Actualiza una zona del cuerpo existente.
     */
    public function actualizarTBZonaCuerpo($zonaCuerpo) {

        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Consulta de actualización
        $queryUpdate = "UPDATE tbzonacuerpo SET tbzonacuerponombre='" . $zonaCuerpo->getNombreZonaCuerpo() .
                "', tbzonacuerpodescripcion='" . $zonaCuerpo->getDescripcionZonaCuerpo() .
                "', tbzonacuerpoactivo=" . $zonaCuerpo->getActivoZonaCuerpo() .
                " WHERE tbzonacuerpoid=" . $zonaCuerpo->getIdZonaCuerpo() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    /**
     * Elimina una zona del cuerpo por su ID.
     */
    public function eliminarTBZonaCuerpo($idZonaCuerpo) {

        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Consulta de eliminación
        $queryDelete = "DELETE from tbzonacuerpo where tbzonacuerpoid=" . $idZonaCuerpo . ";";
        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    /**
     * Obtiene todas las zonas del cuerpo de la base de datos.
     */
    public function getAllTBZonaCuerpo() {

        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Consulta de selección
        $querySelect = "SELECT * FROM tbzonacuerpo;";
        $result = mysqli_query($conn, $querySelect);
        mysqli_close($conn);

        $zonasCuerpo = [];
        while ($row = mysqli_fetch_array($result)) {
            $currentZonaCuerpo = new ZonaCuerpo($row['tbzonacuerpoid'], $row['tbzonacuerponombre'], $row['tbzonacuerpodescripcion'], $row['tbzonacuerpoactivo']);
            array_push($zonasCuerpo, $currentZonaCuerpo);
        }
        return $zonasCuerpo;
    }
}
?>