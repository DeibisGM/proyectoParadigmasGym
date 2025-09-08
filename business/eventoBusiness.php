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
        $salasOcupadas = $this->eventoData->verificarDisponibilidadSalas($salas, $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin());
        if (!empty($salasOcupadas)) {
            return "Error: Las siguientes salas ya están ocupadas en ese horario: " . implode(', ', $salasOcupadas);
        }
        return $this->eventoData->insertarEvento($evento, $salas);
    }

    public function actualizarEvento($evento, $salas)
    {
        $salasOcupadas = $this->eventoData->verificarDisponibilidadSalas(
            $salas, $evento->getFecha(), $evento->getHoraInicio(),
            $evento->getHoraFin(), $evento->getId()
        );
        if (!empty($salasOcupadas)) {
            return "Error: Las siguientes salas ya están ocupadas en ese horario: " . implode(', ', $salasOcupadas);
        }
        return $this->eventoData->actualizarEvento($evento, $salas);
    }

    public function eliminarEvento($id)
    {
        return $this->eventoData->eliminarEvento($id);
    }

    public function getAllEventos()
    {
        return $this->eventoData->getAllEventos();
    }

    public function getSalaIdsPorEventoId($eventoId)
    {
        return $this->eventoData->getSalaIdsPorEventoId($eventoId);
    }
}

?>