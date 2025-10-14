<?php
session_start();
include '../business/instructorHorarioBusiness.php';
include '../business/instructorBusiness.php';

$instructorHorarioBusiness = new InstructorHorarioBusiness();
$instructorBusiness = new InstructorBusiness();
$redirect_path = '../view/instructorHorarioView.php';

if (isset($_POST['create'])) {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("location: " . $redirect_path . "?error=permission_denied");
        exit();
    }

    if (isset($_POST['instructorId']) && isset($_POST['dia']) && isset($_POST['horaInicio']) && isset($_POST['horaFin'])) {

        $instructorId = trim($_POST['instructorId']);
        $dia = trim($_POST['dia']);
        $horaInicio = trim($_POST['horaInicio']);
        $horaFin = trim($_POST['horaFin']);

        // Validaciones
        if (empty($instructorId) || empty($dia) || empty($horaInicio) || empty($horaFin)) {
            header("location: " . $redirect_path . "?error=datos_faltantes");
            exit();
        }

        // Validar que la hora fin sea mayor que la hora inicio
        if (strtotime($horaFin) <= strtotime($horaInicio)) {
            header("location: " . $redirect_path . "?error=horaFin_invalida");
            exit();
        }

        try {
            $id = $instructorHorarioBusiness->getNextHorarioId();
            $horario = new InstructorHorario($id, $instructorId, $dia, $horaInicio, $horaFin, 1);

            if ($instructorHorarioBusiness->insertarTBInstructorHorario($horario)) {
                header("location: " . $redirect_path . "?success=inserted");
            } else {
                header("location: " . $redirect_path . "?error=dbError");
            }
        } catch (Exception $e) {
            header("location: " . $redirect_path . "?error=" . urlencode($e->getMessage()));
        }
    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
    exit();

} else if (isset($_POST['update'])) {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("location: " . $redirect_path . "?error=permission_denied");
        exit();
    }

    if (isset($_POST['id']) && isset($_POST['instructorId']) && isset($_POST['dia']) && isset($_POST['horaInicio']) && isset($_POST['horaFin'])) {

        $id = trim($_POST['id']);
        $instructorId = trim($_POST['instructorId']);
        $dia = trim($_POST['dia']);
        $horaInicio = trim($_POST['horaInicio']);
        $horaFin = trim($_POST['horaFin']);

        // Validaciones
        if (empty($id) || empty($instructorId) || empty($dia) || empty($horaInicio) || empty($horaFin)) {
            header("location: " . $redirect_path . "?error=datos_faltantes");
            exit();
        }

        if (strtotime($horaFin) <= strtotime($horaInicio)) {
            header("location: " . $redirect_path . "?error=horaFin_invalida");
            exit();
        }

        try {
            $horario = new InstructorHorario($id, $instructorId, $dia, $horaInicio, $horaFin, 1);

            if ($instructorHorarioBusiness->actualizarTBInstructorHorario($horario)) {
                header("location: " . $redirect_path . "?success=updated");
            } else {
                header("location: " . $redirect_path . "?error=dbError");
            }
        } catch (Exception $e) {
            header("location: " . $redirect_path . "?error=" . urlencode($e->getMessage()));
        }
    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
    exit();

} else if (isset($_POST['delete'])) {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("location: " . $redirect_path . "?error=permission_denied");
        exit();
    }

    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        if ($instructorHorarioBusiness->eliminarTBInstructorHorario($id)) {
            header("location: " . $redirect_path . "?success=deleted");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=id_faltante");
    }
    exit();
} else {
    header("location: " . $redirect_path . "?error=accion_no_valida");
    exit();
}
?>