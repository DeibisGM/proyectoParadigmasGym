<?php

class HorarioLibre
{
    private $tbhorariolibreid;
    private $tbhorariolibrefecha;
    private $tbhorariolibrehora;
    private $tbhorariolibresalaid;
    private $tbhorariolibreinstructorid;
    private $tbhorariolibrecupos;
    private $tbhorariolibrematriculados;
    private $tbhorariolibreactivo;

    public function __construct($id, $fecha, $hora, $salaid, $instructorid, $cupos, $matriculados, $activo)
    {
        $this->tbhorariolibreid = $id;
        $this->tbhorariolibrefecha = $fecha;
        $this->tbhorariolibrehora = $hora;
        $this->tbhorariolibresalaid = $salaid;
        $this->tbhorariolibreinstructorid = $instructorid;
        $this->tbhorariolibrecupos = $cupos;
        $this->tbhorariolibrematriculados = $matriculados;
        $this->tbhorariolibreactivo = $activo;
    }

    public function getId() { return $this->tbhorariolibreid; }
    public function getFecha() { return $this->tbhorariolibrefecha; }
    public function getHora() { return $this->tbhorariolibrehora; }
    public function getSalaId() { return $this->tbhorariolibresalaid; }
    public function getInstructorId() { return $this->tbhorariolibreinstructorid; }
    public function getCupos() { return $this->tbhorariolibrecupos; }
    public function getMatriculados() { return $this->tbhorariolibrematriculados; }
    public function isActivo() { return $this->tbhorariolibreactivo; }
}
?>