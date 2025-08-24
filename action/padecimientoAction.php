<?php
session_start();

// Incluir las clases necesarias - ajusta estas rutas según tu estructura de carpetas
include_once '../business/padecimientoBusiness.php';
if (!class_exists('Padecimiento')) {
    include_once '../domain/padecimiento.php';
}

// Configurar header para JSON
header('Content-Type: application/json');

// Crear instancia del business
$padecimientoBusiness = new PadecimientoBusiness();
$response = array();

try {
    // Verificar que el usuario esté logueado
    if (!isset($_SESSION['usuario_id'])) {
        $response['success'] = false;
        $response['message'] = 'Error: Debe iniciar sesión para acceder a esta funcionalidad.';
        echo json_encode($response);
        exit();
    }

    // Verificar permisos - Solo administradores pueden gestionar padecimientos
    $esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';

    if (!$esAdmin) {
        $response['success'] = false;
        $response['message'] = 'Error: Solo los administradores pueden gestionar padecimientos.';
        echo json_encode($response);
        exit();
    }

    // =============== CREAR PADECIMIENTO ===============
    if (isset($_POST['create'])) {
        $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        $formaDeActuar = isset($_POST['formaDeActuar']) ? trim($_POST['formaDeActuar']) : '';

        // Validar datos
        $errores = $padecimientoBusiness->validarPadecimiento($tipo, $nombre, $descripcion, $formaDeActuar);

        if (!empty($errores)) {
            $response['success'] = false;
            $response['message'] = 'Error de validación: ' . implode(', ', $errores);
        } else {
            $padecimiento = new Padecimiento(0, $tipo, $nombre, $descripcion, $formaDeActuar);
            $resultado = $padecimientoBusiness->insertarTbpadecimiento($padecimiento);

            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Éxito: Padecimiento registrado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: El nombre del padecimiento ya existe o no se pudo procesar la transacción.';
            }
        }
    }

    // =============== ACTUALIZAR PADECIMIENTO ===============
    else if (isset($_POST['update'])) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        $formaDeActuar = isset($_POST['formaDeActuar']) ? trim($_POST['formaDeActuar']) : '';

        if ($id <= 0) {
            $response['success'] = false;
            $response['message'] = 'Error: ID de padecimiento inválido.';
        } else {
            // Validar datos
            $errores = $padecimientoBusiness->validarPadecimiento($tipo, $nombre, $descripcion, $formaDeActuar);

            if (!empty($errores)) {
                $response['success'] = false;
                $response['message'] = 'Error de validación: ' . implode(', ', $errores);
            } else {
                $padecimiento = new Padecimiento($id, $tipo, $nombre, $descripcion, $formaDeActuar);
                $resultado = $padecimientoBusiness->actualizarTbpadecimiento($padecimiento);

                if ($resultado) {
                    $response['success'] = true;
                    $response['message'] = 'Éxito: Padecimiento actualizado correctamente.';
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error: El nombre del padecimiento ya existe o no se pudo procesar la transacción.';
                }
            }
        }
    }

    // =============== ELIMINAR PADECIMIENTO ===============
    else if (isset($_POST['delete'])) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {
            $response['success'] = false;
            $response['message'] = 'Error: ID de padecimiento inválido.';
        } else {
            $resultado = $padecimientoBusiness->eliminarTbpadecimiento($id);

            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Éxito: Padecimiento eliminado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo eliminar el padecimiento. Puede estar siendo utilizado por otros registros.';
            }
        }
    }

    // =============== OBTENER TIPOS DE PADECIMIENTO ===============
    else if (isset($_GET['getTipos'])) {
        $tipos = $padecimientoBusiness->obtenerTiposPadecimiento();
        $response['success'] = true;
        $response['data'] = $tipos;
    }

    // =============== OBTENER TODOS LOS PADECIMIENTOS ===============
    else if (isset($_GET['getPadecimientos'])) {
        $padecimientos = $padecimientoBusiness->obtenerTbpadecimiento();
        $response['success'] = true;
        $response['data'] = array();

        foreach ($padecimientos as $padecimiento) {
            $response['data'][] = array(
                'id' => $padecimiento->getTbpadecimientoid(),
                'tipo' => $padecimiento->getTbpadecimientotipo(),
                'nombre' => $padecimiento->getTbpadecimientonombre(),
                'descripcion' => $padecimiento->getTbpadecimientodescripcion(),
                'formaDeActuar' => $padecimiento->getTbpadecimientoformadeactuar()
            );
        }
    }

    // =============== OBTENER PADECIMIENTO POR ID ===============
    else if (isset($_GET['getPadecimiento']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $padecimiento = $padecimientoBusiness->obtenerTbpadecimientoPorId($id);

        if ($padecimiento) {
            $response['success'] = true;
            $response['data'] = array(
                'id' => $padecimiento->getTbpadecimientoid(),
                'tipo' => $padecimiento->getTbpadecimientotipo(),
                'nombre' => $padecimiento->getTbpadecimientonombre(),
                'descripcion' => $padecimiento->getTbpadecimientodescripcion(),
                'formaDeActuar' => $padecimiento->getTbpadecimientoformadeactuar()
            );
        } else {
            $response['success'] = false;
            $response['message'] = 'Error: Padecimiento no encontrado.';
        }
    }

    // =============== OBTENER PADECIMIENTOS POR TIPO ===============
    else if (isset($_GET['getPadecimientosPorTipo']) && isset($_GET['tipo'])) {
        $tipo = trim($_GET['tipo']);
        $padecimientos = $padecimientoBusiness->obtenerTbpadecimientoPorTipo($tipo);
        $response['success'] = true;
        $response['data'] = array();

        foreach ($padecimientos as $padecimiento) {
            $response['data'][] = array(
                'id' => $padecimiento->getTbpadecimientoid(),
                'tipo' => $padecimiento->getTbpadecimientotipo(),
                'nombre' => $padecimiento->getTbpadecimientonombre(),
                'descripcion' => $padecimiento->getTbpadecimientodescripcion(),
                'formaDeActuar' => $padecimiento->getTbpadecimientoformadeactuar()
            );
        }
    }

    // =============== ACCIÓN NO VÁLIDA ===============
    else {
        $response['success'] = false;
        $response['message'] = 'Error: Acción no válida.';
        $response['debug'] = [
            'POST_data' => $_POST,
            'GET_data' => $_GET,
            'session_data' => [
                'usuario_id' => isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'no_set',
                'tipo_usuario' => isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'no_set'
            ]
        ];
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
    error_log('Error en padecimientoAction.php: ' . $e->getMessage());
}

// Enviar respuesta JSON
echo json_encode($response);
?>