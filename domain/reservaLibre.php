<?php

class ReservaLibre
{
    private $id;
    private $clienteId;
    private $horarioLibreId;
    private $activo;

    // Properties to hold joined data
    private $clienteNombre;
    private $fecha;
    private $hora;
    private $salaNombre;

    public function __construct($id, $clienteId, $horarioLibreId, $activo)
    {
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->horarioLibreId = $horarioLibreId;
        $this->activo = $activo;
    }

    // --- Getters for main properties ---
    public function getId() { return $this->id; }
    public function getClienteId() { return $this->clienteId; }
    public function getHorarioLibreId() { return $this->horarioLibreId; }
    public function isActivo() { return $this->activo; }

    // --- Getters and Setters for joined data ---
    public function getClienteNombre() { return $this->clienteNombre; }
    public function setClienteNombre($nombre) { $this->clienteNombre = $nombre; }

    public function getFecha() { return $this->fecha; }
    public function setFecha($fecha) { $this->fecha = $fecha; }

    public function getHora() { return $this->hora; }
    public function setHora($hora) { $this->hora = $hora; }

    public function getSalaNombre() { return $this->salaNombre; }
    public function setSalaNombre($salaNombre) { $this->salaNombre = $salaNombre; }
}
?>