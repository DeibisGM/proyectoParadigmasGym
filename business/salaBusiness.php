<?php
include '../data/salaData.php';

class SalaBusiness{

    private $salaData;

    public function __construct(){
        $this->salaData = new SalaData();
    }

    public function insertarTbsala($sala){
        return $this->salaData->insertarTbsala($sala);
    }

    public function actualizarTbsala($sala){
        return $this->salaData->actualizarTbsala($sala);
    }

    public function eliminarTbsala($id){
        return $this->salaData->eliminarTbsala($id);
    }

    public function getAllSalas(){
        return $this->salaData->getAllSalas();
    }
}
?>