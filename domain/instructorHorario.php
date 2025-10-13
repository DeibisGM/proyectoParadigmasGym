<?php

class InstructorHorario
{
    private $id;
    private $instructorId;
    private $dia;
    private $horaInicio;
    private $horaFin;
    private $activo;

    public function __construct($id, $instructorId, $dia, $horaInicio, $horaFin, $activo = 1)
    {
        $this->id = $id;
        $this->instructorId = $instructorId;
        $this->dia = $dia;
        $this->horaInicio = $horaInicio;
        $this->horaFin = $horaFin;
        $this->activo = $activo;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getInstructorId() { return $this->instructorId; }
    public function getDia() { return $this->dia; }
    public function getHoraInicio() { return $this->horaInicio; }
    public function getHoraFin() { return $this->horaFin; }
    public function getActivo() { return $this->activo; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setInstructorId($instructorId) { $this->instructorId = $instructorId; }
    public function setDia($dia) { $this->dia = $dia; }
    public function setHoraInicio($horaInicio) { $this->horaInicio = $horaInicio; }
    public function setHoraFin($horaFin) { $this->horaFin = $horaFin; }
    public function setActivo($activo) { $this->activo = $activo; }
}
?>