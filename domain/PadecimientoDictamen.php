<?php

class PadecimientoDictamen
{
    private $tbpadecimientodictamenid;
    private $tbpadecimientodictamenfechaemision;
    private $tbpadecimientodictamenentidademision;
    private $tbpadecimientodictamenimagenid;

    public function __construct($id, $fechaemision, $entidademision, $imagenid)
    {
        $this->tbpadecimientodictamenid = $id;
        $this->tbpadecimientodictamenfechaemision = $fechaemision;
        $this->tbpadecimientodictamenentidademision = $entidademision;
        $this->tbpadecimientodictamenimagenid = $imagenid;
    }

    // Getters
    public function getPadecimientodictamenid()
    {
        return $this->tbpadecimientodictamenid;
    }

    public function getPadecimientodictamenfechaemision()
    {
        return $this->tbpadecimientodictamenfechaemision;
    }

    public function getPadecimientodictamenentidademision()
    {
        return $this->tbpadecimientodictamenentidademision;
    }

    public function getPadecimientodictamenimagenid()
    {
        return $this->tbpadecimientodictamenimagenid;
    }

    // Setters
    public function setPadecimientodictamenid($id)
    {
        $this->tbpadecimientodictamenid = $id;
    }

    public function setPadecimientodictamenfechaemision($fechaemision)
    {
        $this->tbpadecimientodictamenfechaemision = $fechaemision;
    }

    public function setPadecimientodictamenentidademision($entidademision)
    {
        $this->tbpadecimientodictamenentidademision = $entidademision;
    }

    public function setPadecimientodictamenimagenid($imagenid)
    {
        $this->tbpadecimientodictamenimagenid = $imagenid;
    }
}

?>