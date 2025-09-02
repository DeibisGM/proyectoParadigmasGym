<?php
session_start();
include_once '../business/horarioBusiness.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("location: ../view/loginView.php?error=unauthorized");
    exit();
}

if (isset($_POST['update_horario'])) {
    $horarioBusiness = new HorarioBusiness();
    $result = $horarioBusiness->updateHorarios($_POST);

    if ($result) {
        header("location: ../view/horarioView.php?success=updated");
    } else {
        header("location: ../view/horarioView.php?error=dbError");
    }
} else {
    header("location: ../view/horarioView.php");
}
