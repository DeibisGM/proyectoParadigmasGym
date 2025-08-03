<?php

include '../business/zonaCuerpoBusiness.php';

if (isset($_POST['update'])) {
    if (isset($_POST['idZonaCuerpo']) && isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['activo'])) {
        if (!empty($_POST['nombre']) && !empty($_POST['descripcion'])) {
            $zonaCuerpo = new ZonaCuerpo($_POST['idZonaCuerpo'], $_POST['nombre'], $_POST['descripcion'], $_POST['activo']);
            $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
            $result = $zonaCuerpoBusiness->actualizarTBZonaCuerpo($zonaCuerpo);
            if ($result == 1) {
                header("location: /zonas-cuerpo?success=updated");
            } else {
                header("location: /zonas-cuerpo?error=dbError");
            }
        } else {
            header("location: /zonas-cuerpo?error=emptyField");
        }
    } else {
        header("location: /zonas-cuerpo?error=error");
    }
}
else if (isset($_POST['delete'])) {
    if (isset($_POST['idZonaCuerpo'])) {
        $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
        $result = $zonaCuerpoBusiness->eliminarTBZonaCuerpo($_POST['idZonaCuerpo']);
        if ($result == 1) {
            header("location: /zonas-cuerpo?success=deleted");
        } else {
            header("location: /zonas-cuerpo?error=dbError");
        }
    } else {
        header("location: /zonas-cuerpo?error=error");
    }
}
else if (isset($_POST['create'])) {
    if (isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['activo'])) {
        if (!empty($_POST['nombre']) && !empty($_POST['descripcion'])) {
            $zonaCuerpo = new ZonaCuerpo(0, $_POST['nombre'], $_POST['descripcion'], $_POST['activo']);
            $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
            $result = $zonaCuerpoBusiness->insertarTBZonaCuerpo($zonaCuerpo);
            if ($result == 1) {
                header("location: /zonas-cuerpo?success=inserted");
            } else {
                header("location: /zonas-cuerpo?error=dbError");
            }
        } else {
            header("location: /zonas-cuerpo?error=emptyField");
        }
    } else {
        header("location: /zonas-cuerpo?error=error");
    }
}
?>