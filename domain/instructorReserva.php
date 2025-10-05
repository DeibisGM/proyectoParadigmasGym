<?php

class InstructorReserva
{
    private $id;
    private $instructorId; // This will be the concatenated string
    private $fecha;
    private $horaInicio;
    private $horaFin;
    private $instructorNombre; // New property for concatenated names

    public function __construct($id, $instructorId, $fecha, $horaInicio, $horaFin)
    {
        $this->id = $id;
        $this->instructorId = $instructorId;
        $this->fecha = $fecha;
        $this->horaInicio = $horaInicio;
        $this->horaFin = $horaFin;
        $this->instructorNombre = ''; // Initialize
    }

    // Getters
    public function getId() { return $this->id; }
    public function getInstructorId() { return $this->instructorId; }
    public function getFecha() { return $this->fecha; }
    public function getHoraInicio() { return $this->horaInicio; }
    public function getHoraFin() { return $this->horaFin; }
    public function getInstructorNombre() { return $this->instructorNombre; }

    // Setters
    public function setInstructorNombre($instructorNombre) { $this->instructorNombre = $instructorNombre; }
    public function setInstructorId($instructorId) { $this->id = $instructorId; }
}
?>