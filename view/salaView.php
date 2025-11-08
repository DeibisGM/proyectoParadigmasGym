<?php
include '../business/salaBusiness.php';
include_once '../utility/Validation.php';
session_start();

Validation::start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    // Si no hay sesión, redirigir al login
    header("Location: loginView.php");
    exit();
}

// Obtener información del usuario
$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];
$nombreUsuario = $_SESSION['usuario_nombre'];

// Inicializar el objeto de negocio
$salaBusiness = new SalaBusiness();

// Obtener todas las salas para mostrar
$salas = $salaBusiness->obtenerTbsala();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Gestión de Salas</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="container">
    <header>
<header>
        <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
        <h2>Gestión de Salas</h2>
    </header>
    </header>

    <main>
        <?php
        // Mostrar errores o mensajes
        $generalError = Validation::getError('general');
        if ($generalError) {
            echo '<p class="error-message flash-msg"><b>Error: '.htmlspecialchars($generalError).'</b></p>';
        } else if (isset($_GET['error'])) {
            $error = $_GET['error'];
            echo '<p class="error-message flash-msg"><b>Error: ';
            if ($error == "datos_faltantes") echo 'Datos incompletos.';
            else if ($error == "insertar") echo 'No se pudo insertar la sala.';
            else if ($error == "actualizar") echo 'No se pudo actualizar la sala.';
            else if ($error == "eliminar") echo 'No se pudo eliminar la sala.';
            else echo 'Acción no válida.';
            echo '</b></p>';
        } else if (isset($_GET['success'])) {
            $success = $_GET['success'];
            echo '<p class="success-message flash-msg"><b>Éxito: ';
            if ($success == "insertado") echo 'Sala insertada correctamente.';
            else if ($success == "actualizado") echo 'Sala actualizada correctamente.';
            else if ($success == "eliminado") echo 'Sala eliminada correctamente.';
            echo '</b></p>';
        }
        ?>

        <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor') { ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i>Registrar Sala</h3>
                <form name="salaForm" method="post" action="../action/salaAction.php">
                    <div class="form-group">
                        <label>Nombre de la Sala:</label>
                        <span class="error-message"><?= Validation::getError('nombre') ?></span>
                        <input type="text" name="nombre" placeholder="Ej: Sala de Yoga"
                               value="<?= Validation::getOldInput('nombre') ?>"/>
                    </div>
                    <div class="form-group">
                        <label>Capacidad:</label>
                        <span class="error-message"><?= Validation::getError('capacidad') ?></span>
                        <input type="number" name="capacidad" min="1" placeholder="Ej: 15"
                               value="<?= Validation::getOldInput('capacidad') ?>"/>
                    </div>
                    <button type="submit" name="insertar"><i class="ph ph-plus"></i>Registrar Sala</button>
                </form>
            </section>
        <?php } ?>


        <section>
            <h3><i class="ph ph-list-bullets"></i>Salas Registradas</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor') echo '<th>Acción</th>'; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($salas as $sala): ?>
                        <tr>
                            <?php if ($tipoUsuario == 'admin' || $tipoUsuario == 'instructor'): ?>
                                <form method="post" action="../action/salaAction.php">
                                    <input type="hidden" name="id" value="<?php echo $sala->getTbsalaid(); ?>">
                                    <td>
                                        <input type="text" name="nombre"
                                               value="<?php echo htmlspecialchars(Validation::getOldInput('nombre_'.$sala->getTbsalaid(), $sala->getTbsalanombre())); ?>">
                                        <span class="error-message"><?= Validation::getError('nombre_'.$sala->getTbsalaid()) ?></span>
                                    </td>
                                    <td>
                                        <input type="number" name="capacidad"
                                               value="<?php echo Validation::getOldInput('capacidad_'.$sala->getTbsalaid(), $sala->getTbsalacapacidad()); ?>" min="1">
                                        <span class="error-message"><?= Validation::getError('capacidad_'.$sala->getTbsalaid()) ?></span>
                                    </td>
                                    <td>
                                        <select name="estado">
                                            <option value="1" <?php echo(Validation::getOldInput('estado_'.$sala->getTbsalaid(), $sala->getTbsalaestado()) == 1 ? 'selected' : ''); ?>>
                                                Activa
                                            </option>
                                            <option value="0" <?php echo(Validation::getOldInput('estado_'.$sala->getTbsalaid(), $sala->getTbsalaestado()) == 0 ? 'selected' : ''); ?>>
                                                Inactiva
                                            </option>
                                        </select>
                                        <span class="error-message"><?= Validation::getError('estado_'.$sala->getTbsalaid()) ?></span>
                                    </td>
                                    <td class="actions-cell">
                                        <button type="submit" name="actualizar" title="Actualizar"
                                                onclick="return confirm('¿Estás seguro de actualizar esta sala?');"><i
                                                    class="ph ph-pencil-simple"></i> Actualizar
                                        </button>
                                        <?php if ($tipoUsuario == 'admin'): ?>
                                            <button type="submit" name="eliminar" title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta sala?');"><i
                                                        class="ph ph-trash"></i> Eliminar
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </form>
                            <?php else: // Vista Cliente ?>
                                <td><?php echo htmlspecialchars($sala->getTbsalanombre()); ?></td>
                                <td><?php echo $sala->getTbsalacapacidad(); ?></td>
                                <td><?php echo($sala->getTbsalaestado() == 1 ? 'Activa' : 'Inactiva'); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
<?php Validation::clear(); ?>
<script>
    // Auto-ocultar mensajes de error y éxito después de 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
        const mensajes = document.querySelectorAll('.error-message.flash-msg, .success-message.flash-msg');
        if (mensajes.length > 0) {
            setTimeout(function() {
                mensajes.forEach(function(mensaje) {
                    mensaje.style.display = 'none';
                });
            }, 5000);
        }
    });
</script>
</body>
</html>