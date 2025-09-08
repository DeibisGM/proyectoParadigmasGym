<?php

class SalaReserva
{
    private $id;
    private $salaId; // This will be the concatenated string
    private $eventoId;
    private $fecha;
    private $horaInicio;
    private $horaFin;
    private $salasNombre; // New property for concatenated names

    public function __construct($id, $salaId, $eventoId, $fecha, $horaInicio, $horaFin)
    {
        $this->id = $id;
        $this->salaId = $salaId;
        $this->eventoId = $eventoId;
        $this->fecha = $fecha;
        $this->horaInicio = $horaInicio;
        $this->horaFin = $horaFin;
        $this->salasNombre = ''; // Initialize
    }

    // Getters
    public function getId() { return $this->id; }
    public function getSalaId() { return $this->salaId; }
    public function getEventoId() { return $this->eventoId; }
    public function getFecha() { return $this->fecha; }
    public function getHoraInicio() { return $this->horaInicio; }
    public function getHoraFin() { return $this->horaFin; }
    public function getSalasNombre() { return $this->salasNombre; }

    // Setters
    public function setSalasNombre($salasNombre) { $this->salasNombre = $salasNombre; }
}
?>