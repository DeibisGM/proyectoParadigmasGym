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
    <header class="main-header">
        <div>
            <h2>Gimnasio</h2>
            <p>Bienvenido,&nbsp;<strong><?php echo htmlspecialchars($nombreUsuario); ?></strong>&nbsp
            (<?php echo htmlspecialchars(ucfirst($tipoUsuario)); ?>)</p>
        </div>
        <a href="action/logoutAction.php">
            <button><i class="ph ph-sign-out"></i>Cerrar Sesión</button>
        </a>
    </header>

    <main>
        <section class="body-overview" aria-labelledby="body-overview-title">
            <div class="body-overview__header">
                <h3 id="body-overview-title"><i class="ph ph-flame"></i>Resumen semanal de entrenamiento</h3>
                <div class="body-overview__toggle" role="group" aria-label="Cambiar vista del cuerpo">
                    <button type="button" class="is-active" data-view-target="front">Frontal</button>
                    <button type="button" data-view-target="back">Posterior</button>
                </div>
            </div>
            <p class="body-overview__description">Identifica rápidamente qué zonas trabajan más tus sesiones recientes.</p>
            <div class="body-overview__content" data-current-view="front">
                <svg class="body-overview__figure body-overview__figure--front" viewBox="0 0 160 420" role="img" aria-labelledby="body-overview-front-title">
                    <title id="body-overview-front-title">Figura frontal del cuerpo humano</title>
                    <rect class="body-overview__zone" data-zone="hombros" data-view="front" tabindex="0" x="55" y="35" width="50" height="30" rx="15" />
                    <rect class="body-overview__zone" data-zone="pecho" data-view="front" tabindex="0" x="45" y="70" width="70" height="55" rx="25" />
                    <rect class="body-overview__zone" data-zone="abdomen" data-view="front" tabindex="0" x="55" y="130" width="50" height="70" rx="20" />
                    <rect class="body-overview__zone" data-zone="brazos" data-view="front" tabindex="0" x="25" y="70" width="18" height="95" rx="12" />
                    <rect class="body-overview__zone" data-zone="brazos" data-view="front" tabindex="0" x="117" y="70" width="18" height="95" rx="12" />
                    <rect class="body-overview__zone" data-zone="piernas" data-view="front" tabindex="0" x="60" y="210" width="18" height="120" rx="12" />
                    <rect class="body-overview__zone" data-zone="piernas" data-view="front" tabindex="0" x="87" y="210" width="18" height="120" rx="12" />
                    <rect class="body-overview__zone" data-zone="piernas" data-view="front" tabindex="0" x="60" y="335" width="18" height="60" rx="10" />
                    <rect class="body-overview__zone" data-zone="piernas" data-view="front" tabindex="0" x="87" y="335" width="18" height="60" rx="10" />
                </svg>
                <svg class="body-overview__figure body-overview__figure--back" viewBox="0 0 160 420" role="img" aria-labelledby="body-overview-back-title">
                    <title id="body-overview-back-title">Figura posterior del cuerpo humano</title>
                    <rect class="body-overview__zone" data-zone="trapecio" data-view="back" tabindex="0" x="55" y="40" width="50" height="40" rx="15" />
                    <rect class="body-overview__zone" data-zone="espalda" data-view="back" tabindex="0" x="50" y="85" width="60" height="80" rx="25" />
                    <rect class="body-overview__zone" data-zone="lumbar" data-view="back" tabindex="0" x="60" y="170" width="40" height="60" rx="20" />
                    <rect class="body-overview__zone" data-zone="gluteos" data-view="back" tabindex="0" x="55" y="235" width="50" height="45" rx="20" />
                    <rect class="body-overview__zone" data-zone="isquiotibiales" data-view="back" tabindex="0" x="60" y="285" width="18" height="100" rx="12" />
                    <rect class="body-overview__zone" data-zone="isquiotibiales" data-view="back" tabindex="0" x="87" y="285" width="18" height="100" rx="12" />
                    <rect class="body-overview__zone" data-zone="gemelos" data-view="back" tabindex="0" x="60" y="390" width="18" height="25" rx="10" />
                    <rect class="body-overview__zone" data-zone="gemelos" data-view="back" tabindex="0" x="87" y="390" width="18" height="25" rx="10" />
                </svg>
                <div class="body-overview__tooltip" role="status" aria-live="polite"></div>
            </div>
            <div class="body-overview__legend">
                <ul class="body-overview__legend-list" data-view="front" aria-label="Intensidad por zona frontal">
                    <li data-zone-label="hombros"><span class="body-overview__legend-zone">Hombros</span><span class="body-overview__legend-value" data-zone-value="hombros">--%</span></li>
                    <li data-zone-label="pecho"><span class="body-overview__legend-zone">Pecho</span><span class="body-overview__legend-value" data-zone-value="pecho">--%</span></li>
                    <li data-zone-label="abdomen"><span class="body-overview__legend-zone">Abdomen</span><span class="body-overview__legend-value" data-zone-value="abdomen">--%</span></li>
                    <li data-zone-label="brazos"><span class="body-overview__legend-zone">Brazos</span><span class="body-overview__legend-value" data-zone-value="brazos">--%</span></li>
                    <li data-zone-label="piernas"><span class="body-overview__legend-zone">Piernas</span><span class="body-overview__legend-value" data-zone-value="piernas">--%</span></li>
                </ul>
                <ul class="body-overview__legend-list" data-view="back" aria-label="Intensidad por zona posterior" hidden>
                    <li data-zone-label="trapecio"><span class="body-overview__legend-zone">Trapecio</span><span class="body-overview__legend-value" data-zone-value="trapecio">--%</span></li>
                    <li data-zone-label="espalda"><span class="body-overview__legend-zone">Espalda alta</span><span class="body-overview__legend-value" data-zone-value="espalda">--%</span></li>
                    <li data-zone-label="lumbar"><span class="body-overview__legend-zone">Zona lumbar</span><span class="body-overview__legend-value" data-zone-value="lumbar">--%</span></li>
                    <li data-zone-label="gluteos"><span class="body-overview__legend-zone">Glúteos</span><span class="body-overview__legend-value" data-zone-value="gluteos">--%</span></li>
                    <li data-zone-label="isquiotibiales"><span class="body-overview__legend-zone">Isquiotibiales</span><span class="body-overview__legend-value" data-zone-value="isquiotibiales">--%</span></li>
                    <li data-zone-label="gemelos"><span class="body-overview__legend-zone">Gemelos</span><span class="body-overview__legend-value" data-zone-value="gemelos">--%</span></li>
                </ul>
            </div>
        </section>
        <?php if ($tipoUsuario == 'admin'): ?>
            <section>
                <h3><i class="ph ph-squares-four"></i>Gestión General</h3>
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
                    <!-- NUEVO: Horarios de Instructores para Admin -->
                    <a href='view/instructorHorarioView.php'><button><i class="ph ph-calendar"></i>Horarios Instructores</button></a>
                    <a href='view/ejercicioFuerzaView.php'><button><i class="ph ph-barbell"></i>Ejercicios de fuerza</button></a>
                </div>
            </section>
            <section>
                <h3><i class="ph ph-calendar-blank"></i>Reservas y Eventos</h3>
                <div class="menu-grid">
                    <a href='view/reservaView.php'><button><i class="ph ph-calendar-check"></i>Ver Reservas</button></a>
                    <a href='view/eventoGestionView.php'><button><i class="ph ph-calendar-plus"></i>Gestionar Eventos</button></a>
                    <a href='view/salaReservasView.php'><button><i class="ph ph-presentation-chart"></i>Ocupación de Salas</button></a>
                    <a href='view/horarioLibreView.php'><button><i class="ph ph-clock-afternoon"></i>Gestionar Horario Libre</button></a>
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
                    <!-- NUEVO: Horarios de Instructores para Instructor -->
                    <a href='view/instructorHorarioView.php'><button><i class="ph ph-calendar"></i>Mis Horarios de Trabajo</button></a>
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
                    <!-- NUEVO: Horarios de Instructores para Cliente -->
                    <a href='view/instructorHorarioView.php'><button><i class="ph ph-calendar"></i>Horarios de Instructores</button></a>
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
<script src="view/js/bodyOverview.js"></script>
</body>
</html>