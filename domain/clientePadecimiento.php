<?php
class ClientePadecimiento {
    private $tbclientepadecimientoid;
    private $tbclienteid;
    private $tbpadecimientoid;
    private $tbpadecimientodictamenid;
    private $carnet;
    private $padecimientosNombres;

    public function __construct($tbclientepadecimientoid, $tbclienteid, $tbpadecimientoid, $tbpadecimientodictamenid = null) {
        $this->tbclientepadecimientoid = $tbclientepadecimientoid;
        $this->tbclienteid = $tbclienteid;
        $this->tbpadecimientoid = $tbpadecimientoid;
        $this->tbpadecimientodictamenid = $tbpadecimientodictamenid;
        $this->carnet = '';
        $this->padecimientosNombres = array();
    }

    public function getTbclientepadecimientoid() {
        return $this->tbclientepadecimientoid;
    }

    public function getTbclienteid() {
        return $this->tbclienteid;
    }

    public function getTbpadecimientoid() {
        return $this->tbpadecimientoid;
    }

    public function getTbpadecimientodictamenid() {
        return $this->tbpadecimientodictamenid;
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

        $ids = explode('$', $this->tbpadecimientoid);
        return array_filter($ids, function($id) {
            return !empty(trim($id)) && is_numeric($id);
        });
    }

    public function setTbclientepadecimientoid($tbclientepadecimientoid) {
        $this->tbclientepadecimientoid = $tbclientepadecimientoid;
    }

    public function setTbclienteid($tbclienteid) {
        $this->tbclienteid = $tbclienteid;
    }

    public function setTbpadecimientoid($tbpadecimientoid) {
        $this->tbpadecimientoid = $tbpadecimientoid;
    }

    public function setTbpadecimientodictamenid($tbpadecimientodictamenid) {
        $this->tbpadecimientodictamenid = $tbpadecimientodictamenid;
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

    public function contarPadecimientos() {
        return count($this->getPadecimientosIds());
    }
}
?>