<?php

class Reserva
{
    private $id;
    private $clienteId;
    private $eventoId;
    private $fecha;
    private $horaInicio;
    private $horaFin;
    private $estado;

    // Propiedades adicionales para mostrar datos
    private $clienteNombre;
    private $eventoNombre;

    public function __construct($id, $clienteId, $eventoId, $fecha, $horaInicio, $horaFin, $estado)
    {
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->eventoId = $eventoId;
        $this->fecha = $fecha;
        $this->horaInicio = $horaInicio;
        $this->horaFin = $horaFin;
        $this->estado = $estado;
        $this->clienteNombre = '';
        $this->eventoNombre = 'Uso Libre';
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getClienteId()
    {
        return $this->clienteId;
    }

    public function getEventoId()
    {
        return $this->eventoId;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getHoraInicio()
    {
        return $this->horaInicio;
    }

    public function getHoraFin()
    {
        return $this->horaFin;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getClienteNombre()
    {
        return $this->clienteNombre;
    }

    public function getEventoNombre()
    {
        return $this->eventoNombre;
    }

    // Setters
    public function setClienteNombre($nombre)
    {
        $this->clienteNombre = $nombre;
    }

    public function setEventoNombre($nombre)
    {
        $this->eventoNombre = $nombre;
    }
}

?>