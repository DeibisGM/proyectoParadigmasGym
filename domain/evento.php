<?php

class Evento
{
    private $id;
    private $instructorId;
    private $tipo; // MODIFICADO: Nombre de propiedad más claro
    private $nombre;
    private $descripcion;
    private $fecha;
    private $horaInicio;
    private $horaFin;
    private $aforo;
    private $activo;

    // Propiedades adicionales
    private $instructorNombre;
    private $salasNombre;
    private $reservasCount;

    // MODIFICADO: Añadido $tipo al constructor
    public function __construct($id,  $instructorId, $tipo,  $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $aforo, $activo)
    {
        $this->id = $id;
        $this->instructorId = $instructorId;
        $this->tipo = $tipo;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
        $this->horaInicio = $horaInicio;
        $this->horaFin = $horaFin;
        $this->aforo = $aforo;
        $this->activo = $activo;
        $this->instructorNombre = '';
        $this->salasNombre = '';
        $this->reservasCount = 0;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getInstructorId() { return $this->instructorId; }
    public function getTipo() { return $this->tipo; } // NUEVO
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getFecha() { return $this->fecha; }
    public function getHoraInicio() { return $this->horaInicio; }
    public function getHoraFin() { return $this->horaFin; }
    public function getAforo() { return $this->aforo; }
    public function getActivo() { return $this->activo; }
    public function getInstructorNombre() { return $this->instructorNombre; }
    public function getSalasNombre() { return $this->salasNombre; }
    public function getReservasCount() { return $this->reservasCount; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTipo($tipo) { $this->tipo = $tipo; } // NUEVO
    public function setInstructorNombre($nombre) { $this->instructorNombre = $nombre; }
    public function setSalasNombre($salasNombre) { $this->salasNombre = $salasNombre; }
    public function setReservasCount($count) { $this->reservasCount = $count; }
}

?>