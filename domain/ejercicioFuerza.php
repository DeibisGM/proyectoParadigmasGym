<?php
include_once '../business/ejercicioSubzonaBusiness.php';

class EjercicioFuerza
{
    private $id;
    private $nombre;
    private $descripcion;
    private $repeticion;
    private $serie;
    private $peso;
    private $descanso;
    private $activo;
    private $subzonaIds;

    public function __construct($id, $nombre, $descripcion, $repeticion, $serie, $peso, $descanso, $activo)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->repeticion = $repeticion;
        $this->serie = $serie;
        $this->peso = $peso;
        $this->descanso = $descanso;
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

    public function getRepeticion()
    {
        return $this->repeticion;
    }

    public function setRepeticion($repeticion): void
    {
        $this->repeticion = $repeticion;
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function setSerie($serie): void
    {
        $this->serie = $serie;
    }

    public function getPeso()
    {
        return $this->peso;
    }

    public function setPeso($peso): void
    {
        $this->peso = $peso;
    }

    public function getDescanso()
    {
        return $this->descanso;
    }

    public function setDescanso($descanso): void
    {
        $this->descanso = $descanso;
    }

    public function getActivo()
    {
        return $this->activo;
    }

    public function setActivo($activo): void
    {
        $this->activo = $activo;
    }

    public function getSubzonaIds()
    {
        if ($this->subzonaIds === null) {
            $ejercicioSubzonaBusiness = new ejercicioSubzonaBusiness();
            $subzonas = $ejercicioSubzonaBusiness->getSubzonasPorEjercicio($this->id, 'Fuerza');
            $this->subzonaIds = array_map(fn($s) => $s->getSubzona(), $subzonas);
        }
        return $this->subzonaIds;
    }

    // Métodos legacy para compatibilidad
    public function getTbejerciciofuerzaid() { return $this->id; }
    public function getTbejerciciofuerzanombre() { return $this->nombre; }
    public function getTbejerciciofuerzadescripcion() { return $this->descripcion; }
    public function getTbejerciciofuerzarepeticion() { return $this->repeticion; }
    public function getTbejerciciofuerzaserie() { return $this->serie; }
    public function getTbejerciciofuerzapeso() { return $this->peso; }
    public function getTbejerciciofuerzadescanso() { return $this->descanso; }
}
?>