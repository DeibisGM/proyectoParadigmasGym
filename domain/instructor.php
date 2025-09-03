<?php

class Instructor
{
    private $instructorId;
    private $instructorNombre;
    private $instructorTelefono;
    private $instructorDireccion;
    private $instructorCorreo;
    private $instructorCuenta;
    private $instructorContraseña;
    private $instructorActivo;
    private $instructorCertificado;

    public function __construct($id, $nombre, $telefono, $direccion, $correo, $cuenta, $contraseña, $activo, $certificado = '')
    {
        $this->instructorId = $id;
        $this->instructorNombre = $nombre;
        $this->instructorTelefono = $telefono;
        $this->instructorDireccion = $direccion;
        $this->instructorCorreo = $correo;
        $this->instructorCuenta = $cuenta;
        $this->instructorContraseña = $contraseña;
        $this->instructorActivo = $activo;
        $this->instructorCertificado = $certificado;
    }

    public function getInstructorId()
    {
        return $this->instructorId;
    }

    public function getInstructorNombre()
    {
        return $this->instructorNombre;
    }

    public function getInstructorTelefono()
    {
        return $this->instructorTelefono;
    }

    public function getInstructorDireccion()
    {
        return $this->instructorDireccion;
    }

    public function getInstructorCorreo()
    {
        return $this->instructorCorreo;
    }

    public function getInstructorCuenta()
    {
        return $this->instructorCuenta;
    }

    public function getInstructorContraseña()
    {
        return $this->instructorContraseña;
    }

    public function getInstructorActivo()
    {
        return $this->instructorActivo;
    }

    public function getInstructorCertificado()
    {
        return $this->instructorCertificado;
    }

    public function setInstructorId($id)
    {
        $this->instructorId = $id;
    }

    public function setInstructorNombre($nombre)
    {
        $this->instructorNombre = $nombre;
    }

    public function setInstructorTelefono($telefono)
    {
        $this->instructorTelefono = $telefono;
    }

    public function setInstructorDireccion($direccion)
    {
        $this->instructorDireccion = $direccion;
    }

    public function setInstructorCorreo($correo)
    {
        $this->instructorCorreo = $correo;
    }

    public function setInstructorCuenta($cuenta)
    {
        $this->instructorCuenta = $cuenta;
    }

    public function setInstructorContraseña($contraseña)
    {
        $this->instructorContraseña = $contraseña;
    }

    public function setInstructorActivo($activo)
    {
        $this->instructorActivo = $activo;
    }

    public function setInstructorCertificado($certificado)
    {
        $this->instructorCertificado = $certificado;
    }
}

?>