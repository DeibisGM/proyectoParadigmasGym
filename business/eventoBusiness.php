<?php
include_once '../data/eventoData.php';

class EventoBusiness
{
    private $eventoData;

    public function __construct()
    {
        $this->eventoData = new EventoData();
    }

    public function insertarEvento($evento, $salas)
    {
        // 1. Verificar disponibilidad de las salas
        $salasOcupadas = $this->eventoData->verificarDisponibilidadSalas($salas, $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin());

        if (!empty($salasOcupadas)) {
            // Devuelve un mensaje de error con los nombres de las salas ocupadas.
            return "Error: Las siguientes salas ya están ocupadas en ese horario: " . implode(', ', $salasOcupadas);
        }

        // 2. Si todo está libre, proceder con la inserción
        return $this->eventoData->insertarEvento($evento, $salas);
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