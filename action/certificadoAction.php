<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../business/certificadoBusiness.php';
include_once '../domain/certificado.php'; 
include_once '../utility/ImageManager.php';

$certificadoBusiness = new CertificadoBusiness();
$imageManager = new ImageManager();

// Manejar eliminación de imagen
if (isset($_POST['delete_image'])) {
    if (isset($_POST['id'])) {
        $certificadoId = $_POST['id'];
        $certificado = $certificadoBusiness->getCertificadoPorId($certificadoId);
        if ($certificado) {
            $imageManager->deleteImage($certificado->getTbcertificadoImagenId());
            $certificado->setTbcertificadoImagenId('');
            $certificadoBusiness->updateCertificado($certificado);
            header("location: ../view/certificadoView.php?success=image_deleted");
        } else {
            header("location: ../view/certificadoView.php?error=notFound");
        }
    } else {
        header("location: ../view/certificadoView.php?error=error");
    }
    exit();
}

if (isset($_POST['create'])) {
    if (
        isset($_POST['nombre']) && isset($_POST['descripcion']) &&
        isset($_POST['entidad']) && isset($_POST['idInstructor'])
    ) {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $entidad = trim($_POST['entidad']);
        $idInstructor = intval($_POST['idInstructor']);

        if (empty($nombre) || empty($descripcion) || empty($entidad) || empty($idInstructor)) {
            header("location: ../view/certificadoView.php?error=emptyFields");
            exit();
        }
        if (strlen($nombre) > 100) {
            header("location: ../view/certificadoView.php?error=nameTooLong");
            exit();
        }

        // Validar que el instructor exista
        include '../business/instructorBusiness.php';
        $instructorBusiness = new InstructorBusiness();
        $instructor = $instructorBusiness->getInstructorPorId($idInstructor);

        if ($instructor == null) {
            header("location: ../view/certificadoView.php?error=instructorNotFound");
            exit();
        }

        $certificado = new Certificado(null, $nombre, $descripcion, $entidad, $idInstructor, '');
        $newId = $certificadoBusiness->addCertificado($certificado);

        if ($newId) {
            // Gestionar imagen después de crear el certificado
            if (isset($_FILES['tbcertificadoimagenid']) && !empty($_FILES['tbcertificadoimagenid']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['tbcertificadoimagenid'], $newId, 'cert');

                if (!empty($newImageIds)) {
                    // Actualizar el certificado con el ID de la imagen
                    $certificadoActualizado = $certificadoBusiness->getCertificadoPorId($newId);
                    if ($certificadoActualizado) {
                        $certificadoActualizado->setTbcertificadoImagenId($newImageIds[0]);
                        $certificadoBusiness->updateCertificado($certificadoActualizado);
                    }
                }
            }

            header("location: ../view/certificadoView.php?success=created");
        } else {
            header("location: ../view/certificadoView.php?error=dbError");
        }
    } else {
        header("location: ../view/certificadoView.php?error=error");
    }
}

if (isset($_POST['update'])) {
    if (
        isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['descripcion']) &&
        isset($_POST['entidad']) && isset($_POST['idInstructor'])
    ) {
        $id = intval($_POST['id']);
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $entidad = trim($_POST['entidad']);
        $idInstructor = intval($_POST['idInstructor']);

        if (empty($nombre) || empty($descripcion) || empty($entidad) || empty($idInstructor)) {
            header("location: ../view/certificadoView.php?error=emptyFields");
            exit();
        }
        if (strlen($nombre) > 100) {
            header("location: ../view/certificadoView.php?error=nameTooLong");
            exit();
        }

        // Validar que el instructor exista
        include '../business/instructorBusiness.php';
        $instructorBusiness = new InstructorBusiness();
        $instructor = $instructorBusiness->getInstructorPorId($idInstructor);

        if ($instructor == null) {
            header("location: ../view/certificadoView.php?error=instructorNotFound");
            exit();
        }

        // Obtener el certificado actual
        $certificadoActual = $certificadoBusiness->getCertificadoPorId($id);

        if ($certificadoActual) {
            $certificadoActual->setNombre($nombre);
            $certificadoActual->setDescripcion($descripcion);
            $certificadoActual->setEntidad($entidad);
            $certificadoActual->setIdInstructor($idInstructor);

            // Gestionar imagen
            if (isset($_FILES['tbcertificadoimagenid']) && !empty($_FILES['tbcertificadoimagenid']['name'][0])) {
                if ($certificadoActual->getTbcertificadoImagenId() != '' && $certificadoActual->getTbcertificadoImagenId() != '0') {
                    $imageManager->deleteImage($certificadoActual->getTbcertificadoImagenId());
                }
                $newImageIds = $imageManager->addImages($_FILES['tbcertificadoimagenid'], $id, 'cert');
                if (!empty($newImageIds)) {
                    $certificadoActual->setTbcertificadoImagenId($newImageIds[0]);
                }
            }

            $result = $certificadoBusiness->updateCertificado($certificadoActual);

            if ($result) {
                header("location: ../view/certificadoView.php?success=updated");
            } else {
                header("location: ../view/certificadoView.php?error=dbError");
            }
        } else {
            header("location: ../view/certificadoView.php?error=notFound");
        }
    } else {
        header("location: ../view/certificadoView.php?error=error");
    }
}

if (isset($_POST['delete'])) {
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $result = $certificadoBusiness->deleteCertificado($id);

        if ($result) {
            header("location: ../view/certificadoView.php?success=deleted");
        } else {
            header("location: ../view/certificadoView.php?error=dbError");
        }
    } else {
        header("location: ../view/certificadoView.php?error=error");
    }
}
?>