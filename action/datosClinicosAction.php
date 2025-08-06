<?php

include_once '../business/datosClinicosBusiness.php';
    if (!class_exists('DatosClinicos')) {
        include_once '../domain/datosClinicos.php';
    }

    header('Content-Type: application/json');

    $datosClinicosBusiness = new DatosClinicosBusiness();
    $response = array();

    try {
        if(isset($_POST['create'])){

            $enfermedad = isset($_POST['enfermedad']) ? 1 : 0;
            $otraEnfermedad = isset($_POST['otraEnfermedad']) ? $_POST['otraEnfermedad'] : '';
            $tomaMedicamento = isset($_POST['tomaMedicamento']) ? 1 : 0;
            $medicamento = isset($_POST['medicamento']) ? $_POST['medicamento'] : '';
            $lesion = isset($_POST['lesion']) ? 1 : 0;
            $descripcionLesion = isset($_POST['descripcionLesion']) ? $_POST['descripcionLesion'] : '';
            $discapacidad = isset($_POST['discapacidad']) ? 1 : 0;
            $descripcionDiscapacidad = isset($_POST['descripcionDiscapacidad']) ? $_POST['descripcionDiscapacidad'] : '';
            $restriccionMedica = isset($_POST['restriccionMedica']) ? 1 : 0;
            $descripcionrestriccionmedica = isset($_POST['descripcionrestriccionmedica']) ? $_POST['descripcionrestriccionmedica'] : '';
            $clienteId = isset($_POST['clienteId']) ? $_POST['clienteId'] : '';

            // Valida que clienteId no esté vacío
            if(empty($clienteId)){
                $response['success'] = false;
                $response['message'] = 'Error: Debe seleccionar un cliente.';
                echo json_encode($response);
                exit();
            }

            // revisa si ya existe un registro para este cliente
            if($datosClinicosBusiness->existenDatosClinicosPorCliente($clienteId)){
                $response['success'] = false;
                $response['message'] = 'Error: Ya existen datos clínicos registrados para este cliente.';
                echo json_encode($response);
                exit();
            }

            $errores = $datosClinicosBusiness->validarDatosClinicos($enfermedad, $otraEnfermedad, $tomaMedicamento,
                                               $medicamento, $lesion, $descripcionLesion,
                                               $discapacidad, $descripcionDiscapacidad, $restriccionMedica, $descripcionrestriccionmedica);

            if(!empty($errores)){
                $response['success'] = false;
                $response['message'] = 'Error de validación: ' . implode(', ', $errores);
                echo json_encode($response);
                exit();
            }

            $datosClinicos = new DatosClinicos(0, $enfermedad, $otraEnfermedad, $tomaMedicamento,
                                             $medicamento, $lesion, $descripcionLesion,
                                             $discapacidad, $descripcionDiscapacidad,
                                             $restriccionMedica, $descripcionrestriccionmedica, $clienteId);

            $resultado = $datosClinicosBusiness->insertarTBDatosClinicos($datosClinicos);

            if($resultado){
                $response['success'] = true;
                $response['message'] = 'Éxito: Registro insertado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo procesar la transacción en la base de datos.';
            }

        } else if(isset($_POST['update'])){

            $id = $_POST['id'];
            $enfermedad = isset($_POST['enfermedad']) ? 1 : 0;
            $otraEnfermedad = isset($_POST['otraEnfermedad']) ? $_POST['otraEnfermedad'] : '';
            $tomaMedicamento = isset($_POST['tomaMedicamento']) ? 1 : 0;
            $medicamento = isset($_POST['medicamento']) ? $_POST['medicamento'] : '';
            $lesion = isset($_POST['lesion']) ? 1 : 0;
            $descripcionLesion = isset($_POST['descripcionLesion']) ? $_POST['descripcionLesion'] : '';
            $discapacidad = isset($_POST['discapacidad']) ? 1 : 0;
            $descripcionDiscapacidad = isset($_POST['descripcionDiscapacidad']) ? $_POST['descripcionDiscapacidad'] : '';
            $restriccionMedica = isset($_POST['restriccionMedica']) ? 1 : 0;
            $descripcionrestriccionmedica = isset($_POST['descripcionrestriccionmedica']) ? $_POST['descripcionrestriccionmedica'] : '';

            $clienteId = isset($_POST['clienteId']) ? $_POST['clienteId'] : '';

            if(empty($id) || empty($clienteId)){
                $response['success'] = false;
                $response['message'] = 'Error: Hay campos vacíos.';
                echo json_encode($response);
                exit();
            }

            $errores = $datosClinicosBusiness->validarDatosClinicos($enfermedad, $otraEnfermedad, $tomaMedicamento,
                                                   $medicamento, $lesion, $descripcionLesion,
                                                   $discapacidad, $descripcionDiscapacidad, $restriccionMedica, $descripcionrestriccionmedica);

            if(!empty($errores)){
                $response['success'] = false;
                $response['message'] = 'Error de validación: ' . implode(', ', $errores);
                echo json_encode($response);
                exit();
            }

            $datosClinicos = new DatosClinicos($id, $enfermedad, $otraEnfermedad, $tomaMedicamento,
                                             $medicamento, $lesion, $descripcionLesion,
                                             $discapacidad, $descripcionDiscapacidad,
                                             $restriccionMedica, $descripcionrestriccionmedica, $clienteId);

            $resultado = $datosClinicosBusiness->actualizarTBDatosClinicos($datosClinicos);

            if($resultado){
                $response['success'] = true;
                $response['message'] = 'Éxito: Registro actualizado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo procesar la transacción en la base de datos.';
            }

        } else if(isset($_POST['delete'])){

            $id = $_POST['id'];

            if(empty($id)){
                $response['success'] = false;
                $response['message'] = 'Error: Hay campos vacíos.';
                echo json_encode($response);
                exit();
            }

            $resultado = $datosClinicosBusiness->eliminarTBDatosClinicos($id);

            if($resultado){
                $response['success'] = true;
                $response['message'] = 'Éxito: Registro eliminado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo procesar la transacción en la base de datos.';
            }

        } else {
            $response['success'] = false;
            $response['message'] = 'Error: Acción no válida.';
        }

    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Error: ' . $e->getMessage();
    }

    echo json_encode($response);
?>