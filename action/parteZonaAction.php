<?php
session_start();
include '../business/parteZonaBusiness.php';
include '../business/cuerpoZonaBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../utility/Validation.php';

$redirect_path = '../view/parteZonaView.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

if (!$esAdminOInstructor) {
    header("location: " . $redirect_path . "?error=unauthorized");
    exit();
}

$parteZonaBusiness = new parteZonaBusiness();
$cuerpoZonaBusiness = new cuerpoZonaBusiness();
$imageManager = new ImageManager();

if (isset($_POST['borrar_imagen'])) {
    if (isset($_POST['id'])) {
        $parteId = $_POST['id'];
        $imagenId = $_POST['borrar_imagen'];

        $parte = $parteZonaBusiness->getParteZonaPorId($parteId);
        if ($parte) {
            $imageManager->deleteImage($imagenId);
            $currentIds = $parte->getPartezonaimaenid();
            $newIds = ImageManager::removeIdFromString($imagenId, $currentIds);
            $parte->setPartezonaimaenid($newIds);
            $parteZonaBusiness->actualizarTBParteZona($parte);
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
        } elseif ($parteZonaBusiness->existeParteZonaNombre($nombre)) {
            Validation::setError('nombre', 'El nombre ya está asociado a una zona del cuerpo.');
        }

        if (empty($zonaId)) {
            Validation::setError('zonaId', 'La zona es obligatoria.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $parte = new partezona(0, '', $nombre, $descripcion, $activo);

        $nuevoId = $parteZonaBusiness->insertarTBParteZona($parte);

        if ($nuevoId > 0) {
            if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['imagenes'], $nuevoId, 'par');
                if (!empty($newImageIds)) {

                    $parteAgregada = $parteZonaBusiness->getParteZonaPorId($nuevoId);
                    $idString = ImageManager::addIdsToString($newImageIds, '');
                    $parteAgregada->setPartezonaimaenid($idString);
                    $parteZonaBusiness->actualizarTBParteZona($parteAgregada);
                }
            }

            $partes = $cuerpoZonaBusiness->getCuerpoZonaParteZonaId($zonaId);

            if($partes !== null){
                $partes .= "$" . $nuevoId;
            }

            $cuerpoZonaBusiness->actualizarParteZonaTBCuerpoZona($zonaId, $partes);

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
        $parteActual = $parteZonaBusiness->getParteZonaPorId($id);

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
            } elseif ($parteActual->getPartezonanombre() != $nombre && $parteZonaBusiness->existeParteZonaNombre($nombre)) {
                Validation::setError('nombre_'.$id, 'El nombre ya está asociado a una zona del cuerpo.');
            }

            if (empty($activo)) {
                Validation::setError('activo_'.$id, 'El estado es obligatorio.');
            }

            if (Validation::hasErrors()) {
                header("location: " . $redirect_path);
                exit();
            }

            $parteActual->setPartezonanombre($nombre);
            $parteActual->setPartezonadescripcion($descripcion);
            $parteActual->setPartezonaactivo($activo);

            if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['imagenes'], $id, 'par');
                $currentIdString = $parteActual->getPartezonaimaenid();
                $newIdString = ImageManager::addIdsToString($newImageIds, $currentIdString);
                $parteActual->setPartezonaimaenid($newIdString);
            }

            if ($parteZonaBusiness->actualizarTBParteZona($parteActual)) {
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

        $result = $parteZonaBusiness->eliminarTBParteZona($id);

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
