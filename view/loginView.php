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
            <span class="brand-badge"><i class="ph-fill ph-lightning"></i>NovaGym Control</span>
            <h1>Impulsa tu mejor versión</h1>
            <p>Administra entrenamientos, reservas y seguimiento de clientes desde un panel diseñado para equipos de alto rendimiento.</p>
            <ul class="auth-highlights">
                <li><i class="ph-fill ph-calendar-check"></i>Agenda inteligente de clases y eventos.</li>
                <li><i class="ph-fill ph-activity"></i>Monitoreo de progreso en tiempo real.</li>
                <li><i class="ph-fill ph-shield-check"></i>Datos seguros y siempre disponibles.</li>
            </ul>
        </section>

        <section class="auth-card login-container">
            <div class="auth-card-header">

                <h2>Iniciar Sesión</h2>
                <p>Accede al panel para continuar gestionando el gimnasio.</p>
            </div>

            <form action="../action/loginAction.php" method="post" class="auth-form">
                <div class="form-group">
                    <label for="correo"><i class="ph-fill ph-envelope"></i>Correo electrónico</label>
                    <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
                </div>
                <div class="form-group password-group">
                    <label for="contrasena"><i class="ph-fill ph-key"></i>Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" placeholder="Tu contraseña" required>
                    <i class="ph ph-eye" id="togglePassword"></i>
                </div>
                <div class="form-group">
                    <button type="submit" name="login" class="btn-primary"><i class="ph-fill ph-sign-in"></i>Iniciar Sesión</button>
                </div>
            </form>

            <section class="menu-section">
                <h3>Credenciales de Prueba</h3>
                <div class="nota-credenciales">
                    
                    <ul>
                        <li><span>Cliente:</span> cliente@gmail.com / 12345678</li>
                        <li><span>Instructor:</span> instructor@gmail.com / 12345678</li>
                        <li><span>Admin:</span> admin@gmail.com / admin</li>
                    </ul>
                </div>
            </section>

            <?php if (isset($_GET['error'])) : ?>
                <div class="error-message">
                    <i class="ph-fill ph-warning"></i>Credenciales incorrectas o campos vacíos.
                </div>
            <?php endif; ?>
        </section>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#contrasena');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('ph-eye-slash');
        });
    </script>
</body>
</html>