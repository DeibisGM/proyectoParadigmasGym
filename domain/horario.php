<?php

class Horario
{
    private $id;
    private $dia;
    private $activo;
    private $apertura;
    private $cierre;
    private $bloqueos;

    public function __construct($id, $dia, $activo, $apertura, $cierre, $bloqueosStr = '')
    {
        $this->id = $id;
        $this->dia = $dia;
        $this->activo = $activo;
        $this->apertura = $apertura;
        $this->cierre = $cierre;
        $this->setBloqueosFromString($bloqueosStr);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDia()
    {
        return $this->dia;
    }

    public function isActivo()
    {
        return $this->activo;
    }

    public function getApertura()
    {
        return $this->apertura;
    }

    public function getCierre()
    {
        return $this->cierre;
    }

    public function getBloqueos()
    {
        return $this->bloqueos;
    }

    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    public function setApertura($apertura)
    {
        $this->apertura = $apertura;
    }

    public function setCierre($cierre)
    {
        $this->cierre = $cierre;
    }

    public function setBloqueos($bloqueosArray)
    {
        $this->bloqueos = $bloqueosArray;
    }

    private function setBloqueosFromString($bloqueosStr)
    {
        $this->bloqueos = [];
        if (!empty($bloqueosStr)) {
            $this->bloqueos = explode('$', $bloqueosStr);
        }
    }

    public function getBloqueosAsString()
    {
        if (empty($this->bloqueos)) {
            return '';
        }
        return implode('$', $this->bloqueos);
    }
}