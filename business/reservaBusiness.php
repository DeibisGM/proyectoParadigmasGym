<?php
include_once '../data/reservaData.php';
include_once '../data/clienteData.php';
include_once '../data/eventoData.php';
include_once '../domain/reserva.php';
include_once '../business/horarioBusiness.php';

class ReservaBusiness
{
    private $reservaData;
    private $clienteData;
    private $horarioBusiness;
    private $reglasGimnasio;

    public function __construct()
    {
        $this->reservaData = new ReservaData();
        $this->clienteData = new ClienteData();
        $this->horarioBusiness = new HorarioBusiness();
        $this->reglasGimnasio = include '../config/gymRules.php';
    }

    public function getReservasPorFecha($fecha)
    {
        return $this->reservaData->getReservasPorFecha($fecha);
    }

    public function getReservasPorCliente($clienteId)
    {
        return $this->reservaData->getReservasPorCliente($clienteId);
    }

    public function cancelarReserva($reservaId, $clienteId, $tipoUsuario)
    {
        if ($tipoUsuario == 'admin') {
            $reservaACancelar = $this->reservaData->getReservaPorId($reservaId);
        } else {
            $reservasCliente = $this->getReservasPorCliente($clienteId);
            $reservaACancelar = null;
            foreach ($reservasCliente as $reserva) {
                if ($reserva->getId() == $reservaId) {
                    $reservaACancelar = $reserva;
                    break;
                }
            }
        }

        if (!$reservaACancelar) {
            return "No se encontró la reserva o no tiene permisos.";
        }

        if (new DateTime() > new DateTime($reservaACancelar->getFecha() . ' ' . $reservaACancelar->getHoraInicio())) {
            return "No se puede cancelar una reserva que ya ha comenzado o pasado.";
        }

        if ($this->reservaData->actualizarEstadoReserva($reservaId, 'cancelada')) {
            return true;
        }

        return "Error al actualizar el estado en la base de datos.";
    }


    public function crearReserva($clienteId, $eventoId, $fecha, $horaInicio)
    {
        $cliente = $this->clienteData->getClientePorId($clienteId);
        $reservasEnFecha = $this->reservaData->getReservasPorFecha($fecha);
        $reservasDelCliente = $this->reservaData->getReservasPorCliente($clienteId);

        $evento = null;
        if ($eventoId) {
            $eventoData = new EventoData();
            $todosEventos = $eventoData->getAllEventos();
            foreach ($todosEventos as $ev) {
                if ($ev->getId() == $eventoId) {
                    $evento = $ev;
                    break;
                }
            }
        }

        $puede = $this->puedeReservar($cliente, $fecha, $horaInicio, $evento, $reservasEnFecha, $reservasDelCliente);
        if ($puede !== true) {
            return $puede;
        }

        $diaSemana = date('N', strtotime($fecha));
        $horarioDia = $this->horarioBusiness->getHorarioDelDia($diaSemana);

        $horaFinReal = '';
        if ($evento) {
            $horaFinReal = $evento->getHoraFin();
        } else {
            $horaFinPotencial = date("H:i:s", strtotime($horaInicio) + $this->reglasGimnasio['USO_LIBRE_DURACION_MINUTOS'] * 60);
            $horaFinReal = $horaFinPotencial;

            $horaCierreDia = $horarioDia->getCierre();
            if ($horaFinReal > $horaCierreDia) {
                $horaFinReal = $horaCierreDia;
            }

            foreach ($horarioDia->getBloqueos() as $bloqueo) {
                if ($horaInicio < $bloqueo['inicio'] && $horaFinReal > $bloqueo['inicio']) {
                    $horaFinReal = $bloqueo['inicio'];
                }
            }
        }

        $reserva = new Reserva(0, $clienteId, $eventoId, $fecha, $horaInicio, $horaFinReal, 'activa');
        if ($this->reservaData->insertarReserva($reserva)) {
            return true;
        }

        return "Error al guardar la reserva en la base de datos.";
    }

