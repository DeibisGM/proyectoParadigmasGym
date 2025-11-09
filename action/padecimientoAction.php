<?php
session_start();

include_once '../business/padecimientoBusiness.php';
include_once '../business/clientePadecimientoBusiness.php';
include_once '../utility/Validation.php';
if (!class_exists('Padecimiento')) {
    include_once '../domain/padecimiento.php';
}

header('Content-Type: application/json');

$padecimientoBusiness = new PadecimientoBusiness();
$clientePadecimientoBusiness = new clientePadecimientoBusiness();
$response = array();

try {

    if (!isset($_SESSION['usuario_id'])) {
        $response['success'] = false;
        $response['message'] = 'Error: Debe iniciar sesión para acceder a esta funcionalidad.';
        echo json_encode($response);
        exit();
    }

    $esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
    $esInstructor = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'instructor';

    if (!$esAdmin && !$esInstructor) {
        $response['success'] = false;
        $response['message'] = 'Error: Solo los administradores e instructores pueden gestionar padecimientos.';
        echo json_encode($response);
        exit();
    }

    if (isset($_POST['create'])) {
        Validation::setOldInput($_POST);

        $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        $formaDeActuar = isset($_POST['formaDeActuar']) ? trim($_POST['formaDeActuar']) : '';

        // Validaciones
        if (empty($tipo)) {
            Validation::setError('tipo', 'El tipo de padecimiento es obligatorio.');
        }

        if (empty($nombre)) {
            Validation::setError('nombre', 'El nombre es obligatorio.');
        } elseif (strlen($nombre) < 3) {
            Validation::setError('nombre', 'El nombre debe tener al menos 3 caracteres.');
        }

        if (empty($descripcion)) {
            Validation::setError('descripcion', 'La descripción es obligatoria.');
        } elseif (strlen($descripcion) < 10) {
            Validation::setError('descripcion', 'La descripción debe tener al menos 10 caracteres.');
        }

        if (empty($formaDeActuar)) {
            Validation::setError('formaDeActuar', 'La forma de actuar es obligatoria.');
        } elseif (strlen($formaDeActuar) < 10) {
            Validation::setError('formaDeActuar', 'La forma de actuar debe tener al menos 10 caracteres.');
        }

        if (Validation::hasErrors()) {
            $response['success'] = false;
            // No se establece un mensaje general aquí, ya que los errores específicos se manejarán en el frontend
            $response['errors'] = [
                'tipo' => Validation::getError('tipo'),
                'nombre' => Validation::getError('nombre'),
                'descripcion' => Validation::getError('descripcion'),
                'formaDeActuar' => Validation::getError('formaDeActuar')
            ];
        } else {
            $padecimiento = new Padecimiento(0, $tipo, $nombre, $descripcion, $formaDeActuar);
            $resultado = $padecimientoBusiness->insertarTbpadecimiento($padecimiento);

            if ($resultado) {
                Validation::clear();
                $response['success'] = true;
                $response['message'] = 'Éxito: Padecimiento registrado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: El nombre del padecimiento ya existe o no se pudo procesar la transacción.';
            }
        }
    } else if (isset($_POST['update'])) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {
            $response['success'] = false;
            $response['message'] = 'Error: ID de padecimiento inválido.';
        } else {
            Validation::setOldInput($_POST);

            $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $formaDeActuar = isset($_POST['formaDeActuar']) ? trim($_POST['formaDeActuar']) : '';

            // Validaciones con sufijo del ID
            if (empty($tipo)) {
                Validation::setError('tipo_'.$id, 'El tipo de padecimiento es obligatorio.');
            }

            if (empty($nombre)) {
                Validation::setError('nombre_'.$id, 'El nombre es obligatorio.');
            } elseif (strlen($nombre) < 3) {
                Validation::setError('nombre_'.$id, 'El nombre debe tener al menos 3 caracteres.');
            }

            if (empty($descripcion)) {
                Validation::setError('descripcion_'.$id, 'La descripción es obligatoria.');
            } elseif (strlen($descripcion) < 10) {
                Validation::setError('descripcion_'.$id, 'La descripción debe tener al menos 10 caracteres.');
            }

            if (empty($formaDeActuar)) {
                Validation::setError('formaDeActuar_'.$id, 'La forma de actuar es obligatoria.');
            } elseif (strlen($formaDeActuar) < 10) {
                Validation::setError('formaDeActuar_'.$id, 'La forma de actuar debe tener al menos 10 caracteres.');
            }

            if (Validation::hasErrors()) {
                $response['success'] = false;
                // No se establece un mensaje general aquí, ya que los errores específicos se manejarán en el frontend
                $response['errors'] = [
                    'tipo_'.$id => Validation::getError('tipo_'.$id),
                    'nombre_'.$id => Validation::getError('nombre_'.$id),
                    'descripcion_'.$id => Validation::getError('descripcion_'.$id),
                    'formaDeActuar_'.$id => Validation::getError('formaDeActuar_'.$id)
                ];
            } else {
                $clientesAfectados = $clientePadecimientoBusiness->padecimientoEnUso($id);

                $padecimiento = new Padecimiento($id, $tipo, $nombre, $descripcion, $formaDeActuar);
                $resultado = $padecimientoBusiness->actualizarTbpadecimiento($padecimiento);

                if ($resultado) {
                    Validation::clear();
                    $mensaje = 'Éxito: Padecimiento actualizado correctamente.';

                    if (!empty($clientesAfectados)) {
                        $registrosAfectados = $clientePadecimientoBusiness->modificarPadecimientoEnRegistros($id, $id);
                        if ($registrosAfectados > 0) {
                            $mensaje .= " Se reordenaron $registrosAfectados registros de datos clínicos (el padecimiento modificado se movió al final de las listas).";
                        }
                    }

                    $response['success'] = true;
                    $response['message'] = $mensaje;
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error: El nombre del padecimiento ya existe o no se pudo procesar la transacción.';
                }
            }
        }
    } else if (isset($_POST['delete'])) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {
            $response['success'] = false;
            $response['message'] = 'Error: ID de padecimiento inválido.';
        } else {

            $clientesAfectados = $clientePadecimientoBusiness->padecimientoEnUso($id);

            if (!empty($clientesAfectados)) {

                $carnets = array();
                foreach ($clientesAfectados as $cliente) {
                    $carnets[] = $cliente['carnet'];
                }

                $mensaje = "ADVERTENCIA: Este padecimiento está siendo usado por " . count($clientesAfectados) . " registro(s) de datos clínicos. ";
                $mensaje .= "Clientes con carnet: " . implode(', ', array_unique($carnets)) . ". ";
                $mensaje .= "\n\n¿Desea continuar? Si elimina el padecimiento, se eliminará de todos los registros de datos clínicos donde aparezca.";

                $response['success'] = false;
                $response['message'] = $mensaje;
                $response['requiereConfirmacion'] = true;
                $response['clientesAfectados'] = count($clientesAfectados);
                echo json_encode($response);
                exit();
            }

            $resultado = $padecimientoBusiness->eliminarTbpadecimiento($id);

            if ($resultado) {
                $response['success'] = true;
                $response['message'] = 'Éxito: Padecimiento eliminado correctamente.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo eliminar el padecimiento.';
            }
        }
    } else if (isset($_POST['confirmDelete'])) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {
            $response['success'] = false;
            $response['message'] = 'Error: ID de padecimiento inválido.';
        } else {

            $resultadosLimpieza = $clientePadecimientoBusiness->eliminarPadecimientoDeRegistros($id);

            $resultado = $padecimientoBusiness->eliminarTbpadecimiento($id);

            if ($resultado) {
                $mensaje = 'Éxito: Padecimiento eliminado correctamente.';

                if ($resultadosLimpieza['registrosActualizados'] > 0 || $resultadosLimpieza['registrosEliminados'] > 0) {
                    $mensaje .= " Se procesaron los datos clínicos: ";

                    if ($resultadosLimpieza['registrosActualizados'] > 0) {
                        $mensaje .= "{$resultadosLimpieza['registrosActualizados']} registros actualizados";
                    }

                    if ($resultadosLimpieza['registrosEliminados'] > 0) {
                        if ($resultadosLimpieza['registrosActualizados'] > 0) {
                            $mensaje .= " y ";
                        }
                        $mensaje .= "{$resultadosLimpieza['registrosEliminados']} registros eliminados por quedar sin padecimientos";
                    }

                    $mensaje .= ".";
                }

                $response['success'] = true;
                $response['message'] = $mensaje;
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: No se pudo eliminar el padecimiento de la base de datos.';
            }
        }
    } else if (isset($_GET['getTipos'])) {
        $tipos = $padecimientoBusiness->obtenerTiposPadecimiento();
        $response['success'] = true;
        $response['data'] = $tipos;
    } else if (isset($_GET['getPadecimientos'])) {
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
    } else if (isset($_GET['getPadecimiento']) && isset($_GET['id'])) {
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
    } else if (isset($_GET['getPadecimientosPorTipo']) && isset($_GET['tipo'])) {
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
    } else {
        $response['success'] = false;
        $response['message'] = 'Error: Acción no válida.';
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
    error_log('Error en padecimientoAction.php: ' . $e->getMessage());
}

echo json_encode($response);
?>