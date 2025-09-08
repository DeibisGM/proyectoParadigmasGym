<?php

class Evento
{
    private $id;
    private $nombre;
    private $descripcion;
    private $fecha;
    private $horaInicio;
    private $horaFin;
    private $aforo;
    private $instructorId;
    private $estado;

    // Propiedades adicionales
    private $instructorNombre;
    private $salasNombre;

    public function __construct($id, $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $instructorId, $estado)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->horaInicio = $horaInicio;
        $this->horaFin = $horaFin;
        $this->aforo = $aforo;
        $this->instructorId = $instructorId;
        $this->estado = $estado;
        $this->instructorNombre = '';
        $this->salasNombre = '';
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getHoraInicio()
    {
        return $this->horaInicio;
    }

    public function getHoraFin()
    {
        return $this->horaFin;
    }

    public function getAforo()
    {
        return $this->aforo;
    }

    public function getInstructorId()
    {
        return $this->instructorId;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getInstructorNombre()
    {
        return $this->instructorNombre;
    }

    public function getSalasNombre()
    {
        return $this->salasNombre;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setInstructorNombre($nombre)
    {
        $this->instructorNombre = $nombre;
    }

    public function setSalasNombre($salasNombre)
    {
        $this->salasNombre = $salasNombre;
    }
}

?>