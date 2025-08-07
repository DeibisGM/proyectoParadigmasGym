<?php

include '../business/zonaCuerpoBusiness.php';
include '../utility/ImageManager.php'; // Incluir el nuevo gestor de imágenes

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
}
else if (isset($_POST['delete'])) {
    if (isset($_POST['tbzonacuerpoid'])) {
                $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
        $idZonaCuerpo = $_POST['tbzonacuerpoid'];

        // Primero, eliminar la imagen asociada, si existe
        gestionarImagen('zonas_cuerpo', $idZonaCuerpo, null, true);

        // Luego, eliminar el registro de la base de datos
        $result = $zonaCuerpoBusiness->eliminarTBZonaCuerpo($idZonaCuerpo);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=deleted");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
}
else if (isset($_POST['create'])) {
    if (isset($_POST['tbzonacuerponombre']) && isset($_POST['tbzonacuerpodescripcion']) && isset($_POST['tbzonacuerpoactivo'])) {
        if (!empty($_POST['tbzonacuerponombre']) && !empty($_POST['tbzonacuerpodescripcion'])) {
            $zonaCuerpo = new ZonaCuerpo(0, $_POST['tbzonacuerponombre'], $_POST['tbzonacuerpodescripcion'], $_POST['tbzonacuerpoactivo']);
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