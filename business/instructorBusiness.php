<?php

include '../data/instructorData.php';

class InstructorBusiness
{

    private $instructorData;

    public function __construct()
    {
        $this->instructorData = new InstructorData();
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