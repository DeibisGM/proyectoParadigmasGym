<?php

include '../data/zonaCuerpoData.php';

class ZonaCuerpoBusiness {

    private $zonaCuerpoData;

    public function __construct() {
        $this->zonaCuerpoData = new ZonaCuerpoData();
    }

    public function existeZonaCuerpoNombre($nombreZonaCuerpo) {
        return $this->zonaCuerpoData->existeZonaCuerpoNombre($nombreZonaCuerpo);
    }

    public function insertarTBZonaCuerpo($zonaCuerpo) {
        // Verificar si ya existe una zona con el mismo nombre
        if ($this->existeZonaCuerpoNombre($zonaCuerpo->getNombreZonaCuerpo())) {
            return -1; // Código de error para indicar que ya existe
        }
        // Si la inserción es exitosa, la capa de datos devolverá el nuevo ID.
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