<?php

    if (!class_exists('DatoClinicoData')) {
        include_once '../data/datoClinicoData.php';
    }

    class DatoClinicoBusiness {

        private $datoClinicoData;

        public function __construct() {
            $this->datoClinicoData = new DatoClinicoData();
        }

        public function insertarTBDatoClinico($datoClinico) {
            $existeRegistro = $this->datoClinicoData->obtenerTBDatoClinicoPorCliente($datoClinico->getTbclienteid());
            if ($existeRegistro) {
                return false;
            }
            return $this->datoClinicoData->insertarTBDatoClinico($datoClinico);
        }

        public function actualizarTBDatoClinico($datoClinico) {
            return $this->datoClinicoData->actualizarTBDatoClinico($datoClinico);
        }

        public function eliminarTBDatoClinico($tbdatoclinicoid) {
            return $this->datoClinicoData->eliminarTBDatoClinico($tbdatoclinicoid);
        }

        public function obtenerTBDatoClinico() {
            return $this->datoClinicoData->obtenerTBDatoClinico();
        }

        public function obtenerTBDatoClinicoPorCliente($tbclienteid) {
            return $this->datoClinicoData->obtenerTBDatoClinicoPorCliente($tbclienteid);
        }

        public function obtenerTodosLosClientes() {
            return $this->datoClinicoData->obtenerTodosLosClientes();
        }

        public function obtenerPadecimientos() {
            return $this->datoClinicoData->obtenerPadecimientos();
        }

        public function existenDatoClinicosPorCliente($tbclienteid) {
            $datos = $this->datoClinicoData->obtenerTBDatoClinicoPorCliente($tbclienteid);
            return $datos !== null;
        }

        public function validarDatoClinico($tbclienteid, $tbpadecimientoid) {
            $errores = array();

            if (empty($tbclienteid) || $tbclienteid <= 0) {
                $errores[] = "Debe seleccionar un cliente válido";
            }

            if (empty($tbpadecimientoid)) {
                $errores[] = "Debe seleccionar al menos un padecimiento";
            } else {
                // Validar que los padecimientos existan en la base de datos
                if (!$this->datoClinicoData->validarPadecimientosExisten($tbpadecimientoid)) {
                    $errores[] = "Uno o más padecimientos seleccionados no son válidos";
                }
            }

            return $errores;
        }
    }
?>