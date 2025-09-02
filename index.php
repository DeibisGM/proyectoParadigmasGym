<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    // Si no hay sesión, redirigir al login
    header("Location: view/loginView.php");
    exit();
}

// Obtener información del usuario
$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];
$correoUsuario = $_SESSION['usuario_correo'];
$nombreUsuario = $_SESSION['usuario_nombre'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym - Página Principal</title>
</head>
<body>
<header>
    <h1>Gimnasio</h1>
    <p>Bienvenido, <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong></p>
    <p>Tipo de usuario:
        <strong>
            <?php
            if ($tipoUsuario == 'admin') {
                echo 'Administrador';
            } else if ($tipoUsuario == 'instructor') {
                echo 'Instructor';
            } else {
                echo 'Cliente';
            }
            ?>
        </strong>
    </p>
    <p>Correo electronico:
        <?php echo htmlspecialchars($correoUsuario);
        echo '<br><br>'
        ?>
        <a href="action/logoutAction.php">
            <button>Cerrar Sesión</button>
        </a>
</header>

<hr>

<main>
    <h2>Menú Principal</h2>

    <?php if ($tipoUsuario == 'admin'): ?>
        <section>
            <h3>Gestión General</h3>
            <a href='view/instructorView.php'>
                <button>Gestionar Instructores</button>
            </a>
            <a href='view/clienteView.php'>
                <button>Gestionar Clientes</button>
            </a>
            <a href='view/cuerpoZonaView.php'>
                <button>Gestionar Zonas del Cuerpo</button>
            </a>
            <a href='view/reservaView.php'>
                <button>Gestionar Horarios y Reservas</button>
            </a>
            <a href='view/padecimientoView.php'>
                <button>Ver Padecimientos (General)</button>
            </a>
            <a href='view/certificadoView.php'>
                <button>Ver Certificados</button>
            </a>
            <a href='view/datoClinicoView.php'>
                <button>Ver Datos Clínicos</button>
            </a>
            <a href='view/numeroEmergenciaView.php'>
                <button>Ver Números Emergencia</button>
            </a>
        <a href='view/salaView.php'>
                        <button>Ver las salas</button>
                    </a>
        </section>
    <?php elseif ($tipoUsuario == 'instructor'): ?>
        <section>
            <h3>Panel de Instructor</h3>
            <a href='view/clienteView.php'>
                <button>Clientes</button>
            </a>
            <a href='view/reservaView.php'>
                <button>Mis Horarios y Eventos</button>
            </a>
            <a href='view/certificadoView.php'>
                <button>Mis Certificados</button>
            </a>
            <a href='view/instructorView.php'>
                <button>Ver Instructores</button>
            </a>
            <a href='view/salaView.php'>
                        <button>Ver las salas</button>
                    </a>
        </section>
        <section>
            <h3>Recursos y Salud de Clientes</h3>
            <a href='view/cuerpoZonaView.php'>
                <button>Zonas del Cuerpo</button>
            </a>
            <a href='view/padecimientoView.php'>
                <button>Padecimientos de Clientes</button>
            </a>
            <a href='view/datoClinicoView.php'>
                <button>Datos Clínicos de Clientes</button>
            </a>
            <a href='view/numeroEmergenciaView.php'>
                <button>Emergencias de Clientes</button>
            </a>
        </section>
    <?php elseif ($tipoUsuario == 'cliente'): ?>
        <section>
            <h3>Mi Perfil</h3>
            <a href='view/clienteView.php'>
                <button>Ver mi Perfil</button>
            </a>
            <a href='view/datoClinicoView.php'>
                <button>Mis Datos Clínicos</button>
            </a>
            <a href='view/numeroEmergenciaView.php'>
                <button>Mis Números de Emergencia</button>
            </a>
        </section>
        <section>
            <h3>Actividad en el Gimnasio</h3>
            <a href='view/reservaView.php'>
                <button>Mis Horarios y Reservas</button>
            </a>
            <a href='view/instructorView.php'>
                <button>Ver Instructores</button>
            </a>
            <a href='view/cuerpoZonaView.php'>
                <button>Zonas del Cuerpo</button>
            </a>
            </a>
                    <a href='view/salaView.php'>
                        <button>Ver las salas</button>
                    </a>
        </section>
    <?php endif; ?>

</main>

<hr>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados por Disney.</p>
</footer>

</body>
</html>