<?php

class EjercicioFuerza{
    private $tbejerciciofuerzaid;
    private $tbejerciciofuerzanombre;
    private $tbejerciciofuerzadescripcion;
    private $tbejerciciofuerzarepeticion;
    private $tbejerciciofuerzaserie;
    private $tbejerciciofuerzapeso;
    private $tbejerciciofuerzadescanso;

    public function __construct($tbejerciciofuerzaid, $tbejerciciofuerzanombre, $tbejerciciofuerzadescripcion,
                                $tbejerciciofuerzarepeticion, $tbejerciciofuerzaserie, $tbejerciciofuerzapeso,
                                $tbejerciciofuerzadescanso){
        $this->tbejerciciofuerzaid = $tbejerciciofuerzaid;
        $this->tbejerciciofuerzanombre = $tbejerciciofuerzanombre;
        $this->tbejerciciofuerzadescripcion = $tbejerciciofuerzadescripcion;
        $this->tbejerciciofuerzarepeticion = $tbejerciciofuerzarepeticion;
        $this->tbejerciciofuerzaserie = $tbejerciciofuerzaserie;
        $this->tbejerciciofuerzapeso = $tbejerciciofuerzapeso;
        $this->tbejerciciofuerzadescanso = $tbejerciciofuerzadescanso;
    }

    public function getTbejerciciofuerzaid(){
        return $this->tbejerciciofuerzaid;
    }

    public function setTbejerciciofuerzaid($tbejerciciofuerzaid){
        $this->tbejerciciofuerzaid = $tbejerciciofuerzaid;
    }

    public function getTbejerciciofuerzanombre(){
        return $this->tbejerciciofuerzanombre;
    }

    public function setTbejerciciofuerzanombre($tbejerciciofuerzanombre){
        $this->tbejerciciofuerzanombre = $tbejerciciofuerzanombre;
    }

    public function getTbejerciciofuerzadescripcion(){
        return $this->tbejerciciofuerzadescripcion;
    }

    public function setTbejerciciofuerzadescripcion($tbejerciciofuerzadescripcion){
        $this->tbejerciciofuerzadescripcion = $tbejerciciofuerzadescripcion;
    }

    public function getTbejerciciofuerzarepeticion(){
        return $this->tbejerciciofuerzarepeticion;
    }

    public function setTbejerciciofuerzarepeticion($tbejerciciofuerzarepeticion){
        $this->tbejerciciofuerzarepeticion = $tbejerciciofuerzarepeticion;
    }

    public function getTbejerciciofuerzaserie(){
        return $this->tbejerciciofuerzaserie;
    }

    public function setTbejerciciofuerzaserie($tbejerciciofuerzaserie){
        $this->tbejerciciofuerzaserie = $tbejerciciofuerzaserie;
    }

    public function getTbejerciciofuerzapeso(){
        return $this->tbejerciciofuerzapeso;
    }

    public function setTbejerciciofuerzapeso($tbejerciciofuerzapeso){
        $this->tbejerciciofuerzapeso = $tbejerciciofuerzapeso;
    }

    public function getTbejerciciofuerzadescanso(){
        return $this->tbejerciciofuerzadescanso;
    }

    public function setTbejerciciofuerzadescanso($tbejerciciofuerzadescanso){
        $this->tbejerciciofuerzadescanso = $tbejerciciofuerzadescanso;
    }
}
?>