<?php
include_once '../data/reservaData.php';
include_once '../data/clienteData.php';
include_once '../data/eventoData.php';
include_once '../data/salaData.php';
include_once '../data/salaReservasData.php';
include_once '../domain/reserva.php';

class ReservaBusiness
{
    private $reservaData;
    private $clienteData;
    private $eventoData;
    private $salaData;
    private $salaReservasData;
    private $config;

    public function __construct()
    {
        $this->reservaData = new ReservaData();
        $this->clienteData = new ClienteData();
        $this->eventoData = new EventoData();
        $this->salaData = new SalaData();
        $this->salaReservasData = new SalaReservasData();
        $this->config = include '../config/gymRules.php';
    }

    public function getReservasPorFecha($fecha)
    {
        return $this->reservaData->getReservasPorFecha($fecha);
    }

    public function getAllReservas()
    {
        return $this->reservaData->getAllReservas();
    }

    public function getReservasPorCliente($clienteId)
    {
        return $this->reservaData->getReservasPorCliente($clienteId);
    }

    public function cancelarReserva($reservaId)
    {
        $reserva = $this->reservaData->getReservaPorId($reservaId);

        if (!$reserva) {
            return "No se encontró la reserva.";
        }

        if (new DateTime() > new DateTime($reserva->getFecha() . ' ' . $reserva->getHoraInicio())) {
            return "No se puede cancelar una reserva que ya ha comenzado o pasado.";
        }

        if ($this->reservaData->actualizarEstadoReserva($reservaId, 'cancelada')) {
            // Si es una reserva de uso libre, también debemos liberar la sala
            if ($reserva->getEventoId() === null) {
                $this->salaReservasData->eliminarReservaDeSalaPorReserva($reservaId);
            }
            return true;
        }

        return "Error al cancelar la reserva.";
    }


    public function crearReserva($clienteId, $eventoId, $fecha, $horaInicio, $salaId = null)
    {
        $cliente = $this->clienteData->getClientePorId($clienteId);
        if (!$cliente) {
            return "Cliente no encontrado.";
        }

        $evento = null;
        if ($eventoId) {
            $evento = $this->eventoData->getEventoById($eventoId);
            if (!$evento) {
                return "Evento no encontrado.";
            }
        }

        $puede = $this->puedeReservar($cliente, $fecha, $horaInicio, $evento, $salaId);
        if ($puede !== true) {
            return $puede;
        }

        $horaFin = $evento ? $evento->getHoraFin() : date("H:i:s", strtotime($horaInicio) + $this->config['USO_LIBRE_DURACION_MINUTOS'] * 60);

        $reserva = new Reserva(0, $clienteId, $eventoId, $fecha, $horaInicio, $horaFin, 'activa');

        $reservaId = $this->reservaData->insertarReserva($reserva);

        if ($reservaId) {
            if (!$eventoId && $salaId) {
                // Si es uso libre, creamos la reserva de la sala
                $salaReserva = new SalaReserva(0, [$salaId], null, $fecha, $horaInicio, $horaFin);
                $salaReserva->setReservaId($reservaId); // Vinculamos con la reserva del cliente
                $this->salaReservasData->insertarReservaDeSala($salaReserva);
            }
            return true;
        }

        return "Error al guardar la reserva.";
    }

    private function puedeReservar($cliente, $fecha, $horaInicio, $evento, $salaId)
    {
        // Regla 0: Validaciones de fecha y estado global
        $hoy = new DateTime(date('Y-m-d'));
        $fechaReserva = new DateTime($fecha);
        $diaSemana = date('N', strtotime($fecha));

        if ($fechaReserva < $hoy) return "No se puede reservar en una fecha pasada.";
        $fechaMaxAnticipacion = (clone $hoy)->modify("+" . $this->config['MAX_DIAS_ANTICIPACION'] . " days");
        if ($fechaReserva > $fechaMaxAnticipacion) return "No puede reservar con más de " . $this->config['MAX_DIAS_ANTICIPACION'] . " días de anticipación.";

        if (!in_array($diaSemana, $this->config['DIAS_ABIERTOS'])) return "El gimnasio está cerrado ese día de la semana.";
        if (in_array($fecha, $this->config['DIAS_CERRADOS_ESPECIALES'])) return "El gimnasio permanecerá cerrado en esa fecha.";

        $horaApertura = $this->config['HORARIO_APERTURA'][$diaSemana];
        $horaCierre = $this->config['HORARIO_CIERRE'][$diaSemana];
        if ($horaInicio < $horaApertura || $horaInicio >= $horaCierre) return "La hora de inicio está fuera del horario de apertura ($horaApertura - $horaCierre).";

        foreach ($this->config['HORAS_BLOQUEADAS'][$diaSemana] as $bloqueo) {
            if ($horaInicio >= $bloqueo['inicio'] && $horaInicio < $bloqueo['fin']) return "Este horario está bloqueado por mantenimiento o descanso.";
        }

        // Regla 1: Validaciones de Cliente
        if (!$cliente->getEstado()) return "Su cuenta de cliente está inactiva.";

        $fechaInscripcion = new DateTime($cliente->getInscripcion());
        $fechaLimite = (clone $fechaInscripcion)->modify("+" . $this->config['DURACION_MEMBRESIA_DIAS'] . " days");
        if ($fechaReserva > $fechaLimite) return "Su membresía ha expirado. Fecha límite: " . $fechaLimite->format('Y-m-d');

        // Regla 2: Lógica específica de Evento o Uso Libre
        if ($evento) { // Lógica para eventos
            if (!$evento->getEstado()) return "No se puede reservar en un evento que está inactivo.";

            $reservasDelCliente = $this->reservaData->getReservasPorCliente($cliente->getId());
            foreach ($reservasDelCliente as $res) {
                if ($res->getFecha() == $fecha && $res->getEstado() == 'activa' && $res->getEventoId() == $evento->getId()) {
                    return "Ya tienes una reserva para este evento.";
                }
            }
            $reservasDelEvento = $this->reservaData->getReservasPorEvento($evento->getId());
            if (count($reservasDelEvento) >= $evento->getAforo()) {
                return "Aforo completo para este evento.";
            }
        } else { // Lógica para Uso Libre
            if (empty($salaId)) return "Debe seleccionar una sala para una reserva de uso libre.";

            $sala = $this->salaData->getSalaById($salaId);
            if(!$sala) return "La sala seleccionada no es válida.";
            if(!$sala->getTbsalaestado()) return "La sala seleccionada no está activa.";

            $reservasUsoLibreHoy = $this->reservaData->getReservasUsoLibrePorClienteYFecha($cliente->getId(), $fecha);
            if(count($reservasUsoLibreHoy) >= $this->config['MAX_USO_LIBRE_POR_DIA']){
                return "Ha alcanzado el límite de reservas de 'Uso Libre' para hoy.";
            }

            $horaFin = date("H:i:s", strtotime($horaInicio) + $this->config['USO_LIBRE_DURACION_MINUTOS'] * 60);
            $disponibilidad = $this->salaReservasData->verificarDisponibilidad([$salaId], $fecha, $horaInicio, $horaFin);
            if(isset($disponibilidad['conflictos'])) return "La sala seleccionada no está disponible en ese horario.";

            $reservasEnSala = $this->reservaData->getReservasUsoLibrePorSalaYHorario($salaId, $fecha, $horaInicio, $horaFin);
            if(count($reservasEnSala) >= $sala->getCapacidad()){
                return "Aforo completo para la sala en ese horario.";
            }
        }

        return true;
    }
}

?>