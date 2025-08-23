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
<div class="header">
    <h1>Gimnasio</h1>
    <a href="action/logoutAction.php">
        <button class="logout">Cerrar Sesión</button>
    </a>
</div>

<div class="welcome">
    <p>Bienvenido, <strong><?php echo htmlspecialchars($nombreUsuario); ?></strong></p>
    <p>Tipo de usuario: <span
                class="user-type"><?php
                    if ($tipoUsuario == 'admin') {
                        echo 'Administrador';
                    } else if ($tipoUsuario == 'instructor') {
                        echo 'Instructor';
                    } else {
                        echo 'Cliente';
                    }
                ?></span></p>
</div>

<h2>Módulos</h2>

<div class="button-container">
    <?php
    echo "<a href='view/cuerpoZonaView.php'><button>Ir a Zonas del Cuerpo</button></a>";
    echo "<a href='view/datoClinicoView.php'><button>Datos Clínicos</button></a>";
    echo "<a href='view/instructorView.php'><button>Ir a Instructores</button></a>";
    echo "<a href='view/clienteView.php'><button>Ir a Clientes</button></a>";
    echo "<a href='view/certificadoView.php'><button>Ir a certificados</button></a>";
    echo "<a href='view/numeroEmergenciaView.php'><button>Ir a numeros de emergencia</button></a>";
    ?>
</div>
</body>
</html>