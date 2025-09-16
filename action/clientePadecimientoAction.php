<?php
// SOLUCIÓN COMPLETA: Capturar y limpiar cualquier output
ob_start(); // Iniciar buffer de salida

// Suprimir TODOS los errores y notices
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

include_once '../business/clientePadecimientoBusiness.php';
if (!class_exists('ClientePadecimiento')) {
    include_once '../domain/clientePadecimiento.php';
}

// Limpiar cualquier output previo (notices, warnings, etc.)
ob_clean();

// Configurar headers para JSON
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

$clientePadecimientoBusiness = new ClientePadecimientoBusiness();
$response = array();

try {
    // Validación de sesión
    if (!isset($_SESSION['usuario_id'])) {
        $response['success'] = false;
        $response['message'] = 'Error: Debe iniciar sesión para acceder a esta funcionalidad.';
        ob_clean(); // Limpiar buffer antes de enviar respuesta
        echo json_encode($response);
        exit();
    }

    $esUsuarioCliente = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cliente';
    $esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
    $esInstructor = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'instructor';

    // ============== CREATE ==============
    if (isset($_POST['create'])) {
        // Determinar cliente ID
        if ($esUsuarioCliente) {
            $clienteId = $_SESSION['usuario_id'];
        } else {
            $clienteId = isset($_POST['clienteId']) ? intval($_POST['clienteId']) : 0;
        }

        // Procesar padecimientos
        $padecimientosIds = array();
        if (isset($_POST['padecimientosIds']) && is_array($_POST['padecimientosIds'])) {
            foreach ($_POST['padecimientosIds'] as $id) {
                $id = intval($id);
                if ($id > 0) {
                    $padecimientosIds[] = $id;
                }
            }
        }

        // Validar que se haya seleccionado al menos un padecimiento
        if (empty($padecimientosIds)) {
            $response['success'] = false;
            $response['message'] = 'Error: Debe seleccionar al menos un padecimiento.';
            ob_clean();
            echo json_encode($response);
            exit();
        }

        $padecimientosString = implode('$', $padecimientosIds);

        // Procesar dictamen
        $dictamenId = null;
        if (isset($_POST['dictamenId']) && !empty($_POST['dictamenId']) && $_POST['dictamenId'] !== '0') {
            $dictamenId = intval($_POST['dictamenId']);
        }

        // Validación
        $errores = $clientePadecimientoBusiness->validarClientePadecimiento($clienteId, $padecimientosString);

        if (!empty($errores)) {
            $response['success'] = false;
            $response['message'] = 'Error de validación: ' . implode(', ', $errores);
        } else {
            // Crear objeto y guardar - con manejo de errores silencioso
            $clientePadecimiento = new ClientePadecimiento(0, $clienteId, $padecimientosString, $dictamenId);

            // Capturar cualquier output durante la inserción
            ob_start();
            $resultado = $clientePadecimientoBusiness->insertarTBClientePadecimiento($clientePadecimiento);
            ob_end_clean(); // Descartar cualquier output capturado

            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Éxito: Cliente padecimiento registrado correctamente.';

                // Limpiar datos temporales de sesión si existen
                if (isset($_SESSION['temp_form_data'])) {
                    unset($_SESSION['temp_form_data']);
                }
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo registrar el cliente padecimiento en la base de datos.';
            }
        }
    }

    // ============== UPDATE ==============
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

        if (empty($padecimientosIds)) {
            $response['success'] = false;
            $response['message'] = 'Error: Debe seleccionar al menos un padecimiento.';
            ob_clean();
            echo json_encode($response);
            exit();
        }

        $padecimientosString = implode('$', $padecimientosIds);

        $dictamenId = null;
        if (isset($_POST['dictamenId']) && !empty($_POST['dictamenId']) && $_POST['dictamenId'] !== '0') {
            $dictamenId = intval($_POST['dictamenId']);
        }

        if ($id <= 0) {
            $response['success'] = false;
            $response['message'] = 'Error: ID de registro inválido.';
        } else {
            $errores = $clientePadecimientoBusiness->validarClientePadecimiento($clienteId, $padecimientosString);

            if (!empty($errores)) {
                $response['success'] = false;
                $response['message'] = 'Error de validación: ' . implode(', ', $errores);
            } else {
                $clientePadecimiento = new ClientePadecimiento($id, $clienteId, $padecimientosString, $dictamenId);

                ob_start();
                $resultado = $clientePadecimientoBusiness->actualizarTBClientePadecimiento($clientePadecimiento);
                ob_end_clean();

                if ($resultado) {
                    $response['success'] = true;
                    $response['message'] = 'Éxito: Cliente padecimiento actualizado correctamente.';
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error: No se pudo actualizar el cliente padecimiento.';
                }
            }
        }
    }

    // ============== DELETE ==============
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
                ob_start();
                $resultado = $clientePadecimientoBusiness->eliminarTBClientePadecimientoConDictamenes($id);
                ob_end_clean();

                if ($resultado) {
                    $response['success'] = true;
                    $response['message'] = 'Éxito: Cliente padecimiento eliminado correctamente.';
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error: No se pudo eliminar el cliente padecimiento.';
                }
            }
        }
    }

    // ============== UPDATE INDIVIDUAL ==============
    else if (isset($_POST['updateIndividual'])) {
        $registroId = isset($_POST['registroId']) ? intval($_POST['registroId']) : 0;
        $padecimientoIdAntiguo = isset($_POST['padecimientoIdAntiguo']) ? intval($_POST['padecimientoIdAntiguo']) : 0;
        $padecimientoIdNuevo = isset($_POST['padecimientoIdNuevo']) ? intval($_POST['padecimientoIdNuevo']) : 0;

        if ($registroId <= 0 || $padecimientoIdAntiguo <= 0 || $padecimientoIdNuevo <= 0) {
            $response['success'] = false;
            $response['message'] = 'Error: Datos de registro inválidos.';
        } else {
            ob_start();
            $resultado = $clientePadecimientoBusiness->actualizarPadecimientoIndividual($registroId, $padecimientoIdAntiguo, $padecimientoIdNuevo);
            ob_end_clean();

            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Éxito: Padecimiento actualizado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo actualizar el padecimiento.';
            }
        }
    }

    // ============== DELETE INDIVIDUAL ==============
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
                ob_start();
                $resultado = $clientePadecimientoBusiness->eliminarPadecimientoIndividual($registroId, $padecimientoId);
                ob_end_clean();

                if (is_array($resultado)) {
                    $response['success'] = $resultado['success'];
                    $response['message'] = $resultado['message'];
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error: Respuesta inesperada del servidor.';
                }
            }
        }
    }

    // ============== ACCIÓN NO VÁLIDA ==============
    else {
        $response['success'] = false;
        $response['message'] = 'Error: Acción no válida.';
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error del servidor: ' . $e->getMessage();
    error_log('Error en clientePadecimientoAction.php: ' . $e->getMessage());
} catch (Error $e) {
    $response['success'] = false;
    $response['message'] = 'Error fatal del servidor.';
    error_log('Error fatal en clientePadecimientoAction.php: ' . $e->getMessage());
}

// CRÍTICO: Limpiar completamente el buffer antes de enviar la respuesta
ob_clean();

// Enviar solo la respuesta JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit();
?>