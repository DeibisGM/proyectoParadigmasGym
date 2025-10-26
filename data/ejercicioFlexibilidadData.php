<?php
include_once 'data.php';
include_once '../domain/ejercicioFlexibilidad.php';
include_once 'ejercicioSubzonaData.php';

class ejercicioFlexibilidadData extends Data
{

    public function insertarTBEjercicioFlexibilidad($flexibilidad)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "INSERT INTO tbejercicioflexibilidad (tbejercicioflexibilidadnombre, tbejercicioflexibilidaddescripcion, tbejercicioflexibilidadduracion, tbejercicioflexibilidadseries, tbejercicioflexibilidadequipodeayuda) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        $nombre = $flexibilidad->getNombre();
        $descripcion = $flexibilidad->getDescripcion();
        $duracion = $flexibilidad->getDuracion();
        $series = $flexibilidad->getSeries();
        $equipodeayuda = $flexibilidad->getEquipodeayuda();
        $equipodeayuda = $flexibilidad->getEquipodeayuda();
        $activo = $flexibilidad->getActivo();

        mysqli_stmt_bind_param($stmt, "sssss", $nombre, $descripcion, $duracion, $series, $equipodeayuda);

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
        $query = "SELECT tbejercicioflexibilidadnombre FROM tbejercicioflexibilidad WHERE tbejercicioflexibilidadnombre='" . $nombre . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }

    public function actualizarTBEjercicioFlexibilidad($flexibilidad)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "UPDATE tbejercicioflexibilidad SET tbejercicioflexibilidadnombre=?, tbejercicioflexibilidaddescripcion=?, tbejercicioflexibilidadduracion=?, tbejercicioflexibilidadseries=?, tbejercicioflexibilidadequipodeayuda=?, tbejercicioflexibilidadactivo=? WHERE tbejercicioflexibilidadid=?";
        $stmt = mysqli_prepare($conn, $query);

        $nombre = $flexibilidad->getNombre();
        $descripcion = $flexibilidad->getDescripcion();
        $duracion = (int)$flexibilidad->getDuracion();
        $series = (int)$flexibilidad->getSeries();
        $equipodeayuda = $flexibilidad->getEquipodeayuda();
        $activo = (int)$flexibilidad->getActivo();
        $id = (int)$flexibilidad->getId();

        mysqli_stmt_bind_param($stmt, "ssiisii", $nombre, $descripcion, $duracion, $series, $equipodeayuda, $activo, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBEjercicioFlexibilidad($id)
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
            $resultEjercicioSubzona = $ejercicioSubzona->eliminarTBEjercicioSubZona($id, 'Flexibilidad');

            // 2. Luego eliminar el ejercicio
            $queryDelete = "DELETE FROM tbejercicioflexibilidad WHERE tbejercicioflexibilidadid=?";
            $stmt = mysqli_prepare($conn, $queryDelete);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                $resultFlexibilidad = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparando consulta de ejercicio de flexibilidad");
            }

            // Si ambas operaciones fueron exitosas, confirmar
            if ($resultEjercicioSubzona && $resultFlexibilidad) {
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

    public function getAllTBEjercicioFlexibilidad()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbejercicioflexibilidad;";
        $result = mysqli_query($conn, $querySelect);

        $flexibilidad = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $flexibilidad[] = new ejercicioFlexibilidad(
                $row['tbejercicioflexibilidadid'],
                $row['tbejercicioflexibilidadnombre'],
                $row['tbejercicioflexibilidaddescripcion'],
                $row['tbejercicioflexibilidadduracion'],
                $row['tbejercicioflexibilidadseries'],
                $row['tbejercicioflexibilidadequipodeayuda'],
                $row['tbejercicioflexibilidadactivo']
            );
        }

        mysqli_close($conn);
        return $flexibilidad;
    }

    public function getTBEjercicioFlexibilidadByActivo()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbejercicioflexibilidad WHERE tbejercicioflexibilidadactivo = 1;";
        $result = mysqli_query($conn, $querySelect);

        $flexibilidad = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $flexibilidad[] = new ejercicioFlexibilidad(
                $row['tbejercicioflexibilidadid'],
                $row['tbejercicioflexibilidadnombre'],
                $row['tbejercicioflexibilidaddescripcion'],
                $row['tbejercicioflexibilidadduracion'],
                $row['tbejercicioflexibilidadseries'],
                $row['tbejercicioflexibilidadequipodeayuda'],
                $row['tbejercicioflexibilidadactivo']
            );
        }

        mysqli_close($conn);
        return $flexibilidad;
    }

    public function getEjercicioFlexibilidad($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbejercicioflexibilidad WHERE tbejercicioflexibilidadid = ?;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $ejercicioflexibilidad = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $ejercicioflexibilidad = new ejercicioFlexibilidad(
                $row['tbejercicioflexibilidadid'],
                $row['tbejercicioflexibilidadnombre'],
                $row['tbejercicioflexibilidaddescripcion'],
                $row['tbejercicioflexibilidadduracion'],
                $row['tbejercicioflexibilidadseries'],
                $row['tbejercicioflexibilidadequipodeayuda'],
                $row['tbejercicioflexibilidadactivo']
            );
        }

        mysqli_close($conn);
        return $ejercicioflexibilidad;
    }
}