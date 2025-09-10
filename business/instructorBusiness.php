<?php

include '../data/instructorData.php';
include_once '../utility/ImageManager.php';

class InstructorBusiness
{

    private $instructorData;
    private $imageManager;

    public function __construct()
    {
        $this->instructorData = new InstructorData();
        $this->imageManager = new ImageManager();
    }

    public function insertarTBInstructor($instructor)
    {
        return $this->instructorData->insertarTBInstructor($instructor);
    }

    public function actualizarTBInstructor($instructor)
    {
        return $this->instructorData->actualizarTBInstructor($instructor);
    }

      public function eliminarTBInstructor($idInstructor)
        {
            $instructor = $this->instructorData->getInstructorPorId($idInstructor);
            if ($instructor && $instructor->getTbinstructorImagenId() != '' && $instructor->getTbinstructorImagenId() != '0') {
                $this->imageManager->deleteImage($instructor->getTbinstructorImagenId());
            }
            return $this->instructorData->eliminarTBInstructor($idInstructor);
        }

    public function activarTBInstructor($idInstructor)
    {
        return $this->instructorData->activarTBInstructor($idInstructor);
    }

    public function getAllTBInstructor($esAdmin = false)
    {
        return $this->instructorData->getAllTBInstructor($esAdmin);
    }

    public function autenticarInstructor($correo, $cuenta)
    {
        return $this->instructorData->autenticarInstructor($correo, $cuenta);
    }

    public function getInstructorPorId($id)
    {
        return $this->instructorData->getInstructorPorId($id);
    }

    public function existeInstructorPorCorreo($correo)
    {
        return $this->instructorData->existeInstructorPorCorreo($correo);
    }

    public function actualizarCertificadosInstructor($idInstructor, $certificadosStr)
    {
        return $this->instructorData->actualizarCertificadosInstructor($idInstructor, $certificadosStr);
    }

}

?>