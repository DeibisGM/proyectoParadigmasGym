<?php
include_once '../data/ejercicioSubzonaData.php';

class ejercicioSubzonaBusiness
{
    private $ejercicioSubzonaData;

    public function __construct() {
        $this->ejercicioSubzonaData = new ejercicioSubzonaData();
    }

    public function insertarTBEjercicioSubzona($ejercicioSubzona) {
        return $this->ejercicioSubzonaData->insertarTBEjercicioSubzona($ejercicioSubzona);
    }

    public function actualizarTBEjercicioSubzona($ejercicioSubzona) {
        return $this->ejercicioSubzonaData->actualizarTBEjercicioSubzona($ejercicioSubzona);
    }

    public function eliminarTBEjercicioSubZona($ejercicio, $nombre) {
        return $this->ejercicioSubzonaData->eliminarTBEjercicioSubZona($ejercicio, $nombre);
    }

    public function getEjercicioSubzonaPorEjercicioNombre($ejercicio, $nombre) {
        return $this->ejercicioSubzonaData->getEjercicioSubzonaPorEjercicioNombre($ejercicio, $nombre);
    }

    public function getSubzonasPorEjercicio($idEjercicio, $tipo) {
        return $this->ejercicioSubzonaData->getSubzonasPorEjercicio($idEjercicio, $tipo);
    }
}
?>