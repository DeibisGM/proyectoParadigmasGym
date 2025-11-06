<?php
session_start();

if (isset($_SESSION['usuario_id']) && isset($_SESSION['tipo_usuario'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Iniciar Sesión - Gym</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="auth-body">
    <div class="auth-layout">
        <section class="auth-intro">
            <span class="brand-badge"><i class="ph ph-lightning"></i>NovaGym Control</span>
            <h1>Impulsa tu mejor versión</h1>
            <p>Administra entrenamientos, reservas y seguimiento de clientes desde un panel diseñado para equipos de alto rendimiento.</p>
            <ul class="auth-highlights">
                <li><i class="ph ph-calendar-check"></i>Agenda inteligente de clases y eventos.</li>
                <li><i class="ph ph-activity"></i>Monitoreo de progreso en tiempo real.</li>
                <li><i class="ph ph-shield-check"></i>Datos seguros y siempre disponibles.</li>
            </ul>
        </section>

        <section class="auth-card login-container">
            <div class="auth-card-header">
                <span class="badge-soft">Bienvenido de vuelta</span>
                <h2>Iniciar Sesión</h2>
                <p>Accede al panel para continuar gestionando el gimnasio.</p>
            </div>

            <form action="../action/loginAction.php" method="post" class="auth-form">
                <div class="form-group">
                    <label for="correo"><i class="ph ph-envelope"></i>Correo electrónico</label>
                    <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
                </div>
                <div class="form-group">
                    <label for="contrasena"><i class="ph ph-key"></i>Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" placeholder="Tu contraseña" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="login" class="btn-primary"><i class="ph ph-sign-in"></i>Iniciar Sesión</button>
                </div>
            </form>

            <div class="nota-credenciales">
                <strong>Credenciales de Prueba</strong>
                <ul>
                    <li><span>Cliente:</span> cliente@gmail.com / 12345678</li>
                    <li><span>Instructor:</span> instructor@gmail.com / 12345678</li>
                    <li><span>Admin:</span> admin@gmail.com / admin</li>
                </ul>
            </div>

            <?php if (isset($_GET['error'])) : ?>
                <div class="error-message">
                    <i class="ph ph-warning"></i>Credenciales incorrectas o campos vacíos.
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
