<?php

class subzona
{
    private $subzonaid;
    private $subzonaimaenid;
    private $subzonanombre;
    private $subzonadescripcion;
    private $subzonaactivo;


    public function __construct($subzonaid, $subzonaimaenid, $subzonanombre, $subzonadescripcion, $subzonaactivo)
    {
        $this->subzonaid = $subzonaid;
        $this->subzonaimaenid = $subzonaimaenid;
        $this->subzonanombre = $subzonanombre;
        $this->subzonadescripcion = $subzonadescripcion;
        $this->subzonaactivo = $subzonaactivo;
    }

    public function getSubzonaid()
    {
        return $this->subzonaid;
    }

    public function setSubzonaid($subzonaid): void
    {
        $this->subzonaid = $subzonaid;
    }

    public function getSubzonaimaenid()
    {
        return $this->subzonaimaenid;
    }

    public function setSubzonaimaenid($subzonaimaenid): void
    {
        $this->subzonaimaenid = $subzonaimaenid;
    }

    public function getSubzonanombre()
    {
        return $this->subzonanombre;
    }

    public function setSubzonanombre($subzonanombre): void
    {
        $this->subzonanombre = $subzonanombre;
    }
    public function getSubzonadescripcion()
    {
        return $this->subzonadescripcion;
    }
    public function setSubzonadescripcion($subzonadescripcion): void
    {
        $this->subzonadescripcion = $subzonadescripcion;
    }
    public function getSubzonaactivo()
    {
        return $this->subzonaactivo;
    }
    public function setSubzonaactivo($subzonaactivo): void
    {
        $this->subzonaactivo = $subzonaactivo;
    }

}