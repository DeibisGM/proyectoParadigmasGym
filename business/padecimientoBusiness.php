<?php
include_once '../data/padecimientoData.php';
include_once '../domain/padecimiento.php';

class PadecimientoBusiness {
    private $padecimientoData;

    public function __construct() {
        $this->padecimientoData = new PadecimientoData();
    }

    public function insertarTbpadecimiento($padecimiento) {
        if ($this->padecimientoData->verificarNombreExistente($padecimiento->getTbpadecimientonombre())) {
            return false;
        }
        return $this->padecimientoData->insertarTbpadecimiento($padecimiento);
    }

    public function actualizarTbpadecimiento($padecimiento) {
        if ($this->padecimientoData->verificarNombreExistente(
            $padecimiento->getTbpadecimientonombre(),
            $padecimiento->getTbpadecimientoid()
        )) {
            return false;
        }
        return $this->padecimientoData->actualizarTbpadecimiento($padecimiento);
    }

    public function eliminarTbpadecimiento($padecimientoid) {
        return $this->padecimientoData->eliminarTbpadecimiento($padecimientoid);
    }

    public function obtenerTbpadecimiento() {
        return $this->padecimientoData->obtenerTbpadecimiento();
    }

    public function obtenerTbpadecimientoPorId($padecimientoid) {
        return $this->padecimientoData->obtenerTbpadecimientoPorId($padecimientoid);
    }

    public function obtenerTbpadecimientoPorTipo($tipo) {
        return $this->padecimientoData->obtenerTbpadecimientoPorTipo($tipo);
    }

    public function validarPadecimiento($tipo, $nombre, $descripcion, $formaDeActuar) {
        $errores = array();

        if (empty($tipo) || trim($tipo) === '') {
            $errores[] = "El tipo de padecimiento es obligatorio";
        }

        if (empty($nombre) || trim($nombre) === '') {
            $errores[] = "El nombre del padecimiento es obligatorio";
        } else if (strlen(trim($nombre)) < 3) {
            $errores[] = "El nombre debe tener al menos 3 caracteres";
        } else if (strlen(trim($nombre)) > 100) {
            $errores[] = "El nombre no puede exceder 100 caracteres";
        }

        if (empty($descripcion) || trim($descripcion) === '') {
            $errores[] = "La descripción del padecimiento es obligatoria";
        } else if (strlen(trim($descripcion)) < 10) {
            $errores[] = "La descripción debe tener al menos 10 caracteres";
        } else if (strlen(trim($descripcion)) > 500) {
            $errores[] = "La descripción no puede exceder 500 caracteres";
        }

        if (empty($formaDeActuar) || trim($formaDeActuar) === '') {
            $errores[] = "La forma de actuar es obligatoria";
        } else if (strlen(trim($formaDeActuar)) < 10) {
            $errores[] = "La forma de actuar debe tener al menos 10 caracteres";
        } else if (strlen(trim($formaDeActuar)) > 1000) {
            $errores[] = "La forma de actuar no puede exceder 1000 caracteres";
        }

        return $errores;
    }

    public function obtenerTiposPadecimiento() {
        return array(
            'Enfermedad',
            'Lesión',
            'Discapacidad',
            'Trastorno',
            'Síndrome',
            'Otro'
        );
    }
}
?>