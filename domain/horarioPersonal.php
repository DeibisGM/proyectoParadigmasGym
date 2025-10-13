<?php
class HorarioPersonal
{
    private $horarioPersonalId;
    private $fecha;
    private $hora;
    private $instructorId;
    private $clienteId;
    private $estado;
    private $duracion;
    private $tipo;
    private $instructorNombre;
    private $clienteNombre;

   public function __construct($id, $fecha, $hora, $instructorId, $clienteId = null, $estado = 'disponible', $duracion = 60, $tipo = 'personal')
   {
       $this->horarioPersonalId = $id;
       $this->fecha = $fecha;
       $this->hora = $hora;
       $this->instructorId = $instructorId;
       $this->clienteId = $clienteId;
       $this->estado = $estado ?: 'disponible'; // Valor por defecto
       $this->duracion = $duracion ?: 60; // Valor por defecto
       $this->tipo = $tipo ?: 'personal'; // Valor por defecto
   }

    // Getters
    public function getId() { return $this->horarioPersonalId; }
    public function getFecha() { return $this->fecha; }
    public function getHora() { return $this->hora; }
    public function getInstructorId() { return $this->instructorId; }
    public function getClienteId() { return $this->clienteId; }
    public function getEstado() { return $this->estado; }
    public function getDuracion() { return $this->duracion; }
    public function getTipo() { return $this->tipo; }
    public function getInstructorNombre() { return $this->instructorNombre; }
    public function getClienteNombre() { return $this->clienteNombre; }

    // Setters
    public function setClienteId($clienteId) { $this->clienteId = $clienteId; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setDuracion($duracion) { $this->duracion = $duracion; }
    public function setInstructorNombre($nombre) { $this->instructorNombre = $nombre; }
    public function setClienteNombre($nombre) { $this->clienteNombre = $nombre; }
}
?>