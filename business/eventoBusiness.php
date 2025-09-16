<?php
include_once '../data/eventoData.php';
include_once '../data/salaReservasData.php';

class EventoBusiness
{
    private $eventoData;
    private $salaReservasData;

    public function __construct()
    {
        $this->eventoData = new EventoData();
        $this->salaReservasData = new SalaReservasData();
    }

    public function insertarEvento($evento, $salas)
    {
        $resultado = $this->salaReservasData->verificarDisponibilidad($salas, $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin());

        if (isset($resultado['conflictos'])) {
            return "Error: Las siguientes salas ya están ocupadas en ese horario: " . implode(', ', $resultado['conflictos']);
        }
        if (isset($resultado['error'])) {
            return "Error de sistema: " . $resultado['error'];
        }

        return $this->eventoData->insertarEvento($evento, $salas);
    }

    public function actualizarEvento($evento, $salas)
    {
        $resultado = $this->salaReservasData->verificarDisponibilidad(
            $salas, $evento->getFecha(), $evento->getHoraInicio(),
            $evento->getHoraFin(), $evento->getId()
        );

        if (isset($resultado['conflictos'])) {
            return "Error: Las siguientes salas ya están ocupadas en ese horario: " . implode(', ', $resultado['conflictos']);
        }
        if (isset($resultado['error'])) {
            return "Error de sistema: " . $resultado['error'];
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

    public function getEventoById($id)
    {
        return $this->eventoData->getEventoById($id);
    }

    public function getSalaIdsPorEvento($eventoId)
    {
        return $this->salaReservasData->getSalaIdsPorEventoId($eventoId);
    }
}
?>