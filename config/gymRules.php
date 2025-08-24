<?php
// config/gymRules.php

return [
    // REGLAS PARA USO LIBRE
    'USO_LIBRE_AFORO' => 20, // Aforo máximo para personas sin clase
    'USO_LIBRE_DURACION_MINUTOS' => 120, // Cuánto tiempo dura una reserva de uso libre

    // HORARIOS Y REGLAS GENERALES DEL GIMNASIO
    'DIAS_ABIERTOS' => [1, 2, 3, 4, 5, 6], // Días de la semana que abre el gym (1=Lunes, 7=Domingo)

    'HORARIO_APERTURA' => [ // Hora de apertura para cada día
        1 => '06:00', 2 => '06:00', 3 => '06:00', 4 => '06:00', 5 => '06:00', 6 => '08:00', 7 => null
    ],
    'HORARIO_CIERRE' => [ // Hora de cierre para cada día
        1 => '21:00', 2 => '21:00', 3 => '21:00', 4 => '21:00', 5 => '21:00', 6 => '16:00', 7 => null
    ],

    // Periodos dentro del horario de apertura en los que no se puede reservar (ej. limpieza, almuerzo)
    'HORAS_BLOQUEADAS' => [
        1 => [['inicio' => '12:00', 'fin' => '13:00']],
        2 => [['inicio' => '12:00', 'fin' => '13:00']],
        3 => [['inicio' => '12:00', 'fin' => '13:00']],
        4 => [['inicio' => '12:00', 'fin' => '13:00']],
        5 => [['inicio' => '12:00', 'fin' => '13:00']],
        6 => [],
        7 => [],
    ],

    // Fechas específicas en las que el gimnasio no abrirá
    'DIAS_CERRADOS_ESPECIALES' => ['2025-12-25', '2026-01-01'],

    // REGLAS DE RESERVA PARA CLIENTES
    // --- LÍNEA MODIFICADA ---
    'MAX_USO_LIBRE_POR_DIA' => 1, // Cuántas reservas de USO LIBRE puede tener un cliente al día. Los eventos no cuentan.
    'MAX_DIAS_ANTICIPACION' => 30, // Con cuántos días de antelación se puede reservar
    'DURACION_MEMBRESIA_DIAS' => 30, // Cuántos días es válida una membresía desde la fecha de inscripción
];