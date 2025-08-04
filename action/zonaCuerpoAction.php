<?php

include '../business/zonaCuerpoBusiness.php';

$redirect_path = '../view/zonaCuerpoView.php';

if (isset($_POST['update'])) {
    if (isset($_POST['tbzonacuerpoid']) && isset($_POST['tbzonacuerponombre']) && isset($_POST['tbzonacuerpodescripcion']) && isset($_POST['tbzonacuerpoactivo'])) {
        if (!empty($_POST['tbzonacuerponombre']) && !empty($_POST['tbzonacuerpodescripcion'])) {
            $zonaCuerpo = new ZonaCuerpo($_POST['tbzonacuerpoid'], $_POST['tbzonacuerponombre'], $_POST['tbzonacuerpodescripcion'], $_POST['tbzonacuerpoactivo']);
            $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
            $result = $zonaCuerpoBusiness->actualizarTBZonaCuerpo($zonaCuerpo);
            if ($result == 1) {

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
        $result = $zonaCuerpoBusiness->eliminarTBZonaCuerpo($_POST['tbzonacuerpoid']);
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
            $result = $zonaCuerpoBusiness->insertarTBZonaCuerpo($zonaCuerpo);
            if ($result == 1) {

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