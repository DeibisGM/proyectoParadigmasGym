<?php
try {
    include_once '../data/horarioPersonalData.php';
    include_once '../domain/horarioPersonal.php';
} catch (Exception $e) {
    error_log("ERROR al incluir archivos en business: " . $e->getMessage());
    throw $e;
}

class HorarioPersonalBusiness
{
    private $horarioPersonalData;

    public function __construct()
    {
        try {
            $this->horarioPersonalData = new HorarioPersonalData();
        } catch (Exception $e) {
            error_log("ERROR al crear HorarioPersonalData: " . $e->getMessage());
            throw $e;
        }
    }

    public function crearHorariosPersonales($slots, $instructorId, $duracion = 60)
    {
        $exitos = 0;
        $total = count($slots);
        $errores = [];

        foreach ($slots as $slot) {
            // Validar formato del slot
            if (strpos($slot, ' ') === false) {
                error_log("ERROR: Formato de slot inválido: " . $slot);
                $errores[] = "Formato de slot inválido: $slot";
                continue;
            }

            list($fecha, $hora) = explode(' ', $slot);

            // DEBUG: Verificar que el instructorId no esté vacío
            error_log("DEBUG: Creando horario - Instructor ID: '$instructorId', Fecha: $fecha, Hora: $hora");

            if (empty($instructorId)) {
                error_log("ERROR: Instructor ID está vacío");
                $errores[] = "Instructor ID vacío para slot $slot";
                continue;
            }

            // Validar formato de hora
            if (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
                error_log("ERROR: Formato de hora inválido: " . $hora);
                $errores[] = "Formato de hora inválido: $hora";
                continue;
            }

            try {
                $horario = new HorarioPersonal(0, $fecha, $hora . ':00', $instructorId, null, 'disponible', $duracion, 'personal');
                $resultado = $this->horarioPersonalData->insertarHorarioPersonal($horario);

                if ($resultado) {
                    $exitos++;
                    error_log("✅ SUCCESS: Horario creado con ID: $resultado");
                } else {
                    $errorMsg = "No se pudo crear el horario para $fecha $hora - Posible duplicado";
                    error_log("❌ ERROR: $errorMsg");
                    $errores[] = $errorMsg;
                }
            } catch (Exception $e) {
                $errorMsg = "Excepción al crear horario para $fecha $hora: " . $e->getMessage();
                error_log("❌ EXCEPCIÓN: $errorMsg");
                $errores[] = $errorMsg;
            }
        }

        return [
            'success' => $exitos > 0,
            'created' => $exitos,
            'total' => $total,
            'errors' => $errores
        ];
    }

    public function getHorariosDisponibles($fechaInicio, $fechaFin, $instructorId = null)
    {
        try {
            $horarios = $this->horarioPersonalData->getHorariosPorRangoFechas($fechaInicio, $fechaFin, $instructorId);
            error_log("✅ Business: Obtenidos " . count($horarios) . " horarios disponibles");
            return $horarios;
        } catch (Exception $e) {
            error_log("❌ ERROR en getHorariosDisponibles: " . $e->getMessage());
            return [];
        }
    }

    public function getHorariosPorInstructor($instructorId, $fechaInicio, $fechaFin)
    {
        try {
            return $this->horarioPersonalData->getHorariosPorInstructor($instructorId, $fechaInicio, $fechaFin);
        } catch (Exception $e) {
            error_log("ERROR en getHorariosPorInstructor: " . $e->getMessage());
            return [];
        }
    }

    public function reservarHorarioPersonal($horarioId, $clienteId)
    {
        try {
            error_log("✅ Business: Intentando reservar horario ID: $horarioId para cliente: $clienteId");
            return $this->horarioPersonalData->reservarHorarioPersonal($horarioId, $clienteId);
        } catch (Exception $e) {
            error_log("❌ ERROR en reservarHorarioPersonal: " . $e->getMessage());
            return false;
        }
    }

    public function cancelarReservaPersonal($horarioId, $clienteId)
    {
        try {
            return $this->horarioPersonalData->cancelarReservaPersonal($horarioId, $clienteId);
        } catch (Exception $e) {
            error_log("ERROR en cancelarReservaPersonal: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarHorarioPersonal($horarioId, $instructorId = null)
    {
        try {
            return $this->horarioPersonalData->eliminarHorarioPersonal($horarioId, $instructorId);
        } catch (Exception $e) {
            error_log("ERROR en eliminarHorarioPersonal: " . $e->getMessage());
            return false;
        }
    }

    public function getMisReservasPersonales($clienteId)
    {
        try {
            return $this->horarioPersonalData->getReservasPorCliente($clienteId);
        } catch (Exception $e) {
            error_log("ERROR en getMisReservasPersonales: " . $e->getMessage());
            return [];
        }
    }

    public function getHorariosDeInstructor($instructorId, $fechaInicio, $fechaFin)
    {
        try {
            return $this->horarioPersonalData->getHorariosPorInstructor($instructorId, $fechaInicio, $fechaFin);
        } catch (Exception $e) {
            error_log("ERROR en getHorariosDeInstructor: " . $e->getMessage());
            return [];
        }
    }

    public function getHorarioPersonalPorId($id)
    {
        error_log("✅ Business: Buscando horario por ID: " . $id);

        try {
            return $this->horarioPersonalData->getHorarioPersonalPorId($id);
        } catch (Exception $e) {
            error_log("❌ EXCEPCIÓN en getHorarioPersonalPorId: " . $e->getMessage());
            return null;
        }
    }
}
?>