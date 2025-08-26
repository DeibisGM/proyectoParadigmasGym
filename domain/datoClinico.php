<?php
class DatoClinico {
    private $tbdatoclinicoid;
    private $tbclienteid;
    private $tbpadecimientoid;
    private $carnet;
    private $padecimientosNombres;

    public function __construct($tbdatoclinicoid, $tbclienteid, $tbpadecimientoid) {
        $this->tbdatoclinicoid = $tbdatoclinicoid;
        $this->tbclienteid = $tbclienteid;
        $this->tbpadecimientoid = $tbpadecimientoid;
        $this->carnet = '';
        $this->padecimientosNombres = array();
    }

    public function getTbdatoclinicoid() {
        return $this->tbdatoclinicoid;
    }

    public function getTbclienteid() {
        return $this->tbclienteid;
    }

    public function getTbpadecimientoid() {
        return $this->tbpadecimientoid;
    }

    public function getCarnet() {
        return $this->carnet;
    }

    public function getPadecimientosNombres() {
        return $this->padecimientosNombres;
    }

    public function getPadecimientosNombresString() {
        return implode(', ', $this->padecimientosNombres);
    }

    public function getPadecimientosIds() {
        if (empty($this->tbpadecimientoid)) {
            return array();
        }
        return explode('$', $this->tbpadecimientoid);
    }

    public function setTbdatoclinicoid($tbdatoclinicoid) {
        $this->tbdatoclinicoid = $tbdatoclinicoid;
    }

    public function setTbclienteid($tbclienteid) {
        $this->tbclienteid = $tbclienteid;
    }

    public function setTbpadecimientoid($tbpadecimientoid) {
        $this->tbpadecimientoid = $tbpadecimientoid;
    }

    public function setCarnet($carnet) {
        $this->carnet = $carnet;
    }

    public function setPadecimientosNombres($nombres) {
        $this->padecimientosNombres = $nombres;
    }

    public static function convertirIdsAString($idsArray) {
        if (empty($idsArray)) {
            return '';
        }
        $idsLimpios = array_filter(array_map('intval', $idsArray), function($id) {
            return $id > 0;
        });
        return implode('$', $idsLimpios);
    }
}
?>