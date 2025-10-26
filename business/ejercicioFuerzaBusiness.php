<?php
include_once '../data/ejercicioFuerzaData.php';

class EjercicioFuerzaBusiness
{
    private $ejercicioFuerzaData;

    public function __construct()
    {
        $this->ejercicioFuerzaData = new EjercicioFuerzaData();
    }

    public function insertarTbejerciciofuerza($ejercicio)
    {
        return $this->ejercicioFuerzaData->insertarTbejerciciofuerza($ejercicio);
    }

    public function existeEjercicioPorNombre($nombre)
    {
        return $this->ejercicioFuerzaData->existeEjercicioPorNombre($nombre);
    }

    public function actualizarTbejerciciofuerza($ejercicio)
    {
        return $this->ejercicioFuerzaData->actualizarTbejerciciofuerza($ejercicio);
    }

    public function eliminarTbejerciciofuerza($id)
    {
        return $this->ejercicioFuerzaData->eliminarTbejerciciofuerza($id);
    }

    public function obtenerTbejerciciofuerza()
    {
        return $this->ejercicioFuerzaData->obtenerTbejerciciofuerza();
    }

    public function getTBEjercicioFuerzaByActivo()
    {
        return $this->ejercicioFuerzaData->getTBEjercicioFuerzaByActivo();
    }

    public function getEjercicioFuerza($id)
    {
        return $this->ejercicioFuerzaData->getEjercicioFuerza($id);
    }
}
?>