<?php

class ReservaLibre
{
    private $tbreservalibreid;
    private $tbreservalibreclienteid;
    private $tbreservalibrehorariolibreid;
    private $tbreservalibrefecha;
    private $tbreservalibrehora;
    private $tbreservalibreestado;
    private $clienteNombre;

    public function __construct($id, $clienteid, $horariolibreid, $fecha, $hora, $estado)
    {
        $this->tbreservalibreid = $id;
        $this->tbreservalibreclienteid = $clienteid;
        $this->tbreservalibrehorariolibreid = $horariolibreid;
        $this->tbreservalibrefecha = $fecha;
        $this->tbreservalibrehora = $hora;
        $this->tbreservalibreestado = $estado;
    }

    public function getId() { return $this->tbreservalibreid; }
    public function getClienteId() { return $this->tbreservalibreclienteid; }
    public function getHorarioLibreId() { return $this->tbreservalibrehorariolibreid; }
    public function getFecha() { return $this->tbreservalibrefecha; }
    public function getHora() { return $this->tbreservalibrehora; }
    public function getEstado() { return $this->tbreservalibreestado; }
    public function getClienteNombre() { return $this->clienteNombre; }
    public function setClienteNombre($nombre) { $this->clienteNombre = $nombre; }
}
?>