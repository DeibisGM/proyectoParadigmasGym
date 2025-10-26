<?php
include_once 'data.php';
include_once '../domain/ejercicioFuerza.php';
include_once 'ejercicioSubzonaData.php';

class EjercicioFuerzaData extends Data
{

    public function insertarTbejerciciofuerza($ejercicio)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "INSERT INTO tbejerciciofuerza (tbejerciciofuerzanombre, tbejerciciofuerzadescripcion, tbejerciciofuerzarepeticion, tbejerciciofuerzaserie, tbejerciciofuerzapeso, tbejerciciofuerzadescanso) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        $nombre = $ejercicio->getNombre();
        $descripcion = $ejercicio->getDescripcion();
        $repeticion = $ejercicio->getRepeticion();
        $serie = $ejercicio->getSerie();
        $peso = $ejercicio->getPeso();
        $descanso = $ejercicio->getDescanso();

        mysqli_stmt_bind_param($stmt, "ssiiii", $nombre, $descripcion, $repeticion, $serie, $peso, $descanso);

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
        $query = "SELECT tbejerciciofuerzanombre FROM tbejerciciofuerza WHERE tbejerciciofuerzanombre='" . $nombre . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }

    public function actualizarTbejerciciofuerza($ejercicio)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "UPDATE tbejerciciofuerza SET tbejerciciofuerzanombre=?, tbejerciciofuerzadescripcion=?, tbejerciciofuerzarepeticion=?, tbejerciciofuerzaserie=?, tbejerciciofuerzapeso=?, tbejerciciofuerzadescanso=?, tbejerciciofuerzaactivo=? WHERE tbejerciciofuerzaid=?";
        $stmt = mysqli_prepare($conn, $query);

        $nombre = $ejercicio->getNombre();
        $descripcion = $ejercicio->getDescripcion();
        $repeticion = $ejercicio->getRepeticion();
        $serie = $ejercicio->getSerie();
        $peso = $ejercicio->getPeso();
        $descanso = $ejercicio->getDescanso();
        $activo = $ejercicio->getActivo();
        $id = $ejercicio->getId();

        mysqli_stmt_bind_param($stmt, "ssiiiiii", $nombre, $descripcion, $repeticion, $serie, $peso, $descanso, $activo, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTbejerciciofuerza($id)
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
            $resultEjercicioSubzona = $ejercicioSubzona->eliminarTBEjercicioSubZona($id, 'Fuerza');

            // 2. Luego eliminar el ejercicio
            $queryDelete = "DELETE FROM tbejerciciofuerza WHERE tbejerciciofuerzaid=?";
            $stmt = mysqli_prepare($conn, $queryDelete);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                $resultFuerza = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception("Error preparando consulta de ejercicio de fuerza");
            }

            // Si ambas operaciones fueron exitosas, confirmar
            if ($resultEjercicioSubzona && $resultFuerza) {
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

    public function obtenerTbejerciciofuerza()
    {
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
                $row['tbejerciciofuerzadescanso'],
                $row['tbejerciciofuerzaactivo']
            );
        }

        mysqli_close($conn);
        return $ejercicios;
    }

    public function getTBEjercicioFuerzaByActivo()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbejerciciofuerza WHERE tbejerciciofuerzaactivo = 1;";
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
                $row['tbejerciciofuerzadescanso'],
                $row['tbejerciciofuerzaactivo']
            );
        }

        mysqli_close($conn);
        return $ejercicios;
    }

    public function getEjercicioFuerza($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbejerciciofuerza WHERE tbejerciciofuerzaid = ?;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $ejercicio = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $ejercicio = new EjercicioFuerza(
                $row['tbejerciciofuerzaid'],
                $row['tbejerciciofuerzanombre'],
                $row['tbejerciciofuerzadescripcion'],
                $row['tbejerciciofuerzarepeticion'],
                $row['tbejerciciofuerzaserie'],
                $row['tbejerciciofuerzapeso'],
                $row['tbejerciciofuerzadescanso'],
                $row['tbejerciciofuerzaactivo']
            );
        }

        mysqli_close($conn);
        return $ejercicio;
    }
}
?>