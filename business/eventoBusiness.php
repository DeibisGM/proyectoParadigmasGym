<?php
include_once '../data/eventoData.php';

class EventoBusiness
{
    private $eventoData;

    public function __construct()
    {
        $this->eventoData = new EventoData();
    }

    public function insertarEvento($evento)
    {
        return $this->eventoData->insertarEvento($evento);
    }

    public function actualizarEvento($evento)
    {
        return $this->eventoData->actualizarEvento($evento);
    }

    public function eliminarEvento($id)
    {
        return $this->eventoData->eliminarEvento($id);
    }

    public function getAllEventos()
    {
        return $this->eventoData->getAllEventos();
    }
}

?>