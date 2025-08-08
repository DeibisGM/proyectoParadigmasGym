<?php

include '../data/instructorData.php';

class InstructorBusiness {

    private $instructorData;

    public function __construct() {
        $this->instructorData = new InstructorData();
    }

    public function insertarTBInstructor($instructor) {
        return $this->instructorData->insertarTBInstructor($instructor);
    }

    public function actualizarTBInstructor($instructor) {
        return $this->instructorData->actualizarTBInstructor($instructor);
    }

    public function eliminarTBInstructor($idInstructor) {
        return $this->instructorData->eliminarTBInstructor($idInstructor);
    }

    public function getAllTBInstructor() {
        return $this->instructorData->getAllTBInstructor();
    }
    
    public function autenticarInstructor($correo, $cuenta) {
        return $this->instructorData->autenticarInstructor($correo, $cuenta);
    }
    
    public function getInstructorPorId($id) {
        return $this->instructorData->getInstructorPorId($id);
    }
}
?>