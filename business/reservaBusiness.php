<?php
include_once '../data/reservaData.php';
include_once '../data/clienteData.php';
include_once '../data/eventoData.php'; // Asegurarse de incluir esto
include_once '../domain/reserva.php';

class ReservaBusiness
{
    private $reservaData;
    private $clienteData;
    private $config;

    public function __construct()
    {
        $this->reservaData = new ReservaData();
        $this->clienteData = new ClienteData();
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

    public function cancelarReserva($reservaId, $clienteId, $tipoUsuario)
    {
        // El administrador siempre puede cancelar. El cliente solo si la reserva es suya.
        if ($tipoUsuario == 'admin') {
            $reservaACancelar = $this->reservaData->getReservaPorId($reservaId); // Necesitarás añadir esta función en Data
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

        // No se puede cancelar una reserva que ya ha pasado
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
        // 1. Obtener todos los datos necesarios
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

        // 2. Validar si el cliente puede realizar la reserva
        $puede = $this->puedeReservar($cliente, $fecha, $horaInicio, $evento, $reservasEnFecha, $reservasDelCliente);
        if ($puede !== true) {
            return $puede; // Devuelve el mensaje de error específico
        }

        // 3. Calcular la hora de fin real
        $horaFinReal = '';
        if ($evento) {
            $horaFinReal = $evento->getHoraFin();
        } else {
            $diaSemana = date('N', strtotime($fecha));
            $horaFinPotencial = date("H:i:s", strtotime($horaInicio) + $this->config['USO_LIBRE_DURACION_MINUTOS'] * 60);
            $horaFinReal = $horaFinPotencial;

            // Acortar la reserva si termina después del cierre del gimnasio
            $horaCierreDia = $this->config['HORARIO_CIERRE'][$diaSemana];
            if ($horaFinReal > $horaCierreDia) {
                $horaFinReal = $horaCierreDia;
            }

            // Acortar la reserva si se cruza con una hora bloqueada (ej. limpieza)
            foreach ($this->config['HORAS_BLOQUEADAS'][$diaSemana] as $bloqueo) {
                if ($horaInicio < $bloqueo['inicio'] && $horaFinReal > $bloqueo['inicio']) {
                    $horaFinReal = $bloqueo['inicio'];
                }
            }
        }

        // 4. Crear el objeto y guardarlo
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

        if ($fechaReserva < $hoy) return "No se puede reservar en una fecha pasada.";

        // Regla 1: Vigencia de la membresía
        $fechaInscripcion = new DateTime($cliente->getInscripcion());
        $fechaLimite = (clone $fechaInscripcion)->modify("+" . $this->config['DURACION_MEMBRESIA_DIAS'] . " days");
        if ($fechaReserva > $fechaLimite) return "Su membresía ha expirado. Fecha límite: " . $fechaLimite->format('Y-m-d');

        // Regla 2: Anticipación máxima
        $fechaMaxAnticipacion = (clone $hoy)->modify("+" . $this->config['MAX_DIAS_ANTICIPACION'] . " days");
        if ($fechaReserva > $fechaMaxAnticipacion) return "No puede reservar con más de " . $this->config['MAX_DIAS_ANTICIPACION'] . " días de anticipación.";

        // Regla 3: Gimnasio abierto ese día de la semana
        if (!in_array($diaSemana, $this->config['DIAS_ABIERTOS'])) return "El gimnasio está cerrado ese día de la semana.";

        // Regla 4: No es un día festivo o cerrado especial
        if (in_array($fecha, $this->config['DIAS_CERRADOS_ESPECIALES'])) return "El gimnasio permanecerá cerrado en esa fecha.";

        // --- INICIO DE LA LÓGICA MODIFICADA ---
        // Regla 5: Límites de reserva por tipo (Uso Libre vs. Evento)
        if ($evento) {
            // Si es un evento, solo validamos que no se haya inscrito ya en ESE MISMO evento.
            foreach ($reservasDelCliente as $res) {
                if ($res->getFecha() == $fecha && $res->getEstado() == 'activa' && $res->getEventoId() == $evento->getId()) {
                    return "Ya tienes una reserva para este mismo evento.";
                }
            }
        } else {
            // Si es Uso Libre, contamos cuántas reservas de Uso Libre tiene ya para ese día.
            $reservasUsoLibreHoy = 0;
            foreach ($reservasDelCliente as $res) {
                if ($res->getFecha() == $fecha && $res->getEstado() == 'activa' && $res->getEventoId() === null) {
                    $reservasUsoLibreHoy++;
                }
            }
            if ($reservasUsoLibreHoy >= $this->config['MAX_USO_LIBRE_POR_DIA']) {
                return "Ya ha alcanzado el límite de reservas de 'Uso Libre' para este día.";
            }
        }
        // --- FIN DE LA LÓGICA MODIFICADA ---

        // Regla 6: Horario de apertura
        $horaApertura = $this->config['HORARIO_APERTURA'][$diaSemana];
        $horaCierre = $this->config['HORARIO_CIERRE'][$diaSemana];
        if ($horaInicio < $horaApertura || $horaInicio >= $horaCierre) return "La hora de inicio está fuera del horario de apertura ($horaApertura - $horaCierre).";

        // Regla 7: No empezar en una hora bloqueada
        foreach ($this->config['HORAS_BLOQUEADAS'][$diaSemana] as $bloqueo) {
            if ($horaInicio >= $bloqueo['inicio'] && $horaInicio < $bloqueo['fin']) return "Este horario está bloqueado por mantenimiento o descanso.";
        }

        // Regla 8: Comprobar aforo disponible
        $aforo = $evento ? $evento->getAforo() : $this->config['USO_LIBRE_AFORO'];
        $horaFin = $evento ? $evento->getHoraFin() : date("H:i:s", strtotime($horaInicio) + $this->config['USO_LIBRE_DURACION_MINUTOS'] * 60);
        $reservasSolapadas = 0;

        foreach ($reservasEnFecha as $res) {
            if ($res->getEstado() == 'activa') {
                if ($evento && $res->getEventoId() == $evento->getId()) {
                    $reservasSolapadas++;
                } else if (!$evento && !$res->getEventoId()) { // Es uso libre, chequear solapamiento de tiempo
                    if ($horaInicio < $res->getHoraFin() && $horaFin > $res->getHoraInicio()) {
                        $reservasSolapadas++;
                    }
                }
            }
        }
        if ($reservasSolapadas >= $aforo) return "Aforo completo para este horario/evento.";

        return true; // Si pasa todas las validaciones
    }
}

?>