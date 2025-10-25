<?php
include_once '../data/ejercicioEquilibrioData.php';

class EjercicioEquilibrioBusiness{

    private $ejercicioEquilibrioData;

    public function __construct(){
        $this->ejercicioEquilibrioData = new EjercicioEquilibrioData();
    }

    public function insertarTbejercicioequilibrio($ejercicio){
        return $this->ejercicioEquilibrioData->insertarTbejercicioequilibrio($ejercicio);
    }

    public function actualizarTbejercicioequilibrio($ejercicio){
        return $this->ejercicioEquilibrioData->actualizarTbejercicioequilibrio($ejercicio);
    }

    public function eliminarTbejercicioequilibrio($id){
        return $this->ejercicioEquilibrioData->eliminarTbejercicioequilibrio($id);
    }

    public function obtenerTbejercicioequilibrio(){
        return $this->ejercicioEquilibrioData->obtenerTbejercicioequilibrio();
    }
}
?>