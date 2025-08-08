<?php

include '../business/zonaCuerpoBusiness.php';
include '../utility/ImageManager.php'; // el nuevo gestor de imágenes

$redirect_path = '../view/zonaCuerpoView.php';

if (isset($_POST['update'])) {
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
    if (isset($_POST['tbzonacuerpoid'])) {
        $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
        $idZonaCuerpo = $_POST['tbzonacuerpoid'];
        $result = $zonaCuerpoBusiness->actualizarEstadoTBZonaCuerpo($idZonaCuerpo, 0); // 0 para desactivar

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=deactivated");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['activar'])) {
    if (isset($_POST['tbzonacuerpoid'])) {
        $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
        $idZonaCuerpo = $_POST['tbzonacuerpoid'];
        $result = $zonaCuerpoBusiness->actualizarEstadoTBZonaCuerpo($idZonaCuerpo, 1); // 1 para activar

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=activated");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['create'])) {
    if (isset($_POST['tbzonacuerponombre']) && isset($_POST['tbzonacuerpodescripcion'])) { // tbzonacuerpoactivo ya no se recibe del form
        if (!empty($_POST['tbzonacuerponombre']) && !empty($_POST['tbzonacuerpodescripcion'])) {
            $zonaCuerpo = new ZonaCuerpo(0, $_POST['tbzonacuerponombre'], $_POST['tbzonacuerpodescripcion'], 1); // Siempre activo al crear
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