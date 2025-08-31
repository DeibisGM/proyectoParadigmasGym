<?php
session_start();
header('Content-Type: application/json');

include_once '../business/reservaBusiness.php';
include_once '../business/eventoBusiness.php';

$response = ['success' => false, 'message' => 'Fecha no proporcionada.', 'data' => []];

if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
    $diaSemana = date('N', strtotime($fecha));

    $config = include '../config/gymRules.php';
    $reservaBusiness = new ReservaBusiness();
    $eventoBusiness = new EventoBusiness();

    $reservasDelDia = $reservaBusiness->getReservasPorFecha($fecha);
    $eventos = $eventoBusiness->getAllEventos();

    $disponibilidad = [
        'eventos' => [],
        'uso_libre_slots' => [],
        'dia_info' => null
    ];

    // 1. Calcular disponibilidad para EVENTOS
    foreach ($eventos as $evento) {
        // CAMBIO: Compara la fecha exacta, no el día de la semana
        if ($evento->getFecha() == $fecha && $evento->getEstado() == 1) {
            $reservasParaEvento = 0;
            foreach ($reservasDelDia as $reserva) {
                if ($reserva->getEventoId() == $evento->getId()) {
                    $reservasParaEvento++;
                }
            }
            $disponibilidad['eventos'][] = [
                'id' => $evento->getId(), 'nombre' => $evento->getNombre(), 'hora_inicio' => $evento->getHoraInicio(),
                'hora_fin' => $evento->getHoraFin(), 'ocupados' => $reservasParaEvento, 'aforo' => $evento->getAforo(),
                'disponibles' => $evento->getAforo() - $reservasParaEvento, 'instructor' => $evento->getInstructorNombre()
            ];
        }
    }

    // 2. CALCULAR DISPONIBILIDAD PARA USO LIBRE (sin cambios en esta parte)
    if (in_array($diaSemana, $config['DIAS_ABIERTOS'])) {
        $horaInicioGym = strtotime($config['HORARIO_APERTURA'][$diaSemana]);
        $horaCierreGym = strtotime($config['HORARIO_CIERRE'][$diaSemana]);
        $duracionSesion = $config['USO_LIBRE_DURACION_MINUTOS'] * 60;
        $aforoLibre = $config['USO_LIBRE_AFORO'];
        $bloqueosDia = $config['HORAS_BLOQUEADAS'][$diaSemana];

        $disponibilidad['dia_info'] = [
            'cierre' => $config['HORARIO_CIERRE'][$diaSemana],
            'bloqueos' => $bloqueosDia
        ];

        for ($time = $horaInicioGym; $time < $horaCierreGym; $time += 3600) {
            $slotHoraInicio = date('H:i:s', $time);
            $slotHoraFin = date('H:i:s', $time + $duracionSesion);

            $esValido = true;
            if (strtotime($slotHoraFin) > $horaCierreGym) $esValido = false;
            foreach ($bloqueosDia as $bloqueo) {
                if ($slotHoraInicio >= $bloqueo['inicio'] && $slotHoraInicio < $bloqueo['fin']) {
                    $esValido = false;
                    break;
                }
            }
            if (!$esValido) continue;

            $ocupados = 0;
            foreach ($reservasDelDia as $reserva) {
                if ($reserva->getEventoId() === null && $slotHoraInicio < $reserva->getHoraFin() && $slotHoraFin > $reserva->getHoraInicio()) {
                    $ocupados++;
                }
            }

            $disponibilidad['uso_libre_slots'][date('H:i', $time)] = [
                'ocupados' => $ocupados,
                'disponibles' => $aforoLibre - $ocupados
            ];
        }
    }

    $response['success'] = true;
    $response['message'] = 'Disponibilidad cargada.';
    $response['data'] = $disponibilidad;
}

echo json_encode($response);