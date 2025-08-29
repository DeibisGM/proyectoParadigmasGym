<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include_once '../business/certificadoBusiness.php';
include_once '../domain/certificado.php'; 
$certificadoBusiness = new CertificadoBusiness();

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

        $certificado = new Certificado(null, $nombre, $descripcion, $entidad, $idInstructor);
        $result = $certificadoBusiness->addCertificado($certificado);

        if ($result) {

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

        $certificado = new Certificado($id, $nombre, $descripcion, $entidad, $idInstructor);
        $result = $certificadoBusiness->updateCertificado($certificado);

        if ($result) {
            header("location: ../view/certificadoView.php?success=updated");
        } else {
            header("location: ../view/certificadoView.php?error=dbError");
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