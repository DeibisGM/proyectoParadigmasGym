<?php
include '../data/subZonaData.php';
include_once '../utility/ImageManager.php';

class subZonaBusiness
{

    private $imageManager;
    private $parteZonaData;

    public function __construct()
    {
        $this->imageManager = new ImageManager();
        $this->parteZonaData = new subZonaData();
    }

    public function insertarTBSubZona($partezona) {
        return $this->parteZonaData->insertarTBSubZona($partezona);
    }

    public function actualizarTBSubZona($partezona) {
        return $this->parteZonaData->actualizarTBSubZona($partezona);
    }

    public function eliminarTBSubZona($id) {
        return $this->parteZonaData->eliminarTBSubZona($id);
    }

    public function getAllTBSubZona() {
        return $this->parteZonaData->getAllTBSubZona();
    }

    public function getAllTBSubZonaPorId($parteLista) {
        return $this->parteZonaData->getAllTBSubZonaPorId($parteLista);
    }

    public function existeSubZonaNombre($nombre) {
        return $this->parteZonaData->existeSubZonaNombre($nombre);
    }

    public function getSubZonaPorId($id) {
        return $this->parteZonaData->getSubZonaPorId($id);
    }

    public function desactivarSubZonaLista($lista) {
        return $this->parteZonaData->desactivarSubZonaLista($lista);
    }

}