<?php

class ReservaEvento
{
    private $tbreservaeventoid;
    private $tbreservaeventoclienteid;
    private $tbreservaeventoeventoid;
    private $tbreservaeventofecha;
    private $tbreservaeventohorainicio;
    private $tbreservaeventohorafin;
    private $tbreservaeventoestado;
    private $clienteNombre;
    private $eventoNombre;
    private $instructorNombre;

    public function __construct($id, $clienteid, $eventoid, $fecha, $horaInicio, $horaFin, $estado)
    {
        $this->tbreservaeventoid = $id;
        $this->tbreservaeventoclienteid = $clienteid;
        $this->tbreservaeventoeventoid = $eventoid;
        $this->tbreservaeventofecha = $fecha;
        $this->tbreservaeventohorainicio = $horaInicio;
        $this->tbreservaeventohorafin = $horaFin;
        $this->tbreservaeventoestado = $estado;
    }

    public function getId() { return $this->tbreservaeventoid; }
    public function getClienteId() { return $this->tbreservaeventoclienteid; }
    public function getEventoId() { return $this->tbreservaeventoeventoid; }
    public function getFecha() { return $this->tbreservaeventofecha; }
    public function getHoraInicio() { return $this->tbreservaeventohorainicio; }
    public function getHoraFin() { return $this->tbreservaeventohorafin; }
    public function getEstado() { return $this->tbreservaeventoestado; }
    public function getClienteNombre() { return $this->clienteNombre; }
    public function getEventoNombre() { return $this->eventoNombre; }
    public function getInstructorNombre() { return $this->instructorNombre; }

    public function setClienteNombre($nombre) { $this->clienteNombre = $nombre; }
    public function setEventoNombre($nombre) { $this->eventoNombre = $nombre; }
    public function setInstructorNombre($nombre) { $this->instructorNombre = $nombre; }
}