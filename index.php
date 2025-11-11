<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: view/loginView.php");
    exit();
}

$tipoUsuario = $_SESSION['tipo_usuario'];
$nombreUsuario = $_SESSION['usuario_nombre'];
$usuarioId = $_SESSION['usuario_id']; // Necesitamos el ID para la lógica de progreso

$progresoData = [];
if ($tipoUsuario == 'cliente') {
    include_once 'business/progresoBusiness.php';
    $progresoBusiness = new ProgresoBusiness();
    $progresoData = $progresoBusiness->getProgresoPorPeriodos($usuarioId);
}

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
    <header class="main-header">
        <div>
            <h2>Gimnasio</h2>
            <p>Bienvenido,&nbsp;<strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>&nbsp
                (<?php echo htmlspecialchars(ucfirst($tipoUsuario)); ?>)</p>
        </div>
        <a href="action/logoutAction.php">
            <button class="btn-logout-subtle"><i class="ph-fill ph-sign-out"></i>Cerrar Sesión</button>
        </a>
    </header>

    <main>
        <?php if ($tipoUsuario == 'admin'): ?>
            <section class="menu-section">
                <div class="menu-grid">
                    <a href='view/instructorView.php'><button><i class="ph-fill ph-users-three"></i>Instructores</button></a>
                    <a href='view/clienteView.php'><button><i class="ph-fill ph-users"></i>Clientes</button></a>
                    <a href='view/cuerpoZonaView.php'><button><i class="ph-fill ph-person-simple-run"></i>Zonas del Cuerpo</button></a>
                    <a href='view/horarioView.php'><button><i class="ph-fill ph-clock-clockwise"></i>Horario General</button></a>
                    <a href='view/padecimientoView.php'><button><i class="ph-fill ph-bandaids"></i>Padecimientos</button></a>
                    <a href='view/certificadoView.php'><button><i class="ph-fill ph-certificate"></i>Certificados</button></a>
                    <a href='view/clientePadecimientoView.php'><button><i class="ph-fill ph-first-aid-kit"></i>Datos Clínicos</button></a>
                    <a href='view/seguimientoClientesView.php'><button><i class="ph-fill ph-graph"></i>Seguimiento de clientes</button></a>
                    <a href='view/padecimientoDictamenView.php'><button><i class="ph-fill ph-file-text"></i> Dictamen Médico</button></a>
                    <a href='view/numeroEmergenciaView.php'><button><i class="ph-fill ph-phone-plus"></i>Números Emergencia</button></a>
                    <a href='view/salaView.php'><button><i class="ph-fill ph-storefront"></i>Salas</button></a>
                    <a href='view/subzonaView.php'><button><i class="ph-fill ph-hand"></i>Sub zonas del cuerpo</button></a>
                    <a href='view/instructorHorarioView.php'><button><i class="ph-fill ph-calendar"></i>Horarios Instructores</button></a>
                </div>
            </section>
            <section class="menu-section">
                <h3><i class="ph-fill ph-barbell"></i>Ejercicios</h3>
                <div class="menu-grid">
                    <a href='view/ejercicioFuerzaView.php'><button><i class="ph-fill ph-barbell"></i>Ejercicios de fuerza</button></a>
                    <!-- NUEVO: Ejercicios de Equilibrio/Coordinación -->
                    <a href='view/ejercicioEquilibrioView.php'><button><i class="ph-fill ph-scales"></i>Ejercicios de Equilibrio</button></a>
                    <a href='view/ejercicioResistenciaView.php'><button><i class="ph-fill ph-barbell"></i>Ejercicios de resistencia</button></a>
                    <a href='view/ejercicioFlexibilidadView.php'><button><i class="ph-fill ph-barbell"></i>Ejercicios de Flexibilidad</button></a>
                </div>
            </section>
            <section class="menu-section">
                <h3><i class="ph-fill ph-calendar-blank"></i>Reservas y Eventos</h3>
                <div class="menu-grid">
                    <a href='view/reservaView.php'><button><i class="ph-fill ph-calendar-check"></i>Ver Reservas</button></a>
                    <a href='view/eventoGestionView.php'><button><i class="ph-fill ph-calendar-plus"></i>Gestionar Eventos</button></a>
                    <a href='view/salaReservasView.php'><button><i class="ph-fill ph-presentation-chart"></i>Ocupación de Salas</button></a>
                    <a href='view/horarioLibreView.php'><button><i class="ph-fill ph-clock-afternoon"></i>Gestionar Horario Libre</button></a>
                    <a href='view/horarioPersonalView.php'><button><i class="ph-fill ph-user-plus"></i>Instructor Personal</button></a>
                </div>
            </section>

        <?php elseif ($tipoUsuario == 'instructor'): ?>
            <section class="menu-section">
                <h3><i class="ph-fill ph-chalkboard-teacher"></i>Panel de Instructor</h3>
                <div class="menu-grid">
                    <a href='view/reservaView.php'><button><i class="ph-fill ph-calendar-check"></i>Ver Reservas</button></a>
                    <a href='view/eventoGestionView.php'><button><i class="ph-fill ph-calendar-plus"></i>Gestionar Mis Eventos</button></a>
                    <a href='view/horarioPersonalView.php'><button><i class="ph-fill ph-user-plus"></i>Mis Horarios Personales</button></a>
                    <a href='view/instructorHorarioView.php'><button><i class="ph-fill ph-calendar"></i>Mis Horarios de Trabajo</button></a>
                    <a href='view/clienteView.php'><button><i class="ph-fill ph-users"></i>Ver Clientes</button></a>
                    <a href='view/seguimientoClientesView.php'><button><i class="ph-fill ph-graph"></i>Seguimiento de clientes</button></a>
                    <a href='view/instructorView.php'><button><i class="ph-fill ph-user-rectangle"></i>Ver Instructores</button></a>
                    <a href='view/cuerpoZonaView.php'><button><i class="ph-fill ph-person-simple-run"></i>Zonas del Cuerpo</button></a>
                    <a href='view/subzonaView.php'><button><i class="ph-fill ph-hand"></i>Sub Zonas del cuerpo</button></a>
                    <a href='view/salaView.php'><button><i class="ph-fill ph-storefront"></i>Ver Salas</button></a>
                </div>
            </section>
            <section class="menu-section">
                <h3><i class="ph-fill ph-barbell"></i>Ejercicios</h3>
                <div class="menu-grid">
                    <a href='view/ejercicioFuerzaView.php'><button><i class="ph-fill ph-barbell"></i>Ejercicios de fuerza</button></a>
                    <!-- NUEVO: Ejercicios de Equilibrio/Coordinación -->
                    <a href='view/ejercicioEquilibrioView.php'><button><i class="ph-fill ph-scales"></i>Ejercicios de Equilibrio</button></a>
                    <a href='view/ejercicioResistenciaView.php'><button><i class="ph-fill ph-barbell"></i>Ejercicios de resistencia</button></a>
                    <a href='view/ejercicioFlexibilidadView.php'><button><i class="ph-fill ph-barbell"></i>Ejercicios de Flexibilidad</button></a>
                </div>
            </section>
            <section class="menu-section">
                <h3><i class="ph-fill ph-heartbeat"></i>Salud de Clientes</h3>
                <div class="menu-grid">
                    <a href='view/padecimientoView.php'><button><i class="ph-fill ph-bandaids"></i>Padecimientos</button></a>
                    <a href='view/clientePadecimientoView.php'><button><i class="ph-fill ph-first-aid-kit"></i>Datos Clínicos</button></a>
                    <a href='view/padecimientoDictamenView.php'><button><i class="ph-fill ph-file-text"></i> Dictamen Médico</button></a>
                    <a href='view/numeroEmergenciaView.php'><button><i class="ph-fill ph-phone-plus"></i>Contactos Emergencia</button></a>
                </div>
            </section>

        <?php elseif ($tipoUsuario == 'cliente'): ?>
            <section class="menu-section">
                <h3><i class="ph-fill ph-barbell"></i>Actividad en el Gimnasio</h3>
                <div class="menu-grid">
                    <a href='view/reservaView.php'><button><i class="ph-fill ph-calendar-check"></i>Mis Reservas</button></a>
                    <a href='view/rutinaView.php'><button><i class="ph-fill ph-notebook"></i>Mis Rutinas</button></a>
                    <a href='view/progresoComparativaView.php'><button><i class="ph-fill ph-chart-line-up"></i>Comparar progreso</button></a>
                    <a href='view/horarioPersonalView.php'><button><i class="ph-fill ph-user-plus"></i>Instructor Personal</button></a>
                    <a href='view/horarioLibreView.php'><button><i class="ph-fill ph-barbell"></i>Uso Libre</button></a>

                    <a href='view/instructorHorarioView.php'><button><i class="ph-fill ph-calendar"></i>Horarios de Instructores</button></a>
                    <a href='view/instructorClienteView.php'><button><i class="ph-fill ph-chalkboard-teacher"></i>Ver Instructores</button></a>
                    <a href='view/cuerpoZonaView.php'><button><i class="ph-fill ph-person-simple-run"></i>Zonas del Cuerpo</button></a>
                    <a href='view/subzonaView.php'><button><i class="ph-fill ph-hand"></i>Sub Zonas del cuerpo</button></a>
                    <a href='view/salaView.php'><button><i class="ph-fill ph-storefront"></i>Ver Salas</button></a>
                </div>
            </section>
            <section class="menu-section">
                <h3><i class="ph-fill ph-user-focus"></i>Mi Perfil y Salud</h3>
                <div class="menu-grid">
                    <a href='view/clienteView.php'><button><i class="ph-fill ph-user-rectangle"></i>Ver mi Perfil</button></a>
                    <a href='view/clientePadecimientoView.php'><button><i class="ph-fill ph-clipboard-text"></i>Mis Datos Clínicos</button></a>
                    <a href='view/numeroEmergenciaView.php'><button><i class="ph-fill ph-phone-call"></i>Mis Números de Emergencia</button></a>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <script>
        window.progresoData = <?php echo json_encode($progresoData); ?>;
    </script>

    <?php if ($tipoUsuario === 'cliente') {
        include 'view/body_viewer.php';
    } ?>

</div>
</body>
</html>