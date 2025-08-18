<?php
    session_start();

    include_once '../business/datoClinicoBusiness.php';
    if (!class_exists('DatoClinico')) {
        include_once '../domain/datoClinico.php';
    }

    header('Content-Type: application/json');

    $datoClinicoBusiness = new DatoClinicoBusiness();
    $response = array();

    try {
        $esUsuarioCliente = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cliente';
        $esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
        $esInstructor = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'instructor';

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

            if ($esUsuarioCliente) {
                if (!isset($_SESSION['usuario_id'])) {
                    $response['success'] = false;
                    $response['message'] = 'Error: Usuario no autenticado.';
                    echo json_encode($response);
                    exit();
                }

                $clienteId = $_SESSION['usuario_id'];

            } else if ($esAdmin || $esInstructor) {
                if(empty($clienteId)){
                    $response['success'] = false;
                    $response['message'] = 'Error: Debe seleccionar un cliente.';
                    echo json_encode($response);
                    exit();
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No tiene permisos para realizar esta acción.';
                echo json_encode($response);
                exit();
            }

            if($datoClinicoBusiness->existenDatoClinicosPorCliente($clienteId)){
                $mensaje = $esUsuarioCliente ?
                    'Error: Ya tiene datos clínicos registrados. Puede actualizarlos desde la tabla.' :
                    'Error: Ya existen datos clínicos registrados para este cliente.';

                $response['success'] = false;
                $response['message'] = $mensaje;
                echo json_encode($response);
                exit();
            }

            $errores = $datoClinicoBusiness->validarDatoClinico($enfermedad, $otraEnfermedad, $tomaMedicamento,
                                               $medicamento, $lesion, $descripcionLesion,
                                               $discapacidad, $descripcionDiscapacidad, $restriccionMedica, $descripcionrestriccionmedica);

            if(!empty($errores)){
                $response['success'] = false;
                $response['message'] = 'Error de validación: ' . implode(', ', $errores);
                echo json_encode($response);
                exit();
            }

            $datoClinico = new DatoClinico(0, $clienteId, $enfermedad, $otraEnfermedad, $tomaMedicamento,
                                             $medicamento, $lesion, $descripcionLesion,
                                             $discapacidad,$descripcionDiscapacidad,
                                             $restriccionMedica, $descripcionrestriccionmedica);

            $resultado = $datoClinicoBusiness->insertarTBDatoClinico($datoClinico);

            if($resultado){
                $mensaje = $esUsuarioCliente ?
                    'Éxito: Sus datos clínicos se registraron correctamente.' :
                    'Éxito: Registro insertado correctamente.';

                $response['success'] = true;
                $response['message'] = $mensaje;
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo procesar la transacción en la base de datos.';
            }

        } else if(isset($_POST['update'])){

            $id = $_POST['id'];
            $clienteId = isset($_POST['clienteId']) ? $_POST['clienteId'] : '';
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


            if ($esUsuarioCliente) {
                if (!isset($_SESSION['usuario_id'])) {
                    $response['success'] = false;
                    $response['message'] = 'Error: Usuario no autenticado.';
                    echo json_encode($response);
                    exit();
                }

                $registroExistente = $datoClinicoBusiness->obtenerTBDatoClinicoPorCliente($_SESSION['usuario_id']);
                if(!$registroExistente || $registroExistente->getTbdatoclinicoid() != $id) {
                    $response['success'] = false;
                    $response['message'] = 'Error: No tiene permisos para actualizar este registro.';
                    echo json_encode($response);
                    exit();
                }

                $clienteId = $_SESSION['usuario_id'];

            } else if (!($esAdmin || $esInstructor)) {
                $response['success'] = false;
                $response['message'] = 'Error: No tiene permisos para realizar esta acción.';
                echo json_encode($response);
                exit();
            }

            if(empty($id) || empty($clienteId)){
                $response['success'] = false;
                $response['message'] = 'Error: Hay campos vacíos.';
                echo json_encode($response);
                exit();
            }

            $errores = $datoClinicoBusiness->validarDatoClinico($enfermedad, $otraEnfermedad, $tomaMedicamento,
                                                   $medicamento, $lesion, $descripcionLesion,
                                                   $discapacidad, $descripcionDiscapacidad, $restriccionMedica, $descripcionrestriccionmedica);

            if(!empty($errores)){
                $response['success'] = false;
                $response['message'] = 'Error de validación: ' . implode(', ', $errores);
                echo json_encode($response);
                exit();
            }

            $datoClinico = new DatoClinico($id, $clienteId, $enfermedad, $otraEnfermedad, $tomaMedicamento,
                                             $medicamento, $lesion, $descripcionLesion,
                                             $discapacidad,$descripcionDiscapacidad,
                                             $restriccionMedica, $descripcionrestriccionmedica);

            $resultado = $datoClinicoBusiness->actualizarTBDatoClinico($datoClinico);

            if($resultado){
                $mensaje = $esUsuarioCliente ?
                    'Éxito: Sus datos clínicos se actualizaron correctamente.' :
                    'Éxito: Registro actualizado correctamente.';

                $response['success'] = true;
                $response['message'] = $mensaje;
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo procesar la transacción en la base de datos.';
            }

        } else if(isset($_POST['delete'])){
            if (!($esAdmin || $esInstructor)) {
                $response['success'] = false;
                $response['message'] = 'Error: No tiene permisos para eliminar registros.';
                echo json_encode($response);
                exit();
            }

            $id = $_POST['id'];

            if(empty($id)){
                $response['success'] = false;
                $response['message'] = 'Error: Hay campos vacíos.';
                echo json_encode($response);
                exit();
            }

            $resultado = $datoClinicoBusiness->eliminarTBDatoClinico($id);

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
            $response['debug'] = [
                'POST_data' => $_POST,
                'session_data' => [
                    'usuario_id' => isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'no_set',
                    'tipo_usuario' => isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'no_set'
                ]
            ];
        }

    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Error: ' . $e->getMessage();
        error_log('Error en datoClinicoAction.php: ' . $e->getMessage());
    }

    echo json_encode($response);
?>