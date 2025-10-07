<?php

class ReservaEvento
{
    private $tbreservaeventoid;
    private $tbreservaeventoclienteid;
    private $tbreservaeventoeventoid;
    private $tbreservaeventoclienteresponsableid; // NUEVO
    private $tbreservaeventofecha;
    private $tbreservaeventohorainicio;
    private $tbreservaeventohorafin;
    private $tbreservaeventoactivo;

// Propiedades adicionales
    private $clienteNombre;
    private $eventoNombre;
    private $instructorNombre;
    private $clienteResponsableNombre; // NUEVO

// MODIFICADO: Se añade $clienteResponsableId
    public function __construct($id, $clienteid, $eventoid, $clienteResponsableId, $fecha, $horaInicio, $horaFin, $estado)
    {
        $this->tbreservaeventoid = $id;
        $this->tbreservaeventoclienteid = $clienteid;
        $this->tbreservaeventoeventoid = $eventoid;
        $this->tbreservaeventoclienteresponsableid = $clienteResponsableId; // NUEVO
        $this->tbreservaeventofecha = $fecha;
        $this->tbreservaeventohorainicio = $horaInicio;
        $this->tbreservaeventohorafin = $horaFin;
        $this->tbreservaeventoactivo = $estado;
    }

// Getters
    public function getId() { return $this->tbreservaeventoid; }
    public function getClienteId() { return $this->tbreservaeventoclienteid; }
    public function getEventoId() { return $this->tbreservaeventoeventoid; }
    public function getClienteResponsableId() { return $this->tbreservaeventoclienteresponsableid; } // NUEVO
    public function getFecha() { return $this->tbreservaeventofecha; }
    public function getHoraInicio() { return $this->tbreservaeventohorainicio; }
    public function getHoraFin() { return $this->tbreservaeventohorafin; }
    public function getEstado() { return $this->tbreservaeventoactivo; }
    public function getClienteNombre() { return $this->clienteNombre; }
    public function getEventoNombre() { return $this->eventoNombre; }
    public function getInstructorNombre() { return $this->instructorNombre; }
    public function getClienteResponsableNombre() { return $this->clienteResponsableNombre; } // NUEVO

// Setters
    public function setClienteNombre($nombre) { $this->clienteNombre = $nombre; }
    public function setEventoNombre($nombre) { $this->eventoNombre = $nombre; }
    public function setInstructorNombre($nombre) { $this->instructorNombre = $nombre; }
    public function setClienteResponsableNombre($nombre) { $this->clienteResponsableNombre = $nombre; } // NUEVO
}