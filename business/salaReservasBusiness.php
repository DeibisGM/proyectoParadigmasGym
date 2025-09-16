<?php
include_once '../data/salaReservasData.php';

class SalaReservasBusiness
{
    private $salaReservasData;

    public function __construct()
    {
        $this->salaReservasData = new SalaReservasData();
    }

    public function getAllReservasDeSalas()
    {
        return $this->salaReservasData->getAllReservasDeSalas();
    }
}
?>