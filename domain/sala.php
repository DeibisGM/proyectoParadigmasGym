<?php

class Sala{
    private $tbsalaid;
    private $tbsalanombre;
    private $tbsalacapacidad;
    private $tbsalaestado;

    public function __construct($tbsalaid, $tbsalanombre, $tbsalacapacidad, $tbsalaestado){
        $this->tbsalaid = $tbsalaid;
        $this->tbsalanombre = $tbsalanombre;
        $this->tbsalacapacidad = $tbsalacapacidad;
        $this->tbsalaestado = $tbsalaestado;
    }

    public function getTbsalaid(){
        return $this->tbsalaid;
    }

    public function setTbsalaid($tbsalaid){
        $this->tbsalaid = $tbsalaid;
    }

    public function getTbsalanombre(){
        return $this->tbsalanombre;
    }

    public function setTbsalanombre($tbsalanombre){
        $this->tbsalanombre = $tbsalanombre;
    }

    public function getTbsalacapacidad(){
        return $this->tbsalacapacidad;
    }

    public function setTbsalacapacidad($tbsalacapacidad){
        $this->tbsalacapacidad = $tbsalacapacidad;
    }

    public function getTbsalaestado(){
        return $this->tbsalaestado;
    }

    public function setTbsalaestado($tbsalaestado){
        $this->tbsalaestado = $tbsalaestado;
    }

}
?>