<?php

class ReservaLibre
{
    private $id;
    private $clienteId;
    private $horarioLibreId;
    private $clienteResponsableId; // NUEVO
    private $activo;

    // Propiedades Adicionales
    private $clienteNombre;
    private $clienteResponsableNombre; // NUEVO
    private $fecha;
    private $hora;
    private $salaNombre;
    private $instructorNombre;

    // MODIFICADO: Añadido $clienteResponsableId al constructor
    public function __construct($id, $clienteId, $horarioLibreId, $clienteResponsableId, $activo)
    {
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->horarioLibreId = $horarioLibreId;
        $this->clienteResponsableId = $clienteResponsableId; // NUEVO
        $this->activo = $activo;
    }

    public function getId() { return $this->id; }
    public function getClienteId() { return $this->clienteId; }
    public function getHorarioLibreId() { return $this->horarioLibreId; }
    public function getClienteResponsableId() { return $this->clienteResponsableId; } // NUEVO
    public function isActivo() { return $this->activo; }

    public function getClienteNombre() { return $this->clienteNombre; }
    public function setClienteNombre($nombre) { $this->clienteNombre = $nombre; }

    public function getClienteResponsableNombre() { return $this->clienteResponsableNombre; } // NUEVO
    public function setClienteResponsableNombre($nombre) { $this->clienteResponsableNombre = $nombre; } // NUEVO

    public function getFecha() { return $this->fecha; }
    public function setFecha($fecha) { $this->fecha = $fecha; }

    public function getHora() { return $this->hora; }
    public function setHora($hora) { $this->hora = $hora; }

    public function getSalaNombre() { return $this->salaNombre; }
    public function setSalaNombre($salaNombre) { $this->salaNombre = $salaNombre; }

    public function getInstructorNombre() { return $this->instructorNombre; }
    public function setInstructorNombre($nombre) { $this->instructorNombre = $nombre; }
}