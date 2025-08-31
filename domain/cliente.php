<?php

class Cliente
{
    private $id;
    private $carnet;
    private $nombre;
    private $fechaNacimiento;
    private $telefono;
    private $correo;
    private $direccion;
    private $genero;
    private $inscripcion;
    private $estado;
    private $contrasena;
    private $tbclienteimagenid;

    public function __construct($id, $carnet, $nombre, $fechaNacimiento, $telefono, $correo, $direccion, $genero, $inscripcion, $estado, $contrasena = '', $tbclienteimagenid = '')
    {
        $this->id = $id;
        $this->carnet = $carnet;
        $this->nombre = $nombre;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->direccion = $direccion;
        $this->genero = $genero;
        $this->inscripcion = $inscripcion;
        $this->estado = $estado;
        $this->contrasena = $contrasena;
        $this->tbclienteimagenid = $tbclienteimagenid;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCarnet()
    {
        return $this->carnet;
    }

    public function setCarnet($carnet)
    {
        $this->carnet = $carnet;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function setCorreo($correo)
    {
        $this->correo = $correo;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function getGenero()
    {
        return $this->genero;
    }

    public function setGenero($genero)
    {
        $this->genero = $genero;
    }

    public function getInscripcion()
    {
        return $this->inscripcion;
    }

    public function setInscripcion($inscripcion)
    {
        $this->inscripcion = $inscripcion;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getContrasena()
    {
        return $this->contrasena;
    }

    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;
    }

    public function getTbclienteImagenId()
    {
        return $this->tbclienteimagenid;
    }

    public function setTbclienteImagenId($imagenid)
    {
        $this->tbclienteimagenid = $imagenid;
    }
}

?>
