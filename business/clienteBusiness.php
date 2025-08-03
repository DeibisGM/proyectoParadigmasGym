<?php

include '../data/clienteData.php';

class ClienteBusiness {

    private $clienteData;

    public function __construct() {
        $this->clienteData = new ClienteData();
    }

    public function insertarTBCliente($cliente) {
        return $this->clienteData->insertarTBCliente($cliente);
    }

    public function actualizarTBCliente($cliente) {
        return $this->clienteData->actualizarTBCliente($cliente);
    }

    public function eliminarTBCliente($idCliente) {
        return $this->clienteData->eliminarTBCliente($idCliente);
    }

    public function getAllTBCliente() {
        return $this->clienteData->getAllTBCliente();
    }

    public function existeClientePorCarnet($carnet) {
        return $this->clienteData->existeClientePorCarnet($carnet);
    }
}

?>
