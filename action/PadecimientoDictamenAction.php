<?php
session_start();
ob_start();

error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    include_once '../business/PadecimientoDictamenBusiness.php';
    include_once '../utility/ImageManager.php';
    include_once '../utility/Validation.php';

    Validation::start();

    if (!isset($_SESSION['tipo_usuario'])) {
        header("location: ../view/loginView.php");
        exit();
    }

    $esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');
    $esCliente = ($_SESSION['tipo_usuario'] === 'cliente');

    $padecimientoDictamenBusiness = new PadecimientoDictamenBusiness();
    $imageManager = new ImageManager();

    $redirect = "location: ../view/padecimientoDictamenView.php";

    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'guardar':
            // Detectar si es AJAX
            $isAjax = isset($_POST['ajax_request']) && $_POST['ajax_request'] == '1';

            Validation::setOldInput($_POST);

            $fechaemision = $_POST['fechaemision'] ?? '';
            $entidademision = $_POST['entidademision'] ?? '';

            $clienteId = null;

            if ($esAdminOInstructor) {
                $clienteId = $_POST['clienteId'] ?? '';
                if (empty($clienteId) || !is_numeric($clienteId)) {
                    Validation::setError('clienteId', 'Debe seleccionar un cliente válido.');
                }
            } else if ($esCliente) {
                $clienteId = $_SESSION['usuario_id'];
                if (empty($clienteId)) {
                    Validation::setError('general', 'No se pudo identificar el cliente. Inicie sesión nuevamente.');
                }
            }

            if (empty($fechaemision)) {
                Validation::setError('fechaemision', 'La fecha de emisión es obligatoria.');
            } elseif (strtotime($fechaemision) > time()) {
                Validation::setError('fechaemision', 'La fecha de emisión no puede ser futura.');
            }

            if (empty($entidademision)) {
                Validation::setError('entidademision', 'La entidad de emisión es obligatoria.');
            }

            // Si hay errores
            if (Validation::hasErrors()) {
                if ($isAjax) {
                    ob_clean();
                    header('Content-Type: application/json');
                    $errores = Validation::getErrors();
                    $mensajeError = implode(', ', array_values($errores));
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error de validación: ' . $mensajeError,
                        'errors' => $errores
                    ]);
                    exit();
                }
                header($redirect);
                exit();
            }

            include_once '../business/clientePadecimientoBusiness.php';
            $clientePadecimientoBusiness = new ClientePadecimientoBusiness();
            $dictamenesExistentes = $clientePadecimientoBusiness->obtenerDictamenesPorCliente($clienteId);

            if (!empty($dictamenesExistentes)) {
                if ($isAjax) {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Este cliente ya posee un dictamen registrado. No es posible registrar múltiples dictámenes para el mismo cliente.'
                    ]);
                    exit();
                }
                Validation::setError('general', 'Este cliente ya posee un dictamen registrado. No es posible registrar múltiples dictámenes para el mismo cliente.');
                header($redirect);
                exit();
            }

            $padecimiento = new PadecimientoDictamen(0, $fechaemision, $entidademision, '');

            ob_start();
            $nuevoId = $padecimientoDictamenBusiness->insertarTBPadecimientoDictamen($padecimiento);
            ob_end_clean();

            if ($nuevoId > 0) {
                $imagenIdLista = '';
                if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                    $imageIds = $imageManager->addImages($_FILES['imagenes'], $nuevoId, 'pad');
                    if (!empty($imageIds)) {
                        $imagenIdLista = implode('$', $imageIds);

                        $padecimiento->setPadecimientodictamenid($nuevoId);
                        $padecimiento->setPadecimientodictamenimagenid($imagenIdLista);
                        $padecimientoDictamenBusiness->actualizarTBPadecimientoDictamen($padecimiento);
                    }
                }

                ob_start();
                $asociacionExitosa = $padecimientoDictamenBusiness->asociarDictamenACliente($clienteId, $nuevoId);
                ob_end_clean();

                if ($asociacionExitosa) {
                    Validation::clear();
                    if ($isAjax) {
                        ob_clean();
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'message' => 'Dictamen registrado exitosamente',
                            'dictamenId' => $nuevoId
                        ]);
                        exit();
                    }
                    header($redirect . "?success=created");
                    exit();
                } else {
                    $padecimientoDictamenBusiness->eliminarTBPadecimientoDictamen($nuevoId);
                    if ($isAjax) {
                        ob_clean();
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => 'Error al asociar el dictamen con el cliente.'
                        ]);
                        exit();
                    }
                    Validation::setError('general', 'Error al asociar el dictamen con el cliente.');
                    header($redirect);
                    exit();
                }
            } else {
                if ($isAjax) {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al crear el padecimiento dictamen en la base de datos.'
                    ]);
                    exit();
                }
                Validation::setError('general', 'Error al crear el padecimiento dictamen en la base de datos.');
                header($redirect);
                exit();
            }
            break;

        case 'actualizar':
            ob_clean();
            header('Content-Type: application/json');

            $response = [
                'success' => false,
                'message' => 'Ha ocurrido un error.',
                'padecimiento' => null
            ];

            $id = $_POST['id'] ?? null;
            $fechaemision = $_POST['fechaemision'] ?? null;
            $entidademision = $_POST['entidademision'] ?? null;
            $imagenesNuevas = $_FILES['imagenes'] ?? null;

            if (empty($id) || empty($fechaemision) || empty($entidademision)) {
                $response['message'] = 'Faltan datos obligatorios para actualizar.';
                echo json_encode($response);
                exit();
            }

            if (!is_numeric($id)) {
                $response['message'] = 'ID de padecimiento inválido.';
                echo json_encode($response);
                exit();
            }

            if (strtotime($fechaemision) > time()) {
                $response['message'] = 'La fecha de emisión no puede ser futura.';
                echo json_encode($response);
                exit();
            }

            $padecimientoActual = $padecimientoDictamenBusiness->getPadecimientoDictamenPorId($id);
            if (!$padecimientoActual) {
                $response['message'] = 'Padecimiento no encontrado.';
                echo json_encode($response);
                exit();
            }

            $padecimientoActual->setPadecimientodictamenfechaemision($fechaemision);
            $padecimientoActual->setPadecimientodictamenentidademision($entidademision);

            if (isset($imagenesNuevas['name'][0]) && !empty($imagenesNuevas['name'][0])) {
                $currentIds = $padecimientoActual->getPadecimientodictamenimagenid();
                $imageIds = $imageManager->addImages($_FILES['imagenes'], $id, 'pad');
                if (!empty($imageIds)) {
                    $newIds = ImageManager::addIdsToString($imageIds, $currentIds);
                    $padecimientoActual->setPadecimientodictamenimagenid($newIds);
                }
            }

            if ($padecimientoDictamenBusiness->actualizarTBPadecimientoDictamen($padecimientoActual)) {
                $response['success'] = true;
                $response['message'] = 'Padecimiento actualizado exitosamente.';
                $response['padecimiento'] = [
                    'id' => $id,
                    'fechaemision' => $fechaemision,
                    'entidademision' => $entidademision,
                    'imagenes' => $imageManager->getImagesByIds($padecimientoActual->getPadecimientodictamenimagenid())
                ];
            } else {
                $response['message'] = 'Error al actualizar el padecimiento en la base de datos.';
            }

            echo json_encode($response);
            exit();

        case 'eliminar':
            ob_clean();
            header('Content-Type: application/json');

            $response = [
                'success' => false,
                'message' => 'Ha ocurrido un error.'
            ];

            if (!$esAdminOInstructor) {
                $response['message'] = 'No tiene permisos para eliminar padecimientos.';
                echo json_encode($response);
                exit();
            }

            $id = $_POST['id'] ?? null;
            if (empty($id) || !is_numeric($id)) {
                $response['message'] = 'ID de padecimiento inválido.';
                echo json_encode($response);
                exit();
            }

            $padecimientoExistente = $padecimientoDictamenBusiness->getPadecimientoDictamenPorId($id);
            if (!$padecimientoExistente) {
                $response['message'] = 'El padecimiento no existe en la base de datos.';
                echo json_encode($response);
                exit();
            }

            $eliminacionExitosa = $padecimientoDictamenBusiness->eliminarTBPadecimientoDictamen($id);

            if ($eliminacionExitosa) {
                $response['success'] = true;
                $response['message'] = 'Padecimiento eliminado exitosamente.';
            } else {
                $response['message'] = 'Error al eliminar el padecimiento de la base de datos.';
            }

            echo json_encode($response);
            exit();

        case 'borrar_imagen':
            ob_clean();
            header('Content-Type: application/json');

            $response = [
                'success' => false,
                'message' => 'Ha ocurrido un error.'
            ];

            if (!$esAdminOInstructor) {
                $response['message'] = 'No tiene permisos para eliminar imágenes.';
                echo json_encode($response);
                exit();
            }

            $padecimientoId = $_POST['padecimiento_id'] ?? null;
            $imagenId = $_POST['imagen_id'] ?? null;

            if (empty($padecimientoId) || empty($imagenId)) {
                $response['message'] = 'ID de padecimiento o imagen faltante.';
                echo json_encode($response);
                exit();
            }

            if (!is_numeric($padecimientoId) || !is_numeric($imagenId)) {
                $response['message'] = 'IDs deben ser numéricos.';
                echo json_encode($response);
                exit();
            }

            if ($padecimientoDictamenBusiness->eliminarImagenDePadecimiento($padecimientoId, $imagenId)) {
                $response['success'] = true;
                $response['message'] = 'Imagen eliminada exitosamente.';
            } else {
                $response['message'] = 'Error al eliminar la imagen.';
            }

            echo json_encode($response);
            exit();

        default:
            header($redirect);
            break;
    }

} catch (Exception $e) {
    ob_clean();
    error_log("Exception en PadecimientoDictamenAction: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    Validation::setError('general', 'Error del sistema. Por favor, intente nuevamente.');
    header("location: ../view/padecimientoDictamenView.php");
    exit();
}

exit();
?>