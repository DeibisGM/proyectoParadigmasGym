<?php

include '../data/cuerpoZonaData.php';

class CuerpoZonaBusiness
{

    private $cuerpoZonaData;

    public function __construct()
    {
        $this->cuerpoZonaData = new CuerpoZonaData();
    }

    public function existeCuerpoZonaNombre($nombreCuerpoZona)
    {
        return $this->cuerpoZonaData->existeCuerpoZonaNombre($nombreCuerpoZona);
    }

    public function insertarTBCuerpoZona($cuerpoZona)
    {
        // Verificar si ya existe una zona con el mismo nombre
        if ($this->existeCuerpoZonaNombre($cuerpoZona->getNombreCuerpoZona())) {
            return -1;
        }
        // Si la inserción es exitosa, la capa de datos devolverá el nuevo ID.
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