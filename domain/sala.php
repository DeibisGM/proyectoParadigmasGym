<?php

class Sala{
    private $tbsalaid;
    private $tbsalanombre;
    private $tbsalacapacidad;
    private $tbsalaactivo;

    public function __construct($tbsalaid, $tbsalanombre, $tbsalacapacidad, $tbsalaactivo){
        $this->tbsalaid = $tbsalaid;
        $this->tbsalanombre = $tbsalanombre;
        $this->tbsalacapacidad = $tbsalacapacidad;
        $this->tbsalaactivo = $tbsalaactivo;
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
        return $this->tbsalaactivo;
    }

    public function setTbsalaestado($tbsalaactivo){
        $this->tbsalaactivo = $tbsalaactivo;
    }

}
?>