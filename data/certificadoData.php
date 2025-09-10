<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'data.php';
include_once '../domain/certificado.php';

class CertificadoData extends Data {

    public function getCertificados() {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbcertificado";
        $result = mysqli_query($conn, $query);
        mysqli_close($conn);

        $certificados = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $certificados[] = new Certificado(
                $row['tbcertificadoid'],
                $row['tbcertificadonombre'],
                $row['tbcertificadodescripcion'],
                $row['tbcertificadoentidad'],
                $row['tbinstructorid'],
                isset($row['tbcertificadoimagenid']) ? $row['tbcertificadoimagenid'] : ''
            );
        }
        return $certificados;
    }

    public function getCertificadosPorInstructor($idInstructor) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $idInstructor = mysqli_real_escape_string($conn, $idInstructor);
        $query = "SELECT * FROM tbcertificado WHERE tbinstructorid = '$idInstructor'";
        $result = mysqli_query($conn, $query);
        mysqli_close($conn);

        $certificados = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $certificados[] = new Certificado(
                $row['tbcertificadoid'],
                $row['tbcertificadonombre'],
                $row['tbcertificadodescripcion'],
                $row['tbcertificadoentidad'],
                $row['tbinstructorid'],
                isset($row['tbcertificadoimagenid']) ? $row['tbcertificadoimagenid'] : ''
            );
        }
        return $certificados;
    }

    public function getCertificadoPorId($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $id = mysqli_real_escape_string($conn, $id);
        $query = "SELECT * FROM tbcertificado WHERE tbcertificadoid='$id' LIMIT 1;";
        $result = mysqli_query($conn, $query);

        $certificado = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $certificado = new Certificado(
                $row['tbcertificadoid'],
                $row['tbcertificadonombre'],
                $row['tbcertificadodescripcion'],
                $row['tbcertificadoentidad'],
                $row['tbinstructorid'],
                isset($row['tbcertificadoimagenid']) ? $row['tbcertificadoimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $certificado;
    }

    public function addCertificado($certificado) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $imagenId = $certificado->getTbcertificadoImagenId();
        $imagenValue = ($imagenId === '' || $imagenId === null) ? "NULL" : "'" . mysqli_real_escape_string($conn, $imagenId) . "'";

        $nombre = mysqli_real_escape_string($conn, $certificado->getNombre());
        $descripcion = mysqli_real_escape_string($conn, $certificado->getDescripcion());
        $entidad = mysqli_real_escape_string($conn, $certificado->getEntidad());
        $idInstructor = mysqli_real_escape_string($conn, $certificado->getIdInstructor());

        $query = "INSERT INTO tbcertificado (tbcertificadonombre, tbcertificadodescripcion, tbcertificadoentidad, tbinstructorid, tbcertificadoimagenid) VALUES ('$nombre', '$descripcion', '$entidad', '$idInstructor', $imagenValue)";
        $result = mysqli_query($conn, $query);

        // Obtener el ID del certificado recién insertado
        $newId = null;
        if ($result) {
            $newId = mysqli_insert_id($conn);
        }

        mysqli_close($conn);
        return $newId;
    }

    public function updateCertificado($certificado) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $id = mysqli_real_escape_string($conn, $certificado->getId());
        $nombre = mysqli_real_escape_string($conn, $certificado->getNombre());
        $descripcion = mysqli_real_escape_string($conn, $certificado->getDescripcion());
        $entidad = mysqli_real_escape_string($conn, $certificado->getEntidad());
        $idInstructor = mysqli_real_escape_string($conn, $certificado->getIdInstructor());
        $imagenId = mysqli_real_escape_string($conn, $certificado->getTbcertificadoImagenId());

        $query = "UPDATE tbcertificado SET
            tbcertificadonombre='$nombre',
            tbcertificadodescripcion='$descripcion',
            tbcertificadoentidad='$entidad',
            tbinstructorid='$idInstructor',
            tbcertificadoimagenid='$imagenId'
            WHERE tbcertificadoid='$id'";

        $result = mysqli_query($conn, $query);
        mysqli_close($conn);
        return $result;
    }

    public function deleteCertificado($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $id = mysqli_real_escape_string($conn, $id);
        $query = "DELETE FROM tbcertificado WHERE tbcertificadoid='$id'";
        $result = mysqli_query($conn, $query);
        mysqli_close($conn);
        return $result;
    }
}
?>