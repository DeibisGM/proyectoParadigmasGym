<?php
session_start();
include '../business/salaBusiness.php';
include_once '../utility/Validation.php';

$redirect_path = '../view/salaView.php';

if(isset($_POST['insertar'])){
    if(isset($_POST['nombre']) && isset($_POST['capacidad'])){

        Validation::setOldInput($_POST);

        $nombre = trim($_POST['nombre']);
        $capacidad = trim($_POST['capacidad']);
        $estado = 1;

        // Validaciones
        if (empty($nombre)) {
            Validation::setError('nombre', 'El nombre de la sala es obligatorio.');
        } elseif (strlen($nombre) < 3) {
            Validation::setError('nombre', 'El nombre debe tener al menos 3 caracteres.');
        }

        if (empty($capacidad)) {
            Validation::setError('capacidad', 'La capacidad es obligatoria.');
        } elseif (!is_numeric($capacidad) || $capacidad <= 0) {
            Validation::setError('capacidad', 'La capacidad debe ser un número entero positivo.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $sala = new Sala(null, $nombre, $capacidad, $estado);
        $salaBusiness = new SalaBusiness();
        $result = $salaBusiness->insertarTbsala($sala);

        if ($result == 1) {
            Validation::clear();
            header("location: " . $redirect_path . "?success=insertado");
        } else {
            header("location: " . $redirect_path . "?error=insertar");
        }
    } else{
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
}
else if(isset($_POST['actualizar'])){
    if(isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['capacidad']) && isset($_POST['estado'])){

        $id = $_POST['id'];

        Validation::setOldInput('nombre_'.$id, trim($_POST['nombre']));
        Validation::setOldInput('capacidad_'.$id, $_POST['capacidad']);
        Validation::setOldInput('estado_'.$id, $_POST['estado']);

        $nombre = trim($_POST['nombre']);
        $capacidad = $_POST['capacidad'];
        $estado = $_POST['estado'];

        // Validaciones
        if (empty($nombre)) {
            Validation::setError('nombre_'.$id, 'El nombre de la sala es obligatorio.');
        } elseif (strlen($nombre) < 3) {
            Validation::setError('nombre_'.$id, 'El nombre debe tener al menos 3 caracteres.');
        }

        if (empty($capacidad)) {
            Validation::setError('capacidad_'.$id, 'La capacidad es obligatoria.');
        } elseif (!is_numeric($capacidad) || $capacidad <= 0) {
            Validation::setError('capacidad_'.$id, 'La capacidad debe ser un número entero positivo.');
        }

        if (empty($estado) && $estado !== '0') {
            Validation::setError('estado_'.$id, 'El estado es obligatorio.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $sala = new Sala($id, $nombre, $capacidad, $estado);
        $salaBusiness = new SalaBusiness();
        $result = $salaBusiness->actualizarTbsala($sala);

        if ($result == 1) {
            Validation::clear();
            header("location: " . $redirect_path . "?success=actualizado");
        } else {
            header("location: " . $redirect_path . "?error=actualizar");
        }
    }else {
       header("location: " . $redirect_path . "?error=datos_faltantes");
    }
}
else if(isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $salaBusiness = new SalaBusiness();
        $result = $salaBusiness->eliminarTbsala($id);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=eliminado");
        } else {
            header("location: " . $redirect_path . "?error=eliminar");
        }
    }else{
        header("location: " . $redirect_path . "?error=id_faltante");
    }
} else {
    header("location: " . $redirect_path . "?error=accion_no_valida");
}
?>