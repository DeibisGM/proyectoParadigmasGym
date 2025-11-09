<?php
ob_start();

error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include_once '../business/clientePadecimientoBusiness.php';
include_once '../utility/Validation.php';
if (!class_exists('ClientePadecimiento')) {
    include_once '../domain/clientePadecimiento.php';
}

Validation::start();

if (isset($_GET['action']) && $_GET['action'] == 'get_padecimientos_por_tipo') {
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
    if (!empty($tipo)) {
        include_once '../business/padecimientoBusiness.php';
        $padecimientoBusiness = new PadecimientoBusiness();
        $padecimientos = $padecimientoBusiness->obtenerPadecimientosPorTipo($tipo);
        header('Content-Type: application/json');
        echo json_encode($padecimientos);
    }
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    header("location: ../view/loginView.php");
    exit();
}

$clientePadecimientoBusiness = new ClientePadecimientoBusiness();
$redirect = "location: ../view/clientePadecimientoView.php";

$esUsuarioCliente = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cliente';
$esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
$esInstructor = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'instructor';

if (isset($_POST['create'])) {
    Validation::setOldInput($_POST);

    if ($esUsuarioCliente) {
        $clienteId = $_SESSION['usuario_id'];
    } else {
        $clienteId = isset($_POST['clienteId']) ? intval($_POST['clienteId']) : 0;
    }

    $tipoPadecimiento = isset($_POST['tipoPadecimiento']) ? trim($_POST['tipoPadecimiento']) : '';

    $padecimientosIds = array();
    if (isset($_POST['padecimientosIds']) && is_array($_POST['padecimientosIds'])) {
        foreach ($_POST['padecimientosIds'] as $id) {
            $id = intval($id);
            if ($id > 0) {
                $padecimientosIds[] = $id;
            }
        }
    }

    $dictamenId = null;
    if (isset($_POST['dictamenId']) && !empty($_POST['dictamenId']) && $_POST['dictamenId'] !== '0') {
        $dictamenId = intval($_POST['dictamenId']);
    }

    // Validaciones
    if (!$esUsuarioCliente && (empty($clienteId) || $clienteId <= 0)) {
        Validation::setError('clienteId', 'Debe seleccionar un cliente válido.');
    }

    if (empty($tipoPadecimiento)) {
        Validation::setError('tipoPadecimiento', 'Debe seleccionar un tipo de padecimiento.');
    }

    if (empty($padecimientosIds)) {
        Validation::setError('padecimientos', 'Debe seleccionar al menos un padecimiento.');
    } else {
        $padecimientosString = implode('$', $padecimientosIds);
        if (!$clientePadecimientoBusiness->validarPadecimientosExisten($padecimientosString)) {
            Validation::setError('padecimientos', 'Uno o más padecimientos seleccionados no son válidos.');
        }
    }

    if (Validation::hasErrors()) {
        header($redirect);
        exit();
    }

    $padecimientosString = implode('$', $padecimientosIds);
    $clientePadecimiento = new ClientePadecimiento(0, $clienteId, $padecimientosString, $dictamenId);

    ob_start();
    $resultado = $clientePadecimientoBusiness->insertarTBClientePadecimiento($clientePadecimiento);
    ob_end_clean();

    if ($resultado) {
        Validation::clear();
        if (isset($_SESSION['temp_form_data'])) {
            unset($_SESSION['temp_form_data']);
        }
        header($redirect . "?success=created");
    } else {
        Validation::setError('general', 'No se pudo registrar el cliente padecimiento en la base de datos.');
        header($redirect);
    }

} else if (isset($_POST['update'])) {
    Validation::setOldInput($_POST);

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($esUsuarioCliente) {
        $clienteId = $_SESSION['usuario_id'];
    } else {
        $clienteId = isset($_POST['clienteId']) ? intval($_POST['clienteId']) : 0;
    }

    $tipoPadecimiento = isset($_POST['tipoPadecimiento']) ? trim($_POST['tipoPadecimiento']) : '';

    $padecimientosIds = array();
    if (isset($_POST['padecimientosIds']) && is_array($_POST['padecimientosIds'])) {
        foreach ($_POST['padecimientosIds'] as $padId) {
            $padId = intval($padId);
            if ($padId > 0) {
                $padecimientosIds[] = $padId;
            }
        }
    }

    $dictamenId = null;
    if (isset($_POST['dictamenId']) && !empty($_POST['dictamenId']) && $_POST['dictamenId'] !== '0') {
        $dictamenId = intval($_POST['dictamenId']);
    }

    // Validaciones
    if ($id <= 0) {
        Validation::setError('id', 'ID de registro inválido.');
    }

    if (!$esUsuarioCliente && (empty($clienteId) || $clienteId <= 0)) {
        Validation::setError('clienteId', 'Debe seleccionar un cliente válido.');
    }

    if (empty($tipoPadecimiento)) {
        Validation::setError('tipoPadecimiento', 'Debe seleccionar un tipo de padecimiento.');
    }

    if (empty($padecimientosIds)) {
        Validation::setError('padecimientos', 'Debe seleccionar al menos un padecimiento.');
    } else {
        $padecimientosString = implode('$', $padecimientosIds);
        if (!$clientePadecimientoBusiness->validarPadecimientosExisten($padecimientosString)) {
            Validation::setError('padecimientos', 'Uno o más padecimientos seleccionados no son válidos.');
        }
    }

    if (Validation::hasErrors()) {
        header($redirect);
        exit();
    }

    $padecimientosString = implode('$', $padecimientosIds);
    $clientePadecimiento = new ClientePadecimiento($id, $clienteId, $padecimientosString, $dictamenId);

    ob_start();
    $resultado = $clientePadecimientoBusiness->actualizarTBClientePadecimiento($clientePadecimiento);
    ob_end_clean();

    if ($resultado) {
        Validation::clear();
        header($redirect . "?success=updated");
    } else {
        Validation::setError('general', 'No se pudo actualizar el cliente padecimiento.');
        header($redirect);
    }

} else if (isset($_POST['delete'])) {
    if (!$esAdmin) {
        Validation::setError('general', 'Solo los administradores pueden eliminar registros.');
        header($redirect);
        exit();
    }

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        Validation::setError('general', 'ID de registro inválido.');
        header($redirect);
        exit();
    }

    ob_start();
    $resultado = $clientePadecimientoBusiness->eliminarTBClientePadecimientoConDictamenes($id);
    ob_end_clean();

    if ($resultado) {
        Validation::clear();
        header($redirect . "?success=deleted");
    } else {
        Validation::setError('general', 'No se pudo eliminar el cliente padecimiento.');
        header($redirect);
    }

} else if (isset($_POST['updateIndividual'])) {
    $registroId = isset($_POST['registroId']) ? intval($_POST['registroId']) : 0;
    $padecimientoIdAntiguo = isset($_POST['padecimientoIdAntiguo']) ? intval($_POST['padecimientoIdAntiguo']) : 0;
    $padecimientoIdNuevo = isset($_POST['padecimientoIdNuevo']) ? intval($_POST['padecimientoIdNuevo']) : 0;

    ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    if ($registroId <= 0 || $padecimientoIdAntiguo <= 0 || $padecimientoIdNuevo <= 0) {
        echo json_encode(['success' => false, 'message' => 'Error: Datos de registro inválidos.']);
        exit();
    }

    ob_start();
    $resultado = $clientePadecimientoBusiness->actualizarPadecimientoIndividual($registroId, $padecimientoIdAntiguo, $padecimientoIdNuevo);
    ob_end_clean();

    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Éxito: Padecimiento actualizado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: No se pudo actualizar el padecimiento.']);
    }
    exit();

} else if (isset($_POST['deleteIndividual'])) {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    if (!$esAdmin) {
        echo json_encode(['success' => false, 'message' => 'Error: Solo los administradores pueden eliminar padecimientos.']);
        exit();
    }

    $registroId = isset($_POST['registroId']) ? intval($_POST['registroId']) : 0;
    $padecimientoId = isset($_POST['padecimientoId']) ? intval($_POST['padecimientoId']) : 0;

    if ($registroId <= 0 || $padecimientoId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Error: Datos de registro inválidos.']);
        exit();
    }

    ob_start();
    $resultado = $clientePadecimientoBusiness->eliminarPadecimientoIndividual($registroId, $padecimientoId);
    ob_end_clean();

    if (is_array($resultado)) {
        echo json_encode($resultado);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: Respuesta inesperada del servidor.']);
    }
    exit();

} else {
    header($redirect);
}

exit();
?>