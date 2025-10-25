<?php

class RutinaEjercicio
{
    private $tbrutinaejercicioid;
    private $tbrutinaid;
    private $tbrutinaejerciciotipo;
    private $tbejercicioid;
    private $tbrutinaejercicioseries;
    private $tbrutinaejerciciorepeticiones;
    private $tbrutinaejerciciopeso;
    private $tbrutinaejerciciotiempo_seg;
    private $tbrutinaejerciciodescanso_seg;
    private $tbrutinaejerciciocomentario;
    private $nombreEjercicio;

    public function __construct($id, $rutinaId, $tipo, $ejercicioId, $series, $repeticiones, $peso, $tiempo, $descanso, $comentario)
    {
        $this->tbrutinaejercicioid = $id;
        $this->tbrutinaid = $rutinaId;
        $this->tbrutinaejerciciotipo = $tipo;
        $this->tbejercicioid = $ejercicioId;
        $this->tbrutinaejercicioseries = $series;
        $this->tbrutinaejerciciorepeticiones = $repeticiones;
        $this->tbrutinaejerciciopeso = $peso;
        $this->tbrutinaejerciciotiempo_seg = $tiempo;
        $this->tbrutinaejerciciodescanso_seg = $descanso;
        $this->tbrutinaejerciciocomentario = $comentario;
        $this->nombreEjercicio = '';
    }

    public function getId() { return $this->tbrutinaejercicioid; }
    public function setId($id) { $this->tbrutinaejercicioid = $id; }
    public function getRutinaId() { return $this->tbrutinaid; }
    public function setRutinaId($rutinaId) { $this->tbrutinaid = $rutinaId; }
    public function getTipo() { return $this->tbrutinaejerciciotipo; }
    public function getEjercicioId() { return $this->tbejercicioid; }
    public function getSeries() { return $this->tbrutinaejercicioseries; }
    public function getRepeticiones() { return $this->tbrutinaejerciciorepeticiones; }
    public function getPeso() { return $this->tbrutinaejerciciopeso; }
    public function getTiempo() { return $this->tbrutinaejerciciotiempo_seg; }
    public function getDescanso() { return $this->tbrutinaejerciciodescanso_seg; }
    public function getComentario() { return $this->tbrutinaejerciciocomentario; }
    public function getNombreEjercicio() { return $this->nombreEjercicio; }
    public function setNombreEjercicio($nombre) { $this->nombreEjercicio = $nombre; }
}
?>