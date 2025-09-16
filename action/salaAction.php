<?php
include '../business/salaBusiness.php';

if(isset($_POST['insertar'])){
    if(isset($_POST['nombre']) && isset($_POST['capacidad'])){
        $nombre = trim($_POST['nombre']);
        $capacidad = trim($_POST['capacidad']);
        $estado = 1;

        if (empty($nombre) || empty($capacidad)) {
            header("location: ../view/salaView.php?error=datos_faltantes");
            exit();
        }

        $sala = new Sala(null, $nombre, $capacidad, $estado);

        $salaBusiness = new SalaBusiness();

        $result = $salaBusiness->insertarTbsala($sala);

        if ($result == 1) {
            header("location: ../view/salaView.php?success=insertado");
        } else {
            header("location: ../view/salaView.php?error=insertar");
        }
    } else{
        header("location: ../view/salaView.php?error=datos_faltantes");
    }
}
else if(isset($_POST['actualizar'])){
    if(isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['capacidad']) && isset($_POST['estado'])){

        $id = $_POST['id'];
        $nombre = trim($_POST['nombre']);
        $capacidad = $_POST['capacidad'];
        $estado = $_POST['estado'];

        if (empty($id) || empty($nombre) || empty($capacidad)) {
            header("location: ../view/salaView.php?error=datos_faltantes");
            exit();
        }

        $sala = new Sala($id, $nombre, $capacidad, $estado);

        $salaBusiness = new SalaBusiness();
        $result = $salaBusiness->actualizarTbsala($sala);

        if ($result == 1) {
            header("location: ../view/salaView.php?success=actualizado");
        } else {
            header("location: ../view/salaView.php?error=actualizar");
        }
    }else {
       header("location: ../view/salaView.php?error=datos_faltantes");
    }
}
else if(isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $salaBusiness = new SalaBusiness();
        $result = $salaBusiness->eliminarTbsala($id);

        if ($result == 1) {
            header("location: ../view/salaView.php?success=eliminado");
        } else {
            header("location: ../view/salaView.php?error=eliminar");
        }
    }else{
        header("location: ../view/salaView.php?error=id_faltante");
    }
} else {
    header("location: ../view/salaView.php?error=accion_no_valida");
}
?>