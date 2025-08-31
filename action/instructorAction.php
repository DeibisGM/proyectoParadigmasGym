<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Usar include_once para evitar re-declaración de clases
include_once '../business/instructorBusiness.php';
include_once '../utility/ImageManager.php';

$instructorBusiness = new InstructorBusiness();
$imageManager = new ImageManager();
$redirect_path = '../view/instructorView.php';

// Acción para eliminar solo la imagen
if (isset($_POST['delete_image'])) {
    if (isset($_POST['id'])) {
        $instructorId = $_POST['id'];
        $instructor = $instructorBusiness->getInstructorPorId($instructorId);
        if ($instructor) {
            $imageManager->deleteImage($instructor->getTbinstructorImagenId());
            $instructor->setTbinstructorImagenId('');
            $instructorBusiness->actualizarTBInstructor($instructor);
            header("location: " . $redirect_path . "?success=image_deleted");
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
    exit();
}

// Acción para crear un nuevo instructor
if (isset($_POST['create'])) {
    if (isset($_POST['id'], $_POST['nombre'], $_POST['telefono'], $_POST['direccion'], $_POST['correo'], $_POST['cuenta'], $_POST['contraseña'])) {
        $id = trim($_POST['id']);
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $correo = trim($_POST['correo']);
        $cuenta = trim($_POST['cuenta']);
        $contraseña = trim($_POST['contraseña']);

        // Aquí irían las validaciones de servidor (longitud, formato, etc.)

        $instructor = new Instructor($id, $nombre, $telefono, $direccion, $correo, $cuenta, $contraseña, 1, '', '');

        if ($instructorBusiness->insertarTBInstructor($instructor)) {
            if (isset($_FILES['tbinstructorimagenid']) && !empty($_FILES['tbinstructorimagenid']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['tbinstructorimagenid'], $id, 'ins');
                if (!empty($newImageIds)) {
                    $instructorCreado = $instructorBusiness->getInstructorPorId($id);
                    $instructorCreado->setTbinstructorImagenId($newImageIds[0]);
                    $instructorBusiness->actualizarTBInstructor($instructorCreado);
                }
            }
            header("location: " . $redirect_path . "?success=created");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
    exit();
}

// Acción para actualizar un instructor existente
if (isset($_POST['update'])) {
    if (isset($_POST['id'], $_POST['nombre'], $_POST['telefono'], $_POST['direccion'], $_POST['correo'], $_POST['cuenta'], $_POST['contraseña'])) {
        $id = trim($_POST['id']);

        $instructorActual = $instructorBusiness->getInstructorPorId($id);
        if ($instructorActual) {
            $instructorActual->setInstructorNombre(trim($_POST['nombre']));
            $instructorActual->setInstructorTelefono(trim($_POST['telefono']));
            $instructorActual->setInstructorDireccion(trim($_POST['direccion']));
            $instructorActual->setInstructorCorreo(trim($_POST['correo']));
            $instructorActual->setInstructorCuenta(trim($_POST['cuenta']));
            $instructorActual->setInstructorContraseña(trim($_POST['contraseña']));

            if (isset($_FILES['tbinstructorimagenid']) && !empty($_FILES['tbinstructorimagenid']['name'][0])) {
                if ($instructorActual->getTbinstructorImagenId() != '' && $instructorActual->getTbinstructorImagenId() != '0') {
                    $imageManager->deleteImage($instructorActual->getTbinstructorImagenId());
                }
                $newImageIds = $imageManager->addImages($_FILES['tbinstructorimagenid'], $id, 'ins');
                if (!empty($newImageIds)) {
                    $instructorActual->setTbinstructorImagenId($newImageIds[0]);
                }
            }

            if ($instructorBusiness->actualizarTBInstructor($instructorActual)) {
                header("location: " . $redirect_path . "?success=updated");
            } else {
                header("location: " . $redirect_path . "?error=dbError");
            }
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
    exit();
}

// Acción para desactivar (eliminar lógicamente)
if (isset($_POST['delete'])) {
    if (isset($_POST['id'])) {
        if ($instructorBusiness->eliminarTBInstructor($_POST['id'])) {
            header("location: " . $redirect_path . "?success=deleted");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
    exit();
}

// Acción para activar
if (isset($_POST['activate'])) {
    if (isset($_POST['id'])) {
        if ($instructorBusiness->activarTBInstructor($_POST['id'])) {
            header("location: " . $redirect_path . "?success=activated");
        } else {
            header("location: " . $redirect_path . "?error=dbError");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
    exit();
}

header("location: " . $redirect_path . "?error=invalidRequest");
?>