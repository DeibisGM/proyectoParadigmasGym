<?php
include '../data/ejercicioResistenciaData.php';

class ejercicioResistenciaBusiness
{

    private $ejercicioResistenciaData;

    public function __construct() {
        $this->ejercicioResistenciaData = new ejercicioResistenciaData();
    }

    public function insertarTBEjercicioResistencia($ejercicio) {
        return $this->ejercicioResistenciaData->insertarTBEjercicioResistencia($ejercicio);
    }

    public function existeEjercicioPorNombre($nombre) {
        return $this->ejercicioResistenciaData->existeEjercicioPorNombre($nombre);
    }

    public function actualizarTBEjercicioResistencia($ejercicio) {
        return $this->ejercicioResistenciaData->actualizarTBEjercicioResistencia($ejercicio);
    }

    public function eliminarTBEjercicioResistencia($ejercicio) {
        return $this->ejercicioResistenciaData->eliminarTBEjercicioResistencia($ejercicio);
    }

    public function getAllTBEjercicioResistecia() {
        return $this->ejercicioResistenciaData->getAllTBEjercicioResistecia();
    }

    public function getTBEjercicioResisteciaByActivo() {
        return $this->ejercicioResistenciaData->getTBEjercicioResisteciaByActivo();
    }

    public function getEjercicioResistencia($ejercicio) {
        return $this->ejercicioResistenciaData->getEjercicioResistencia($ejercicio);
    }

}