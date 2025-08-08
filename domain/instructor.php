<?php

class Instructor {

    private $instructorId;
    private $instructorNombre;
    private $instructorTelefono;
    private $instructorDireccion;
    private $instructorCorreo;
    private $instructorCuenta;
    private $instructorContraseña;


    public function __construct($instructorId, $instructorNombre, $instructorTelefono, $instructorDireccion, $instructorCorreo, $instructorCuenta, $instructorContraseña) {
        $this->instructorId = $instructorId;
        $this->instructorNombre = $instructorNombre;
        $this->instructorTelefono = $instructorTelefono;
        $this->instructorDireccion = $instructorDireccion;
        $this->instructorCorreo = $instructorCorreo;
        $this->instructorCuenta = $instructorCuenta;
        $this->instructorContraseña = $instructorContraseña;

    }


    public function getInstructorId() {
        return $this->instructorId;
    }

    public function getInstructorNombre() {
        return $this->instructorNombre;
    }
   public function getInstructorTelefono() {
        return $this->instructorTelefono;
    }
    public function getInstructorDireccion() {
        return $this->instructorDireccion;
    }
    public function getInstructorCorreo() {
        return $this->instructorCorreo;
    }
    public function getInstructorCuenta() {
        return $this->instructorCuenta;
    }
    public function getInstructorContraseña() {
        return $this->instructorContraseña;
    }


    public function setInstructorId($instructorId) {
        $this->instructorId = $instructorId;
    }
    public function setInstructorNombre($instructorNombre) {
        $this->instructorNombre = $instructorNombre;
    }
    public function setInstructorTelefono($instructorTelefono) {
        $this->instructorTelefono = $instructorTelefono;
    }
    public function setInstructorDireccion($instructorDireccion) {
        $this->instructorDireccion = $instructorDireccion;
    }
    public function setInstructorCorreo($instructorCorreo) {
        $this->instructorCorreo = $instructorCorreo;
    }
    public function setInstructorCuenta($instructorCuenta) {
        $this->instructorCuenta = $instructorCuenta;
    }
    public function setInstructorContraseña($instructorContraseña) {
        $this->instructorContraseña = $instructorContraseña;
    }

}
?>