    private function puedeReservar($cliente, $fecha, $horaInicio, $evento, $reservasEnFecha, $reservasDelCliente)
    {
        $diaSemana = date('N', strtotime($fecha));
        $fechaReserva = new DateTime($fecha);
        $hoy = new DateTime(date('Y-m-d'));
        $horarioDia = $this->horarioBusiness->getHorarioDelDia($diaSemana);

        if ($fechaReserva < $hoy) return "No se puede reservar en una fecha pasada.";

        $fechaInscripcion = new DateTime($cliente->getInscripcion());
        $fechaLimite = (clone $fechaInscripcion)->modify("+" . $this->reglasGimnasio['DURACION_MEMBRESIA_DIAS'] . " days");
        if ($fechaReserva > $fechaLimite) return "Su membresía ha expirado. Fecha límite: " . $fechaLimite->format('Y-m-d');

        $fechaMaxAnticipacion = (clone $hoy)->modify("+" . $this->reglasGimnasio['MAX_DIAS_ANTICIPACION'] . " days");
        if ($fechaReserva > $fechaMaxAnticipacion) return "No puede reservar con más de " . $this->reglasGimnasio['MAX_DIAS_ANTICIPACION'] . " días de anticipación.";

        if (!$horarioDia || !$horarioDia->isActivo()) return "El gimnasio está cerrado ese día de la semana.";

        if (in_array($fecha, $this->reglasGimnasio['DIAS_CERRADOS_ESPECIALES'])) return "El gimnasio permanecerá cerrado en esa fecha.";

        if ($evento) {
            foreach ($reservasDelCliente as $res) {
                if ($res->getFecha() == $fecha && $res->getEstado() == 'activa' && $res->getEventoId() == $evento->getId()) {
                    return "Ya tienes una reserva para este mismo evento.";
                }
            }
        } else {
            $reservasUsoLibreHoy = 0;
            foreach ($reservasDelCliente as $res) {
                if ($res->getFecha() == $fecha && $res->getEstado() == 'activa' && $res->getEventoId() === null) {
                    $reservasUsoLibreHoy++;
                }
            }
            if ($reservasUsoLibreHoy >= $this->reglasGimnasio['MAX_USO_LIBRE_POR_DIA']) {
                return "Ya ha alcanzado el límite de reservas de 'Uso Libre' para este día.";
            }
        }

        $horaApertura = $horarioDia->getApertura();
        $horaCierre = $horarioDia->getCierre();
        if ($horaInicio < $horaApertura || $horaInicio >= $horaCierre) return "La hora de inicio está fuera del horario de apertura ($horaApertura - $horaCierre).";

        foreach ($horarioDia->getBloqueos() as $bloqueo) {
            if ($horaInicio >= $bloqueo['inicio'] && $horaInicio < $bloqueo['fin']) return "Este horario está bloqueado por mantenimiento o descanso.";
        }

        $aforo = $evento ? $evento->getAforo() : $this->reglasGimnasio['USO_LIBRE_AFORO'];
        $horaFin = $evento ? $evento->getHoraFin() : date("H:i:s", strtotime($horaInicio) + $this->reglasGimnasio['USO_LIBRE_DURACION_MINUTOS'] * 60);
        $reservasSolapadas = 0;

        foreach ($reservasEnFecha as $res) {
            if ($res->getEstado() == 'activa') {
                if ($evento && $res->getEventoId() == $evento->getId()) {
                    $reservasSolapadas++;
                } else if (!$evento && !$res->getEventoId()) {
                    if ($horaInicio < $res->getHoraFin() && $horaFin > $res->getHoraInicio()) {
                        $reservasSolapadas++;
                    }
                }
            }
        }
        if ($reservasSolapadas >= $aforo) return "Aforo completo para este horario/evento.";

        return true;
    }

    public function getAllReservas()
    {
        return $this->reservaData->getAllReservas();
    }

    public function getReservasPorEvento($eventoId)
    {
        return $this->reservaData->getReservasPorEvento($eventoId);
    }
}

?>