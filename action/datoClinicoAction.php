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

        if(isset($_POST['create'])) {
            $clienteId = isset($_POST['clienteId']) ? (int)$_POST['clienteId'] : 0;
            $padecimientosIds = isset($_POST['padecimientosIds']) ? $_POST['padecimientosIds'] : array();

            if ($esUsuarioCliente) {
                if (!isset($_SESSION['usuario_id'])) {
                    $response['success'] = false;
                    $response['message'] = 'Error: Usuario no autenticado.';
                    echo json_encode($response);
                    exit();
                }
                $clienteId = (int)$_SESSION['usuario_id'];

            } else if ($esAdmin || $esInstructor) {
                if(empty($clienteId) || $clienteId <= 0) {
                    $response['success'] = false;
                    $response['message'] = 'Error: Debe seleccionar un cliente válido.';
                    echo json_encode($response);
                    exit();
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No tiene permisos para realizar esta acción.';
                echo json_encode($response);
                exit();
            }

            if (empty($padecimientosIds) || !is_array($padecimientosIds)) {
                $response['success'] = false;
                $response['message'] = 'Error: Debe seleccionar al menos un padecimiento.';
                echo json_encode($response);
                exit();
            }

            $padecimientosString = DatoClinico::convertirIdsAString($padecimientosIds);

            $errores = $datoClinicoBusiness->validarDatoClinico($clienteId, $padecimientosString);

            if(!empty($errores)) {
                $response['success'] = false;
                $response['message'] = 'Error de validación: ' . implode(', ', $errores);
                echo json_encode($response);
                exit();
            }

            $datoClinico = new DatoClinico(0, $clienteId, $padecimientosString);
            $resultado = $datoClinicoBusiness->insertarTBDatoClinico($datoClinico);

            if($resultado) {
                $mensaje = $esUsuarioCliente ?
                    'Éxito: Sus datos clínicos se registraron correctamente.' :
                    'Éxito: Registro insertado correctamente.';

                $response['success'] = true;
                $response['message'] = $mensaje;
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo procesar la transacción en la base de datos.';
            }
        } else if(isset($_POST['update'])) {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $clienteId = isset($_POST['clienteId']) ? (int)$_POST['clienteId'] : 0;
            $padecimientosIds = isset($_POST['padecimientosIds']) ? $_POST['padecimientosIds'] : array();

            if ($esUsuarioCliente) {
                if (!isset($_SESSION['usuario_id'])) {
                    $response['success'] = false;
                    $response['message'] = 'Error: Usuario no autenticado.';
                    echo json_encode($response);
                    exit();
                }

                $todosLosRegistros = $datoClinicoBusiness->obtenerTodosTBDatoClinicoPorCliente($_SESSION['usuario_id']);
                $esRegistroDelCliente = false;
                foreach ($todosLosRegistros as $registro) {
                    if ($registro->getTbdatoclinicoid() == $id) {
                        $esRegistroDelCliente = true;
                        break;
                    }
                }

                if(!$esRegistroDelCliente) {
                    $response['success'] = false;
                    $response['message'] = 'Error: No tiene permisos para actualizar este registro.';
                    echo json_encode($response);
                    exit();
                }
                $clienteId = (int)$_SESSION['usuario_id'];

            } else if (!($esAdmin || $esInstructor)) {
                $response['success'] = false;
                $response['message'] = 'Error: No tiene permisos para realizar esta acción.';
                echo json_encode($response);
                exit();
            }

            if(empty($id) || $id <= 0 || empty($clienteId) || $clienteId <= 0) {
                $response['success'] = false;
                $response['message'] = 'Error: Datos inválidos.';
                echo json_encode($response);
                exit();
            }

            if (empty($padecimientosIds) || !is_array($padecimientosIds)) {
                $response['success'] = false;
                $response['message'] = 'Error: Debe seleccionar al menos un padecimiento.';
                echo json_encode($response);
                exit();
            }

            $padecimientosString = DatoClinico::convertirIdsAString($padecimientosIds);

            $errores = $datoClinicoBusiness->validarDatoClinico($clienteId, $padecimientosString);

            if(!empty($errores)) {
                $response['success'] = false;
                $response['message'] = 'Error de validación: ' . implode(', ', $errores);
                echo json_encode($response);
                exit();
            }

            $datoClinico = new DatoClinico($id, $clienteId, $padecimientosString);
            $resultado = $datoClinicoBusiness->actualizarTBDatoClinico($datoClinico);

            if($resultado) {
                $mensaje = $esUsuarioCliente ?
                    'Éxito: Sus datos clínicos se actualizaron correctamente.' :
                    'Éxito: Registro actualizado correctamente.';

                $response['success'] = true;
                $response['message'] = $mensaje;
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo procesar la transacción en la base de datos.';
            }

        } else if(isset($_POST['delete'])) {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($esUsuarioCliente) {
                if (!isset($_SESSION['usuario_id'])) {
                    $response['success'] = false;
                    $response['message'] = 'Error: Usuario no autenticado.';
                    echo json_encode($response);
                    exit();
                }

                $todosLosRegistros = $datoClinicoBusiness->obtenerTodosTBDatoClinicoPorCliente($_SESSION['usuario_id']);
                $esRegistroDelCliente = false;
                foreach ($todosLosRegistros as $registro) {
                    if ($registro->getTbdatoclinicoid() == $id) {
                        $esRegistroDelCliente = true;
                        break;
                    }
                }

                if(!$esRegistroDelCliente) {
                    $response['success'] = false;
                    $response['message'] = 'Error: No tiene permisos para eliminar este registro.';
                    echo json_encode($response);
                    exit();
                }
            } else if (!($esAdmin || $esInstructor)) {
                $response['success'] = false;
                $response['message'] = 'Error: No tiene permisos para eliminar registros.';
                echo json_encode($response);
                exit();
            }

            if(empty($id) || $id <= 0) {
                $response['success'] = false;
                $response['message'] = 'Error: ID inválido.';
                echo json_encode($response);
                exit();
            }

            $resultado = $datoClinicoBusiness->eliminarTBDatoClinico($id);

            if($resultado) {
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
        $response['message'] = 'Error interno del servidor.';
        error_log('Error en datoClinicoAction.php: ' . $e->getMessage());
    }

    echo json_encode($response);
?>