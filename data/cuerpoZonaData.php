<?php

include_once 'data.php';
include '../domain/cuerpoZona.php';

class CuerpoZonaData extends Data {

    /**
     * Verifica si ya existe una zona del cuerpo con el nombre especificado.
     */
    public function existeCuerpoZonaNombre($nombreCuerpoZona) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Consulta para verificar si existe una zona con el mismo nombre
        $queryCheck = "SELECT COUNT(*) as count FROM tbcuerpozona WHERE tbcuerpozonanombre = '" . $nombreCuerpoZona . "';";
        $result = mysqli_query($conn, $queryCheck);
        $row = mysqli_fetch_assoc($result);
        
        mysqli_close($conn);
        return ($row['count'] > 0);
    }

    /**
     * Inserta una nueva zona del cuerpo en la base de datos.
     */
    public function insertarTBCuerpoZona($cuerpoZona) {

        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Obtener el siguiente ID disponible
        $queryGetLastId = "SELECT MAX(tbcuerpozonaid) AS idcuerpozona FROM tbcuerpozona";
        $resultId = mysqli_query($conn, $queryGetLastId);
        $nextId = 1;

        if ($row = mysqli_fetch_row($resultId)) {
            if ($row[0] !== null) {
                $nextId = (int)$row[0] + 1;
            }
        }

        // Consulta de inserción
        $queryInsert = "INSERT INTO tbcuerpozona VALUES (" . $nextId . ",'" .
                $cuerpoZona->getNombreCuerpoZona() . "','" .
                $cuerpoZona->getDescripcionCuerpoZona() . "'," .
                $cuerpoZona->getActivoCuerpoZona() . ");";

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
    public function actualizarTBCuerpoZona($cuerpoZona) {

        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Consulta de actualización
        $queryUpdate = "UPDATE tbcuerpozona SET tbcuerpozonanombre='" . $cuerpoZona->getNombreCuerpoZona() .
                "', tbcuerpozonadescripcion='" . $cuerpoZona->getDescripcionCuerpoZona() .
                "', tbcuerpozonaactivo=" . $cuerpoZona->getActivoCuerpoZona() .
                " WHERE tbcuerpozonaid=" . $cuerpoZona->getIdCuerpoZona() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    /**
     * Actualiza el estado (activo/inactivo) de una zona del cuerpo.
     */
    public function actualizarEstadoTBCuerpoZona($idCuerpoZona, $estado) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbcuerpozona SET tbcuerpozonaactivo=" . $estado . " WHERE tbcuerpozonaid=" . $idCuerpoZona . ";";
        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    

    /**
     * Obtiene todas las zonas del cuerpo de la base de datos.
     */
    public function getAllTBCuerpoZona() {

        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Consulta de selección
        $querySelect = "SELECT * FROM tbcuerpozona;";
        $result = mysqli_query($conn, $querySelect);
        mysqli_close($conn);

        $cuerpoZonas = [];
        while ($row = mysqli_fetch_array($result)) {
            $currentCuerpoZona = new CuerpoZona($row['tbcuerpozonaid'], $row['tbcuerpozonanombre'], $row['tbcuerpozonadescripcion'], $row['tbcuerpozonaactivo']);
            array_push($cuerpoZonas, $currentCuerpoZona);
        }
        return $cuerpoZonas;
    }
    
    /**
     * Obtiene solo las zonas del cuerpo activas de la base de datos.
     */
    public function getActiveTBCuerpoZona() {

        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Consulta de selección filtrando solo activos (tbcuerpozonaactivo = 1)
        $querySelect = "SELECT * FROM tbcuerpozona WHERE tbcuerpozonaactivo = 1;";
        $result = mysqli_query($conn, $querySelect);
        mysqli_close($conn);

        $cuerpoZonas = [];
        while ($row = mysqli_fetch_array($result)) {
            $currentCuerpoZona = new CuerpoZona($row['tbcuerpozonaid'], $row['tbcuerpozonanombre'], $row['tbcuerpozonadescripcion'], $row['tbcuerpozonaactivo']);
            array_push($cuerpoZonas, $currentCuerpoZona);
        }
        return $cuerpoZonas;
    }
}
?>