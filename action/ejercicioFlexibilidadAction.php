<?php
session_start();
include '../business/ejercicioFlexibilidadBusiness.php';
include_once '../business/ejercicioSubzonaBusiness.php';
include_once '../utility/Validation.php';

$redirect_path = '../view/ejercicioFlexibilidadView.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

if (!$esAdminOInstructor) {
    header("location: " . $redirect_path . "?error=unauthorized");
    exit();
}

$ejercicioFlexibilidadBusiness = new ejercicioFlexibilidadBusiness();
$ejercicioSubzonaBusiness = new ejercicioSubzonaBusiness();

if (isset($_POST['guardar'])) {

    // Se verifica que todos los campos obligatorios y opcionales (texto) existan
    if (isset($_POST['nombre']) && isset($_POST['subzona']) && isset($_POST['duracion'])
        && isset($_POST['descripcion']) && isset($_POST['series']) && isset($_POST['equipodeayuda'])) {

        Validation::setOldInput($_POST);

        // Limpieza de datos (trim para strings)
        $nombre = trim($_POST['nombre']);
        $subzona = $_POST['subzona']; // Array de subzonas
        $descripcion = trim($_POST['descripcion']);
        $duracion = (int)trim($_POST['duracion']);
        $series = (int)trim($_POST['series']);
        // CORRECCIÓN: Procesar equipodeayuda como TEXTO (string)
        $equipodeayuda = trim($_POST['equipodeayuda']);
        $activo = 1;

        if (empty($nombre)) {
            Validation::setError('nombre', 'El nombre es obligatorio.');
        } elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre', 'El nombre no puede contener números.');
        } elseif ($ejercicioFlexibilidadBusiness->existeEjercicioPorNombre($nombre)) {
            Validation::setError('nombre', 'El ejercicio ya está registrado.');
        }

        if (empty($subzona)) {
            Validation::setError('subzona', 'La zona es obligatoria.');
        }

        // Se añade validación para que sean números positivos
        if (empty($duracion) || !is_numeric($duracion) || $duracion <= 0) {
            Validation::setError('duracion', 'La duración del ejercicio es obligatoria y debe ser un número positivo.');
        }

        if (empty($series) || !is_numeric($series) || $series <= 0) {
            Validation::setError('series', 'Las series son obligatorias y deben ser un número positivo.');
        }

        // NOTA: Se omite validación para 'equipodeayuda' (texto), asumiendo que puede ser vacío.

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        // Se inserta el ejercicio con equipodeayuda como string
        $ejercicioFlexibilidad = new ejercicioFlexibilidad(0, $nombre, $descripcion, $duracion, $series, $equipodeayuda, $activo);
        $nuevoId = $ejercicioFlexibilidadBusiness->insertarTBEjercicioFlexibilidad($ejercicioFlexibilidad);

        if ($nuevoId > 0) {

            // Lógica para guardar las subzonas
            // Nota: Se asume que getEjercicioSubzonaPorEjercicioNombre ya tiene la lógica de BD
            $subzonaActual = $ejercicioSubzonaBusiness->getEjercicioSubzonaPorEjercicioNombre($nuevoId, 'Flexibilidad');

            if ($subzonaActual) {
                 // Si ya existe la entrada de subzona, se actualiza (lógica heredada)
                 $subzonaActual->setSubzona(implode('$', $subzona));
                 $okSub = $ejercicioSubzonaBusiness->actualizarTBEjercicioSubzona($subzonaActual);
            } else {
                 // Si no existe la entrada, se inserta
                 $nuevaSubzona = new ejercicioSubzona(0, $nuevoId, implode('$', $subzona), 'Flexibilidad');
                 $okSub = $ejercicioSubzonaBusiness->insertarTBEjercicioSubzona($nuevaSubzona) > 0;
            }

             if (isset($okSub) && $okSub) {
                 Validation::clear();
                 header("location: " . $redirect_path . "?success=inserted");
                 exit();
             } else {
                 // Error en la inserción de subzonas, aunque el ejercicio se insertó
                 header("location: " . $redirect_path . "?error=dbError");
                 exit();
             }

        } else {
            header("location: " . $redirect_path . "?error=insertar");
            exit();
        }

    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
        exit();
    }
} else if (isset($_POST['actualizar'])) {

    // Se corrige la validación del POST y se incluye 'equipodeayuda'
    if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['duracion'])
        && isset($_POST['descripcion']) && isset($_POST['series']) && isset($_POST['activo'])
        && isset($_POST['equipodeayuda'])) { // 'equipodeayuda' siempre existe como campo de texto

        $id = $_POST['id'];
        $ejercicioActual = $ejercicioFlexibilidadBusiness->getEjercicioFlexibilidad($id);

        if ($ejercicioActual) {

            $nombre = trim($_POST['nombre']);
            $subzonaArray = isset($_POST['subzona']) && is_array($_POST['subzona']) ? $_POST['subzona'] : [];
            $subzona = implode('$', $subzonaArray);
            $descripcion = trim($_POST['descripcion']);
            $duracion = trim($_POST['duracion']);
            $series = trim($_POST['series']);
            // CORRECCIÓN: Procesar como TEXTO (string)
            $equipodeayuda = trim($_POST['equipodeayuda']);
            $activo = $_POST['activo'];

            // Guardar old input por fila
            Validation::setOldInput('nombre_' . $id, $nombre);
            Validation::setOldInput('subzona_' . $id, $subzona);
            Validation::setOldInput('descripcion_' . $id, $descripcion);
            Validation::setOldInput('duracion_' . $id, $duracion);
            Validation::setOldInput('series_' . $id, $series);
            // Guardar el texto de equipodeayuda
            Validation::setOldInput('equipodeayuda_' . $id, $equipodeayuda);
            Validation::setOldInput('activo_' . $id, $activo);

            // Validaciones
            if ($nombre === '') {
                Validation::setError('nombre_' . $id, 'El nombre es obligatorio.');
            } elseif (preg_match('/[0-9]/', $nombre)) {
                Validation::setError('nombre_' . $id, 'El nombre no puede contener números.');
            } elseif ($ejercicioActual->getNombre() != $nombre && $ejercicioFlexibilidadBusiness->existeEjercicioPorNombre($nombre)) {
                Validation::setError('nombre_' . $id, 'El nombre ya está registrado.');
            }

            if (empty($subzonaArray)) {
                Validation::setError('subzona_' . $id, 'La subzona es obligatoria.');
            }

            // Se añade validación para que sean números positivos
            if ($duracion === '' || !is_numeric($duracion) || $duracion <= 0) {
                Validation::setError('duracion_' . $id, 'La duración es obligatoria y debe ser un número positivo.');
            }

            if ($series === '' || !is_numeric($series) || $series <= 0) {
                Validation::setError('series_' . $id, 'Las series son obligatorias y deben ser un número positivo.');
            }

            if (!in_array($activo, ['0', '1'], true)) {
                Validation::setError('activo_' . $id, 'El estado es obligatorio.');
            }

            if (Validation::hasErrors()) {
                header("location: " . $redirect_path);
                exit();
            }

            // Asignación de valores (equipodeayuda como string)
            $ejercicioActual->setNombre($nombre);
            $ejercicioActual->setDescripcion($descripcion);
            $ejercicioActual->setDuracion((int)$duracion);
            $ejercicioActual->setSeries((int)$series);
            $ejercicioActual->setEquipodeayuda($equipodeayuda); // Se actualiza con el texto
            $ejercicioActual->setActivo((int)$activo);

            $subzonaActual = $ejercicioSubzonaBusiness->getEjercicioSubzonaPorEjercicioNombre($id, 'Flexibilidad');

            if ($subzonaActual) {
                $subzonaActual->setSubzona($subzona);
                $okSub = $ejercicioSubzonaBusiness->actualizarTBEjercicioSubzona($subzonaActual);
            } else {
                $nuevaSubzona = new ejercicioSubzona(0, $id, $subzona, 'Flexibilidad');
                $okSub = $ejercicioSubzonaBusiness->insertarTBEjercicioSubzona($nuevaSubzona) > 0;
            }

            $okEjer = $ejercicioFlexibilidadBusiness->actualizarTBEjercicioFlexibilidad($ejercicioActual);

            if ($okEjer && $okSub) {
                Validation::clear();
                header("location: " . $redirect_path . "?success=updated");
                exit();
            } else {
                header("location: " . $redirect_path . "?error=dbError");
                exit();
            }

        } else {
            header("location: " . $redirect_path . "?error=notFound");
            exit();
        }
    } else {

        // Error al faltar datos en el POST
        header("location: " . $redirect_path . "?error=datos_faltantes");
        exit();
    }

} else if (isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $result = $ejercicioFlexibilidadBusiness->eliminarTBEjercicioFlexibilidad($id);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=eliminado");
        } else {
            header("location: " . $redirect_path . "?error=eliminar");
        }
    } else {
        header("location: " . $redirect_path . "?error=id_faltante");
    }
} else {
    header("location: " . $redirect_path . "?error=accion_no_valida");
}
?>