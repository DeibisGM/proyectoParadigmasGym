<?php
session_start();
include_once '../business/ejercicioFuerzaBusiness.php';
include_once '../utility/Validation.php';

$redirect_path = '../view/ejercicioFuerzaView.php';

if(isset($_POST['insertar'])){
    if(isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['repeticion']) &&
       isset($_POST['serie']) && isset($_POST['peso']) && isset($_POST['descanso'])){

        Validation::setOldInput($_POST);

        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $repeticion = trim($_POST['repeticion']);
        $serie = trim($_POST['serie']);
        $peso = !empty(trim($_POST['peso'])) ? trim($_POST['peso']) : 0;
        $descanso = trim($_POST['descanso']);

        // Validaciones
        if (empty($nombre)) {
            Validation::setError('nombre', 'El nombre del ejercicio es obligatorio.');
        } elseif (strlen($nombre) < 3) {
            Validation::setError('nombre', 'El nombre debe tener al menos 3 caracteres.');
        }

        if (empty($descripcion)) {
            Validation::setError('descripcion', 'La descripción es obligatoria.');
        } elseif (strlen($descripcion) < 10) {
            Validation::setError('descripcion', 'La descripción debe tener al menos 10 caracteres.');
        }

        if (empty($repeticion)) {
            Validation::setError('repeticion', 'El número de repeticiones es obligatorio.');
        } elseif (!is_numeric($repeticion) || $repeticion <= 0) {
            Validation::setError('repeticion', 'Las repeticiones deben ser un número entero positivo.');
        }

        if (empty($serie)) {
            Validation::setError('serie', 'El número de series es obligatorio.');
        } elseif (!is_numeric($serie) || $serie <= 0) {
            Validation::setError('serie', 'Las series deben ser un número entero positivo.');
        }

        /*if (empty($peso)) {
            Validation::setError('peso', 'El peso es obligatorio.');
        } elseif (!is_numeric($peso) || $peso < 0) {
            Validation::setError('peso', 'El peso debe ser un número positivo.');
        }*/

        if (empty($descanso)) {
            Validation::setError('descanso', 'El tiempo de descanso es obligatorio.');
        } elseif (!is_numeric($descanso) || $descanso < 0) {
            Validation::setError('descanso', 'El descanso debe ser un número positivo (en segundos).');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $ejercicio = new EjercicioFuerza(null, $nombre, $descripcion, $repeticion, $serie, $peso, $descanso);
        $ejercicioBusiness = new EjercicioFuerzaBusiness();
        $result = $ejercicioBusiness->insertarTbejerciciofuerza($ejercicio);

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
    if(isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['descripcion']) &&
       isset($_POST['repeticion']) && isset($_POST['serie']) && isset($_POST['peso']) &&
       isset($_POST['descanso'])){

        $id = $_POST['id'];

        Validation::setOldInput('nombre_'.$id, trim($_POST['nombre']));
        Validation::setOldInput('descripcion_'.$id, trim($_POST['descripcion']));
        Validation::setOldInput('repeticion_'.$id, $_POST['repeticion']);
        Validation::setOldInput('serie_'.$id, $_POST['serie']);
        Validation::setOldInput('peso_'.$id, $_POST['peso']);
        Validation::setOldInput('descanso_'.$id, $_POST['descanso']);

        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $repeticion = $_POST['repeticion'];
        $serie = $_POST['serie'];
        $peso = !empty($_POST['peso']) ? $_POST['peso'] : 0;
        $descanso = $_POST['descanso'];

        // Validaciones
        if (empty($nombre)) {
            Validation::setError('nombre_'.$id, 'El nombre del ejercicio es obligatorio.');
        } elseif (strlen($nombre) < 3) {
            Validation::setError('nombre_'.$id, 'El nombre debe tener al menos 3 caracteres.');
        }

        if (empty($descripcion)) {
            Validation::setError('descripcion_'.$id, 'La descripción es obligatoria.');
        } elseif (strlen($descripcion) < 10) {
            Validation::setError('descripcion_'.$id, 'La descripción debe tener al menos 10 caracteres.');
        }

        if (empty($repeticion)) {
            Validation::setError('repeticion_'.$id, 'El número de repeticiones es obligatorio.');
        } elseif (!is_numeric($repeticion) || $repeticion <= 0) {
            Validation::setError('repeticion_'.$id, 'Las repeticiones deben ser un número entero positivo.');
        }

        if (empty($serie)) {
            Validation::setError('serie_'.$id, 'El número de series es obligatorio.');
        } elseif (!is_numeric($serie) || $serie <= 0) {
            Validation::setError('serie_'.$id, 'Las series deben ser un número entero positivo.');
        }

        /*if (empty($peso)) {
            Validation::setError('peso_'.$id, 'El peso es obligatorio.');
        } elseif (!is_numeric($peso) || $peso < 0) {
            Validation::setError('peso_'.$id, 'El peso debe ser un número positivo.');
        }*/

        if (empty($descanso)) {
            Validation::setError('descanso_'.$id, 'El tiempo de descanso es obligatorio.');
        } elseif (!is_numeric($descanso) || $descanso < 0) {
            Validation::setError('descanso_'.$id, 'El descanso debe ser un número positivo (en segundos).');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $ejercicio = new EjercicioFuerza($id, $nombre, $descripcion, $repeticion, $serie, $peso, $descanso);
        $ejercicioBusiness = new EjercicioFuerzaBusiness();
        $result = $ejercicioBusiness->actualizarTbejerciciofuerza($ejercicio);

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

        $ejercicioBusiness = new EjercicioFuerzaBusiness();
        $result = $ejercicioBusiness->eliminarTbejerciciofuerza($id);

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