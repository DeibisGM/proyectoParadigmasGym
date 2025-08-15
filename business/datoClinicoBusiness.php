<?php

    if (!class_exists('DatoClinicoData')) {
        include_once '../data/datoClinicoData.php';
    }

    class DatoClinicoBusiness{

        private $datoClinicoData;

        public function __construct(){
            $this->datoClinicoData = new DatoClinicoData();
        }

        public function insertarTBDatoClinico($datoClinico){
            $existeRegistro = $this->datoClinicoData->obtenerTBDatoClinicoPorCliente($datoClinico->getTbclienteid());
            if ($existeRegistro) {
                return false;
            }
            return $this->datoClinicoData->insertarTBDatoClinico($datoClinico);
        }

        public function actualizarTBDatoClinico($datoClinico){
            return $this->datoClinicoData->actualizarTBDatoClinico($datoClinico);
        }

        public function eliminarTBDatoClinico($tbdatoclinicoid){
            return $this->datoClinicoData->eliminarTBDatoClinico($tbdatoclinicoid);
        }

        public function obtenerTBDatoClinico(){
            return $this->datoClinicoData->obtenerTBDatoClinico();
        }

        public function obtenerTBDatoClinicoPorCliente($tbclienteid){
            return $this->datoClinicoData->obtenerTBDatoClinicoPorCliente($tbclienteid);
        }

        public function obtenerTodosLosClientes(){
            return $this->datoClinicoData->obtenerTodosLosClientes();
        }

        public function existenDatoClinicosPorCliente($tbclienteid){
            $datos = $this->datoClinicoData->obtenerTBDatoClinicoPorCliente($tbclienteid);
            return $datos !== null;
        }

        public function validarDatoClinico($enfermedad, $otraEnfermedad, $tomaMedicamento,
                                               $medicamento, $lesion, $descripcionLesion,
                                               $discapacidad, $descripcionDiscapacidad, $restriccionMedica,
                                               $descripcionrestriccionmedica){
            $errores = array();

            if($enfermedad == 1 && (empty($otraEnfermedad) || trim($otraEnfermedad) == "")){
                $errores[] = "Debe especificar la enfermedad que posee";
            }

            if($tomaMedicamento == 1 && (empty($medicamento) || trim($medicamento) == "")){
                $errores[] = "Debe especificar el medicamento que toma";
            }

            if($lesion == 1 && (empty($descripcionLesion) || trim($descripcionLesion) == "")){
                $errores[] = "Debe describir la lesión que posee";
            }

            if($discapacidad == 1 && (empty($descripcionDiscapacidad) || trim($descripcionDiscapacidad) == "")){
                $errores[] = "Debe describir la discapacidad que posee";
            }

            if($restriccionMedica ==1 && (empty($descripcionrestriccionmedica) || trim($descripcionrestriccionmedica) == "")){
                $errores[] = "Debe describir la restricción médica";
            }

            return $errores;
        }

    }
?>