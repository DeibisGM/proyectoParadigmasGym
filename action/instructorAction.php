<?php
session_start();
include '../business/instructorBusiness.php';

if (isset($_POST['update'])) {
    if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['telefono']) && isset($_POST['direccion']) && isset($_POST['correo']) && isset($_POST['cuenta']) && isset($_POST['contraseña'])) {
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $correo = trim($_POST['correo']);
        $cuenta = trim($_POST['cuenta']);
        $contraseña = trim($_POST['contraseña']);


        // Validaciones
        if (empty($nombre) || empty($correo)) {
            header("location: ../view/instructorView.php?error=emptyFields");
            exit();
        }

        if (preg_match('/[0-9]/', $nombre)) {
            header("location: ../view/instructorView.php?error=invalidName");
            exit();
        }

        if (strlen($nombre) > 100) {
            header("location: ../view/instructorView.php?error=nameTooLong");
            exit();
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            header("location: ../view/instructorView.php?error=invalidEmail");
            exit();
        }

       if (strlen($contraseña) < 4 || strlen($contraseña) > 8) {
           header("location: ../view/instructorView.php?error=passwordLengthInvalid");
           exit();
       }

        $instructor = new Instructor($_POST['id'], $nombre, $telefono, $direccion, $correo, $cuenta, $contraseña, 1);
        $instructorBusiness = new InstructorBusiness();
        $result = $instructorBusiness->actualizarTBInstructor($instructor);

        if ($result == 1) {
            header("location: ../view/instructorView.php?success=updated");
        } else {
            header("location: ../view/instructorView.php?error=dbError");
        }
    } else {
        header("location: ../view/instructorView.php?error=error");
    }
}
else if (isset($_POST['delete'])) {
    if (isset($_POST['id'])) {
        $instructorBusiness = new InstructorBusiness();
        $result = $instructorBusiness->eliminarTBInstructor($_POST['id']);
        if ($result == 1) {
            header("location: ../view/instructorView.php?success=deleted");
        } else {
            header("location: ../view/instructorView.php?error=dbError");
        }
    } else {
        header("location: ../view/instructorView.php?error=error");
    }
}
else if (isset($_POST['activate'])) {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("location: ../view/instructorView.php?error=permission_denied");
        exit();
    }
    if (isset($_POST['id'])) {
        $instructorBusiness = new InstructorBusiness();
        $result = $instructorBusiness->activarTBInstructor($_POST['id']);
        if ($result == 1) {
            header("location: ../view/instructorView.php?success=activated");
        } else {
            header("location: ../view/instructorView.php?error=dbError");
        }
    } else {
        header("location: ../view/instructorView.php?error=error");
    }
}
else if (isset($_POST['create'])) {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("location: ../view/instructorView.php?error=permission_denied");
        exit();
    }
    if (isset($_POST['nombre']) && isset($_POST['telefono']) && isset($_POST['direccion']) && isset($_POST['correo']) && isset($_POST['cuenta']) && isset($_POST['contraseña'])) {
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $correo = trim($_POST['correo']);
        $cuenta = trim($_POST['cuenta']);
        $contraseña = trim($_POST['contraseña']);


        // Validaciones
        if (empty($nombre) || empty($correo)) {
            header("location: ../view/instructorView.php?error=emptyFields");
            exit();
        }

        if (preg_match('/[0-9]/', $nombre)) {
            header("location: ../view/instructorView.php?error=invalidName");
            exit();
        }

        if (strlen($nombre) > 100) {
            header("location: ../view/instructorView.php?error=nameTooLong");
            exit();
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            header("location: ../view/instructorView.php?error=invalidEmail");
            exit();
        }

        if (strlen($contraseña) < 4 || strlen($contraseña) > 8) {
               header("location: ../view/instructorView.php?error=passwordLengthInvalid");
               exit();
        }


        $instructor = new Instructor(null, $nombre, $telefono, $direccion, $correo, $cuenta, $contraseña, 1);
        $instructorBusiness = new InstructorBusiness();
        $result = $instructorBusiness->insertarTBInstructor($instructor);

        if ($result == 1) {
            header("location: ../view/instructorView.php?success=created");
        } else {
            header("location: ../view/instructorView.php?error=dbError");
        }
    } else {
        header("location: ../view/instructorView.php?error=error");
    }
}
?>