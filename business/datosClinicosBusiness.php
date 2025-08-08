<?php

if (!class_exists('DatosClinicosData')) {
    include_once '../data/datosClinicosData.php';
}

class DatosClinicosBusiness{

    private $datosClinicosData;

    public function __construct(){
        $this->datosClinicosData = new DatosClinicosData();
    }

    public function insertarTBDatosClinicos($datosClinicos){
        $existeRegistro = $this->datosClinicosData->obtenerTBDatosClinicosPorCliente($datosClinicos->getTbclientesid());
        if ($existeRegistro) {
            return false;
        }
        return $this->datosClinicosData->insertarTBDatosClinicos($datosClinicos);
    }

    public function actualizarTBDatosClinicos($datosClinicos){
        return $this->datosClinicosData->actualizarTBDatosClinicos($datosClinicos);
    }

    public function eliminarTBDatosClinicos($tbdatosclinicosid){
        return $this->datosClinicosData->eliminarTBDatosClinicos($tbdatosclinicosid);
    }

    public function eliminarTBDatosClinicosPorCliente($tbclientesid){
        return $this->datosClinicosData->eliminarTBDatosClinicosPorCliente($tbclientesid);
    }

    public function obtenerTBDatosClinicos(){
        return $this->datosClinicosData->obtenerTBDatosClinicos();
    }

    public function obtenerTBDatosClinicosPorId($tbdatosclinicosid){
        return $this->datosClinicosData->obtenerTBDatosClinicosPorId($tbdatosclinicosid);
    }

    public function obtenerTBDatosClinicosPorCliente($tbclientesid){
        return $this->datosClinicosData->obtenerTBDatosClinicosPorCliente($tbclientesid);
    }

    public function obtenerTodosLosClientes(){
        return $this->datosClinicosData->obtenerTodosLosClientes();
    }

    public function existenDatosClinicosPorCliente($tbclientesid){
        $datos = $this->datosClinicosData->obtenerTBDatosClinicosPorCliente($tbclientesid);
        return $datos !== null;
    }

    public function validarDatosClinicos($enfermedad, $otraEnfermedad, $tomaMedicamento,
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