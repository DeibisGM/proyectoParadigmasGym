<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: view/loginView.php");
    exit();
}

$tipoUsuario = $_SESSION['tipo_usuario'];
$nombreUsuario = $_SESSION['usuario_nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym - Página Principal</title>
    <link rel="stylesheet" href="view/styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <h2>Gimnasio</h2>
        <p><i class="ph ph-user-circle"></i>Bienvenido, <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>
            (<?php echo htmlspecialchars(ucfirst($tipoUsuario)); ?>)</p>
        <a href="action/logoutAction.php">
            <button><i class="ph ph-sign-out"></i>Cerrar Sesión</button>
        </a>
    </header>

    <main>
        <?php if ($tipoUsuario == 'admin'): ?>
            <section>
                <h3><i class="ph ph-wrench"></i>Gestión General</h3>
                <div class="menu-grid">
                    <a href='view/instructorView.php'><button><i class="ph ph-users-three"></i>Instructores</button></a>
                    <a href='view/clienteView.php'><button><i class="ph ph-users"></i>Clientes</button></a>
                    <a href='view/cuerpoZonaView.php'><button><i class="ph ph-person-simple-run"></i>Zonas del Cuerpo</button></a>
                    <a href='view/horarioView.php'><button><i class="ph ph-clock-clockwise"></i>Horario General</button></a>
                    <a href='view/padecimientoView.php'><button><i class="ph ph-bandaids"></i>Padecimientos</button></a>
                    <a href='view/certificadoView.php'><button><i class="ph ph-certificate"></i>Certificados</button></a>
                    <a href='view/clientePadecimientoView.php'><button><i class="ph ph-first-aid-kit"></i>Datos Clínicos</button></a>
                    <a href='view/padecimientoDictamenView.php'><button><i class="ph ph-file-text"></i> Dictamen Médico</button></a>
                    <a href='view/numeroEmergenciaView.php'><button><i class="ph ph-phone-plus"></i>Números Emergencia</button></a>
                    <a href='view/salaView.php'><button><i class="ph ph-storefront"></i>Salas</button></a>
                    <a href='view/parteZonaView.php'><button><i class="ph ph-hand"></i>Partes de Zona</button></a>
                </div>
            </section>
            <section>
                <h3><i class="ph ph-calendar"></i>Reservas y Eventos</h3>
                <div class="menu-grid">
                    <a href='view/reservaView.php'><button><i class="ph ph-calendar-check"></i>Ver Reservas</button></a>
                    <a href='view/eventoGestionView.php'><button><i class="ph ph-calendar-plus"></i>Gestionar Eventos</button></a>
                    <a href='view/salaReservasView.php'><button><i class="ph ph-presentation-chart"></i>Ocupación de Salas</button></a>
                    <a href='view/horarioLibreView.php'><button><i class="ph ph-clock-afternoon"></i>Gestionar Horario Libre</button></a>
                    <!-- NUEVO: Instructor Personal para Admin -->
                    <a href='view/horarioPersonalView.php'><button><i class="ph ph-user-plus"></i>Instructor Personal</button></a>
                </div>
            </section>

        <?php elseif ($tipoUsuario == 'instructor'): ?>
            <section>
                <h3><i class="ph ph-chalkboard-teacher"></i>Panel de Instructor</h3>
                <div class="menu-grid">
                    <a href='view/reservaView.php'><button><i class="ph ph-calendar-check"></i>Ver Reservas</button></a>
                    <a href='view/eventoGestionView.php'><button><i class="ph ph-calendar-plus"></i>Gestionar Mis Eventos</button></a>
                    <a href='view/horarioPersonalView.php'><button><i class="ph ph-user-plus"></i>Mis Horarios Personales</button></a>
                    <a href='view/clienteView.php'><button><i class="ph ph-users"></i>Ver Clientes</button></a>
                    <a href='view/instructorView.php'><button><i class="ph ph-user-rectangle"></i>Ver Instructores</button></a>
                    <a href='view/cuerpoZonaView.php'><button><i class="ph ph-person-simple-run"></i>Zonas del Cuerpo</button></a>
                    <a href='view/parteZonaView.php'><button><i class="ph ph-hand"></i>Partes de Zona</button></a>
                    <a href='view/salaView.php'><button><i class="ph ph-storefront"></i>Ver Salas</button></a>
                </div>
            </section>
            <section>
                <h3><i class="ph ph-heartbeat"></i>Salud de Clientes</h3>
                <div class="menu-grid">
                    <a href='view/padecimientoView.php'><button><i class="ph ph-bandaids"></i>Padecimientos</button></a>
                    <a href='view/clientePadecimientoView.php'><button><i class="ph ph-first-aid-kit"></i>Datos Clínicos</button></a>
                    <a href='view/padecimientoDictamenView.php'><button><i class="ph ph-file-text"></i> Dictamen Médico</button></a>
                    <a href='view/numeroEmergenciaView.php'><button><i class="ph ph-phone-plus"></i>Contactos Emergencia</button></a>
                </div>
            </section>

        <?php elseif ($tipoUsuario == 'cliente'): ?>
            <section>
                <h3><i class="ph ph-barbell"></i>Actividad en el Gimnasio</h3>
                <div class="menu-grid">
                    <a href='view/reservaView.php'><button><i class="ph ph-calendar-check"></i>Mis Reservas</button></a>
                    <a href='view/horarioPersonalView.php'><button><i class="ph ph-user-plus"></i>Instructor Personal</button></a>
                    <a href='view/horarioLibreView.php'><button><i class="ph ph-barbell"></i>Uso Libre</button></a>
                    <a href='view/instructorClienteView.php'><button><i class="ph ph-chalkboard-teacher"></i>Ver Instructores</button></a>
                    <a href='view/cuerpoZonaView.php'><button><i class="ph ph-person-simple-run"></i>Zonas del Cuerpo</button></a>
                    <a href='view/parteZonaView.php'><button><i class="ph ph-hand"></i>Partes de Zonas</button></a>
                    <a href='view/salaView.php'><button><i class="ph ph-storefront"></i>Ver Salas</button></a>
                </div>
            </section>
            <section>
                <h3><i class="ph ph-user-focus"></i>Mi Perfil y Salud</h3>
                <div class="menu-grid">
                    <a href='view/clienteView.php'><button><i class="ph ph-user-rectangle"></i>Ver mi Perfil</button></a>
                    <a href='view/clientePadecimientoView.php'><button><i class="ph ph-clipboard-text"></i>Mis Datos Clínicos</button></a>
                    <a href='view/numeroEmergenciaView.php'><button><i class="ph ph-phone-call"></i>Mis Números de Emergencia</button></a>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
</body>
</html>