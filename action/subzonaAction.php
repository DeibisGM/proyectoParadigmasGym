<?php
session_start();
include '../business/subZonaBusiness.php';
include '../business/cuerpoZonaBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../utility/Validation.php';

$redirect_path = '../view/subzonaView.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

if (!$esAdminOInstructor) {
    header("location: " . $redirect_path . "?error=unauthorized");
    exit();
}

$subZonaBusiness = new subZonaBusiness();
$cuerpoZonaBusiness = new cuerpoZonaBusiness();
$imageManager = new ImageManager();

if (isset($_POST['delete_image'])) {
    if (isset($_POST['id'])) {
        $parteId = $_POST['id'];
        $imagenId = $_POST['delete_image'];

        $subZona = $subZonaBusiness->getSubZonaPorId($parteId);
        if ($subZona) {
            $imageManager->deleteImage($imagenId);
            $currentIds = $subZona->getSubzonaimaenid();
            $newIds = ImageManager::removeIdFromString($imagenId, $currentIds);
            $subZona->setSubzonaimaenid($newIds);
            $subZonaBusiness->actualizarTBSubZona($subZona);
            header("location: " . $redirect_path . "?success=image_deleted");
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }

} else if (isset($_POST['guardar'])) {

    if (isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['zonaId'])) {

        Validation::setOldInput($_POST);

        $nombre = trim($_POST['nombre']);
        $descripcion = $_POST['descripcion'];
        $zonaId = $_POST['zonaId'];
        $activo = 1;

        if (empty($nombre)) {
            Validation::setError('nombre', 'El nombre es obligatorio.');
        } elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre', 'El nombre no puede contener números.');
        } elseif ($subZonaBusiness->existeSubZonaNombre($nombre)) {
            Validation::setError('nombre', 'El nombre ya está asociado a una zona del cuerpo.');
        }

        if (empty($zonaId)) {
            Validation::setError('zonaId', 'La zona es obligatoria.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $subZona = new subzona(0, '', $nombre, $descripcion, $activo);

        $nuevoId = $subZonaBusiness->insertarTBSubZona($subZona);

        if ($nuevoId > 0) {
            if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['imagenes'], $nuevoId, 'par');
                if (!empty($newImageIds)) {

                    $parteAgregada = $subZonaBusiness->getSubZonaPorId($nuevoId);
                    $idString = ImageManager::addIdsToString($newImageIds, '');
                    $parteAgregada->setSubzonaimaenid($idString);
                    $subZonaBusiness->actualizarTBSubZona($parteAgregada);
                }
            }

            $partes = $cuerpoZonaBusiness->getCuerpoZonaSubZonaId($zonaId);

            if($partes !== null && $partes !== ''){
                $partes .= "$" . $nuevoId;
            }else {
                $partes = $nuevoId;
            }

            $cuerpoZonaBusiness->actualizarSubZonaTBCuerpoZona($zonaId, $partes);

            Validation::clear();
            header("location: " . $redirect_path . "?success=inserted");
        } else {
            header("location: " . $redirect_path . "?error=insertar");
        }
    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }

} else if (isset($_POST['actualizar'])) {

    if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['activo'])) {

        $id = $_POST['id'];
        $parteActual = $subZonaBusiness->getSubZonaPorId($id);

        if ($parteActual) {

            $nombre = trim($_POST['nombre']);
            $descripcion = $_POST['descripcion'];
            $activo = $_POST['activo'];

            // Guardar old input por fila
            Validation::setOldInput('nombre_'.$id, $nombre);
            Validation::setOldInput('descripcion_'.$id, $descripcion);
            Validation::setOldInput('activo_'.$id, $activo);

            // Validación por fila
            if (empty($nombre)) {
                Validation::setError('nombre_'.$id, 'El nombre es obligatorio.');
            } elseif (preg_match('/[0-9]/', $nombre)) {
                Validation::setError('nombre_'.$id, 'El nombre no puede contener números.');
            } elseif ($parteActual->getSubzonanombre() != $nombre && $subZonaBusiness->existeSubZonaNombre($nombre)) {
                Validation::setError('nombre_'.$id, 'El nombre ya está asociado a una zona del cuerpo.');
            }

            if (empty($activo)) {
                Validation::setError('activo_'.$id, 'El estado es obligatorio.');
            }

            if (Validation::hasErrors()) {
                header("location: " . $redirect_path);
                exit();
            }

            $parteActual->setSubzonanombre($nombre);
            $parteActual->setSubzonadescripcion($descripcion);
            $parteActual->setSubzonaactivo($activo);

            if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['imagenes'], $id, 'par');
                $currentIdString = $parteActual->getSubzonaimaenid();
                $newIdString = ImageManager::addIdsToString($newImageIds, $currentIdString);
                $parteActual->setSubzonaimaenid($newIdString);
            }

            if ($subZonaBusiness->actualizarTBSubZona($parteActual)) {
                Validation::clear();
                header("location: " . $redirect_path . "?success=updated");
            } else {
                header("location: " . $redirect_path . "?error=dbError");
            }
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }

} else if (isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $result = $subZonaBusiness->eliminarTBSubZona($id);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=eliminado");
        } else {
            header("location: " . $redirect_path . "?error=eliminar");
        }
    } else {
        header("location: " . $redirect_path . "?error=id_faltante");
    }
} else {
    header("location: " . $redirect_path . "?error=accion_no_valida");
}
?>
