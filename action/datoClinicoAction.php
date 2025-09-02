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

    if (!isset($_SESSION['usuario_id'])) {
        $response['success'] = false;
        $response['message'] = 'Error: Debe iniciar sesión para acceder a esta funcionalidad.';
        echo json_encode($response);
        exit();
    }

    $esUsuarioCliente = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cliente';
    $esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
    $esInstructor = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'instructor';

    if (isset($_POST['create'])) {

        if ($esUsuarioCliente) {
            $clienteId = $_SESSION['usuario_id'];
        } else {
            $clienteId = isset($_POST['clienteId']) ? intval($_POST['clienteId']) : 0;
        }

        $padecimientosIds = array();
        if (isset($_POST['padecimientosIds']) && is_array($_POST['padecimientosIds'])) {
            foreach ($_POST['padecimientosIds'] as $id) {
                $id = intval($id);
                if ($id > 0) {
                    $padecimientosIds[] = $id;
                }
            }
        }

        $padecimientosString = empty($padecimientosIds) ? '' : implode('$', $padecimientosIds);

        $errores = $datoClinicoBusiness->validarDatoClinico($clienteId, $padecimientosString);

        if (!empty($errores)) {
            $response['success'] = false;
            $response['message'] = 'Error de validación: ' . implode(', ', $errores);
        } else {
            $datoClinico = new DatoClinico(0, $clienteId, $padecimientosString);
            $resultado = $datoClinicoBusiness->insertarTBDatoClinico($datoClinico);

            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Éxito: Dato clínico registrado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo registrar el dato clínico.';
            }
        }
    }

    else if (isset($_POST['update'])) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($esUsuarioCliente) {
            $clienteId = $_SESSION['usuario_id'];
        } else {
            $clienteId = isset($_POST['clienteId']) ? intval($_POST['clienteId']) : 0;
        }

        $padecimientosIds = array();
        if (isset($_POST['padecimientosIds']) && is_array($_POST['padecimientosIds'])) {
            foreach ($_POST['padecimientosIds'] as $padId) {
                $padId = intval($padId);
                if ($padId > 0) {
                    $padecimientosIds[] = $padId;
                }
            }
        }

        $padecimientosString = empty($padecimientosIds) ? '' : implode('$', $padecimientosIds);

        if ($id <= 0) {
            $response['success'] = false;
            $response['message'] = 'Error: ID de registro inválido.';
        } else {

            $errores = $datoClinicoBusiness->validarDatoClinico($clienteId, $padecimientosString);

            if (!empty($errores)) {
                $response['success'] = false;
                $response['message'] = 'Error de validación: ' . implode(', ', $errores);
            } else {
                $datoClinico = new DatoClinico($id, $clienteId, $padecimientosString);
                $resultado = $datoClinicoBusiness->actualizarTBDatoClinico($datoClinico);

                if ($resultado) {
                    $response['success'] = true;
                    $response['message'] = 'Éxito: Dato clínico actualizado correctamente.';
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error: No se pudo actualizar el dato clínico.';
                }
            }
        }
    }

    else if (isset($_POST['delete'])) {
        if (!$esAdmin) {
            $response['success'] = false;
            $response['message'] = 'Error: Solo los administradores pueden eliminar registros.';
        } else {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

            if ($id <= 0) {
                $response['success'] = false;
                $response['message'] = 'Error: ID de registro inválido.';
            } else {
                $resultado = $datoClinicoBusiness->eliminarTBDatoClinico($id);

                if ($resultado) {
                    $response['success'] = true;
                    $response['message'] = 'Éxito: Dato clínico eliminado correctamente.';
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error: No se pudo eliminar el dato clínico.';
                }
            }
        }
    }

    else if (isset($_POST['updateIndividual'])) {
        $registroId = isset($_POST['registroId']) ? intval($_POST['registroId']) : 0;
        $padecimientoIdAntiguo = isset($_POST['padecimientoIdAntiguo']) ? intval($_POST['padecimientoIdAntiguo']) : 0;
        $padecimientoIdNuevo = isset($_POST['padecimientoIdNuevo']) ? intval($_POST['padecimientoIdNuevo']) : 0;

        if ($registroId <= 0 || $padecimientoIdAntiguo <= 0 || $padecimientoIdNuevo <= 0) {
            $response['success'] = false;
            $response['message'] = 'Error: Datos de registro inválidos.';
        } else {
            $resultado = $datoClinicoBusiness->actualizarPadecimientoIndividual($registroId, $padecimientoIdAntiguo, $padecimientoIdNuevo);

            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Éxito: Padecimiento actualizado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo actualizar el padecimiento.';
            }
        }
    }

    else if (isset($_POST['deleteIndividual'])) {
        if (!$esAdmin) {
            $response['success'] = false;
            $response['message'] = 'Error: Solo los administradores pueden eliminar padecimientos.';
        } else {
            $registroId = isset($_POST['registroId']) ? intval($_POST['registroId']) : 0;
            $padecimientoId = isset($_POST['padecimientoId']) ? intval($_POST['padecimientoId']) : 0;

            if ($registroId <= 0 || $padecimientoId <= 0) {
                $response['success'] = false;
                $response['message'] = 'Error: Datos de registro inválidos.';
            } else {
                $resultado = $datoClinicoBusiness->eliminarPadecimientoIndividual($registroId, $padecimientoId);

                if ($resultado['success']) {
                    $response['success'] = true;
                    $response['message'] = $resultado['message'];
                } else {
                    $response['success'] = false;
                    $response['message'] = $resultado['message'];
                }
            }
        }
    }

    else {
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