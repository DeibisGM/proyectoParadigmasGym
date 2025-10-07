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
            if ($incluirClienteNombre) {
                $reserva['cliente'] = $r->getClienteNombre();
                $reserva['responsable'] = $r->getClienteResponsableNombre();
            }
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
            if ($incluirClienteNombre) {
                $reserva['cliente'] = $r->getClienteNombre();
                $reserva['responsable'] = $r->getClienteResponsableNombre(); // NUEVO
            }
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
                'descripcion' => 'Sesión de ' . $reserva->getDuracion() . ' min',
                'instructor' => $reserva->getInstructorNombre() ?? 'N/A',
                'estado' => ucfirst($reserva->getEstado())
            ];
        }

        return $reservasFormateadas;
    }

    public function getTodasMisReservas($clienteId)
    {
        $reservasEventos = $this->reservaEventoData->getReservasEventoPorCliente($clienteId);
        $reservasLibres = $this->reservaLibreData->getReservasLibrePorCliente($clienteId);
        $reservasPersonales = $this->getReservasInstructorPersonalPorCliente($clienteId);

        $todas = array_merge(
            $this->_procesarListasDeReservas($reservasEventos, $reservasLibres, false),
            $reservasPersonales
        );

        usort($todas, function ($a, $b) {
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

    public function crearReserva($clienteId, $eventoId, $horarioLibreId, $idsInvitados = '')
    {
        if ($eventoId) {
            return $this->crearReservaEventoAgrupada($clienteId, $eventoId, true, '', 0);
        } elseif ($horarioLibreId) {
            return $this->crearReservaLibreAgrupada($clienteId, [$horarioLibreId], true, $idsInvitados);
        }
        return "Tipo de reserva no especificado.";
    }

    public function crearReservaEventoAgrupada($responsableId, $eventoId, $incluirResponsable, $idsInvitados, $invitadosAnonimos)
    {
        $evento = $this->eventoData->getEventoById($eventoId);
        if (!$evento) return "Evento no encontrado.";

        if ($evento->getTipo() === 'privado' && $invitadosAnonimos > 0) {
            return "No se pueden añadir invitados anónimos a un evento privado.";
        }

        $clientesAReservar = [];
        $erroresId = [];

        if ($incluirResponsable) {
            $clientesAReservar[] = $responsableId;
        }

        if (!empty($idsInvitados)) {
            $ids = preg_split('/[ ,]+/', $idsInvitados, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($ids as $id) {
                if (!is_numeric(trim($id))) {
                    $erroresId[] = $id . " (inválido)";
                    continue;
                }
                $cliente = $this->clienteData->getClientePorId(trim($id));
                if ($cliente) {
                    $clientesAReservar[] = $cliente->getId();
                } else {
                    $erroresId[] = $id;
                }
            }
        }

        if (!empty($erroresId)) {
            return "No se encontraron clientes con los siguientes IDs: " . implode(', ', $erroresId);
        }

        $clientesAReservar = array_unique($clientesAReservar);
        $totalSpots = count($clientesAReservar) + $invitadosAnonimos;

        if ($totalSpots === 0) {
            return "Debe seleccionar al menos una persona para la reserva.";
        }

        $reservasActuales = count($this->reservaEventoData->getReservasPorEvento($eventoId));
        if (($reservasActuales + $totalSpots) > $evento->getAforo()) {
            $disponibles = $evento->getAforo() - $reservasActuales;
            return "No hay suficientes cupos. Solicitados: {$totalSpots}, Disponibles: {$disponibles}.";
        }

        foreach ($clientesAReservar as $clienteId) {
            if ($this->reservaEventoData->clienteYaTieneReserva($clienteId, $eventoId)) {
                $clienteInfo = $this->clienteData->getClientePorId($clienteId);
                return "El cliente con ID " . $clienteInfo->getId() . " (" . $clienteInfo->getNombre() . ") ya tiene una reserva para este evento.";
            }
        }

        $exitos = 0;
        foreach ($clientesAReservar as $clienteId) {
            $reserva = new ReservaEvento(0, $clienteId, $eventoId, $responsableId, $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin(), 1);
            if ($this->reservaEventoData->insertarReservaEvento($reserva)) {
                $exitos++;
            }
        }

        for ($i = 0; $i < $invitadosAnonimos; $i++) {
            $reserva = new ReservaEvento(0, $responsableId, $eventoId, $responsableId, $evento->getFecha(), $evento->getHoraInicio(), $evento->getHoraFin(), 1);
            if ($this->reservaEventoData->insertarReservaEvento($reserva)) {
                $exitos++;
            }
        }

        if ($exitos === $totalSpots) {
            return true;
        } else {
            return "Se crearon {$exitos} de {$totalSpots} reservas. Ocurrió un error parcial.";
        }
    }

    public function crearReservaLibreAgrupada($responsableId, $horarioLibreIds, $incluirResponsable, $idsInvitados)
    {
        $resultados = ['success_count' => 0, 'failure_count' => 0, 'details' => []];

        $clientesAReservarIds = [];
        if ($incluirResponsable) {
            $clientesAReservarIds[] = $responsableId;
        }

        if (!empty($idsInvitados)) {
            $ids = preg_split('/[ ,]+/', $idsInvitados, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($ids as $id) {
                if (!is_numeric(trim($id))) continue;
                $cliente = $this->clienteData->getClientePorId(trim($id));
                if ($cliente) {
                    $clientesAReservarIds[] = $cliente->getId();
                } else {
                    $resultados['failure_count']++;
                    $resultados['details'][] = ['id' => 'ID ' . $id, 'status' => 'failure', 'reason' => 'ID de cliente no encontrado.'];
                }
            }
        }
        $clientesAReservarIds = array_unique($clientesAReservarIds);

        if (empty($clientesAReservarIds)) {
            return ['success_count' => 0, 'failure_count' => count($horarioLibreIds), 'details' => [['id' => 'General', 'status' => 'failure', 'reason' => 'No se especificó ningún cliente para la reserva.']]];
        }

        foreach ($horarioLibreIds as $horarioId) {
            $horario = $this->horarioLibreData->getHorarioLibrePorId($horarioId);
            if (!$horario) {
                $resultados['failure_count'] += count($clientesAReservarIds);
                $resultados['details'][] = ['id' => $horarioId, 'status' => 'failure', 'reason' => 'El horario seleccionado ya no existe.'];
                continue;
            }

            $cuposDisponibles = $horario->getCupos() - $horario->getMatriculados();
            if (count($clientesAReservarIds) > $cuposDisponibles) {
                $resultados['failure_count'] += count($clientesAReservarIds);
                $resultados['details'][] = ['id' => $horarioId, 'status' => 'failure', 'reason' => "No hay suficientes cupos. Solicitados: " . count($clientesAReservarIds) . ", Disponibles: " . $cuposDisponibles];
                continue;
            }

            foreach ($clientesAReservarIds as $clienteId) {
                if ($this->reservaLibreData->existeReservaLibre($clienteId, $horarioId)) {
                    $resultados['failure_count']++;
                    $resultados['details'][] = ['id' => $horarioId, 'status' => 'failure', 'reason' => "El cliente ID {$clienteId} ya tiene una reserva en este horario."];
                    continue;
                }

                $reserva = new ReservaLibre(0, $clienteId, $horarioId, $responsableId, 1);
                if ($this->reservaLibreData->insertarReservaLibre($reserva)) {
                    $this->horarioLibreData->incrementarMatriculados($horarioId);
                    $resultados['success_count']++;
                    $resultados['details'][] = ['id' => $horarioId, 'status' => 'success'];
                } else {
                    $resultados['failure_count']++;
                    $resultados['details'][] = ['id' => $horarioId, 'status' => 'failure', 'reason' => "Error de base de datos al guardar la reserva para el cliente ID {$clienteId}."];
                }
            }
        }
        return $resultados;
    }

    public function cancelarReservaLibre($reservaId, $clienteId)
    {
        $reserva = $this->reservaLibreData->getReservaLibreById($reservaId);
        if (!$reserva) return "Reserva no encontrada.";
        if ($reserva->getClienteId() != $clienteId && $reserva->getClienteResponsableId() != $clienteId) {
            return "No tienes permiso para cancelar esta reserva.";
        }
        if ($this->reservaLibreData->eliminarReservaLibre($reservaId)) {
            $this->horarioLibreData->decrementarMatriculados($reserva->getHorarioLibreId());
            return true;
        }
        return "Error al cancelar la reserva.";
    }

    // MÉTODO RESTAURADO
    public function getReservasLibrePorCliente($clienteId)
    {
        return $this->reservaLibreData->getReservasLibrePorCliente($clienteId);
    }

    public function getHorarioLibrePorId($id)
    {
        return $this->horarioLibreData->getHorarioLibrePorId($id);
    }
}