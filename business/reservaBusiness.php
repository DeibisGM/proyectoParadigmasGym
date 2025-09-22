<?php
include_once '../data/reservaEventoData.php';
include_once '../data/reservaLibreData.php';
include_once '../data/clienteData.php';
include_once '../data/eventoData.php';
include_once '../data/horarioLibreData.php';
include_once '../domain/reservaEvento.php';
include_once '../domain/reservaLibre.php';

class ReservaBusiness
{
    private $reservaEventoData;
    private $reservaLibreData;
    private $clienteData;
    private $eventoData;
    private $horarioLibreData;

    public function __construct()
    {
        $this->reservaEventoData = new ReservaEventoData();
        $this->reservaLibreData = new ReservaLibreData();
        $this->clienteData = new ClienteData();
        $this->eventoData = new EventoData();
        $this->horarioLibreData = new HorarioLibreData();
    }

    public function crearReserva($clienteId, $eventoId, $horarioLibreId)
    {
        if ($eventoId) {
            return $this->crearReservaEvento($clienteId, $eventoId);
        } elseif ($horarioLibreId) {
            return $this->crearReservaLibre($clienteId, $horarioLibreId);
        }
        return "Tipo de reserva no especificado.";
    }

    private function crearReservaEvento($clienteId, $eventoId)
    {
        $evento = $this->eventoData->getEventoById($eventoId);
        if (!$evento) return "Evento no encontrado.";

        // Validar cupos para evento
        $reservasActuales = $this->reservaEventoData->getReservasPorEvento($eventoId);
        if(count($reservasActuales) >= $evento->getAforo()){
            return "No hay cupos disponibles para este evento.";
        }

        $reserva = new ReservaEvento(0, $clienteId, $eventoId, $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin(), 'activa');
        if ($this->reservaEventoData->insertarReservaEvento($reserva)) {
            return true;
        }
        return "Error al procesar la reserva del evento.";
    }

    private function crearReservaLibre($clienteId, $horarioLibreId)
    {
        $horario = $this->horarioLibreData->getHorarioLibrePorId($horariolibreId);
        if (!$horario) return "Horario no disponible.";

        if ($horario->getMatriculados() >= $horario->getCupos()) {
            return "No hay cupos disponibles para este horario.";
        }

        $reserva = new ReservaLibre(0, $clienteId, $horarioLibreId, $horario->getFecha(), $horario->getHora(), 'activa');
        if ($this->reservaLibreData->insertarReservaLibre($reserva)) {
            $this->horarioLibreData->incrementarMatriculados($horarioLibreId);
            return true;
        }
        return "Error al crear la reserva de uso libre.";
    }

    public function getTodasMisReservas($clienteId) {
        $reservasEventos = $this->reservaEventoData->getReservasEventoPorCliente($clienteId);
        $reservasLibres = $this->reservaLibreData->getReservasLibrePorCliente($clienteId);

        $todas = [];
        foreach($reservasEventos as $r) {
            $todas[] = [
                'tipo' => 'Evento',
                'nombre' => $r->getEventoNombre(),
                'fecha' => $r->getFecha(),
                'hora' => date('H:i', strtotime($r->getHoraInicio())),
                'estado' => $r->getEstado()
            ];
        }
        foreach($reservasLibres as $r) {
            $todas[] = [
                'tipo' => 'Uso Libre',
                'nombre' => 'Acceso General Gimnasio',
                'fecha' => $r->getFecha(),
                'hora' => date('H:i', strtotime($r->getHora())),
                'estado' => $r->getEstado()
            ];
        }

        usort($todas, function($a, $b) {
            return strtotime($b['fecha'] . ' ' . $b['hora']) - strtotime($a['fecha'] . ' ' . $a['hora']);
        });

        return $todas;
    }
}
?>