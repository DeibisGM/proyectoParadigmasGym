<?php
include_once __DIR__ . '/data.php';
include_once __DIR__ . '/../domain/rutina.php';
include_once __DIR__ . '/../domain/rutinaEjercicio.php';

class RutinaData extends Data
{
    public function insertarRutina($rutina)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "INSERT INTO tbrutina (tbclienteid, tbrutinafecha, tbrutinaobservacion) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        $clienteId = $rutina->getClienteId();
        $fecha = $rutina->getFecha();
        $observacion = $rutina->getObservacion();
        mysqli_stmt_bind_param($stmt, "iss", $clienteId, $fecha, $observacion);
        $result = mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_close($conn);
        return $result ? $id : 0;
    }

    public function insertarRutinaEjercicio($ejercicio)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "INSERT INTO tbrutinaejercicio (tbrutinaid, tbrutinaejerciciotipo, tbejercicioid, tbrutinaejercicioseries, tbrutinaejerciciorepeticiones, tbrutinaejerciciopeso, tbrutinaejerciciotiempo_seg, tbrutinaejerciciodescanso_seg, tbrutinaejerciciocomentario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        $rutinaId = $ejercicio->getRutinaId();
        $tipo = $ejercicio->getTipo();
        $ejercicioId = $ejercicio->getEjercicioId();
        $series = $ejercicio->getSeries();
        $repeticiones = $ejercicio->getRepeticiones();
        $peso = $ejercicio->getPeso();
        $tiempo = $ejercicio->getTiempo();
        $descanso = $ejercicio->getDescanso();
        $comentario = $ejercicio->getComentario();
        mysqli_stmt_bind_param($stmt, "isiididis", $rutinaId, $tipo, $ejercicioId, $series, $repeticiones, $peso, $tiempo, $descanso, $comentario);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getRutinasPorCliente($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbrutina WHERE tbclienteid = ? ORDER BY tbrutinafecha DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rutinas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rutinas[] = new Rutina($row['tbrutinaid'], $row['tbclienteid'], $row['tbrutinafecha'], $row['tbrutinaobservacion']);
        }
        mysqli_close($conn);
        return $rutinas;
    }

    public function getEjerciciosPorRutinaId($rutinaId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbrutinaejercicio WHERE tbrutinaid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $rutinaId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $ejercicios = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ejercicios[] = new RutinaEjercicio($row['tbrutinaejercicioid'], $row['tbrutinaid'], $row['tbrutinaejerciciotipo'], $row['tbejercicioid'], $row['tbrutinaejercicioseries'], $row['tbrutinaejerciciorepeticiones'], $row['tbrutinaejerciciopeso'], $row['tbrutinaejerciciotiempo_seg'], $row['tbrutinaejerciciodescanso_seg'], $row['tbrutinaejerciciocomentario']);
        }
        mysqli_close($conn);
        return $ejercicios;
    }

    public function eliminarRutina($rutinaId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        mysqli_autocommit($conn, false);
        $success = true;

        $queryEjercicios = "DELETE FROM tbrutinaejercicio WHERE tbrutinaid = ?";
        $stmtEjercicios = mysqli_prepare($conn, $queryEjercicios);
        mysqli_stmt_bind_param($stmtEjercicios, "i", $rutinaId);
        if (!mysqli_stmt_execute($stmtEjercicios)) $success = false;

        $queryRutina = "DELETE FROM tbrutina WHERE tbrutinaid = ?";
        $stmtRutina = mysqli_prepare($conn, $queryRutina);
        mysqli_stmt_bind_param($stmtRutina, "i", $rutinaId);
        if (!mysqli_stmt_execute($stmtRutina)) $success = false;

        if ($success) {
            mysqli_commit($conn);
        } else {
            mysqli_rollback($conn);
        }
        mysqli_close($conn);
        return $success;
    }

    public function getRutinasPorClienteEnRangoFechas($clienteId, $fechaInicio, $fechaFin)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbrutina WHERE tbclienteid = ? AND tbrutinafecha BETWEEN ? AND ? ORDER BY tbrutinafecha DESC";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iss", $clienteId, $fechaInicio, $fechaFin);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rutinas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rutinas[] = new Rutina($row['tbrutinaid'], $row['tbclienteid'], $row['tbrutinafecha'], $row['tbrutinaobservacion']);
        }
        mysqli_close($conn);
        return $rutinas;
    }
}
?>