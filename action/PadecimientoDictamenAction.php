<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

include '../business/PadecimientoDictamenBusiness.php';
include_once '../utility/ImageManager.php';

// Usar ruta relativa como en el ejemplo
$redirect_path = '../view/PadecimientoDictamenView.php';

// Verificar sesión
if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

// Verificar permisos
$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');
$esCliente = ($_SESSION['tipo_usuario'] === 'cliente');

if (!$esAdminOInstructor && !$esCliente) {
    header("location: " . $redirect_path . "?error=unauthorized");
    exit();
}

$padecimientoDictamenBusiness = new PadecimientoDictamenBusiness();
$imageManager = new ImageManager();

// Logging para debugging
error_log("POST data: " . print_r($_POST, true));

if (isset($_POST['borrar_imagen'])) {
    // Solo admin e instructor pueden borrar imágenes
    if (!$esAdminOInstructor) {
        header("location: " . $redirect_path . "?error=unauthorized");
        exit();
    }

    if (isset($_POST['id'])) {
        $padecimientoId = $_POST['id'];
        $imagenId = $_POST['borrar_imagen'];

        $padecimiento = $padecimientoDictamenBusiness->getPadecimientoDictamenPorId($padecimientoId);
        if ($padecimiento) {
            $imageManager->deleteImage($imagenId);
            $currentIds = $padecimiento->getPadecimientodictamenimagenid();
            $newIds = ImageManager::removeIdFromString($imagenId, $currentIds);
            $padecimiento->setPadecimientodictamenimagenid($newIds);
            $padecimientoDictamenBusiness->actualizarTBPadecimientoDictamen($padecimiento);
            header("location: " . $redirect_path . "?success=image_deleted");
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }

} else if (isset($_POST['guardar'])) {
    if (isset($_POST['fechaemision']) && isset($_POST['entidademision'])) {

        $fechaemision = trim($_POST['fechaemision']);
        $entidademision = trim($_POST['entidademision']);

        // Validaciones
        if (empty($fechaemision) || empty($entidademision)) {
            header("location: " . $redirect_path . "?error=datos_faltantes");
            exit();
        }

        // Para admin/instructor, validar que se haya proporcionado un carnet
        if ($esAdminOInstructor) {
            if (!isset($_POST['cliente_carnet']) || empty(trim($_POST['cliente_carnet']))) {
                header("location: " . $redirect_path . "?error=cliente_requerido");
                exit();
            }

            // Validar que el cliente existe
            $cliente = $padecimientoDictamenBusiness->getClientePorCarnet(trim($_POST['cliente_carnet']));
            if (!$cliente) {
                header("location: " . $redirect_path . "?error=cliente_no_encontrado");
                exit();
            }
        }

        // Validar que la fecha no sea futura
        if (strtotime($fechaemision) > time()) {
            header("location: " . $redirect_path . "?error=fecha_futura");
            exit();
        }

        try {
            $padecimiento = new PadecimientoDictamen(0, $fechaemision, $entidademision, '');
            $nuevoId = $padecimientoDictamenBusiness->insertarTBPadecimientoDictamen($padecimiento);

            if ($nuevoId > 0) {
                // Procesar imágenes si existen
                if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                    $newImageIds = $imageManager->addImages($_FILES['imagenes'], $nuevoId, 'pad');
                    if (!empty($newImageIds)) {
                        $padecimientoAgregado = $padecimientoDictamenBusiness->getPadecimientoDictamenPorId($nuevoId);
                        $idString = ImageManager::addIdsToString($newImageIds, '');
                        $padecimientoAgregado->setPadecimientodictamenimagenid($idString);
                        $padecimientoDictamenBusiness->actualizarTBPadecimientoDictamen($padecimientoAgregado);
                    }
                }

                // TODO: Aquí deberías agregar la lógica para asociar el padecimiento
                // con el cliente en tu tabla intermedia cuando esté disponible

                header("location: " . $redirect_path . "?success=inserted");
            } else {
                error_log("Error al insertar: nuevoId = " . $nuevoId);
                header("location: " . $redirect_path . "?error=insertar");
            }
        } catch (Exception $e) {
            error_log("Exception en guardar: " . $e->getMessage());
            header("location: " . $redirect_path . "?error=exception&msg=" . urlencode($e->getMessage()));
        }
    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }

} else if (isset($_POST['actualizar'])) {
    if (isset($_POST['id']) && isset($_POST['fechaemision']) && isset($_POST['entidademision'])) {

        $id = $_POST['id'];
        $padecimientoActual = $padecimientoDictamenBusiness->getPadecimientoDictamenPorId($id);

        if ($padecimientoActual) {
            // TODO: Si es cliente, aquí deberías verificar que sea dueño del registro
            // usando la tabla intermedia cuando esté disponible

            $fechaemision = trim($_POST['fechaemision']);
            $entidademision = trim($_POST['entidademision']);

            // Validaciones
            if (empty($fechaemision) || empty($entidademision)) {
                header("location: " . $redirect_path . "?error=datos_faltantes");
                exit();
            }

            // Validar que la fecha no sea futura
            if (strtotime($fechaemision) > time()) {
                header("location: " . $redirect_path . "?error=fecha_futura");
                exit();
            }

            try {
                $padecimientoActual->setPadecimientodictamenfechaemision($fechaemision);
                $padecimientoActual->setPadecimientodictamenentidademision($entidademision);

                // Procesar nuevas imágenes si existen
                if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                    $newImageIds = $imageManager->addImages($_FILES['imagenes'], $id, 'pad');
                    $currentIdString = $padecimientoActual->getPadecimientodictamenimagenid();
                    $newIdString = ImageManager::addIdsToString($newImageIds, $currentIdString);
                    $padecimientoActual->setPadecimientodictamenimagenid($newIdString);
                }

                if ($padecimientoDictamenBusiness->actualizarTBPadecimientoDictamen($padecimientoActual)) {
                    header("location: " . $redirect_path . "?success=updated");
                } else {
                    error_log("Error al actualizar en la base de datos");
                    header("location: " . $redirect_path . "?error=dbError");
                }
            } catch (Exception $e) {
                error_log("Exception en actualizar: " . $e->getMessage());
                header("location: " . $redirect_path . "?error=exception&msg=" . urlencode($e->getMessage()));
            }
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }

} else if (isset($_POST['eliminar'])) {
    // Solo admin e instructor pueden eliminar
    if (!$esAdminOInstructor) {
        header("location: " . $redirect_path . "?error=unauthorized");
        exit();
    }

    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        try {
            // TODO: Aquí deberías remover las asociaciones del padecimiento
            // con clientes en la tabla intermedia cuando esté disponible

            $result = $padecimientoDictamenBusiness->eliminarTBPadecimientoDictamen($id);

            if ($result == 1) {
                header("location: " . $redirect_path . "?success=eliminado");
            } else {
                error_log("Error al eliminar: result = " . $result);
                header("location: " . $redirect_path . "?error=eliminar");
            }
        } catch (Exception $e) {
            error_log("Exception en eliminar: " . $e->getMessage());
            header("location: " . $redirect_path . "?error=exception&msg=" . urlencode($e->getMessage()));
        }
    } else {
        header("location: " . $redirect_path . "?error=id_faltante");
    }

} else {
    header("location: " . $redirect_path . "?error=accion_no_valida");
}
?>