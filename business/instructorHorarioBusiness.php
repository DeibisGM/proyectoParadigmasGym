<?php
include '../data/instructorHorarioData.php';

class InstructorHorarioBusiness
{
    private $instructorHorarioData;

    public function __construct()
    {
        $this->instructorHorarioData = new InstructorHorarioData();
    }

    public function insertarTBInstructorHorario($instructorHorario)
    {
        // Validar que no haya horarios superpuestos
        if ($this->instructorHorarioData->existeHorarioSuperpuesto(
            $instructorHorario->getInstructorId(),
            $instructorHorario->getDia(),
            $instructorHorario->getHoraInicio(),
            $instructorHorario->getHoraFin()
        )) {
            throw new Exception("El instructor ya tiene un horario en ese rango de tiempo.");
        }

        return $this->instructorHorarioData->insertarTBInstructorHorario($instructorHorario);
    }

    public function actualizarTBInstructorHorario($instructorHorario)
    {
        // Validar que no haya horarios superpuestos (excluyendo el actual)
        if ($this->instructorHorarioData->existeHorarioSuperpuesto(
            $instructorHorario->getInstructorId(),
            $instructorHorario->getDia(),
            $instructorHorario->getHoraInicio(),
            $instructorHorario->getHoraFin(),
            $instructorHorario->getId()
        )) {
            throw new Exception("El instructor ya tiene un horario en ese rango de tiempo.");
        }

        return $this->instructorHorarioData->actualizarTBInstructorHorario($instructorHorario);
    }

    public function eliminarTBInstructorHorario($id)
    {
        return $this->instructorHorarioData->eliminarTBInstructorHorario($id);
    }

    public function getAllTBInstructorHorario($esAdmin = false)
    {
        return $this->instructorHorarioData->getAllTBInstructorHorario($esAdmin);
    }

    public function getHorariosPorInstructor($instructorId)
    {
        return $this->instructorHorarioData->getHorariosPorInstructor($instructorId);
    }

    public function getHorarioPorId($id)
    {
        return $this->instructorHorarioData->getHorarioPorId($id);
    }

    public function getNextHorarioId()
    {
        return $this->instructorHorarioData->getNextHorarioId();
    }
}
?>