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
    <link rel="stylesheet" href="../styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="login-container">
    <h1><i class="ph ph-sign-in"></i>Iniciar Sesión</h1>
    <form action="../action/loginAction.php" method="post">
        <div class="form-group">
            <label for="correo"><i class="ph ph-envelope"></i>Correo electrónico:</label>
            <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
        </div>
        <div class="form-group">
            <label for="contrasena"><i class="ph ph-key"></i>Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" placeholder="Tu contraseña" required>
        </div>
        <div class="form-group">
            <input type="submit" name="login" value="Iniciar Sesión">
        </div>
    </form>


    <div class="nota-credenciales"
         style="margin-top:1.5rem; padding:1rem; background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; font-size:0.9rem; line-height:1.5;">
        <strong><i class="ph ph-info"></i>Credenciales de Prueba:</strong><br>
        • <strong>Cliente:</strong> cliente@gmail.com / 12345678<br>
        • <strong>Instructor:</strong> instructor@gmail.com / 12345678<br>
        • <strong>Admin:</strong> root@gmail.com / root
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-message" style="color:red; margin-top:1rem;">
            Error: Credenciales incorrectas o campos vacíos.
        </div>
    <?php endif; ?>
</div>
</body>
</html>