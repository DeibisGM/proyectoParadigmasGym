<?php

class ZonaCuerpo {

    private $idZonaCuerpo;
    private $nombreZonaCuerpo;
    private $descripcionZonaCuerpo;
    private $activoZonaCuerpo;

    public function __construct($idZonaCuerpo, $nombreZonaCuerpo, $descripcionZonaCuerpo, $activoZonaCuerpo) {
        $this->idZonaCuerpo = $idZonaCuerpo;
        $this->nombreZonaCuerpo = $nombreZonaCuerpo;
        $this->descripcionZonaCuerpo = $descripcionZonaCuerpo;
        $this->activoZonaCuerpo = $activoZonaCuerpo;
    }

    public function getIdZonaCuerpo() {
        return $this->idZonaCuerpo;
    }

    public function getNombreZonaCuerpo() {
        return $this->nombreZonaCuerpo;
    }

    public function getDescripcionZonaCuerpo() {
        return $this->descripcionZonaCuerpo;
    }

    public function getActivoZonaCuerpo() {
        return $this->activoZonaCuerpo;
    }
}
?>