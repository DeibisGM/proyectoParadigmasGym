<?php
session_start();
include '../business/parteZonaBusiness.php';
include '../business/cuerpoZonaBusiness.php';
include_once '../utility/ImageManager.php';
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

        $nombre = trim($_POST['nombre']);
        $descripcion = $_POST['descripcion'];
        $zonaId = $_POST['zonaId'];
        $activo = 1;

        if (empty($nombre)) {
            header("location: " . $redirect_path . "?error=datos_faltantes");
            exit();
        }

        if ($parteZonaBusiness->existeParteZonaNombre($nombre)) {
            header("location: " . $redirect_path . "?error=existe");
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
            $parteActual->setPartezonanombre(trim($_POST['nombre']));
            $parteActual->setPartezonadescripcion(trim($_POST['descripcion']));
            $parteActual->setPartezonaactivo($_POST['activo']);


            if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['imagenes'], $id, 'par');
                $currentIdString = $parteActual->getPartezonaimaenid();
                $newIdString = ImageManager::addIdsToString($newImageIds, $currentIdString);
                $parteActual->setPartezonaimaenid($newIdString);
            }

            if ($parteZonaBusiness->actualizarTBParteZona($parteActual)) {
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
