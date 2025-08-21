<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Certificado {
    private $id;
    private $nombre;
    private $descripcion;
    private $entidad;
    private $idInstructor;


    public function __construct($id, $nombre, $descripcion, $entidad, $idInstructor) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->entidad = $entidad;
        $this->idInstructor = $idInstructor;
    }


    public function getId() {
        return $this->id;
    }       

    public function getNombre() {
        return $this->nombre;
    }
    public function getDescripcion() {
        return $this->descripcion;
    }
    public function getEntidad() {
        return $this->entidad;
    }
    public function getIdInstructor() {
        return $this->idInstructor;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }
    public function setEntidad($entidad) {
        $this->entidad = $entidad;
    }  
    public function setIdInstructor($idInstructor) {
        $this->idInstructor = $idInstructor;
    }   
}
?>
