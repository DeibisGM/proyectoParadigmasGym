<?php
include '../data/ejercicioFlexibilidadData.php';

class ejercicioFlexibilidadBusiness
{

    private $ejercicioFlexibilidadData;

    public function __construct() {
        $this->ejercicioFlexibilidadData = new ejercicioFlexibilidadData();
    }

    public function insertarTBEjercicioFlexibilidad($ejercicio) {
        return $this->ejercicioFlexibilidadData->insertarTBEjercicioFlexibilidad($ejercicio);
    }

    public function existeEjercicioPorNombre($nombre) {
        return $this->ejercicioFlexibilidadData->existeEjercicioPorNombre($nombre);
    }

    public function actualizarTBEjercicioFlexibilidad($ejercicio) {
        return $this->ejercicioFlexibilidadData->actualizarTBEjercicioFlexibilidad($ejercicio);
    }

    public function eliminarTBEjercicioFlexibilidad($ejercicio) {
        return $this->ejercicioFlexibilidadData->eliminarTBEjercicioFlexibilidad($ejercicio);
    }

    public function getAllTBEjercicioFlexibilidad() {
        return $this->ejercicioFlexibilidadData->getAllTBEjercicioFlexibilidad();
    }

    public function getTBEjercicioFlexibilidadByActivo() {
        return $this->ejercicioFlexibilidadData->getTBEjercicioFlexibilidadByActivo();
    }

    public function getEjercicioFlexibilidad($ejercicio) {
        return $this->ejercicioFlexibilidadData->getEjercicioFlexibilidad($ejercicio);
    }

}