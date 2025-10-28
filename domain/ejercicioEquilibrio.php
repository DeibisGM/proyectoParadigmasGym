<?php
include_once '../business/ejercicioSubzonaBusiness.php';

class EjercicioEquilibrio{
    private $tbejercicioequilibrioid;
    private $tbejercicioequilibrionombre;
    private $tbejercicioequilibriodescripcion;
    private $tbejercicioequilibriodificultad;
    private $tbejercicioequilibrioduracion;
    private $tbejercicioequilibriomateriales;
    private $tbejercicioequilibriopostura;

    private $subzonaIds;

    public function __construct($tbejercicioequilibrioid, $tbejercicioequilibrionombre, $tbejercicioequilibriodescripcion,
                                $tbejercicioequilibriodificultad, $tbejercicioequilibrioduracion, $tbejercicioequilibriomateriales,
                                $tbejercicioequilibriopostura){
        $this->tbejercicioequilibrioid = $tbejercicioequilibrioid;
        $this->tbejercicioequilibrionombre = $tbejercicioequilibrionombre;
        $this->tbejercicioequilibriodescripcion = $tbejercicioequilibriodescripcion;
        $this->tbejercicioequilibriodificultad = $tbejercicioequilibriodificultad;
        $this->tbejercicioequilibrioduracion = $tbejercicioequilibrioduracion;
        $this->tbejercicioequilibriomateriales = $tbejercicioequilibriomateriales;
        $this->tbejercicioequilibriopostura = $tbejercicioequilibriopostura;
    }

    public function getTbejercicioequilibrioid(){
        return $this->tbejercicioequilibrioid;
    }

    public function setTbejercicioequilibrioid($tbejercicioequilibrioid){
        $this->tbejercicioequilibrioid = $tbejercicioequilibrioid;
    }

    public function getTbejercicioequilibrionombre(){
        return $this->tbejercicioequilibrionombre;
    }

    public function setTbejercicioequilibrionombre($tbejercicioequilibrionombre){
        $this->tbejercicioequilibrionombre = $tbejercicioequilibrionombre;
    }

    public function getTbejercicioequilibriodescripcion(){
        return $this->tbejercicioequilibriodescripcion;
    }

    public function setTbejercicioequilibriodescripcion($tbejercicioequilibriodescripcion){
        $this->tbejercicioequilibriodescripcion = $tbejercicioequilibriodescripcion;
    }

    public function getTbejercicioequilibriodificultad(){
        return $this->tbejercicioequilibriodificultad;
    }

    public function setTbejercicioequilibriodificultad($tbejercicioequilibriodificultad){
        $this->tbejercicioequilibriodificultad = $tbejercicioequilibriodificultad;
    }

    public function getTbejercicioequilibrioduracion(){
        return $this->tbejercicioequilibrioduracion;
    }

    public function setTbejercicioequilibrioduracion($tbejercicioequilibrioduracion){
        $this->tbejercicioequilibrioduracion = $tbejercicioequilibrioduracion;
    }

    public function getTbejercicioequilibriomateriales(){
        return $this->tbejercicioequilibriomateriales;
    }

    public function setTbejercicioequilibriomateriales($tbejercicioequilibriomateriales){
        $this->tbejercicioequilibriomateriales = $tbejercicioequilibriomateriales;
    }

    public function getTbejercicioequilibriopostura(){
        return $this->tbejercicioequilibriopostura;
    }

    public function setTbejercicioequilibriopostura($tbejercicioequilibriopostura){
        $this->tbejercicioequilibriopostura = $tbejercicioequilibriopostura;
    }

    public function getSubzonaIds() {
        if ($this->subzonaIds === null) {
            $ejercicioSubzonaBusiness = new ejercicioSubzonaBusiness();
            $subzonas = $ejercicioSubzonaBusiness->getSubzonasPorEjercicio($this->tbejercicioequilibrioid, 'Equilibrio');
            $this->subzonaIds = array_map(fn($s) => $s->getSubzona(), $subzonas);
        }
        return $this->subzonaIds;
    }
}
?>