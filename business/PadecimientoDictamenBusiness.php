<?php
include '../data/PadecimientoDictamenData.php';
include_once '../utility/ImageManager.php';

class PadecimientoDictamenBusiness
{

    private $imageManager;
    private $padecimientoDictamenData;

    public function __construct()
    {
        $this->imageManager = new ImageManager();
        $this->padecimientoDictamenData = new PadecimientoDictamenData();
    }

    public function insertarTBPadecimientoDictamen($padecimientodictamen) {
        return $this->padecimientoDictamenData->insertarTBPadecimientoDictamen($padecimientodictamen);
    }

    public function actualizarTBPadecimientoDictamen($padecimientodictamen) {
        return $this->padecimientoDictamenData->actualizarTBPadecimientoDictamen($padecimientodictamen);
    }

    public function eliminarTBPadecimientoDictamen($id) {
        return $this->padecimientoDictamenData->eliminarTBPadecimientoDictamen($id);
    }

    public function getAllTBPadecimientoDictamen() {
        return $this->padecimientoDictamenData->getAllTBPadecimientoDictamen();
    }

    // Método temporal - se puede expandir cuando esté disponible la tabla intermedia
    public function getPadecimientosDictamenPorCliente($clienteId) {
        // Por ahora retorna todos - esto se debería implementar con la tabla intermedia
        return $this->getAllTBPadecimientoDictamen();
    }

    public function getAllTBPadecimientoDictamenPorId($padecimientoLista) {
        return $this->padecimientoDictamenData->getAllTBPadecimientoDictamenPorId($padecimientoLista);
    }

    public function existePadecimientoDictamenEntidad($entidad) {
        return $this->padecimientoDictamenData->existePadecimientoDictamenEntidad($entidad);
    }

    public function getPadecimientoDictamenPorId($id) {
        return $this->padecimientoDictamenData->getPadecimientoDictamenPorId($id);
    }

    public function getAllClientes() {
        return $this->padecimientoDictamenData->getAllClientes();
    }

    public function getClientePorCarnet($carnet) {
        return $this->padecimientoDictamenData->getClientePorCarnet($carnet);
    }
}
?>