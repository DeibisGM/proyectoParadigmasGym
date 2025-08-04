<?php
include '../business/instructorBusiness.php';

if (isset($_POST['update'])) {
    if (isset($_POST['tbinstructorid']) && isset($_POST['tbinstructornombre']) && isset($_POST['tbinstructortelefono']) && isset($_POST['tbinstructordireccion']) && isset($_POST['tbinstructorcorreo']) && isset($_POST['tbinstructorcuenta'])) {
        $nombre = trim($_POST['tbinstructornombre']);
        $telefono = trim($_POST['tbinstructortelefono']);
        $direccion = trim($_POST['tbinstructordireccion']);
        $correo = trim($_POST['tbinstructorcorreo']);
        $cuenta = trim($_POST['tbinstructorcuenta']);

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

        $instructor = new Instructor($_POST['tbinstructorid'], $nombre, $telefono, $direccion, $correo, $cuenta);
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
    if (isset($_POST['tbinstructorid'])) {
        $instructorBusiness = new InstructorBusiness();
        $result = $instructorBusiness->eliminarTBInstructor($_POST['tbinstructorid']);
        if ($result == 1) {
            header("location: ../view/instructorView.php?success=deleted");
        } else {
            header("location: ../view/instructorView.php?error=dbError");
        }
    } else {
        header("location: ../view/instructorView.php?error=error");
    }
}
else if (isset($_POST['create'])) {
    if (isset($_POST['tbinstructornombre']) && isset($_POST['tbinstructortelefono']) && isset($_POST['tbinstructordireccion']) && isset($_POST['tbinstructorcorreo']) && isset($_POST['tbinstructorcuenta'])) {
        $nombre = trim($_POST['tbinstructornombre']);
        $telefono = trim($_POST['tbinstructortelefono']);
        $direccion = trim($_POST['tbinstructordireccion']);
        $correo = trim($_POST['tbinstructorcorreo']);
        $cuenta = trim($_POST['tbinstructorcuenta']);

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

        $instructor = new Instructor(null, $nombre, $telefono, $direccion, $correo, $cuenta);
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