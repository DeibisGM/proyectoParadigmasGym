<?php

include_once '../data/cuerpoZonaData.php';
include_once '../utility/ImageManager.php';

class CuerpoZonaBusiness
{
    private $cuerpoZonaData;
    private $imageManager;

    public function __construct()
    {
        $this->cuerpoZonaData = new CuerpoZonaData();
        $this->imageManager = new ImageManager();
    }

    public function getCuerpoZonaById($id)
    {
        return $this->cuerpoZonaData->getCuerpoZonaById($id);
    }

    public function existeCuerpoZonaNombre($nombreCuerpoZona)
    {
        return $this->cuerpoZonaData->existeCuerpoZonaNombre($nombreCuerpoZona);
    }

    public function insertarTBCuerpoZona($cuerpoZona)
    {
        if ($this->existeCuerpoZonaNombre($cuerpoZona->getNombreCuerpoZona())) {
            return -1;
        }
        return $this->cuerpoZonaData->insertarTBCuerpoZona($cuerpoZona);
    }

    public function actualizarTBCuerpoZona($cuerpoZona)
    {
        return $this->cuerpoZonaData->actualizarTBCuerpoZona($cuerpoZona);
    }

    public function actualizarEstadoTBCuerpoZona($idCuerpoZona, $estado)
    {
        return $this->cuerpoZonaData->actualizarEstadoTBCuerpoZona($idCuerpoZona, $estado);
    }

    public function eliminarTBCuerpoZona($id)
    {
        $zona = $this->cuerpoZonaData->getCuerpoZonaById($id);
        if ($zona) {
            $this->imageManager->deleteImagesFromString($zona->getImagenesIds());
            return $this->cuerpoZonaData->eliminarTBCuerpoZona($id);
        }
        return false;
    }

    public function getAllTBCuerpoZona()
    {
        return $this->cuerpoZonaData->getAllTBCuerpoZona();
    }

    public function getActiveTBCuerpoZona()
    {
        return $this->cuerpoZonaData->getActiveTBCuerpoZona();
    }
}

?>