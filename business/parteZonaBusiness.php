<?php
include '../data/parteZonaData.php';

class parteZonaBusiness
{

    private $imageManager;
    private $parteZonaData;

    public function __construct()
    {
        $this->imageManager = new ImageManager();
        $this->parteZonaData = new parteZonaData();
    }

    public function insertarTBParteZona($partezona) {
        return $this->parteZonaData->insertarTBParteZona($partezona);
    }

    public function actualizarTBParteZona($partezona) {
        return $this->parteZonaData->actualizarTBParteZona($partezona);
    }

    public function eliminarTBParteZona($id) {
        return $this->parteZonaData->eliminarTBParteZona($id);
    }

    public function getAllTBParteZona() {
        return $this->parteZonaData->getAllTBParteZona();
    }

    public function getAllTBParteZonaPorId($parteLista) {
        return $this->parteZonaData->getAllTBParteZonaPorId($parteLista);
    }

    public function existeParteZonaNombre($nombre) {
        return $this->parteZonaData->existeParteZonaNombre($nombre);
    }

    public function getParteZonaPorId($id) {
        return $this->parteZonaData->getParteZonaPorId($id);
    }

    public function desactivarParteZonaLista($lista) {
        return $this->parteZonaData->desactivarParteZonaLista($lista);
    }

}