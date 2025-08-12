<?php
session_start();

// Si ya hay una sesión iniciada, redirigir a la página principal
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
</head>
<body>
<h1>Iniciar Sesión - Gym</h1>

<form action="../action/loginAction.php" method="post">
    <div class="form-group">
        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo" required>
    </div>

    <div class="form-group">
        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>
    </div>

    <div class="form-group">
        <input type="submit" name="login" value="Iniciar Sesión">
    </div>
</form>

<div class="nota-credenciales"
     style="margin-top:1rem; padding:.75rem; background:#fffbe6; border:1px solid #ffe58f; border-radius:4px; font-size:.95rem;">
    <strong>Nota para ver si funciona:</strong><br>
    • Cliente: correo cliente@gmail.com — contraseña 12345678<br>
    • Admin: correo admin@gmail.com — contraseña 12345678
</div>

<?php
// Mostrar mensajes de error si existen
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    echo '<div class="error-message">';

    switch ($error) {
        case 'empty_fields':
            echo 'Error: Por favor complete todos los campos.';
            break;
        case 'invalid_credentials':
            echo 'Error: Correo electrónico o contraseña incorrectos.';
            break;
        case 'not_found':
            echo 'Error: Usuario no encontrado.';
            break;
        default:
            echo 'Error: Ha ocurrido un error al iniciar sesión.';
    }

    echo '</div>';
}
?>
</body>
</html>