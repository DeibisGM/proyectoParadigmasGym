<?php

class partezona
{
    private $partezonaid;
    private $partezonaimaenid;
    private $partezonanombre;
    private $partezonadescripcion;
    private $partezonaactivo;


    public function __construct($partezonaid, $partezonaimaenid, $partezonanombre, $partezonadescripcion, $partezonaactivo)
    {
        $this->partezonaid = $partezonaid;
        $this->partezonaimaenid = $partezonaimaenid;
        $this->partezonanombre = $partezonanombre;
        $this->partezonadescripcion = $partezonadescripcion;
        $this->partezonaactivo = $partezonaactivo;
    }

    public function getPartezonaid()
    {
        return $this->partezonaid;
    }

    public function setPartezonaid($partezonaid): void
    {
        $this->partezonaid = $partezonaid;
    }

    public function getPartezonaimaenid()
    {
        return $this->partezonaimaenid;
    }

    public function setPartezonaimaenid($partezonaimaenid): void
    {
        $this->partezonaimaenid = $partezonaimaenid;
    }

    public function getPartezonanombre()
    {
        return $this->partezonanombre;
    }

    public function setPartezonanombre($partezonanombre): void
    {
        $this->partezonanombre = $partezonanombre;
    }
    public function getPartezonadescripcion()
    {
        return $this->partezonadescripcion;
    }
    public function setPartezonadescripcion($partezonadescripcion): void
    {
        $this->partezonadescripcion = $partezonadescripcion;
    }
    public function getPartezonaactivo()
    {
        return $this->partezonaactivo;
    }
    public function setPartezonaactivo($partezonaactivo): void
    {
        $this->partezonaactivo = $partezonaactivo;
    }

}