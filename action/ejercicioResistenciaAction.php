<?php
session_start();
include '../business/ejercicioResistenciaBusiness.php';
include_once '../business/ejercicioSubzonaBusiness.php';
include_once '../utility/Validation.php';

$redirect_path = '../view/ejercicioResistenciaView.php';

if (!isset($_SESSION['tipo_usuario'])) {
    header("location: ../view/loginView.php");
    exit();
}

$esAdminOInstructor = ($_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'instructor');

if (!$esAdminOInstructor) {
    header("location: " . $redirect_path . "?error=unauthorized");
    exit();
}

$ejercicioResistenciaBusiness = new ejercicioResistenciaBusiness();
$ejercicioSubzonaBusiness = new ejercicioSubzonaBusiness();

if (isset($_POST['guardar'])) {

    if (isset($_POST['nombre']) && isset($_POST['subzona']) && isset($_POST['tiempo'])
        && isset($_POST['descripcion'])) {

        Validation::setOldInput($_POST);

        $nombre = $_POST['nombre'];
        $subzona = $_POST['subzona'];
        $tiempo = $_POST['tiempo'];
        $peso = isset($_POST['peso']) ? 1 : 0;
        $descripcion = $_POST['descripcion'];
        $activo = 1;

        if (empty($nombre)) {
            Validation::setError('nombre', 'El nombre es obligatorio.');
        } elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre', 'El nombre no puede contener números.');
        } elseif ($ejercicioResistenciaBusiness->existeEjercicioPorNombre($nombre)) {
            Validation::setError('nombre', 'El ejercicio ya está registrado.');
        }

        if (empty($subzona)) {
            Validation::setError('subzona', 'La zona es obligatoria.');
        }

        if (empty($tiempo)) {
            Validation::setError('tiempo', 'La duración del ejercicio es obligatoria.');
        }

        if (Validation::hasErrors()) {
            header("location: " . $redirect_path);
            exit();
        }

        $ejercicioResistencia = new ejercicioResistencia(0, $nombre, $tiempo, $peso, $descripcion, $activo);
        $nuevoId = $ejercicioResistenciaBusiness->insertarTBEjercicioResistencia($ejercicioResistencia);

        if ($nuevoId > 0) {

            $subzonas = $ejercicioSubzonaBusiness->getEjercicioSubzonaPorEjercicioNombre($nuevoId, 'Resistencia');

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

                $nuevaSubzona = new ejercicioSubzona(0, $nuevoId, implode('$', $subzona), 'Resistencia');
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

    if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['subzona']) && isset($_POST['tiempo'])
        && isset($_POST['descripcion']) && isset($_POST['activo'])) {

        $id = $_POST['id'];
        $ejercicioActual = $ejercicioResistenciaBusiness->getEjercicioResistencia($id);

        if ($ejercicioActual) {

            $nombre = trim($_POST['nombre']);
            $subzonaArray = is_array($_POST['subzona']) ? $_POST['subzona'] : [];
            $subzona = implode('$', $subzonaArray);
            $tiempo = trim($_POST['tiempo']);
            // Checkbox: si no viene, es 0
            $peso = isset($_POST['peso']) ? 1 : 0;
            $descripcion = trim($_POST['descripcion']);
            $activo = $_POST['activo'];

            // Guardar old input por fila
            Validation::setOldInput('nombre_' . $id, $nombre);
            Validation::setOldInput('subzona_' . $id, $subzona);
            Validation::setOldInput('tiempo_' . $id, $tiempo);
            Validation::setOldInput('peso_' . $id, $peso);
            Validation::setOldInput('descripcion_' . $id, $descripcion);
            Validation::setOldInput('activo_' . $id, $activo);

            // Validaciones
            if ($nombre === '') {
                Validation::setError('nombre_' . $id, 'El nombre es obligatorio.');
            } elseif (preg_match('/[0-9]/', $nombre)) {
                Validation::setError('nombre_' . $id, 'El nombre no puede contener números.');
            } elseif ($ejercicioActual->getNombre() != $nombre && $ejercicioResistenciaBusiness->existeEjercicioPorNombre($nombre)) {
                Validation::setError('nombre_' . $id, 'El nombre ya está registrado.');
            }

            if (empty($subzonaArray)) {
                Validation::setError('subzona_' . $id, 'La subzona es obligatoria.');
            }

            if ($tiempo === '') {
                Validation::setError('tiempo_' . $id, 'El tiempo es obligatorio.');
            }

            if (!in_array($activo, ['0', '1'], true)) {
                Validation::setError('activo_' . $id, 'El estado es obligatorio.');
            }

            if (Validation::hasErrors()) {
                header("location: " . $redirect_path);
                exit();
            }

            $ejercicioActual->setNombre($nombre);
            $ejercicioActual->setTiempo($tiempo);
            $ejercicioActual->setPeso($peso);
            $ejercicioActual->setDescripcion($descripcion);
            $ejercicioActual->setActivo((int)$activo);

            $subzonaActual = $ejercicioSubzonaBusiness->getEjercicioSubzonaPorEjercicioNombre($id, 'Resistencia');

            if ($subzonaActual) {
                $subzonaActual->setSubzona($subzona);
                $okSub = $ejercicioSubzonaBusiness->actualizarTBEjercicioSubzona($subzonaActual);
            } else {

                $nuevaSubzona = new ejercicioSubzona(0, $id, $subzona, 'Resistencia');
                $okSub = $ejercicioSubzonaBusiness->insertarTBEjercicioSubzona($nuevaSubzona) > 0;
            }

            $okEjer = $ejercicioResistenciaBusiness->actualizarTBEjercicioResistencia($ejercicioActual);

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

        $result = $ejercicioResistenciaBusiness->eliminarTBEjercicioResistencia($id);

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
