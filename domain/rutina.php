<?php

class Rutina
{
    private $tbrutinaid;
    private $tbclienteid;
    private $tbrutinafecha;
    private $tbrutinaobservacion;
    private $tbrutinatipo;
    private $ejercicios;

    public function __construct($id, $clienteId, $fecha, $observacion, $tipo = 'completada')
    {
        $this->tbrutinaid = $id;
        $this->tbclienteid = $clienteId;
        $this->tbrutinafecha = $fecha;
        $this->tbrutinaobservacion = $observacion;
        $this->tbrutinatipo = $tipo;
        $this->ejercicios = [];
    }

    public function getId() { return $this->tbrutinaid; }
    public function setId($id) { $this->tbrutinaid = $id; }
    public function getClienteId() { return $this->tbclienteid; }
    public function setClienteId($clienteId) { $this->tbclienteid = $clienteId; }
    public function getFecha() { return $this->tbrutinafecha; }
    public function setFecha($fecha) { $this->tbrutinafecha = $fecha; }
    public function getObservacion() { return $this->tbrutinaobservacion; }
    public function setObservacion($observacion) { $this->tbrutinaobservacion = $observacion; }
    public function getTipo() { return $this->tbrutinatipo; }
    public function setTipo($tipo) { $this->tbrutinatipo = $tipo; }
    public function getEjercicios() { return $this->ejercicios; }
    public function setEjercicios($ejercicios) { $this->ejercicios = $ejercicios; }
    public function addEjercicio($ejercicio) { $this->ejercicios[] = $ejercicio; }
}

?>