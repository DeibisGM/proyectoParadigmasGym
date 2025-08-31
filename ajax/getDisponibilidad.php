<?php
// ajax/getDisponibilidad.php
header('Content-Type: application/json');

include_once '../business/reservaBusiness.php';
include_once '../business/horarioBusiness.php'; // Nuevo

// Validar que se ha enviado una fecha
if (!isset($_GET['fecha'])) {
    echo json_encode(['error' => 'Fecha no proporcionada']);
    exit;
}

$fechaSeleccionada = $_GET['fecha'];
$diaSemana = date('N', strtotime($fechaSeleccionada));

// Cargar reglas y horario desde sus respectivas fuentes
$reglasGimnasio = include '../config/gymRules.php'; // Para reglas que no son de horario
$horarioBusiness = new HorarioBusiness();
$horarioDia = $horarioBusiness->getHorarioDelDia($diaSemana);

// Comprobar si el gimnasio está abierto ese día
if (!$horarioDia || !$horarioDia->isActivo() || in_array($fechaSeleccionada, $reglasGimnasio['DIAS_CERRADOS_ESPECIALES'])) {
    echo json_encode(['estaAbierto' => false, 'slots' => []]);
    exit;
}

// Obtener datos necesarios
$reservaBusiness = new ReservaBusiness();
$reservasEnFecha = $reservaBusiness->getReservasPorFecha($fechaSeleccionada);

$horaApertura = $horarioDia->getApertura();
$horaCierre = $horarioDia->getCierre();
$bloqueos = $horarioDia->getBloqueos();
$duracionSlot = $reglasGimnasio['USO_LIBRE_DURACION_MINUTOS'];
$aforoMaximo = $reglasGimnasio['USO_LIBRE_AFORO'];

$slotsDisponibles = [];
$horaActual = new DateTime($horaApertura);
$horaFinJornada = new DateTime($horaCierre);

// Iterar por cada posible slot de tiempo en el día
while ($horaActual < $horaFinJornada) {
    $slotInicio = $horaActual->format('H:i:s');
    $slotFin = (clone $horaActual)->modify("+" . $duracionSlot . " minutes")->format('H:i:s');

    // No generar slots que terminen después de la hora de cierre
    if ($slotFin > $horaCierre) {
        break;
    }

    // Comprobar si el slot está dentro de una hora bloqueada
    $estaBloqueado = false;
    foreach ($bloqueos as $bloqueo) {
        if ($slotInicio < $bloqueo['fin'] && $slotFin > $bloqueo['inicio']) {
            $estaBloqueado = true;
            break;
        }
    }
    if ($estaBloqueado) {
        $horaActual->modify("+30 minutes"); // Avanzar de 30 en 30 para buscar el siguiente slot libre
        continue;
    }

    // Comprobar el aforo para el slot actual
    $reservasSolapadas = 0;
    foreach ($reservasEnFecha as $reserva) {
        // Solo contar reservas de uso libre (sin evento)
        if ($reserva->getEventoId() === null && $reserva->getEstado() === 'activa') {
            if ($slotInicio < $reserva->getHoraFin() && $slotFin > $reserva->getHoraInicio()) {
                $reservasSolapadas++;
            }
        }
    }

    // Si hay espacio, añadir el slot a la lista
    if ($reservasSolapadas < $aforoMaximo) {
        $slotsDisponibles[] = $slotInicio;
    }

    // Avanzar al siguiente slot potencial (cada 30 minutos)
    $horaActual->modify("+30 minutes");
}

echo json_encode(['estaAbierto' => true, 'slots' => $slotsDisponibles]);
