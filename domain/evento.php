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
    private $activo;

    // Propiedades adicionales
    private $instructorNombre;
    private $salasNombre;
    private $reservasCount;

    public function __construct($id,  $instructorId,  $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $activo)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->horaInicio = $horaInicio;
        $this->horaFin = $horaFin;
        $this->aforo = $aforo;
        $this->instructorId = $instructorId;
        $this->activo = $activo;
        $this->instructorNombre = '';
        $this->salasNombre = '';
        $this->reservasCount = 0;
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

    public function getActivo()
    {
        return $this->activo;
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

    public function getReservasCount()
    {
        return $this->reservasCount;
    }

    public function setReservasCount($count)
    {
        $this->reservasCount = $count;
    }
}

?>