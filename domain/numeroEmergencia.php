<?php

class numeroEmergencia
{
    private $numeroemergenciaId;
    private $numeroemergenciaClienteId;
    private $numeroemergenciaNombre;
    private $numeroemergenciaTelefono;
    private $numeroemergenciaRelacion;

    public function __construct($numeroemergenciaId, $numeroemergenciaClienteId, $numeroemergenciaNombre, $numeroemergenciaTelefono, $numeroemergenciaRelacion)
    {
        $this->numeroemergenciaId = $numeroemergenciaId;
        $this->numeroemergenciaClienteId = $numeroemergenciaClienteId;
        $this->numeroemergenciaNombre = $numeroemergenciaNombre;
        $this->numeroemergenciaTelefono = $numeroemergenciaTelefono;
        $this->numeroemergenciaRelacion = $numeroemergenciaRelacion;
    }

    public function getId()
    {
        return $this->numeroemergenciaId;
    }

    public function setId($id): void
    {
        $this->numeroemergenciaId = $id;
    }

    public function getClienteId()
    {
        return $this->numeroemergenciaClienteId;
    }

    public function setClienteId($clienteId): void
    {
        $this->numeroemergenciaClienteId = $clienteId;
    }

    public function getNombre()
    {
        return $this->numeroemergenciaNombre;
    }

    public function setNombre($nombre): void
    {
        $this->numeroemergenciaNombre = $nombre;
    }

    public function getTelefono()
    {
        return $this->numeroemergenciaTelefono;
    }

    public function setTelefono($telefono): void
    {
        $this->numeroemergenciaTelefono = $telefono;
    }

    public function getRelacion()
    {
        return $this->numeroemergenciaRelacion;
    }

    public function setRelacion($relacion): void
    {
        $this->numeroemergenciaRelacion = $relacion;
    }

}