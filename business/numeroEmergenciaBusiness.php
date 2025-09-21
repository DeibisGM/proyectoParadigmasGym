<?php
include_once '../data/numeroEmergenciaData.php';
include_once '../domain/numeroEmergencia.php';

class numeroEmergenciaBusiness
{
    private $numeroEmergenciaData;

    public function __construct() {
        $this->numeroEmergenciaData = new numeroEmergenciaData();
    }

    public function insertarTBNumeroEmergencia($numeroEmergencia) {
        return $this->numeroEmergenciaData->insertarTBNumeroEmergencia($numeroEmergencia);
    }

    public function actualizarTBNumeroEmergencia($numeroEmergencia) {
        return $this->numeroEmergenciaData->actualizarTBNumeroEmergencia($numeroEmergencia);
    }

    public function eliminarTBNumeroEmergencia($numeroEmergenciaId) {
        return $this->numeroEmergenciaData->eliminarTBNumeroEmergencia($numeroEmergenciaId);
    }

    public function getAllTBNumeroEmergencia() {
        return $this->numeroEmergenciaData->getAllTBNumeroEmergencia();
    }

    public function getAllTBNumeroEmergenciaByClienteId($numeroEmergenciaClienteId) {
        return $this->numeroEmergenciaData->getAllTBNumeroEmergenciaByClienteId($numeroEmergenciaClienteId);
    }

    public function existeNumeroEmergencia($clienteId, $telefono) {
        return $this->numeroEmergenciaData->existeNumeroEmergencia($clienteId, $telefono);
    }

    public function getNumeroPorId($id) {
        return $this->numeroEmergenciaData->getNumeroPorId($id);
    }

}