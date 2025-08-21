<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once '../data/certificadoData.php';

class CertificadoBusiness {

    private $certificadoData;

    public function __construct() {
        $this->certificadoData = new CertificadoData();
    }

    public function getCertificados() {
        return $this->certificadoData->getCertificados();
    }

    public function getCertificadosPorInstructor($idInstructor) {
        return $this->certificadoData->getCertificadosPorInstructor($idInstructor);
    }

    public function addCertificado($certificado) {
        return $this->certificadoData->addCertificado($certificado);
    }

    public function updateCertificado($certificado) {
        return $this->certificadoData->updateCertificado($certificado);
    }

    public function deleteCertificado($id) {
        return $this->certificadoData->deleteCertificado($id);
    }
}
?>