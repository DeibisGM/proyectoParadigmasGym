<?php
session_start();
include '../business/zonaCuerpoBusiness.php';
include '../utility/ImageManager.php';

$redirect_path = '../view/zonaCuerpoView.php';

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

    if (isset($_POST['tbzonacuerpoid']) && isset($_POST['tbzonacuerponombre']) && isset($_POST['tbzonacuerpodescripcion']) && isset($_POST['tbzonacuerpoactivo'])) {
        if (!empty($_POST['tbzonacuerponombre']) && !empty($_POST['tbzonacuerpodescripcion'])) {
            $zonaCuerpo = new ZonaCuerpo($_POST['tbzonacuerpoid'], $_POST['tbzonacuerponombre'], $_POST['tbzonacuerpodescripcion'], $_POST['tbzonacuerpoactivo']);
            $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
            $result = $zonaCuerpoBusiness->actualizarTBZonaCuerpo($zonaCuerpo);

            // Después de actualizar, gestionar la imagen
            if ($result == 1) {
                $id = $_POST['tbzonacuerpoid'];
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

    if (isset($_POST['tbzonacuerpoid'])) {
        $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
        $idZonaCuerpo = $_POST['tbzonacuerpoid'];
        $result = $zonaCuerpoBusiness->actualizarEstadoTBZonaCuerpo($idZonaCuerpo, 0);

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

    if (isset($_POST['tbzonacuerpoid'])) {
        $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
        $idZonaCuerpo = $_POST['tbzonacuerpoid'];
        $result = $zonaCuerpoBusiness->actualizarEstadoTBZonaCuerpo($idZonaCuerpo, 1);

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

    if (isset($_POST['tbzonacuerponombre']) && isset($_POST['tbzonacuerpodescripcion'])) {
        if (!empty($_POST['tbzonacuerponombre']) && !empty($_POST['tbzonacuerpodescripcion'])) {
            $zonaCuerpo = new ZonaCuerpo(0, $_POST['tbzonacuerponombre'], $_POST['tbzonacuerpodescripcion'], 1);
            $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
            $nuevoId = $zonaCuerpoBusiness->insertarTBZonaCuerpo($zonaCuerpo);

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