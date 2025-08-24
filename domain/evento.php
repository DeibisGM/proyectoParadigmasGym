<?php

class Evento
{
    private $id;
    private $nombre;
    private $descripcion;
    // CAMBIO: De 'diaSemana' a 'fecha'
    private $fecha;
    private $horaInicio;
    private $horaFin;
    private $aforo;
    private $instructorId;
    private $estado;

    // Propiedad adicional para mostrar nombre del instructor
    private $instructorNombre;

    // CAMBIO: Constructor actualizado
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

    // CAMBIO: Getter actualizado
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

    // Setters
    public function setInstructorNombre($nombre)
    {
        $this->instructorNombre = $nombre;
    }
}

?>