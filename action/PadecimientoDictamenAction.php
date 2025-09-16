<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

ob_start();

try {
    include_once '../business/PadecimientoDictamenBusiness.php';
    include_once '../utility/ImageManager.php';

    header('Content-Type: application/json');

    $response = [
        'success' => false,
        'message' => 'Ha ocurrido un error.',
        'padecimiento' => null
    ];

    if (!isset($_SESSION['tipo_usuario'])) {
        $response['message'] = 'No autorizado. Por favor, inicie sesión.';
        echo json_encode($response);
        exit();
    }

    $esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');
    $esCliente = ($_SESSION['tipo_usuario'] === 'cliente');

    $padecimientoDictamenBusiness = new PadecimientoDictamenBusiness();
    $imageManager = new ImageManager();

    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'guardar':
            ob_clean();

            $fechaemision = $_POST['fechaemision'] ?? '';
            $entidademision = $_POST['entidademision'] ?? '';

            $clienteId = null;

            if ($esAdminOInstructor) {

                $clienteId = $_POST['clienteId'] ?? '';
                if (empty($clienteId) || !is_numeric($clienteId)) {
                    $response['message'] = 'Debe seleccionar un cliente válido.';
                    break;
                }
            } else if ($esCliente) {
                $clienteId = $_SESSION['usuario_id'];

                if (empty($clienteId)) {
                    $response['message'] = 'No se pudo identificar el cliente. Inicie sesión nuevamente.';
                    break;
                }
            }

            if (empty($fechaemision) || empty($entidademision)) {
                $response['message'] = 'La fecha y entidad de emisión son obligatorias.';
                break;
            }

            if (strtotime($fechaemision) > time()) {
                $response['message'] = 'La fecha de emisión no puede ser futura.';
                break;
            }

            include_once '../business/clientePadecimientoBusiness.php';
            $clientePadecimientoBusiness = new ClientePadecimientoBusiness();
            $dictamenesExistentes = $clientePadecimientoBusiness->obtenerDictamenesPorCliente($clienteId);

            if (!empty($dictamenesExistentes)) {
                $response['message'] = 'Este cliente ya posee un dictamen registrado. No es posible registrar múltiples dictámenes para el mismo cliente.';
                break;
            }

            $padecimiento = new PadecimientoDictamen(0, $fechaemision, $entidademision, '');
            $nuevoId = $padecimientoDictamenBusiness->insertarTBPadecimientoDictamen($padecimiento);

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

                $asociacionExitosa = $padecimientoDictamenBusiness->asociarDictamenACliente($clienteId, $nuevoId);

                if ($asociacionExitosa) {
                    $response['success'] = true;
                    $response['message'] = 'Padecimiento dictamen creado exitosamente.';
                    $response['dictamenId'] = $nuevoId;
                    $response['entidadEmision'] = $entidademision;
                    $response['padecimiento'] = [
                        'id' => $nuevoId,
                        'fechaemision' => $fechaemision,
                        'entidademision' => $entidademision,
                        'imagenes' => $imageManager->getImagesByIds($imagenIdLista)
                    ];
                } else {

                    $padecimientoDictamenBusiness->eliminarTBPadecimientoDictamen($nuevoId);
                    $response['message'] = 'Error al asociar el dictamen con el cliente.';
                }
            } else {
                $response['message'] = 'Error al crear el padecimiento dictamen en la base de datos.';
            }
            break;

        case 'actualizar':
            ob_clean();

            $id = $_POST['id'] ?? null;
            $fechaemision = $_POST['fechaemision'] ?? null;
            $entidademision = $_POST['entidademision'] ?? null;
            $imagenesNuevas = $_FILES['imagenes'] ?? null;

            if (empty($id) || empty($fechaemision) || empty($entidademision)) {
                $response['message'] = 'Faltan datos obligatorios para actualizar.';
                break;
            }

            if (!is_numeric($id)) {
                $response['message'] = 'ID de padecimiento inválido.';
                break;
            }

            if (strtotime($fechaemision) > time()) {
                $response['message'] = 'La fecha de emisión no puede ser futura.';
                break;
            }

            $padecimientoActual = $padecimientoDictamenBusiness->getPadecimientoDictamenPorId($id);
            if (!$padecimientoActual) {
                $response['message'] = 'Padecimiento no encontrado.';
                break;
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
            break;

        case 'eliminar':
            ob_clean();

            if (!$esAdminOInstructor) {
                $response['message'] = 'No tiene permisos para eliminar padecimientos.';
                break;
            }

            $id = $_POST['id'] ?? null;
            if (empty($id) || !is_numeric($id)) {
                $response['message'] = 'ID de padecimiento inválido.';
                break;
            }

            $padecimientoExistente = $padecimientoDictamenBusiness->getPadecimientoDictamenPorId($id);
            if (!$padecimientoExistente) {
                $response['message'] = 'El padecimiento no existe en la base de datos.';
                break;
            }

            $eliminacionExitosa = $padecimientoDictamenBusiness->eliminarTBPadecimientoDictamen($id);

            if ($eliminacionExitosa) {
                $response['success'] = true;
                $response['message'] = 'Padecimiento eliminado exitosamente.';
            } else {
                $response['message'] = 'Error al eliminar el padecimiento de la base de datos.';
            }
            break;

        case 'borrar_imagen':
            ob_clean();

            if (!$esAdminOInstructor) {
                $response['message'] = 'No tiene permisos para eliminar imágenes.';
                break;
            }

            $padecimientoId = $_POST['padecimiento_id'] ?? null;
            $imagenId = $_POST['imagen_id'] ?? null;

            if (empty($padecimientoId) || empty($imagenId)) {
                $response['message'] = 'ID de padecimiento o imagen faltante.';
                break;
            }

            if (!is_numeric($padecimientoId) || !is_numeric($imagenId)) {
                $response['message'] = 'IDs deben ser numéricos.';
                break;
            }

            if ($padecimientoDictamenBusiness->eliminarImagenDePadecimiento($padecimientoId, $imagenId)) {
                $response['success'] = true;
                $response['message'] = 'Imagen eliminada exitosamente.';
            } else {
                $response['message'] = 'Error al eliminar la imagen.';
            }
            break;

        default:
            $response['message'] = 'Acción no válida. Acciones disponibles: guardar, actualizar, eliminar, borrar_imagen';
            break;
    }

} catch (Exception $e) {
    ob_clean();
    error_log("Exception en PadecimientoDictamenAction: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    $response = [
        'success' => false,
        'message' => 'Error del sistema. Por favor, revise los logs del servidor.'
    ];
}

ob_clean();
echo json_encode($response);
exit();
?>