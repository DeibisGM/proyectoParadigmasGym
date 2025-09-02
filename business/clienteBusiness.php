<?php

include '../data/clienteData.php';
include_once '../utility/ImageManager.php';

class ClienteBusiness {

    private $clienteData;
    private $imageManager;

    public function __construct() {
        $this->clienteData = new ClienteData();
        $this->imageManager = new ImageManager();
    }

    public function insertarTBCliente($cliente) {
        return $this->clienteData->insertarTBCliente($cliente);
    }

    public function actualizarTBCliente($cliente) {
        return $this->clienteData->actualizarTBCliente($cliente);
    }

    public function eliminarTBCliente($idCliente) {
        $cliente = $this->clienteData->getClientePorId($idCliente);
        if ($cliente && $cliente->getTbclienteImagenId() != '' && $cliente->getTbclienteImagenId() != '0') {
            $this->imageManager->deleteImage($cliente->getTbclienteImagenId());
        }
        return $this->clienteData->eliminarTBCliente($idCliente);
    }

    public function getAllTBCliente() {
        return $this->clienteData->getAllTBCliente();
    }

    public function existeClientePorCarnet($carnet) {
        return $this->clienteData->existeClientePorCarnet($carnet);
    }
    
    public function autenticarCliente($correo, $contrasena) {
        return $this->clienteData->autenticarCliente($correo, $contrasena);
    }
    
    public function getClientePorId($id) {
        return $this->clienteData->getClientePorId($id);
    }
}

?>
