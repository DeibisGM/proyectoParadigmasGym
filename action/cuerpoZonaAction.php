<?php
session_start();
include_once '../business/cuerpoZonaBusiness.php';
include_once '../utility/ImageManager.php';

$redirect_path = '../view/cuerpoZonaView.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

if (!$esAdminOInstructor) {
    header("location: " . $redirect_path . "?error=unauthorized");
    exit();
}

$cuerpoZonaBusiness = new CuerpoZonaBusiness();
$imageManager = new ImageManager();

if (isset($_POST['delete_image'])) {
    if (isset($_POST['tbcuerpozonaid'])) {
        $zonaId = $_POST['tbcuerpozonaid'];
        $imagenId = $_POST['delete_image'];

        $zona = $cuerpoZonaBusiness->getCuerpoZonaById($zonaId);
        if ($zona) {
            $imageManager->deleteImage($imagenId);
            $currentIds = $zona->getImagenesIds();
            $newIds = ImageManager::removeIdFromString($imagenId, $currentIds);
            $zona->setImagenesIds($newIds);
            $cuerpoZonaBusiness->actualizarTBCuerpoZona($zona);
            header("location: " . $redirect_path . "?success=image_deleted");
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['create'])) {
    if (isset($_POST['tbcuerpozonanombre']) && isset($_POST['tbcuerpozonadescripcion'])) {
        if (!empty($_POST['tbcuerpozonanombre']) && !empty($_POST['tbcuerpozonadescripcion'])) {
            $cuerpoZona = new CuerpoZona(0, $_POST['tbcuerpozonanombre'], $_POST['tbcuerpozonadescripcion'], 1);
            $nuevoId = $cuerpoZonaBusiness->insertarTBCuerpoZona($cuerpoZona);

            if ($nuevoId > 0) {
                if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                    $newImageIds = $imageManager->addImages($_FILES['imagenes'], $nuevoId, 'cue');
                    if (!empty($newImageIds)) {
                        $zonaCreada = $cuerpoZonaBusiness->getCuerpoZonaById($nuevoId);
                        $idString = ImageManager::addIdsToString($newImageIds, '');
                        $zonaCreada->setImagenesIds($idString);
                        $cuerpoZonaBusiness->actualizarTBCuerpoZona($zonaCreada);
                    }
                }
                header("location: " . $redirect_path . "?success=inserted");
            } else if ($nuevoId == -1) {
                header("location: " . $redirect_path . "?error=duplicateZone");
            } else {
                header("location: " . $redirect_path . "?error=dbError");
            }
        } else {
            header("location: " . $redirect_path . "?error=emptyField");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['update'])) {
    if (isset($_POST['tbcuerpozonaid']) && isset($_POST['tbcuerpozonanombre']) && isset($_POST['tbcuerpozonadescripcion']) && isset($_POST['tbcuerpozonaactivo'])) {
        $id = $_POST['tbcuerpozonaid'];
        $zonaActual = $cuerpoZonaBusiness->getCuerpoZonaById($id);
        if ($zonaActual) {
            $zonaActual->setNombreCuerpoZona($_POST['tbcuerpozonanombre']);
            $zonaActual->setDescripcionCuerpoZona($_POST['tbcuerpozonadescripcion']);
            $zonaActual->setActivoCuerpoZona($_POST['tbcuerpozonaactivo']);

            if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['imagenes'], $id, 'cue');
                $currentIdString = $zonaActual->getImagenesIds();
                $newIdString = ImageManager::addIdsToString($newImageIds, $currentIdString);
                $zonaActual->setImagenesIds($newIdString);
            }

            if ($cuerpoZonaBusiness->actualizarTBCuerpoZona($zonaActual)) {
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
} else if (isset($_POST['desactivar'])) {
    if (isset($_POST['tbcuerpozonaid'])) {
        $cuerpoZonaBusiness->actualizarEstadoTBCuerpoZona($_POST['tbcuerpozonaid'], 0);
        header("location: " . $redirect_path . "?success=deactivated");
    }
} else if (isset($_POST['activar'])) {
    if (isset($_POST['tbcuerpozonaid'])) {
        $cuerpoZonaBusiness->actualizarEstadoTBCuerpoZona($_POST['tbcuerpozonaid'], 1);
        header("location: " . $redirect_path . "?success=activated");
    }
} else if (isset($_POST['delete'])) {
    if (isset($_POST['tbcuerpozonaid'])) {
        if ($cuerpoZonaBusiness->eliminarTBCuerpoZona($_POST['tbcuerpozonaid'])) {
            header("location: " . $redirect_path . "?success=deleted");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    }
} else {
    header("location: " . $redirect_path . "?error=invalid_action");
}
?>