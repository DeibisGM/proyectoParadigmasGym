<?php
include_once '../business/ejercicioSubzonaBusiness.php';

class ejercicioResistencia
{
    private $id;
    private $nombre;
    private $tiempo;
    private $peso;
    private $descripcion;
    private $activo;

    private $subzonaIds;

    public function __construct($id, $nombre, $tiempo, $peso, $descripcion, $activo)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->tiempo = $tiempo;
        $this->peso = $peso;
        $this->descripcion = $descripcion;
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

    public function getTiempo()
    {
        return $this->tiempo;
    }

    public function setTiempo($tiempo): void
    {
        $this->tiempo = $tiempo;
    }

    public function getPeso()
    {
        return $this->peso;
    }

    public function setPeso($peso): void
    {
        $this->peso = $peso;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion): void
    {
        $this->descripcion = $descripcion;
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
            $subzonas = $ejercicioSubzonaBusiness->getSubzonasPorEjercicio($this->id, 'Resistencia');
            $this->subzonaIds = array_map(fn($s) => $s->getSubzona(), $subzonas);
        }
        return $this->subzonaIds;
    }



}