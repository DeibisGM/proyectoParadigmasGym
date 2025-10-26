<?php
include_once '../business/ejercicioSubzonaBusiness.php';

class ejercicioFlexibilidad
{
    private $id;
    private $nombre;
    private $descripcion;
    private $duracion;
    private $series;
    private $equipodeayuda;
    private $activo;

    private $subzonaIds;

    public function __construct($id, $nombre, $descripcion, $duracion, $series, $equipodeayuda, $activo)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->duracion = $duracion;
        $this->series = $series;
        $this->equipodeayuda = $equipodeayuda;
        $this->activo = $activo;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getDuracion()
    {
        return $this->duracion;
    }

    public function setDuracion($duracion): void
    {
        $this->duracion = $duracion;
    }

    public function getSeries()
    {
        return $this->series;
    }

    public function setSeries($series): void
    {
        $this->series = $series;
    }

    public function getEquipodeayuda()
    {
        return $this->equipodeayuda;
    }

    public function setEquipodeayuda($equipodeayuda): void
    {
        $this->equipodeayuda = $equipodeayuda;
    }

    public function getActivo()
    {
        return $this->activo;
    }

    public function setActivo($activo): void
    {
        $this->activo = $activo;
    }

    public function getSubzonaIds() {
        if ($this->subzonaIds === null) {
            $ejercicioSubzonaBusiness = new ejercicioSubzonaBusiness();
            $subzonas = $ejercicioSubzonaBusiness->getSubzonasPorEjercicio($this->id, 'Flexibilidad');
            $this->subzonaIds = array_map(fn($s) => $s->getSubzona(), $subzonas);
        }
        return $this->subzonaIds;
    }
}