<?php
include_once '../data/instructorData.php';
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
        $instructor = $this->getInstructorPorId($idInstructor);
        // Si el instructor existe y tiene una imagen, se elimina
        if ($instructor && $instructor->getTbinstructorImagenId() != '' && $instructor->getTbinstructorImagenId() != '0') {
            $this->imageManager->deleteImage($instructor->getTbinstructorImagenId());
            // Se actualiza el instructor para que no tenga referencia a la imagen eliminada
            $instructor->setTbinstructorImagenId('');
            $this->actualizarTBInstructor($instructor);
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

    public function autenticarInstructor($correo, $contrasena)
    {
        return $this->instructorData->autenticarInstructor($correo, $contrasena);
    }

    public function getInstructorPorId($id)
    {
        return $this->instructorData->getInstructorPorId($id);
    }

    public function existeInstructorPorCorreo($correo)
    {
        // Esta es la línea que causaba el error. Ahora funcionará.
        return $this->instructorData->existeInstructorPorCorreo($correo);
    }

    public function actualizarCertificadosInstructor($idInstructor, $certificadosStr)
    {
        // Este método no estaba definido, lo agrego por si se necesita en el futuro
        $instructor = $this->getInstructorPorId($idInstructor);
        if ($instructor) {
            $instructor->setInstructorCertificado($certificadosStr);
            return $this->actualizarTBInstructor($instructor);
        }
        return false;
    }
}

?>