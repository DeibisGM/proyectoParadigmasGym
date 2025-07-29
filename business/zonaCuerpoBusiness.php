<?php

include '../data/zonaCuerpoData.php';

class ZonaCuerpoBusiness {

    private $zonaCuerpoData;

    public function __construct() {
        $this->zonaCuerpoData = new ZonaCuerpoData();
    }

    public function insertarTBZonaCuerpo($zonaCuerpo) {
        return $this->zonaCuerpoData->insertarTBZonaCuerpo($zonaCuerpo);
    }

    public function actualizarTBZonaCuerpo($zonaCuerpo) {
        return $this->zonaCuerpoData->actualizarTBZonaCuerpo($zonaCuerpo);
    }

    public function eliminarTBZonaCuerpo($idZonaCuerpo) {
        return $this->zonaCuerpoData->eliminarTBZonaCuerpo($idZonaCuerpo);
    }

    public function getAllTBZonaCuerpo() {
        return $this->zonaCuerpoData->getAllTBZonaCuerpo();
    }
}
?>