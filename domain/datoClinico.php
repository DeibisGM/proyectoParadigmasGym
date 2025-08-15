<?php

class DatoClinico {
    private $tbdatoclinicoid;
    private $tbclienteid;
    private $tbdatoclinicoenfermedad;
    private $tbdatoclinicoenfermedaddescripcion;
    private $tbdatoclinicomedicamento;
    private $tbdatoclinicomedicamentodescripcion;
    private $tbdatoclinicolesion;
    private $tbdatoclinicolesiondescripcion;
    private $tbdatoclinicodiscapacidad;
    private $tbdatoclinicodiscapacidaddescripcion;
    private $tbdatoclinicorestriccionmedica;
    private $tbdatoclinicorestriccionmedicadescripcion;

    private $carnet;

    public function __construct($tbdatoclinicoid, $tbclienteid, $tbdatoclinicoenfermedad,
                                $tbdatoclinicoenfermedaddescripcion, $tbdatoclinicomedicamento,
                                $tbdatoclinicomedicamentodescripcion, $tbdatoclinicolesion,
                                $tbdatoclinicolesiondescripcion, $tbdatoclinicodiscapacidad,
                                $tbdatoclinicodiscapacidaddescripcion, $tbdatoclinicorestriccionmedica,
                                 $tbdatoclinicorestriccionmedicadescripcion){

        $this->tbdatoclinicoid = $tbdatoclinicoid;
        $this->tbclienteid = $tbclienteid;
        $this->tbdatoclinicoenfermedad = $tbdatoclinicoenfermedad;
        $this->tbdatoclinicoenfermedaddescripcion = $tbdatoclinicoenfermedaddescripcion;
        $this->tbdatoclinicomedicamento = $tbdatoclinicomedicamento;
        $this->tbdatoclinicomedicamentodescripcion = $tbdatoclinicomedicamentodescripcion;
        $this->tbdatoclinicolesion = $tbdatoclinicolesion;
        $this->tbdatoclinicolesiondescripcion = $tbdatoclinicolesiondescripcion;
        $this->tbdatoclinicodiscapacidad = $tbdatoclinicodiscapacidad;
        $this->tbdatoclinicodiscapacidaddescripcion = $tbdatoclinicodiscapacidaddescripcion;
        $this->tbdatoclinicorestriccionmedica = $tbdatoclinicorestriccionmedica;
        $this->tbdatoclinicorestriccionmedicadescripcion = $tbdatoclinicorestriccionmedicadescripcion;
        $this->carnet = '';
    }

    public function getTbdatoclinicoid(){ return $this->tbdatoclinicoid; }
    public function getTbclienteid(){ return $this->tbclienteid; }
    public function getTbdatoclinicoenfermedad(){ return $this->tbdatoclinicoenfermedad; }
    public function getTbdatoclinicoenfermedaddescripcion(){ return $this->tbdatoclinicoenfermedaddescripcion; }
    public function getTbdatoclinicomedicamento(){ return $this->tbdatoclinicomedicamento; }
    public function getTbdatoclinicomedicamentodescripcion(){ return $this->tbdatoclinicomedicamentodescripcion; }
    public function getTbdatoclinicolesion(){ return $this->tbdatoclinicolesion; }
    public function getTbdatoclinicolesiondescripcion(){ return $this->tbdatoclinicolesiondescripcion; }
    public function getTbdatoclinicodiscapacidad(){ return $this->tbdatoclinicodiscapacidad; }
    public function getTbdatoclinicodiscapacidaddescripcion(){ return $this->tbdatoclinicodiscapacidaddescripcion; }
    public function getTbdatoclinicorestriccionmedica(){ return $this->tbdatoclinicorestriccionmedica; }
    public function getTbdatoclinicorestriccionmedicadescripcion(){return $this->tbdatoclinicorestriccionmedicadescripcion;}
    public function getCarnet() { return $this->carnet; }

    public function setTbdatoclinicoid($tbdatoclinicoid){ $this->tbdatoclinicoid = $tbdatoclinicoid; }
    public function setTbclienteid($tbclienteid){ $this->tbclienteid = $tbclienteid; }
    public function setTbdatoclinicoenfermedad($tbdatoclinicoenfermedad){ $this->tbdatoclinicoenfermedad = $tbdatoclinicoenfermedad; }
    public function setTbdatoclinicoenfermedaddescripcion($tbdatoclinicoenfermedaddescripcion){ $this->tbdatoclinicoenfermedaddescripcion = $tbdatoclinicoenfermedaddescripcion; }
    public function setTbdatoclinicomedicamento($tbdatoclinicomedicamento){ $this->tbdatoclinicomedicamento = $tbdatoclinicomedicamento; }
    public function setTbdatoclinicomedicamentodescripcion($tbdatoclinicomedicamentodescripcion){ $this->tbdatoclinicomedicamentodescripcion = $tbdatoclinicomedicamentodescripcion; }
    public function setTbdatoclinicolesion($tbdatoclinicolesion){ $this->tbdatoclinicolesion = $tbdatoclinicolesion; }
    public function setTbdatoclinicolesiondescripcion($tbdatoclinicolesiondescripcion){ $this->tbdatoclinicolesiondescripcion = $tbdatoclinicolesiondescripcion; }
    public function setTbdatoclinicodiscapacidad($tbdatoclinicodiscapacidad){ $this->tbdatoclinicodiscapacidad = $tbdatoclinicodiscapacidad; }
    public function setTbdatoclinicodiscapacidaddescripcion($tbdatoclinicodiscapacidaddescripcion){ $this->tbdatoclinicodiscapacidaddescripcion = $tbdatoclinicodiscapacidaddescripcion; }
    public function setTbdatoclinicorestriccionmedica($tbdatoclinicorestriccionmedica){ $this->tbdatoclinicorestriccionmedica = $tbdatoclinicorestriccionmedica; }
    public function setTbdatoclinicorestriccionmedicadescripcion($tbdatoclinicorestriccionmedicadescripcion){$this->tbdatoclinicorestriccionmedicadescripcion = $tbdatoclinicorestriccionmedicadescripcion;}
    public function setCarnet($carnet) { $this->carnet = $carnet; }
}
?>