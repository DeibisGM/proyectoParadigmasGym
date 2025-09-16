<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../business/instructorBusiness.php';
include_once '../utility/ImageManager.php';

$instructorBusiness = new InstructorBusiness();
$imageManager = new ImageManager();
$redirect_path = '../view/instructorView.php';

// Mejora la sección de delete_image
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
}else if (isset($_POST['create'])) {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("location: " . $redirect_path . "?error=permission_denied");
        exit();
    }

    if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['telefono']) &&
        isset($_POST['direccion']) && isset($_POST['correo']) && isset($_POST['cuenta']) &&
        isset($_POST['contraseña'])) {

        $id = trim($_POST['id']);
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);
        $direccion = trim($_POST['direccion']);
        $correo = trim($_POST['correo']);
        $cuenta = trim($_POST['cuenta']);
        $contraseña = trim($_POST['contraseña']);
        $estado = 1; // Por defecto activo

        // Validaciones
        if (empty($id) || empty($nombre) || empty($correo) || empty($contraseña)) {
            header("location: " . $redirect_path . "?error=datos_faltantes");
            exit();
        }

        // Validar que la cédula tenga exactamente 3 dígitos
        if (!preg_match('/^[0-9]{3}$/', $id)) {
            header("location: " . $redirect_path . "?error=invalidId");
            exit();
        }

        // Validar que el ID no exista ya
        if ($instructorBusiness->getInstructorPorId($id)) {
            header("location: " . $redirect_path . "?error=existe");
            exit();
        }

        if (preg_match('/[0-9]/', $nombre)) {
            header("location: " . $redirect_path . "?error=invalidName");
            exit();
        }

        if (strlen($nombre) > 100) {
            header("location: " . $redirect_path . "?error=nameTooLong");
            exit();
        }

        // Validación de teléfono (solo números)
        if (!empty($telefono) && !preg_match('/^[0-9]+$/', $telefono)) {
            header("location: " . $redirect_path . "?error=invalidPhone");
            exit();
        }

        // Validación de longitud de teléfono (8-15 dígitos)
        if (!empty($telefono) && (strlen($telefono) < 8 || strlen($telefono) > 15)) {
            header("location: " . $redirect_path . "?error=phoneLengthInvalid");
            exit();
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            header("location: " . $redirect_path . "?error=correo_invalido");
            exit();
        }

        if (strlen($contraseña) < 4 || strlen($contraseña) > 8) {
            header("location: " . $redirect_path . "?error=passwordLengthInvalid");
            exit();
        }

        // Validar que el correo sea único
        if ($instructorBusiness->existeInstructorPorCorreo($correo)) {
            header("location: " . $redirect_path . "?error=emailExists");
            exit();
        }

      $instructor = new Instructor($id, $nombre, $telefono, $direccion, $correo, $cuenta, $contraseña, $estado, '', '');

         // Primero insertar el instructor sin imagen
         $result = $instructorBusiness->insertarTBInstructor($instructor);

         if ($result) {
             // Gestionar imagen después de crear el instructor
             if (isset($_FILES['tbinstructorimagenid']) && !empty($_FILES['tbinstructorimagenid']['name'][0])) {
                 $newImageIds = $imageManager->addImages($_FILES['tbinstructorimagenid'], $id, 'ins');

                 if (!empty($newImageIds)) {
                     // Actualizar el instructor con el ID de la imagen
                     $instructorActualizado = $instructorBusiness->getInstructorPorId($id);
                     if ($instructorActualizado) {
                         $instructorActualizado->setTbinstructorImagenId($newImageIds[0]);
                         $instructorBusiness->actualizarTBInstructor($instructorActualizado);
                     }
                 }
             }
             header("location: " . $redirect_path . "?success=inserted");
         } else {
             header("location: " . $redirect_path . "?error=create");
         }
         exit();
     }
} else if (isset($_POST['update'])) {
    if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['telefono']) &&
        isset($_POST['direccion']) && isset($_POST['correo']) && isset($_POST['cuenta']) &&
        isset($_POST['contraseña'])) {

        $id = trim($_POST['id']);

        // NO VALIDAR EL ID EN UPDATE - ya existe en BD
        $instructorActual = $instructorBusiness->getInstructorPorId($id);

        if ($instructorActual) {
            $nombre = trim($_POST['nombre']);
            $telefono = trim($_POST['telefono']);
            $direccion = trim($_POST['direccion']);
            $correo = trim($_POST['correo']);
            $cuenta = trim($_POST['cuenta']);
            $contraseña = trim($_POST['contraseña']);

            // Validaciones (SIN validar ID porque ya existe)
            if (empty($nombre) || empty($correo) || empty($contraseña)) {
                header("location: " . $redirect_path . "?error=datos_faltantes");
                exit();
            }

            if (preg_match('/[0-9]/', $nombre)) {
                header("location: " . $redirect_path . "?error=invalidName");
                exit();
            }

            if (strlen($nombre) > 100) {
                header("location: " . $redirect_path . "?error=nameTooLong");
                exit();
            }

            // Validación de teléfono (solo números)
            if (!empty($telefono) && !preg_match('/^[0-9]+$/', $telefono)) {
                header("location: " . $redirect_path . "?error=invalidPhone");
                exit();
            }

            // Validación de longitud de teléfono (8-15 dígitos)
            if (!empty($telefono) && (strlen($telefono) < 8 || strlen($telefono) > 15)) {
                header("location: " . $redirect_path . "?error=phoneLengthInvalid");
                exit();
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                header("location: " . $redirect_path . "?error=correo_invalido");
                exit();
            }

            if (strlen($contraseña) < 4 || strlen($contraseña) > 8) {
                header("location: " . $redirect_path . "?error=passwordLengthInvalid");
                exit();
            }

            // Validar que el correo sea único (excepto para el instructor actual)
            $correoExistente = $instructorBusiness->existeInstructorPorCorreo($correo);
            if ($correoExistente && $instructorActual->getInstructorCorreo() !== $correo) {
                header("location: " . $redirect_path . "?error=emailExists");
                exit();
            }

            // Actualizar datos del instructor
            $instructorActual->setInstructorNombre($nombre);
            $instructorActual->setInstructorTelefono($telefono);
            $instructorActual->setInstructorDireccion($direccion);
            $instructorActual->setInstructorCorreo($correo);
            $instructorActual->setInstructorCuenta($cuenta);
            $instructorActual->setInstructorContraseña($contraseña);

            // Gestionar imagen
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
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
    exit();
} else if (isset($_POST['delete'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $result = $instructorBusiness->eliminarTBInstructor($id);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=eliminado");
        } else {
            header("location: " . $redirect_path . "?error=delete");
        }
    } else {
        header("location: " . $redirect_path . "?error=id_faltante");
    }
    exit();
} else if (isset($_POST['activate'])) {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("location: " . $redirect_path . "?error=permission_denied");
        exit();
    }

    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $result = $instructorBusiness->activarTBInstructor($id);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=activated");
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