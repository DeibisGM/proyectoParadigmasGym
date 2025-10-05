<?php
include_once '../data/horarioPersonalData.php';
include_once '../domain/horarioPersonal.php';

class HorarioPersonalBusiness
{
    private $horarioPersonalData;

    public function __construct()
    {
        $this->horarioPersonalData = new HorarioPersonalData();
    }

    public function crearHorariosPersonales($slots, $instructorId, $duracion = 60)
    {
        $exitos = 0;
        foreach ($slots as $slot) {
            list($fecha, $hora) = explode(' ', $slot);
            $horario = new HorarioPersonal(0, $fecha, $hora . ':00', $instructorId, null, 'disponible', $duracion, 'personal');
            if ($this->horarioPersonalData->insertarHorarioPersonal($horario)) {
                $exitos++;
            }
        }
        return $exitos;
    }

    public function getHorariosDisponibles($fechaInicio, $fechaFin, $instructorId = null)
    {
        return $this->horarioPersonalData->getHorariosPorRangoFechas($fechaInicio, $fechaFin, $instructorId);
    }

    public function getHorariosPorInstructor($instructorId, $fechaInicio, $fechaFin)
    {
        return $this->horarioPersonalData->getHorariosDisponiblesPorInstructor($instructorId, $fechaInicio, $fechaFin);
    }

    public function reservarHorarioPersonal($horarioId, $clienteId)
    {
        return $this->horarioPersonalData->reservarHorarioPersonal($horarioId, $clienteId);
    }

    public function cancelarReservaPersonal($horarioId, $clienteId)
    {
        return $this->horarioPersonalData->cancelarReservaPersonal($horarioId, $clienteId);
    }

    public function getMisReservasPersonales($clienteId)
    {
        return $this->horarioPersonalData->getReservasPorCliente($clienteId);
    }

    public function getHorarioPersonalPorId($id)
    {
        // Método simple - puedes mejorarlo después
        $horarios = $this->horarioPersonalData->getHorariosPorRangoFechas('2000-01-01', '2100-01-01');
        foreach ($horarios as $horario) {
            if ($horario->getId() == $id) {
                return $horario;
            }
        }
        return null;
    }
}
?>