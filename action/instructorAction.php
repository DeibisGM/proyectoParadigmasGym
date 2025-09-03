<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../business/instructorBusiness.php';
include '../utility/ImageManager.php';

if (isset($_POST['update'])) {
    if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['telefono']) && isset($_POST['direccion']) && isset($_POST['correo']) && isset($_POST['cuenta']) && isset($_POST['contraseña'])) {
        $id = trim($_POST['id']);
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $correo = trim($_POST['correo']);
        $cuenta = trim($_POST['cuenta']);
        $contraseña = trim($_POST['contraseña']);

        // Validaciones
        if (empty($id) || empty($nombre) || empty($correo) || empty($contraseña)) {
            header("location: ../view/instructorView.php?error=emptyFields");
            exit();
        }

        // Validar que la cédula tenga exactamente 3 dígitos
        if (!preg_match('/^[0-9]{3}$/', $id)) {
            header("location: ../view/instructorView.php?error=invalidId");
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
        // Validación de teléfono (solo números)
        if (!empty($telefono) && !preg_match('/^[0-9]+$/', $telefono)) {
            header("location: ../view/instructorView.php?error=invalidPhone");
            exit();
        }

        // Validación de longitud de teléfono (8-15 dígitos)
        if (!empty($telefono) && (strlen($telefono) < 8 || strlen($telefono) > 15)) {
            header("location: ../view/instructorView.php?error=phoneLengthInvalid");
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

        $instructorBusiness = new InstructorBusiness();

        // Validar que el correo sea único (excepto para el instructor actual)
        $correoExistente = $instructorBusiness->existeInstructorPorCorreo($correo);
        if ($correoExistente) {
            $instructorActual = $instructorBusiness->getInstructorPorId($id);
            if ($instructorActual && $instructorActual->getInstructorCorreo() !== $correo) {
                header("location: ../view/instructorView.php?error=emailExists");
                exit();
            }
        }

        $instructor = new Instructor($id, $nombre, $telefono, $direccion, $correo, $cuenta, $contraseña, 1);
        $result = $instructorBusiness->actualizarTBInstructor($instructor);

        if ($result == 1) {
            // Gestionar imagen después de actualizar
            $eliminarImagen = isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == '1';
            $resultadoImagen = gestionarImagen('instructores', $id, $_FILES['imagen'], $eliminarImagen);

            header("location: ../view/instructorView.php?success=updated");
        } else {
            header("location: ../view/instructorView.php?error=dbError");
        }
    } else {
        header("location: ../view/instructorView.php?error=error");
    }
} else if (isset($_POST['delete'])) {
    if (isset($_POST['id'])) {
        $instructorBusiness = new InstructorBusiness();
        $result = $instructorBusiness->eliminarTBInstructor($_POST['id']);
        if ($result == 1) {
            $resultadoImagen = gestionarImagen('instructores', $_POST['id'], null, true);
            header("location: ../view/instructorView.php?success=deleted");
        } else {
            header("location: ../view/instructorView.php?error=dbError");
        }
    } else {
        header("location: ../view/instructorView.php?error=error");
    }
} else if (isset($_POST['activate'])) {
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
} else if (isset($_POST['create'])) {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("location: ../view/instructorView.php?error=permission_denied");
        exit();
    }
    if (isset($_POST['id']) && ($_POST['nombre']) && isset($_POST['telefono']) && isset($_POST['direccion']) && isset($_POST['correo']) && isset($_POST['cuenta']) && isset($_POST['contraseña'])) {
        $id = trim($_POST['id']);
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $correo = trim($_POST['correo']);
        $cuenta = trim($_POST['cuenta']);
        $contraseña = trim($_POST['contraseña']);

        // Validaciones
        if (empty($id) || empty($nombre) || empty($correo) || empty($contraseña)) {
            header("location: ../view/instructorView.php?error=emptyFields");
            exit();
        }

        // Validar que la cédula tenga exactamente 3 dígitos
        if (!preg_match('/^[0-9]{3}$/', $id)) {
            header("location: ../view/instructorView.php?error=invalidId");
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
        // Validación de teléfono (solo números)
        if (!empty($telefono) && !preg_match('/^[0-9]+$/', $telefono)) {
            header("location: ../view/instructorView.php?error=invalidPhone");
            exit();
        }

        // Validación de longitud de teléfono (8-15 dígitos)
        if (!empty($telefono) && (strlen($telefono) < 8 || strlen($telefono) > 15)) {
            header("location: ../view/instructorView.php?error=phoneLengthInvalid");
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

        // Validar que el ID no exista ya
        $instructorBusiness = new InstructorBusiness();
        $instructorExistente = $instructorBusiness->getInstructorPorId($id);
        if ($instructorExistente) {
            header("location: ../view/instructorView.php?error=idExists");
            exit();
        }

        // Validar que el correo sea único
        $correoExistente = $instructorBusiness->existeInstructorPorCorreo($correo);
        if ($correoExistente) {
            header("location: ../view/instructorView.php?error=emailExists");
            exit();
        }

        $instructor = new Instructor($id, $nombre, $telefono, $direccion, $correo, $cuenta, $contraseña, 1);
        $result = $instructorBusiness->insertarTBInstructor($instructor);

        if ($result == 1) {
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $resultadoImagen = gestionarImagen('instructores', $id, $_FILES['imagen']);
            }
            header("location: ../view/instructorView.php?success=created");
        } else {
            header("location: ../view/instructorView.php?error=dbError");
        }
    } else {
        header("location: ../view/instructorView.php?error=error");
    }
} else {
    header("location: ../view/instructorView.php?error=invalidRequest");
}
?>