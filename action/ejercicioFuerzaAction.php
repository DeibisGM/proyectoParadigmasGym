<?php
session_start();
include '../business/ejercicioFuerzaBusiness.php';
include_once '../business/ejercicioSubzonaBusiness.php';
include_once '../utility/Validation.php';

$redirect_path = '../view/ejercicioFuerzaView.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

if (!$esAdminOInstructor) {
    header("location: " . $redirect_path . "?error=unauthorized");
    exit();
}

$ejercicioFuerzaBusiness = new EjercicioFuerzaBusiness();
$ejercicioSubzonaBusiness = new ejercicioSubzonaBusiness();

if (isset($_POST['guardar'])) {

    if (isset($_POST['nombre']) && isset($_POST['subzona']) && isset($_POST['descripcion'])
        && isset($_POST['repeticion']) && isset($_POST['serie']) && isset($_POST['descanso'])) {

        Validation::setOldInput($_POST);

        $nombre = $_POST['nombre'];
        $subzona = $_POST['subzona'];
        $descripcion = $_POST['descripcion'];
        $repeticion = $_POST['repeticion'];
        $serie = $_POST['serie'];
        $peso = isset($_POST['peso']) ? 1 : 0;
        $descanso = $_POST['descanso'];
        $activo = 1;

        if (empty($nombre)) {
            Validation::setError('nombre', 'El nombre es obligatorio.');
        } elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre', 'El nombre no puede contener números.');
        } elseif ($ejercicioFuerzaBusiness->existeEjercicioPorNombre($nombre)) {
            Validation::setError('nombre', 'El ejercicio ya está registrado.');
        }

        if (empty($subzona)) {
            Validation::setError('subzona', 'La zona es obligatoria.');
        }

        if (empty($descripcion)) {
            Validation::setError('descripcion', 'La descripción es obligatoria.');
        }

        if (empty($repeticion)) {
            Validation::setError('repeticion', 'El número de repeticiones es obligatorio.');
        } elseif (!is_numeric($repeticion) || $repeticion <= 0) {
            Validation::setError('repeticion', 'Las repeticiones deben ser un número positivo.');
        }

        if (empty($serie)) {
            Validation::setError('serie', 'El número de series es obligatorio.');
        } elseif (!is_numeric($serie) || $serie <= 0) {
            Validation::setError('serie', 'Las series deben ser un número positivo.');
        }

        if (empty($descanso)) {
            Validation::setError('descanso', 'El tiempo de descanso es obligatorio.');
        } elseif (!is_numeric($descanso) || $descanso < 0) {
            Validation::setError('descanso', 'El descanso debe ser un número positivo.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $ejercicioFuerza = new EjercicioFuerza(0, $nombre, $descripcion, $repeticion, $serie, $peso, $descanso, $activo);
        $nuevoId = $ejercicioFuerzaBusiness->insertarTbejerciciofuerza($ejercicioFuerza);

        if ($nuevoId > 0) {

            $subzonas = $ejercicioSubzonaBusiness->getEjercicioSubzonaPorEjercicioNombre($nuevoId, 'Fuerza');

            if ($subzonas !== null) {

                if ($subzonas->getSubzona() !== null && $subzonas->getSubzona() !== '') {
                    $nuevaSubZona = $subzonas->getSubzona();
                    $nuevaSubZona .= "$" . $nuevoId;
                    $subzonas->setSubzona($nuevaSubZona);
                } else {
                    $subzonas->setSubzona($nuevoId);
                }

                $ejercicioSubzonaBusiness->actualizarTBEjercicioSubzona($subzonas);
            } else {

                $nuevaSubzona = new ejercicioSubzona(0, $nuevoId, implode('$', $subzona), 'Fuerza');
                $ejercicioSubzonaBusiness->insertarTBEjercicioSubzona($nuevaSubzona);
            }

            Validation::clear();
            header("location: " . $redirect_path . "?success=inserted");
            exit();
        } else {
            header("location: " . $redirect_path . "?error=insertar");
            exit();
        }

    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
        exit();
    }
} else if (isset($_POST['actualizar'])) {

    if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['subzona']) && isset($_POST['descripcion'])
        && isset($_POST['repeticion']) && isset($_POST['serie']) && isset($_POST['descanso']) && isset($_POST['activo'])) {

        $id = $_POST['id'];
        $ejercicioActual = $ejercicioFuerzaBusiness->getEjercicioFuerza($id);

        if ($ejercicioActual) {

            $nombre = trim($_POST['nombre']);
            $subzonaArray = is_array($_POST['subzona']) ? $_POST['subzona'] : [];
            $subzona = implode('$', $subzonaArray);
            $descripcion = trim($_POST['descripcion']);
            $repeticion = trim($_POST['repeticion']);
            $serie = trim($_POST['serie']);
            $peso = isset($_POST['peso']) ? 1 : 0;
            $descanso = trim($_POST['descanso']);
            $activo = $_POST['activo'];

            // Guardar old input por fila
            Validation::setOldInput('nombre_' . $id, $nombre);
            Validation::setOldInput('subzona_' . $id, $subzona);
            Validation::setOldInput('descripcion_' . $id, $descripcion);
            Validation::setOldInput('repeticion_' . $id, $repeticion);
            Validation::setOldInput('serie_' . $id, $serie);
            Validation::setOldInput('peso_' . $id, $peso);
            Validation::setOldInput('descanso_' . $id, $descanso);
            Validation::setOldInput('activo_' . $id, $activo);

            // Validaciones
            if ($nombre === '') {
                Validation::setError('nombre_' . $id, 'El nombre es obligatorio.');
            } elseif (preg_match('/[0-9]/', $nombre)) {
                Validation::setError('nombre_' . $id, 'El nombre no puede contener números.');
            } elseif ($ejercicioActual->getNombre() != $nombre && $ejercicioFuerzaBusiness->existeEjercicioPorNombre($nombre)) {
                Validation::setError('nombre_' . $id, 'El nombre ya está registrado.');
            }

            if (empty($subzonaArray)) {
                Validation::setError('subzona_' . $id, 'La subzona es obligatoria.');
            }

            if ($descripcion === '') {
                Validation::setError('descripcion_' . $id, 'La descripción es obligatoria.');
            }

            if ($repeticion === '') {
                Validation::setError('repeticion_' . $id, 'Las repeticiones son obligatorias.');
            } elseif (!is_numeric($repeticion) || $repeticion <= 0) {
                Validation::setError('repeticion_' . $id, 'Las repeticiones deben ser un número positivo.');
            }

            if ($serie === '') {
                Validation::setError('serie_' . $id, 'Las series son obligatorias.');
            } elseif (!is_numeric($serie) || $serie <= 0) {
                Validation::setError('serie_' . $id, 'Las series deben ser un número positivo.');
            }

            if ($descanso === '') {
                Validation::setError('descanso_' . $id, 'El descanso es obligatorio.');
            } elseif (!is_numeric($descanso) || $descanso < 0) {
                Validation::setError('descanso_' . $id, 'El descanso debe ser un número positivo.');
            }

            if (!in_array($activo, ['0', '1'], true)) {
                Validation::setError('activo_' . $id, 'El estado es obligatorio.');
            }

            if (Validation::hasErrors()) {
                header("location: " . $redirect_path);
                exit();
            }

            $ejercicioActual->setNombre($nombre);
            $ejercicioActual->setDescripcion($descripcion);
            $ejercicioActual->setRepeticion($repeticion);
            $ejercicioActual->setSerie($serie);
            $ejercicioActual->setPeso($peso);
            $ejercicioActual->setDescanso($descanso);
            $ejercicioActual->setActivo((int)$activo);

            $subzonaActual = $ejercicioSubzonaBusiness->getEjercicioSubzonaPorEjercicioNombre($id, 'Fuerza');

            if ($subzonaActual) {
                $subzonaActual->setSubzona($subzona);
                $okSub = $ejercicioSubzonaBusiness->actualizarTBEjercicioSubzona($subzonaActual);
            } else {

                $nuevaSubzona = new ejercicioSubzona(0, $id, $subzona, 'Fuerza');
                $okSub = $ejercicioSubzonaBusiness->insertarTBEjercicioSubzona($nuevaSubzona) > 0;
            }

            $okEjer = $ejercicioFuerzaBusiness->actualizarTbejerciciofuerza($ejercicioActual);

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

        header("location: " . $redirect_path . "?error=error");
        exit();
    }

} else if (isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $result = $ejercicioFuerzaBusiness->eliminarTbejerciciofuerza($id);

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