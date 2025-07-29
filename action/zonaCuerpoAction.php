<?php

include '../business/zonaCuerpoBusiness.php';

if (isset($_POST['update'])) {
    if (isset($_POST['idZonaCuerpo']) && isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['activo'])) {
        if (!empty($_POST['nombre']) && !empty($_POST['descripcion'])) {
            $zonaCuerpo = new ZonaCuerpo($_POST['idZonaCuerpo'], $_POST['nombre'], $_POST['descripcion'], $_POST['activo']);
            $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
            $result = $zonaCuerpoBusiness->actualizarTBZonaCuerpo($zonaCuerpo);
            if ($result == 1) {
                header("location: ../view/zonaCuerpoView.php?success=updated");
            } else {
                header("location: ../view/zonaCuerpoView.php?error=dbError");
            }
        } else {
            header("location: ../view/zonaCuerpoView.php?error=emptyField");
        }
    } else {
        header("location: ../view/zonaCuerpoView.php?error=error");
    }
}
else if (isset($_POST['delete'])) {
    if (isset($_POST['idZonaCuerpo'])) {
        $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
        $result = $zonaCuerpoBusiness->eliminarTBZonaCuerpo($_POST['idZonaCuerpo']);
        if ($result == 1) {
            header("location: ../view/zonaCuerpoView.php?success=deleted");
        } else {
            header("location: ../view/zonaCuerpoView.php?error=dbError");
        }
    } else {
        header("location: ../view/zonaCuerpoView.php?error=error");
    }
}
else if (isset($_POST['create'])) {
    if (isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['activo'])) {
        if (!empty($_POST['nombre']) && !empty($_POST['descripcion'])) {
            $zonaCuerpo = new ZonaCuerpo(0, $_POST['nombre'], $_POST['descripcion'], $_POST['activo']);
            $zonaCuerpoBusiness = new ZonaCuerpoBusiness();
            $result = $zonaCuerpoBusiness->insertarTBZonaCuerpo($zonaCuerpo);
            if ($result == 1) {
                header("location: ../view/zonaCuerpoView.php?success=inserted");
            } else {
                header("location: ../view/zonaCuerpoView.php?error=dbError");
            }
        } else {
            header("location: ../view/zonaCuerpoView.php?error=emptyField");
        }
    } else {
        header("location: ../view/zonaCuerpoView.php?error=error");
    }
}
?>