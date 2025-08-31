<?php
// config/gymRules.php

return [
    // REGLAS PARA USO LIBRE
    'USO_LIBRE_AFORO' => 20, // Aforo máximo para personas sin clase
    'USO_LIBRE_DURACION_MINUTOS' => 120, // Cuánto tiempo dura una reserva de uso libre

    // Fechas específicas en las que el gimnasio no abrirá
    'DIAS_CERRADOS_ESPECIALES' => ['2025-12-25', '2026-01-01'],


    'MAX_USO_LIBRE_POR_DIA' => 1,
    'MAX_DIAS_ANTICIPACION' => 30,
    'DURACION_MEMBRESIA_DIAS' => 30,
];