<?php

include '../business/zonaCuerpoBusiness.php';

// Define la ruta base para la redirección.
// Asegúrate de que '/paradigmas/' sea la carpeta raíz de tu proyecto en htdocs.
$redirect_path = '/paradigmas/view/zonaCuerpoView.php';

if (isset($_POST['update'])) {
    if (isset($_POST['idZonaCuerpo']) && isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['activo'])) {
        if (!empty($_POST['nombre']) && !empty($_POST['descripcion'])) {
            $zonaCuerpo = new ZonaCuerpo($_POST['idZonaCuerpo'], $_POST['nombre'], $_POST['descripcion'], $_POST['activo']);
            $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
            $result = $zonaCuerpoBusiness->actualizarTBZonaCuerpo($zonaCuerpo);
            if ($result == 1) {
                // CORRECCIÓN: Redirigir al archivo PHP correcto.
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
    if (isset($_POST['idZonaCuerpo'])) {
        $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
        $result = $zonaCuerpoBusiness->eliminarTBZonaCuerpo($_POST['idZonaCuerpo']);
        if ($result == 1) {
            // CORRECCIÓN: Redirigir al archivo PHP correcto.
            header("location: " . $redirect_path . "?success=deleted");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
}
else if (isset($_POST['create'])) {
    if (isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['activo'])) {
        if (!empty($_POST['nombre']) && !empty($_POST['descripcion'])) {
            $zonaCuerpo = new ZonaCuerpo(0, $_POST['nombre'], $_POST['descripcion'], $_POST['activo']);
            $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
            $result = $zonaCuerpoBusiness->insertarTBZonaCuerpo($zonaCuerpo);
            if ($result == 1) {
                // CORRECCIÓN: Redirigir al archivo PHP correcto.
                header("location: " . $redirect_path . "?success=inserted");
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