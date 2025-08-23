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
                $row['tbinstructorid']
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
                $row['tbinstructorid']
            );
        }
        return $certificados;
    }

    public function addCertificado($certificado) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $nombre = mysqli_real_escape_string($conn, $certificado->getNombre());
        $descripcion = mysqli_real_escape_string($conn, $certificado->getDescripcion());
        $entidad = mysqli_real_escape_string($conn, $certificado->getEntidad());
        $idInstructor = mysqli_real_escape_string($conn, $certificado->getIdInstructor());

        $query = "INSERT INTO tbcertificado (tbcertificadonombre, tbcertificadodescripcion, tbcertificadoentidad, tbinstructorid) VALUES ('$nombre', '$descripcion', '$entidad', '$idInstructor')";
        $result = mysqli_query($conn, $query);
        mysqli_close($conn);
        return $result;
    }

    public function updateCertificado($certificado) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $id = mysqli_real_escape_string($conn, $certificado->getId());
        $nombre = mysqli_real_escape_string($conn, $certificado->getNombre());
        $descripcion = mysqli_real_escape_string($conn, $certificado->getDescripcion());
        $entidad = mysqli_real_escape_string($conn, $certificado->getEntidad());
        $idInstructor = mysqli_real_escape_string($conn, $certificado->getIdInstructor());

        $query = "UPDATE tbcertificado SET tbcertificadonombre='$nombre', tbcertificadodescripcion='$descripcion', tbcertificadoentidad='$entidad', tbinstructorid='$idInstructor' WHERE tbcertificadoid='$id'";
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