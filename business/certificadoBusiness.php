<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once '../data/certificadoData.php';
include_once '../utility/ImageManager.php';

class CertificadoBusiness {

    private $certificadoData;
    private $imageManager;

    public function __construct() {
        $this->certificadoData = new CertificadoData();
        $this->imageManager = new ImageManager();
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
        // Primero obtener el certificado para eliminar su imagen si existe
        $certificado = $this->getCertificadoPorId($id);
        if ($certificado && $certificado->getTbcertificadoImagenId() != '' && $certificado->getTbcertificadoImagenId() != '0') {
            $this->imageManager->deleteImage($certificado->getTbcertificadoImagenId());
        }

        return $this->certificadoData->deleteCertificado($id);
    }

    public function getCertificadoPorId($id) {
     return $this->certificadoData->getCertificadoPorId($id);
    }
}
?>