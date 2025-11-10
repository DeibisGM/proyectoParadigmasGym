<?php
session_start();
include_once __DIR__ . '/../business/rutinaBusiness.php';
include_once __DIR__ . '/../domain/rutina.php';
include_once __DIR__ . '/../domain/rutinaEjercicio.php';

$tipoUsuario = $_SESSION['tipo_usuario'] ?? '';
if (!isset($_SESSION['usuario_id']) || !in_array($tipoUsuario, ['cliente', 'instructor', 'admin'], true)) {
    header('Location: ../view/loginView.php');
    exit();
}

$rutinaBusiness = new RutinaBusiness();

if (isset($_GET['action']) && $_GET['action'] == 'get_ejercicios_por_tipo') {
    header('Content-Type: application/json');
    $tipo = $_GET['tipo'] ?? '';
    if (empty($tipo)) {
        echo json_encode([]);
        exit();
    }
    $ejercicios = $rutinaBusiness->getEjerciciosPorTipo($tipo);
    echo json_encode($ejercicios);
    exit();
}

if (isset($_POST['create_rutina'])) {
    $clienteId = ($tipoUsuario === 'cliente')
        ? (int) $_SESSION['usuario_id']
        : (int) ($_POST['cliente_id'] ?? 0);

    if ($clienteId <= 0) {
        $redirect = $tipoUsuario === 'cliente'
            ? '../view/rutinaView.php?error=missing_client'
            : '../view/seguimientoClientesView.php?error=missing_client';
        header('Location: ' . $redirect);
        exit();
    }

    $fecha = $_POST['fecha_rutina'];
    $observacion = $_POST['observacion_rutina'];
    $ejerciciosData = $_POST['ejercicios'] ?? [];

    $rutina = new Rutina(0, $clienteId, $fecha, $observacion);
    $ejercicios = [];

    foreach ($ejerciciosData as $ejData) {
        $ejercicio = new RutinaEjercicio(
            0, 0,
            $ejData['tipo'],
            $ejData['id'],
            $ejData['series'] ?? null,
            $ejData['repeticiones'] ?? null,
            $ejData['peso'] ?? null,
            $ejData['tiempo'] ?? null,
            $ejData['descanso'] ?? null,
            $ejData['comentario'] ?? ''
        );
        $ejercicios[] = $ejercicio;
    }
    $rutina->setEjercicios($ejercicios);

    $success = $rutinaBusiness->crearRutinaCompleta($rutina);

    if ($tipoUsuario === 'cliente') {
        header('Location: ../view/rutinaView.php?' . ($success ? 'success=created' : 'error=db_error'));
    } else {
        $suffix = 'cliente=' . $clienteId . '&' . ($success ? 'success=created' : 'error=db_error');
        header('Location: ../view/seguimientoClientesView.php?' . $suffix);
    }
    exit();
}

if (isset($_POST['delete_rutina'])) {
    $rutinaId = $_POST['rutina_id'];
    if ($rutinaBusiness->eliminarRutinaCompleta($rutinaId)) {
        header('Location: ../view/rutinaView.php?success=deleted');
    } else {
        header('Location: ../view/rutinaView.php?error=db_error');
    }
    exit();
}
?>