<?php
session_start();
include '../business/clienteBusiness.php';
include '../business/instructorBusiness.php';

if (isset($_POST['login'])) {
    // Verificar que se hayan enviado los campos necesarios
    if (isset($_POST['correo']) && isset($_POST['contrasena']) &&
        !empty($_POST['correo']) && !empty($_POST['contrasena'])) {

        $correo = trim($_POST['correo']);
        $contrasena = trim($_POST['contrasena']);

        if ($correo === 'root@gmail.com' && $contrasena === 'root') {
            // Es el administrador principal
            $_SESSION['usuario_id'] = 0;
            $_SESSION['usuario_nombre'] = 'Admin';
            $_SESSION['usuario_correo'] = 'root@gmail.com';
            $_SESSION['tipo_usuario'] = 'admin';

            // Redirigir a la página principal
            header("Location: ../index.php");
            exit();
        }

        $instructorBusiness = new InstructorBusiness();
        $clienteBusiness = new ClienteBusiness();

        // Primero verificamos si el correo existe como instructor
        if ($instructorBusiness->existeInstructorPorCorreo($correo)) {
            $instructor = $instructorBusiness->autenticarInstructor($correo, $contrasena);
            if ($instructor != null) {
                // Es un instructor, guardamos la información en la sesión
                $_SESSION['usuario_id'] = $instructor->getInstructorId();
                $_SESSION['usuario_nombre'] = $instructor->getInstructorNombre();
                $_SESSION['usuario_correo'] = $instructor->getInstructorCorreo();
                $_SESSION['tipo_usuario'] = 'instructor';

                // Redirigir a la página principal
                header("Location: ../index.php");
                exit();
            } else {
                // El correo existe como instructor pero las credenciales son incorrectas o está inactivo
                header("Location: ../view/loginView.php?error=invalid_credentials");
                exit();
            }
        } else {
            // Si no es un instructor, verificamos si es un cliente
            $cliente = $clienteBusiness->autenticarCliente($correo, $contrasena);

            if ($cliente != null) {
                // Es un cliente, guardamos la información en la sesión
                $_SESSION['usuario_id'] = $cliente->getId();
                $_SESSION['usuario_nombre'] = $cliente->getNombre();
                $_SESSION['usuario_correo'] = $cliente->getCorreo();
                $_SESSION['tipo_usuario'] = 'cliente';

                // Redirigir a la página principal
                header("Location: ../index.php");
                exit();
            } else {
                // No es ni cliente ni instructor
                header("Location: ../view/loginView.php?error=invalid_credentials");
                exit();
            }
        }
    } else {
        // Campos vacíos
        header("Location: ../view/loginView.php?error=empty_fields");
        exit();
    }
} else {
    // Acceso directo a la página sin enviar el formulario
    header("Location: ../view/loginView.php");
    exit();
}
?>