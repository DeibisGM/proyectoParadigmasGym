<?php
session_start();
include '../business/cuerpoZonaBusiness.php';
include '../utility/ImageManager.php';

$redirect_path = '../view/cuerpoZonaView.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

// Solo los administradores pueden crear, editar, activar o desactivar zonas
$esAdmin = ($_SESSION['tipo_usuario'] === 'admin');

if (isset($_POST['update'])) {
    // Verificar si es administrador
    if (!$esAdmin) {
        header("location: " . $redirect_path . "?error=unauthorized");
        exit();
    }

    if (isset($_POST['tbcuerpozonaid']) && isset($_POST['tbcuerpozonanombre']) && isset($_POST['tbcuerpozonadescripcion']) && isset($_POST['tbcuerpozonaactivo'])) {
        if (!empty($_POST['tbcuerpozonanombre']) && !empty($_POST['tbcuerpozonadescripcion'])) {
            $cuerpoZona = new CuerpoZona($_POST['tbcuerpozonaid'], $_POST['tbcuerpozonanombre'], $_POST['tbcuerpozonadescripcion'], $_POST['tbcuerpozonaactivo']);
            $cuerpoZonaBusiness = new CuerpoZonaBusiness();
            $result = $cuerpoZonaBusiness->actualizarTBCuerpoZona($cuerpoZona);

            // Después de actualizar, gestionar la imagen
            if ($result == 1) {
                $id = $_POST['tbcuerpozonaid'];
                $eliminarImagen = isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == '1';

                // Pasar el archivo y la solicitud de eliminación a la función
                gestionarImagen('zonas_cuerpo', $id, $_FILES['imagen'], $eliminarImagen);

                header("location: " . $redirect_path . "?success=updated");
            } else {
                header("location: " . $redirect_path . "?error=dbError");
            }
        } else {
            header("location: " . $redirect_path . "?error=emptyField");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['desactivar'])) {
    // Verificar si es administrador
    if (!$esAdmin) {
        header("location: " . $redirect_path . "?error=unauthorized");
        exit();
    }

    if (isset($_POST['tbcuerpozonaid'])) {
        $cuerpoZonaBusiness = new CuerpoZonaBusiness();
        $idCuerpoZona = $_POST['tbcuerpozonaid'];
        $result = $cuerpoZonaBusiness->actualizarEstadoTBCuerpoZona($idCuerpoZona, 0);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=deactivated");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['activar'])) {
    // Verificar si es administrador
    if (!$esAdmin) {
        header("location: " . $redirect_path . "?error=unauthorized");
        exit();
    }

    if (isset($_POST['tbcuerpozonaid'])) {
        $cuerpoZonaBusiness = new CuerpoZonaBusiness();
        $idCuerpoZona = $_POST['tbcuerpozonaid'];
        $result = $cuerpoZonaBusiness->actualizarEstadoTBCuerpoZona($idCuerpoZona, 1);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=activated");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['create'])) {
    // Verificar si es administrador
    if (!$esAdmin) {
        header("location: " . $redirect_path . "?error=unauthorized");
        exit();
    }

    if (isset($_POST['tbcuerpozonanombre']) && isset($_POST['tbcuerpozonadescripcion'])) {
        if (!empty($_POST['tbcuerpozonanombre']) && !empty($_POST['tbcuerpozonadescripcion'])) {
            $cuerpoZona = new CuerpoZona(0, $_POST['tbcuerpozonanombre'], $_POST['tbcuerpozonadescripcion'], 1);
            $cuerpoZonaBusiness = new CuerpoZonaBusiness();
            $nuevoId = $cuerpoZonaBusiness->insertarTBCuerpoZona($cuerpoZona);

            if ($nuevoId > 0) {
                // Si se insertó correctamente y tenemos un ID, procesamos la imagen
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    gestionarImagen('zonas_cuerpo', $nuevoId, $_FILES['imagen']);
                }
                header("location: " . $redirect_path . "?success=inserted");
            } else if ($nuevoId == -1) {
                // Error específico para zona duplicada
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
}
?>