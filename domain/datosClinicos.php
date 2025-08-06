<?php

class DatosClinicos {
    private $idtbdatosclinicos;
    private $tbdatosclinicosenfermedad;
    private $tbdatosclinicosotraenfermedad;
    private $tbdatosclinicostomamedicamento;
    private $tbdatosclinicosmedicamento;
    private $tbdatosclinicoslesion;
    private $tbdatosclinicosdescripcionlesion;
    private $tbdatosclinicosdiscapacidad;
    private $tbdatosclinicosdescripciondiscapacidad;
    private $tbdatosclinicosrestriccionmedica;
    public $tbdatosclinicosdescripcionrestriccionmedica;
    private $tbclientesid;
    public $carnet;

    public function __construct($idtbdatosclinicos, $tbdatosclinicosenfermedad, $tbdatosclinicosotraenfermedad,
                                $tbdatosclinicostomamedicamento, $tbdatosclinicosmedicamento,
                                $tbdatosclinicoslesion, $tbdatosclinicosdescripcionlesion,
                                $tbdatosclinicosdiscapacidad, $tbdatosclinicosdescripciondiscapacidad,
                                $tbdatosclinicosrestriccionmedica, $tbdatosclinicosdescripcionrestriccionmedica,
                                $tbclientesid){

        $this->idtbdatosclinicos = $idtbdatosclinicos;
        $this->tbdatosclinicosenfermedad = $tbdatosclinicosenfermedad;
        $this->tbdatosclinicosotraenfermedad = $tbdatosclinicosotraenfermedad;
        $this->tbdatosclinicostomamedicamento = $tbdatosclinicostomamedicamento;
        $this->tbdatosclinicosmedicamento = $tbdatosclinicosmedicamento;
        $this->tbdatosclinicoslesion = $tbdatosclinicoslesion;
        $this->tbdatosclinicosdescripcionlesion = $tbdatosclinicosdescripcionlesion;
        $this->tbdatosclinicosdiscapacidad = $tbdatosclinicosdiscapacidad;
        $this->tbdatosclinicosdescripciondiscapacidad = $tbdatosclinicosdescripciondiscapacidad;
        $this->tbdatosclinicosrestriccionmedica = $tbdatosclinicosrestriccionmedica;
        $this->tbdatosclinicosdescripcionrestriccionmedica = $tbdatosclinicosdescripcionrestriccionmedica;
        $this->tbclientesid = $tbclientesid;
        $this->carnet = '';
    }

    public function getIdtbdatosclinicos(){ return $this->idtbdatosclinicos; }
    public function getTbdatosclinicosid(){ return $this->idtbdatosclinicos; }
    public function getTbdatosclinicosenfermedad(){ return $this->tbdatosclinicosenfermedad; }
    public function getTbdatosclinicosotraenfermedad(){ return $this->tbdatosclinicosotraenfermedad; }
    public function getTbdatosclinicostomamedicamento(){ return $this->tbdatosclinicostomamedicamento; }
    public function getTbdatosclinicosmedicamento(){ return $this->tbdatosclinicosmedicamento; }
    public function getTbdatosclinicoslesion(){ return $this->tbdatosclinicoslesion; }
    public function getTbdatosclinicosdescripcionlesion(){ return $this->tbdatosclinicosdescripcionlesion; }
    public function getTbdatosclinicosdiscapacidad(){ return $this->tbdatosclinicosdiscapacidad; }
    public function getTbdatosclinicosdescripciondiscapacidad(){ return $this->tbdatosclinicosdescripciondiscapacidad; }
    public function getTbdatosclinicosrestriccionmedica(){ return $this->tbdatosclinicosrestriccionmedica; }
    public function getTbdatosclinicosdescripcionrestriccionmedica(){return $this->tbdatosclinicosdescripcionrestriccionmedica;}
    public function getTbclientesid(){ return $this->tbclientesid; }
    public function getCarnet() { return $this->carnet; }


    public function setIdtbdatosclinicos($idtbdatosclinicos){ $this->idtbdatosclinicos = $idtbdatosclinicos; }
    public function setTbdatosclinicosid($tbdatosclinicosid){ $this->idtbdatosclinicos = $tbdatosclinicosid; }
    public function setTbdatosclinicosenfermedad($tbdatosclinicosenfermedad){ $this->tbdatosclinicosenfermedad = $tbdatosclinicosenfermedad; }
    public function setTbdatosclinicosotraenfermedad($tbdatosclinicosotraenfermedad){ $this->tbdatosclinicosotraenfermedad = $tbdatosclinicosotraenfermedad; }
    public function setTbdatosclinicostomamedicamento($tbdatosclinicostomamedicamento){ $this->tbdatosclinicostomamedicamento = $tbdatosclinicostomamedicamento; }
    public function setTbdatosclinicosmedicamento($tbdatosclinicosmedicamento){ $this->tbdatosclinicosmedicamento = $tbdatosclinicosmedicamento; }
    public function setTbdatosclinicoslesion($tbdatosclinicoslesion){ $this->tbdatosclinicoslesion = $tbdatosclinicoslesion; }
    public function setTbdatosclinicosdescripcionlesion($tbdatosclinicosdescripcionlesion){ $this->tbdatosclinicosdescripcionlesion = $tbdatosclinicosdescripcionlesion; }
    public function setTbdatosclinicosdiscapacidad($tbdatosclinicosdiscapacidad){ $this->tbdatosclinicosdiscapacidad = $tbdatosclinicosdiscapacidad; }
    public function setTbdatosclinicosdescripciondiscapacidad($tbdatosclinicosdescripciondiscapacidad){ $this->tbdatosclinicosdescripciondiscapacidad = $tbdatosclinicosdescripciondiscapacidad; }
    public function setTbdatosclinicosrestriccionmedica($tbdatosclinicosrestriccionmedica){ $this->tbdatosclinicosrestriccionmedica = $tbdatosclinicosrestriccionmedica; }
    public function setTbdatosclinicosdescripcionrestriccionmedica($tbdatosclinicosdescripcionrestriccionmedica){$this->tbdatosclinicosdescripcionrestriccionmedica = $tbdatosclinicosdescripcionrestriccionmedica;}
    public function setTbclientesid($tbclientesid){ $this->tbclientesid = $tbclientesid; }
    public function setCarnet($carnet) { $this->carnet = $carnet; }
}
?>