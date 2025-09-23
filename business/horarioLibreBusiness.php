<?php
include_once '../data/horarioLibreData.php';
include_once '../domain/horarioLibre.php';

class HorarioLibreBusiness
{
    private $horarioLibreData;

    public function __construct()
    {
        $this->horarioLibreData = new HorarioLibreData();
    }

    public function crearMultiplesHorarios($slots, $salaId, $instructorId, $cupos)
    {
        $exitos = 0;
        foreach ($slots as $slot) {
            list($fecha, $hora) = explode(' ', $slot);
            $horario = new HorarioLibre(0, $fecha, $hora . ':00', $salaId, $instructorId, $cupos, 0, 1);
            if ($this->horarioLibreData->insertarHorarioLibre($horario)) {
                $exitos++;
            }
        }
        return $exitos;
    }

    public function getHorariosPorRangoDeFechas($fechaInicio, $fechaFin)
    {
        return $this->horarioLibreData->getHorariosPorRangoDeFechas($fechaInicio, $fechaFin);
    }

    public function eliminarHorarioLibre($id)
    {
        return $this->horarioLibreData->eliminarHorarioLibre($id);
    }

    public function getHorarioLibrePorId($id)
    {
        return $this->horarioLibreData->getHorarioLibrePorId($id);
    }
}
?>