<?php

class ejercicioSubzona
{
    private $id;
    private $ejercicio;
    private $subzona;
    private $nombre;

    public function __construct($id, $ejercicio, $subzona, $nombre)
    {
        $this->id = $id;
        $this->ejercicio = $ejercicio;
        $this->subzona = $subzona;
        $this->nombre = $nombre;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getEjercicio()
    {
        return $this->ejercicio;
    }

    public function setEjercicio($ejercicio): void
    {
        $this->ejercicio = $ejercicio;
    }

    public function getSubzona()
    {
        return $this->subzona;
    }

    public function setSubzona($subzona): void
    {
        $this->subzona = $subzona;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre): void
    {
        $this->nombre = $nombre;
    }



}