<?php
include_once 'data.php';
include_once '../domain/ejercicioSubzona.php';

class ejercicioSubzonaData extends Data
{

    public function insertarTBEjercicioSubzona($ejercicioSubzona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "INSERT INTO tbejerciciosubzona (tbejerciciosubzonaejercicioid, tbejerciciosubzonasubid, tbejerciciosubzonanombre) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        $ejercicio = $ejercicioSubzona->getEjercicio();
        $subzona = $ejercicioSubzona->getSubzona();
        $nombre = $ejercicioSubzona->getNombre();

        mysqli_stmt_bind_param($stmt, "iss", $ejercicio, $subzona, $nombre);

        $result = mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        mysqli_close($conn);
        return $result ? $id : 0;
    }

    public function actualizarTBEjercicioSubzona($ejercicioSubzona)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "UPDATE tbejerciciosubzona SET tbejerciciosubzonaejercicioid=?, tbejerciciosubzonasubid=?, tbejerciciosubzonanombre=? WHERE tbejerciciosubzonaid=?";
        $stmt = mysqli_prepare($conn, $query);

        $jercicio = $ejercicioSubzona->getEjercicio();
        $subZona = $ejercicioSubzona->getSubZona();
        $nombre = $ejercicioSubzona->getNombre();
        $id = $ejercicioSubzona->getId();

        mysqli_stmt_bind_param($stmt, "issi", $jercicio, $subZona, $nombre, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBEjercicioSubZona($ejercicio, $nombre)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryDelete = "DELETE FROM tbejerciciosubzona WHERE tbejerciciosubzonaejercicioid = ? and tbejerciciosubzonanombre = ?;";
        $stmt = mysqli_prepare($conn, $queryDelete);

        mysqli_stmt_bind_param($stmt, 'is', $ejercicio, $nombre);
        $result = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return $result;
    }

    public function getEjercicioSubzonaPorEjercicioNombre($ejercicio, $nombre)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbejerciciosubzona WHERE tbejerciciosubzonaejercicioid = ? and tbejerciciosubzonanombre = ?;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $ejercicio, $nombre);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $ejercicioSubzona = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $ejercicioSubzona = new ejercicioSubzona(
                $row['tbejerciciosubzonaid'],
                $row['tbejerciciosubzonaejercicioid'],
                $row['tbejerciciosubzonasubid'],
                $row['tbejerciciosubzonanombre']
            );
        }

        mysqli_close($conn);
        return $ejercicioSubzona;
    }

    public function getSubzonasPorEjercicio($idEjercicio, $tipo)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT * FROM tbejerciciosubzona 
              WHERE tbejerciciosubzonaejercicioid = ? 
              AND tbejerciciosubzonanombre = ?;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $idEjercicio, $tipo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $subzonas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $subzonas[] = new ejercicioSubzona(
                $row['tbejerciciosubzonaid'],
                $row['tbejerciciosubzonaejercicioid'],
                $row['tbejerciciosubzonasubid'],
                $row['tbejerciciosubzonanombre']
            );
        }

        mysqli_close($conn);
        return $subzonas;
    }

}