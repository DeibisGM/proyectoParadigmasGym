<?php

class CuerpoZona
{

    private $idCuerpoZona;
    private $nombreCuerpoZona;
    private $descripcionCuerpoZona;
    private $activoCuerpoZona;
    private $imagenesIds;

    public function __construct($idCuerpoZona, $nombreCuerpoZona, $descripcionCuerpoZona, $activoCuerpoZona, $imagenesIds = '')
    {
        $this->idCuerpoZona = $idCuerpoZona;
        $this->nombreCuerpoZona = $nombreCuerpoZona;
        $this->descripcionCuerpoZona = $descripcionCuerpoZona;
        $this->activoCuerpoZona = $activoCuerpoZona;
        $this->imagenesIds = $imagenesIds;
    }

    public function getIdCuerpoZona()
    {
        return $this->idCuerpoZona;
    }

    public function getNombreCuerpoZona()
    {
        return $this->nombreCuerpoZona;
    }

    public function getDescripcionCuerpoZona()
    {
        return $this->descripcionCuerpoZona;
    }

    public function getActivoCuerpoZona()
    {
        return $this->activoCuerpoZona;
    }

    public function getImagenesIds()
    {
        return $this->imagenesIds;
    }

    public function setImagenesIds($ids)
    {
        $this->imagenesIds = $ids;
    }

    public function setNombreCuerpoZona($nombre)
    {
        $this->nombreCuerpoZona = $nombre;
    }

    public function setDescripcionCuerpoZona($descripcion)
    {
        $this->descripcionCuerpoZona = $descripcion;
    }

    public function setActivoCuerpoZona($activo)
    {
        $this->activoCuerpoZona = $activo;
    }
}

?>