<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/loginView.php?error=not_logged_in");
    exit;
}

include_once '../business/horarioLibreBusiness.php';
$horarioLibreBusiness = new HorarioLibreBusiness();
$redirectURL = '../view/horarioLibreView.php';

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    if ($accion === 'crear' && $_SESSION['tipo_usuario'] === 'admin') {
        $slots = $_POST['slots'] ?? [];
        $instructorId = $_POST['instructorId'] ?? null;
        $cupos = $_POST['cupos'] ?? 0;
        $salaId = 1;

        if (!empty($slots) && $instructorId && $cupos > 0) {
            $creados = $horarioLibreBusiness->crearMultiplesHorarios($slots, $salaId, $instructorId, $cupos);
            if ($creados > 0) {
                header("Location: {$redirectURL}?success=created");
            } else {
                header("Location: {$redirectURL}?error=No+se+pudo+crear+ningun+espacio.+Verifique+que+no+existan+previamente.");
            }
        } else {
            header("Location: {$redirectURL}?error=Datos+incompletos.");
        }
        exit;

    } elseif ($accion === 'eliminar' && $_SESSION['tipo_usuario'] === 'admin') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            if ($horarioLibreBusiness->eliminarHorarioLibre($id)) {
                header("Location: {$redirectURL}?success=deleted");
            } else {
                header("Location: {$redirectURL}?error=db_error_on_delete");
            }
        } else {
            header("Location: {$redirectURL}?error=id_missing");
        }
        exit;
    }
}

header("Location: {$redirectURL}?error=invalid_action");
exit;
?>