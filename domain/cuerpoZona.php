<?php

class CuerpoZona {

    private $idCuerpoZona;
    private $nombreCuerpoZona;
    private $descripcionCuerpoZona;
    private $activoCuerpoZona;

    public function __construct($idCuerpoZona, $nombreCuerpoZona, $descripcionCuerpoZona, $activoCuerpoZona) {
        $this->idCuerpoZona = $idCuerpoZona;
        $this->nombreCuerpoZona = $nombreCuerpoZona;
        $this->descripcionCuerpoZona = $descripcionCuerpoZona;
        $this->activoCuerpoZona = $activoCuerpoZona;
    }

    public function getIdCuerpoZona() {
        return $this->idCuerpoZona;
    }

    public function getNombreCuerpoZona() {
        return $this->nombreCuerpoZona;
    }

    public function getDescripcionCuerpoZona() {
        return $this->descripcionCuerpoZona;
    }

    public function getActivoCuerpoZona() {
        return $this->activoCuerpoZona;
    }
}
?>