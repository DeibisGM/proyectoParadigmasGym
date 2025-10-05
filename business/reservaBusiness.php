<?php
include_once '../data/reservaEventoData.php';
include_once '../data/reservaLibreData.php';
include_once '../data/clienteData.php';
include_once '../data/eventoData.php';
include_once '../data/horarioLibreData.php';
include_once '../domain/reservaEvento.php';
include_once '../domain/reservaLibre.php';
include_once 'horarioPersonalBusiness.php';

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

    private function _procesarListasDeReservas($reservasEventos, $reservasLibres, $incluirClienteNombre = false)
    {
        $todas = [];
        foreach ($reservasEventos as $r) {
            $reserva = [
                'fecha' => $r->getFecha(),
                'hora' => date('H:i', strtotime($r->getHoraInicio())),
                'tipo' => 'Evento',
                'descripcion' => $r->getEventoNombre(),
                'instructor' => $r->getInstructorNombre(),
                'estado' => $r->getEstado() ? 'Activa' : 'Inactiva',
            ];
            if ($incluirClienteNombre) $reserva['cliente'] = $r->getClienteNombre();
            $todas[] = $reserva;
        }

        foreach ($reservasLibres as $r) {
            $reserva = [
                'fecha' => $r->getFecha(),
                'hora' => date('H:i', strtotime($r->getHora())),
                'tipo' => 'Uso Libre',
                'descripcion' => 'Uso de ' . $r->getSalaNombre(),
                'instructor' => $r->getInstructorNombre(),
                'estado' => $r->isActivo() ? 'Activa' : 'Inactiva',
            ];
            if ($incluirClienteNombre) $reserva['cliente'] = $r->getClienteNombre();
            $todas[] = $reserva;
        }
        return $todas;
    }

   public function getReservasInstructorPersonalPorCliente($clienteId)
   {
       $horarioPersonalBusiness = new HorarioPersonalBusiness();
       $reservasPersonales = $horarioPersonalBusiness->getMisReservasPersonales($clienteId);

       $reservasFormateadas = [];
       foreach ($reservasPersonales as $reserva) {
           $reservasFormateadas[] = [
               'fecha' => $reserva->getFecha(),
               'hora' => date('H:i', strtotime($reserva->getHora())),
               'tipo' => 'Instructor Personal',
               'descripcion' => 'Sesión personalizada',
               'instructor' => 'Instructor ID: ' . $reserva->getInstructorId(), // Puedes mejorar esto
               'estado' => ucfirst($reserva->getEstado())
           ];
       }

       return $reservasFormateadas;
   }

   public function getTodasMisReservas($clienteId) {
       $reservasEventos = $this->reservaEventoData->getReservasEventoPorCliente($clienteId);
       $reservasLibres = $this->reservaLibreData->getReservasLibrePorCliente($clienteId);
       $reservasPersonales = $this->getReservasInstructorPersonalPorCliente($clienteId);

       $todas = array_merge(
           $this->_procesarListasDeReservas($reservasEventos, $reservasLibres, false),
           $reservasPersonales
       );

       usort($todas, function($a, $b) {
           return strtotime($b['fecha'] . ' ' . $b['hora']) - strtotime($a['fecha'] . ' ' . $a['hora']);
       });

       return $todas;
   }

    public function getAllReservas()
    {
        $reservasEventos = $this->reservaEventoData->getAllReservasEvento();
        $reservasLibres = $this->reservaLibreData->getAllReservasLibre();

        $todas = $this->_procesarListasDeReservas($reservasEventos, $reservasLibres, true);

        usort($todas, function ($a, $b) {
            $dateComparison = strcmp($b['fecha'], $a['fecha']);
            if ($dateComparison === 0) return strcmp($b['hora'], $a['hora']);
            return $dateComparison;
        });

        return $todas;
    }

    // El resto de los métodos no necesitan cambios
    public function crearReserva($clienteId, $eventoId, $horarioLibreId) { if ($eventoId) { return $this->crearReservaEvento($clienteId, $eventoId); } elseif ($horarioLibreId) { return $this->crearReservaLibre($clienteId, $horarioLibreId); } return "Tipo de reserva no especificado."; }
    private function crearReservaEvento($clienteId, $eventoId) { $evento = $this->eventoData->getEventoById($eventoId); if (!$evento) return "Evento no encontrado."; if(count($this->reservaEventoData->getReservasPorEvento($eventoId)) >= $evento->getAforo()){ return "No hay cupos disponibles para este evento."; } $reserva = new ReservaEvento(0, $clienteId, $eventoId, $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin(), 'activa'); if ($this->reservaEventoData->insertarReservaEvento($reserva)) { return true; } return "Error al procesar la reserva del evento."; }
    private function crearReservaLibre($clienteId, $horarioLibreId) { $horario = $this->horarioLibreData->getHorarioLibrePorId($horarioLibreId); if (!$horario) return "Horario no disponible."; if ($horario->getMatriculados() >= $horario->getCupos()) { return "No hay cupos disponibles para este horario."; } if ($this->reservaLibreData->existeReservaLibre($clienteId, $horarioLibreId)) { return "Ya tienes una reserva para este horario."; } $reserva = new ReservaLibre(0, $clienteId, $horarioLibreId, 1); if ($this->reservaLibreData->insertarReservaLibre($reserva)) { $this->horarioLibreData->incrementarMatriculados($horarioLibreId); return true; } return "Error al crear la reserva de uso libre."; }
    public function crearMultiplesReservasLibre($clienteId, $horarioLibreIds) { $resultados = ['success_count' => 0, 'failure_count' => 0, 'details' => []]; foreach ($horarioLibreIds as $id) { $resultado = $this->crearReservaLibre($clienteId, $id); if ($resultado === true) { $resultados['success_count']++; $resultados['details'][] = ['id' => $id, 'status' => 'success']; } else { $resultados['failure_count']++; $resultados['details'][] = ['id' => $id, 'status' => 'failure', 'reason' => $resultado]; } } return $resultados; }
    public function getReservasLibrePorCliente($clienteId) { return $this->reservaLibreData->getReservasLibrePorCliente($clienteId); }
    public function cancelarReservaLibre($reservaId, $clienteId) { $reserva = $this->reservaLibreData->getReservaLibreById($reservaId); if (!$reserva) return "Reserva no encontrada."; if ($reserva->getClienteId() != $clienteId) return "No tienes permiso para cancelar esta reserva."; if ($this->reservaLibreData->eliminarReservaLibre($reservaId)) { $this->horarioLibreData->decrementarMatriculados($reserva->getHorarioLibreId()); return true; } return "Error al cancelar la reserva."; }
    public function getHorarioLibrePorId($id) { return $this->horarioLibreData->getHorarioLibrePorId($id); }
}