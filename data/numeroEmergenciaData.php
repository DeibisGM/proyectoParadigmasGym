<?php
include_once 'data.php';
include_once '../domain/numeroEmergencia.php';

class numeroEmergenciaData extends Data
{
    public function insertarTBNumeroEmergencia($numeroEmergencia) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryGetLastId = "SELECT MAX(tbnumeroemergenciaid) FROM tbnumeroemergencia;";
        $resultId = mysqli_query($conn, $queryGetLastId);
        $nextId = 1;

        if ($row = mysqli_fetch_row($resultId)) {
            if ($row[0] !== null) {
                $nextId = (int)$row[0] + 1;
            }
        }

        $queryInsert = "INSERT INTO tbnumeroemergencia 
        (tbnumeroemergenciaid, tbnumeroemergenciaclienteid, tbnumeroemergencianombre, tbnumeroemergenciatelefono, tbnumeroemergenciarelacion) 
        VALUES (" . $nextId . ",'" .
            $numeroEmergencia->getClienteId() . "','" .
            $numeroEmergencia->getNombre() . "','" .
            $numeroEmergencia->getTelefono() . "','" .
            $numeroEmergencia->getRelacion() . "');";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTBNumeroEmergencia($numeroEmergencia) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbnumeroemergencia SET 
        tbnumeroemergenciaclienteid='" . $numeroEmergencia->getClienteId() . "',
        tbnumeroemergencianombre='" . $numeroEmergencia->getNombre() . "',
        tbnumeroemergenciatelefono='" . $numeroEmergencia->getTelefono() . "',
        tbnumeroemergenciarelacion='" . $numeroEmergencia->getRelacion() . "'
        WHERE tbnumeroemergenciaid=" . $numeroEmergencia->getId() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBNumeroEmergencia($numeroEmergenciaId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbnumeroemergencia WHERE tbnumeroemergenciaid=" . $numeroEmergenciaId . ";";

        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    public function getAllTBNumeroEmergencia() {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbnumeroemergencia;";
        $result = mysqli_query($conn, $querySelect);

        $numeros = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $numeros[] = new numeroEmergencia(
                $row['tbnumeroemergenciaid'],
                $row['tbnumeroemergenciaclienteid'],
                $row['tbnumeroemergencianombre'],
                $row['tbnumeroemergenciatelefono'],
                $row['tbnumeroemergenciarelacion']
            );
        }

        mysqli_close($conn);
        return $numeros;
    }

    public function getAllTBNumeroEmergenciaByClienteId($clienteId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbnumeroemergencia WHERE tbnumeroemergenciaclienteid=" . intval($clienteId) . ";";
        $result = mysqli_query($conn, $querySelect);

        $numeros = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $numeros[] = new numeroEmergencia(
                $row['tbnumeroemergenciaid'],
                $row['tbnumeroemergenciaclienteid'],
                $row['tbnumeroemergencianombre'],
                $row['tbnumeroemergenciatelefono'],
                $row['tbnumeroemergenciarelacion']
            );
        }

        mysqli_close($conn);
        return $numeros;
    }

    public function existeNumeroEmergencia($clienteId, $telefono) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT COUNT(*) as total 
              FROM tbnumeroemergencia 
              WHERE tbnumeroemergenciaclienteid=" . intval($clienteId) . " 
              AND tbnumeroemergenciatelefono='" . mysqli_real_escape_string($conn, $telefono) . "';";

        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        mysqli_close($conn);

        return ($row['total'] > 0);
    }


}