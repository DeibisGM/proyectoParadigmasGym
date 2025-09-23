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
        $horario = $this->horarioLibreData->getHorarioLibrePorId($horarioLibreId);
        if (!$horario) return "Horario no disponible.";

        if ($horario->getMatriculados() >= $horario->getCupos()) {
            return "No hay cupos disponibles para este horario.";
        }

        // The constructor now only needs the active status (1 for active)
        $reserva = new ReservaLibre(0, $clienteId, $horarioLibreId, 1);
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
                'nombre' => 'Uso de ' . $r->getSalaNombre(),
                'fecha' => $r->getFecha(),
                'hora' => date('H:i', strtotime($r->getHora())),
                'estado' => $r->isActivo() ? 'Activa' : 'Inactiva'
            ];
        }

        usort($todas, function($a, $b) {
            return strtotime($b['fecha'] . ' ' . $b['hora']) - strtotime($a['fecha'] . ' ' . $a['hora']);
        });

        return $todas;
    }

    public function getAllReservas()
    {
        $reservasEventos = $this->reservaEventoData->getAllReservasEvento();
        $reservasLibres = $this->reservaLibreData->getAllReservasLibre();

        $todas = [];

        // Process event reservations
        foreach ($reservasEventos as $r) {
            $todas[] = [
                'fecha' => $r['fecha'],
                'hora' => date('H:i', strtotime($r['hora'])),
                'cliente' => $r['cliente'],
                'tipo' => 'Evento',
                'nombre' => $r['nombre'],
                'estado' => $r['estado'],
            ];
        }

        // Process free space reservations
        foreach ($reservasLibres as $r) {
            $todas[] = [
                'fecha' => $r->getFecha(),
                'hora' => date('H:i', strtotime($r->getHora())),
                'cliente' => $r->getClienteNombre(),
                'tipo' => 'Uso Libre',
                'nombre' => 'Uso de ' . $r->getSalaNombre(),
                'estado' => $r->isActivo() ? 'Activa' : 'Inactiva',
            ];
        }

        // Sort all reservations by date and time, descending
        usort($todas, function ($a, $b) {
            $dateComparison = strcmp($b['fecha'], $a['fecha']);
            if ($dateComparison === 0) {
                return strcmp($b['hora'], $a['hora']);
            }
            return $dateComparison;
        });

        return $todas;
    }

    public function crearMultiplesReservasLibre($clienteId, $horarioLibreIds)
    {
        $resultados = [
            'success_count' => 0,
            'failure_count' => 0,
            'details' => []
        ];

        foreach ($horarioLibreIds as $id) {
            $resultado = $this->crearReservaLibre($clienteId, $id);
            if ($resultado === true) {
                $resultados['success_count']++;
                $resultados['details'][] = ['id' => $id, 'status' => 'success'];
            } else {
                $resultados['failure_count']++;
                $resultados['details'][] = ['id' => $id, 'status' => 'failure', 'reason' => $resultado];
            }
        }

        return $resultados;
    }

    public function getReservasLibrePorCliente($clienteId)
    {
        return $this->reservaLibreData->getReservasLibrePorCliente($clienteId);
    }

    public function cancelarReservaLibre($reservaId, $clienteId)
    {
        $reserva = $this->reservaLibreData->getReservaLibreById($reservaId);

        if (!$reserva) {
            return "Reserva no encontrada.";
        }
        if ($reserva->getClienteId() != $clienteId) {
            return "No tienes permiso para cancelar esta reserva.";
        }

        if ($this->reservaLibreData->eliminarReservaLibre($reservaId)) {
            // Decrement the count in the schedule table
            $this->horarioLibreData->decrementarMatriculados($reserva->getHorarioLibreId());
            return true;
        }

        return "Error al cancelar la reserva.";
    }
}
?>