<?php
session_start();
include_once '../business/ejercicioEquilibrioBusiness.php';
include_once '../business/ejercicioSubzonaBusiness.php';
include_once '../utility/Validation.php';

$redirect_path = '../view/ejercicioEquilibrioView.php';

if(isset($_POST['insertar'])){
    if(isset($_POST['nombre']) && isset($_POST['descripcion']) && isset($_POST['dificultad']) &&
       isset($_POST['duracion']) && isset($_POST['postura']) && isset($_POST['subzona'])){

        Validation::setOldInput($_POST);

        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $dificultad = trim($_POST['dificultad']);
        $duracion = trim($_POST['duracion']);
        $materiales = !empty(trim($_POST['materiales'])) ? trim($_POST['materiales']) : '';
        $postura = trim($_POST['postura']);
        $subzona = $_POST['subzona'];

        // Validaciones (mantén las mismas)
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

        if (empty($dificultad)) {
            Validation::setError('dificultad', 'La dificultad es obligatoria.');
        }

        if (empty($duracion)) {
            Validation::setError('duracion', 'La duración es obligatoria.');
        } elseif (!is_numeric($duracion) || $duracion <= 0) {
            Validation::setError('duracion', 'La duración debe ser un número entero positivo (en segundos).');
        }

        if (empty($postura)) {
            Validation::setError('postura', 'La postura es obligatoria.');
        }

        if (empty($subzona)) {
            Validation::setError('subzona', 'La subzona es obligatoria.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $ejercicio = new EjercicioEquilibrio(null, $nombre, $descripcion, $dificultad, $duracion, $materiales, $postura);
        $ejercicioBusiness = new EjercicioEquilibrioBusiness();
        $nuevoId = $ejercicioBusiness->insertarTbejercicioequilibrio($ejercicio);

        if ($nuevoId > 0) {
            // Insertar subzonas - UNA POR CADA SUBZONA SELECCIONADA
            $ejercicioSubzonaBusiness = new ejercicioSubzonaBusiness();
            $insertSuccess = true;

            foreach ($subzona as $subzonaId) {
                $nuevaSubzona = new ejercicioSubzona(0, $nuevoId, $subzonaId, 'Equilibrio');
                $resultSubzona = $ejercicioSubzonaBusiness->insertarTBEjercicioSubzona($nuevaSubzona);
                if (!$resultSubzona) {
                    $insertSuccess = false;
                }
            }

            if ($insertSuccess) {
                Validation::clear();
                header("location: " . $redirect_path . "?success=insertado");
            } else {
                header("location: " . $redirect_path . "?error=insertar_subzona");
            }
        } else {
            header("location: " . $redirect_path . "?error=insertar");
        }
    } else{
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
}
else if(isset($_POST['actualizar'])){
    if(isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['descripcion']) &&
       isset($_POST['dificultad']) && isset($_POST['duracion']) && isset($_POST['postura']) && isset($_POST['subzona'])){

        $id = $_POST['id'];

        Validation::setOldInput('nombre_'.$id, trim($_POST['nombre']));
        Validation::setOldInput('descripcion_'.$id, trim($_POST['descripcion']));
        Validation::setOldInput('dificultad_'.$id, $_POST['dificultad']);
        Validation::setOldInput('duracion_'.$id, $_POST['duracion']);
        Validation::setOldInput('materiales_'.$id, $_POST['materiales']);
        Validation::setOldInput('postura_'.$id, $_POST['postura']);
        Validation::setOldInput('subzona_'.$id, $_POST['subzona']);

        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $dificultad = $_POST['dificultad'];
        $duracion = $_POST['duracion'];
        $materiales = !empty($_POST['materiales']) ? $_POST['materiales'] : '';
        $postura = $_POST['postura'];
        $subzonaArray = is_array($_POST['subzona']) ? $_POST['subzona'] : [];

        // Validaciones (mantén las mismas)
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

        if (empty($dificultad)) {
            Validation::setError('dificultad_'.$id, 'La dificultad es obligatoria.');
        }

        if (empty($duracion)) {
            Validation::setError('duracion_'.$id, 'La duración es obligatoria.');
        } elseif (!is_numeric($duracion) || $duracion <= 0) {
            Validation::setError('duracion_'.$id, 'La duración debe ser un número entero positivo (en segundos).');
        }

        if (empty($postura)) {
            Validation::setError('postura_'.$id, 'La postura es obligatoria.');
        }

        if (empty($subzonaArray)) {
            Validation::setError('subzona_'.$id, 'La subzona es obligatoria.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $ejercicio = new EjercicioEquilibrio($id, $nombre, $descripcion, $dificultad, $duracion, $materiales, $postura);
        $ejercicioBusiness = new EjercicioEquilibrioBusiness();
        $result = $ejercicioBusiness->actualizarTbejercicioequilibrio($ejercicio);

        if ($result == 1) {
            // Actualizar subzonas - ELIMINAR LAS EXISTENTES E INSERTAR LAS NUEVAS
            $ejercicioSubzonaBusiness = new ejercicioSubzonaBusiness();

            // Eliminar subzonas existentes para este ejercicio
            $ejercicioSubzonaBusiness->eliminarTBEjercicioSubZona($id, 'Equilibrio');

            // Insertar las nuevas subzonas
            $okSub = true;
            foreach ($subzonaArray as $subzonaId) {
                $nuevaSubzona = new ejercicioSubzona(0, $id, $subzonaId, 'Equilibrio');
                $resultSubzona = $ejercicioSubzonaBusiness->insertarTBEjercicioSubzona($nuevaSubzona);
                if (!$resultSubzona) {
                    $okSub = false;
                }
            }

            if ($okSub) {
                Validation::clear();
                header("location: " . $redirect_path . "?success=actualizado");
            } else {
                header("location: " . $redirect_path . "?error=actualizar_subzona");
            }
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

        $ejercicioBusiness = new EjercicioEquilibrioBusiness();
        $result = $ejercicioBusiness->eliminarTbejercicioequilibrio($id);

        if ($result == 1) {
            // También eliminar las subzonas asociadas
            $ejercicioSubzonaBusiness = new ejercicioSubzonaBusiness();
            $ejercicioSubzonaBusiness->eliminarTBEjercicioSubZona($id, 'Equilibrio');

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