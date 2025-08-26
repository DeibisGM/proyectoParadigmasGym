<?php

class Padecimiento {
    private $tbpadecimientoid;
    private $tbpadecimientotipo;
    private $tbpadecimientonombre;
    private $tbpadecimientodescripcion;
    private $tbpadecimientoformadeactuar;

    public function __construct($tbpadecimientoid, $tbpadecimientotipo, $tbpadecimientonombre,
                               $tbpadecimientodescripcion, $tbpadecimientoformadeactuar) {
        $this->tbpadecimientoid = $tbpadecimientoid;
        $this->tbpadecimientotipo = $tbpadecimientotipo;
        $this->tbpadecimientonombre = $tbpadecimientonombre;
        $this->tbpadecimientodescripcion = $tbpadecimientodescripcion;
        $this->tbpadecimientoformadeactuar = $tbpadecimientoformadeactuar;
    }

    public function getTbpadecimientoid() {
        return $this->tbpadecimientoid;
    }

    public function getTbpadecimientotipo() {
        return $this->tbpadecimientotipo;
    }

    public function getTbpadecimientonombre() {
        return $this->tbpadecimientonombre;
    }

    public function getTbpadecimientodescripcion() {
        return $this->tbpadecimientodescripcion;
    }

    public function getTbpadecimientoformadeactuar() {
        return $this->tbpadecimientoformadeactuar;
    }

    public function setTbpadecimientoid($tbpadecimientoid) {
        $this->tbpadecimientoid = $tbpadecimientoid;
    }

    public function setTbpadecimientotipo($tbpadecimientotipo) {
        $this->tbpadecimientotipo = $tbpadecimientotipo;
    }

    public function setTbpadecimientonombre($tbpadecimientonombre) {
        $this->tbpadecimientonombre = $tbpadecimientonombre;
    }

    public function setTbpadecimientodescripcion($tbpadecimientodescripcion) {
        $this->tbpadecimientodescripcion = $tbpadecimientodescripcion;
    }

    public function setTbpadecimientoformadeactuar($tbpadecimientoformadeactuar) {
        $this->tbpadecimientoformadeactuar = $tbpadecimientoformadeactuar;
    }
}
?>