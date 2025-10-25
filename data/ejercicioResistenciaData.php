<?php
include_once 'data.php';
include_once '../domain/ejercicioResistencia.php';
include_once 'ejercicioSubzonaData.php';

class ejercicioResistenciaData extends Data
{

    public function insertarTBEjercicioResistencia($resistencia)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "INSERT INTO tbejercicioresistencia (tbejercicioresistencianombre, tbejercicioresistenciatiempo, tbejercicioresistenciapeso, tbejercicioresistenciadescripcion) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        $nombre = $resistencia->getNombre();
        $tiempo = $resistencia->getTiempo();
        $peso = $resistencia->getPeso();
        $descripcion = $resistencia->getDescripcion();

        mysqli_stmt_bind_param($stmt, "ssis", $nombre, $tiempo, $peso, $descripcion);

        $result = mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_close($conn);
        return $result ? $id : 0;
    }

    public function existeEjercicioPorNombre($nombre)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $nombre = mysqli_real_escape_string($conn, $nombre);
        $query = "SELECT tbejercicioresistencianombre FROM tbejercicioresistencia WHERE tbejercicioresistencianombre='" . $nombre . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }

    public function actualizarTBEjercicioResistencia($resistencia)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "UPDATE tbejercicioresistencia SET tbejercicioresistencianombre=?, tbejercicioresistenciatiempo=?, tbejercicioresistenciapeso=?, tbejercicioresistenciadescripcion=?, tbejercicioresistenciaactivo=? WHERE tbejercicioresistenciaid=?";
        $stmt = mysqli_prepare($conn, $query);

        $nombre = $resistencia->getNombre();
        $tiempo = $resistencia->getTiempo();
        $peso = $resistencia->getPeso();
        $descripcion = $resistencia->getDescripcion();
        $activo = $resistencia->getActivo();
        $id = $resistencia->getId();

        mysqli_stmt_bind_param($stmt, "ssisii", $nombre, $tiempo, $peso, $descripcion, $activo, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBEjercicioResistencia($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return false;
        }
        $conn->set_charset('utf8');

        mysqli_autocommit($conn, false);

        try {
// 1. Primero eliminar subzonas asociadas
            $ejercicioSubzona = new ejercicioSubzonaData();
            $resultEjercicioSubzona = $ejercicioSubzona->eliminarTBEjercicioSubZona($id, 'Resistencia');

// 2. Luego eliminar el cliente
            $queryDelete = "DELETE FROM tbejercicioresistencia WHERE tbejercicioresistenciaid=?";
            $stmt = mysqli_prepare($conn, $queryDelete);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                $resultResistencia = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparando consulta de ejercicio de resistencia");
            }

// Si ambas operaciones fueron exitosas, confirmar
            if ($resultEjercicioSubzona && $resultResistencia) {
                mysqli_commit($conn);
                $result = true;
            } else {
                mysqli_rollback($conn);
                $result = false;
            }

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $result = false;
        }

        mysqli_close($conn);
        return $result;
    }

    public function getAllTBEjercicioResistecia()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbejercicioresistencia;";
        $result = mysqli_query($conn, $querySelect);

        $resistencia = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $resistencia[] = new ejercicioResistencia(
                $row['tbejercicioresistenciaid'],
                $row['tbejercicioresistencianombre'],
                $row['tbejercicioresistenciatiempo'],
                $row['tbejercicioresistenciapeso'],
                $row['tbejercicioresistenciadescripcion'],  // ✅ corregido
                $row['tbejercicioresistenciaactivo']
            );
        }

        mysqli_close($conn);
        return $resistencia;
    }

    public function getTBEjercicioResisteciaByActivo()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbejercicioresistencia WHERE tbejercicioresistenciaactivo = 1;";
        $result = mysqli_query($conn, $querySelect);

        $resistencia = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $resistencia[] = new ejercicioResistencia(
                $row['tbejercicioresistenciaid'],
                $row['tbejercicioresistencianombre'],
                $row['tbejercicioresistenciatiempo'],
                $row['tbejercicioresistenciapeso'],
                $row['tbejercicioresistenciadescripcion'], // ✅ corregido
                $row['tbejercicioresistenciaactivo']
            );
        }

        mysqli_close($conn);
        return $resistencia;
    }

    public function getEjercicioResistencia($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbejercicioresistencia WHERE tbejercicioresistenciaid = ?;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $ejercicioresistencia = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $ejercicioresistencia = new ejercicioresistencia(
                $row['tbejercicioresistenciaid'],
                $row['tbejercicioresistencianombre'],
                $row['tbejercicioresistenciatiempo'],
                $row['tbejercicioresistenciapeso'],
                $row['tbejercicioresistenciadescripcion'],
                $row['tbejercicioresistenciaactivo']
            );
        }

        mysqli_close($conn);
        return $ejercicioresistencia;
    }


}