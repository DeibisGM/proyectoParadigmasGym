<?php
session_start();
include_once '../business/ejercicioFuerzaBusiness.php';
include_once '../utility/Validation.php';

Validation::start();

$ejercicioBusiness = new EjercicioFuerzaBusiness();
$ejercicios = $ejercicioBusiness->obtenerTbejerciciofuerza();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ejercicios de Fuerza</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            display: block;
            margin-top: 0.25rem;
        }
        .success-message {
            color: #28a745;
            font-size: 1rem;
        }
        .flash-msg {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            border: 1px solid transparent;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .success-message.flash-msg {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .error-message.flash-msg {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .actions-cell {
            white-space: nowrap;
        }
        .actions-cell button {
            margin: 0.25rem;
        }
        table input[type="text"],
        table input[type="number"],
        table textarea {
            width: 100%;
            box-sizing: border-box;
        }
        table td {
            vertical-align: top;
            padding: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button">
                <i class="ph ph-arrow-left"></i>
            </a>
            <h2>
                <i class="ph ph-barbell"></i>
                Gestión de Ejercicios de Fuerza
            </h2>
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
                else if ($error == "insertar") echo 'No se pudo insertar el ejercicio.';
                else if ($error == "actualizar") echo 'No se pudo actualizar el ejercicio.';
                else if ($error == "eliminar") echo 'No se pudo eliminar el ejercicio.';
                else if ($error == "id_faltante") echo 'ID no proporcionado.';
                else if ($error == "accion_no_valida") echo 'Acción no válida.';
                else echo 'Error desconocido.';
                echo '</b></p>';
            } else if (isset($_GET['success'])) {
                $success = $_GET['success'];
                echo '<p class="success-message flash-msg"><b>Éxito: ';
                if ($success == "insertado") echo 'Ejercicio insertado correctamente.';
                else if ($success == "actualizado") echo 'Ejercicio actualizado correctamente.';
                else if ($success == "eliminado") echo 'Ejercicio eliminado correctamente.';
                echo '</b></p>';
            }
            ?>

            <!-- Formulario de inserción -->
            <section>
                <h3><i class="ph ph-plus-circle"></i> Registrar Ejercicio</h3>
                <form name="ejercicioForm" method="post" action="../action/ejercicioFuerzaAction.php">
                    <div class="form-group">
                        <label>Nombre del Ejercicio:</label>
                        <span class="error-message"><?= Validation::getError('nombre') ?></span>
                        <input type="text" name="nombre" placeholder="Ej: Press de banca"
                               value="<?= htmlspecialchars(Validation::getOldInput('nombre')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Descripción:</label>
                        <span class="error-message"><?= Validation::getError('descripcion') ?></span>
                        <textarea name="descripcion" placeholder="Descripción del ejercicio" rows="3"><?= htmlspecialchars(Validation::getOldInput('descripcion')) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Repeticiones:</label>
                        <span class="error-message"><?= Validation::getError('repeticion') ?></span>
                        <input type="number" name="repeticion" placeholder="Ej: 10" min="1"
                               value="<?= htmlspecialchars(Validation::getOldInput('repeticion')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Series:</label>
                        <span class="error-message"><?= Validation::getError('serie') ?></span>
                        <input type="number" name="serie" placeholder="Ej: 3" min="1"
                               value="<?= htmlspecialchars(Validation::getOldInput('serie')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Peso (kg):</label>
                        <span class="error-message"><?= Validation::getError('peso') ?></span>
                        <input type="number" step="0.01" name="peso" placeholder="Ej: 50.5" min="0"
                               value="<?= htmlspecialchars(Validation::getOldInput('peso')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Descanso (segundos):</label>
                        <span class="error-message"><?= Validation::getError('descanso') ?></span>
                        <input type="number" name="descanso" placeholder="Ej: 60" min="0"
                               value="<?= htmlspecialchars(Validation::getOldInput('descanso')) ?>"/>
                    </div>

                    <button type="submit" name="insertar">
                        <i class="ph ph-plus"></i> Registrar Ejercicio
                    </button>
                </form>
            </section>

            <!-- Tabla de ejercicios -->
            <section>
                <h3><i class="ph ph-list-bullets"></i> Ejercicios Registrados</h3>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Repeticiones</th>
                                <th>Series</th>
                                <th>Peso (kg)</th>
                                <th>Descanso (seg)</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ejercicios as $ejercicio): ?>
                            <tr>
                                <form method="post" action="../action/ejercicioFuerzaAction.php">
                                    <input type="hidden" name="id" value="<?php echo $ejercicio->getTbejerciciofuerzaid(); ?>">

                                    <td>
                                        <input type="text" name="nombre"
                                               value="<?php echo htmlspecialchars(Validation::getOldInput('nombre_'.$ejercicio->getTbejerciciofuerzaid(), $ejercicio->getTbejerciciofuerzanombre())); ?>">
                                        <span class="error-message"><?= Validation::getError('nombre_'.$ejercicio->getTbejerciciofuerzaid()) ?></span>
                                    </td>

                                    <td>
                                        <textarea name="descripcion" rows="2"><?php echo htmlspecialchars(Validation::getOldInput('descripcion_'.$ejercicio->getTbejerciciofuerzaid(), $ejercicio->getTbejerciciofuerzadescripcion())); ?></textarea>
                                        <span class="error-message"><?= Validation::getError('descripcion_'.$ejercicio->getTbejerciciofuerzaid()) ?></span>
                                    </td>

                                    <td>
                                        <input type="number" name="repeticion" min="1"
                                               value="<?php echo htmlspecialchars(Validation::getOldInput('repeticion_'.$ejercicio->getTbejerciciofuerzaid(), $ejercicio->getTbejerciciofuerzarepeticion())); ?>">
                                        <span class="error-message"><?= Validation::getError('repeticion_'.$ejercicio->getTbejerciciofuerzaid()) ?></span>
                                    </td>

                                    <td>
                                        <input type="number" name="serie" min="1"
                                               value="<?php echo htmlspecialchars(Validation::getOldInput('serie_'.$ejercicio->getTbejerciciofuerzaid(), $ejercicio->getTbejerciciofuerzaserie())); ?>">
                                        <span class="error-message"><?= Validation::getError('serie_'.$ejercicio->getTbejerciciofuerzaid()) ?></span>
                                    </td>

                                    <td>
                                        <input type="number" step="0.01" name="peso" min="0"
                                               value="<?php echo htmlspecialchars(Validation::getOldInput('peso_'.$ejercicio->getTbejerciciofuerzaid(), $ejercicio->getTbejerciciofuerzapeso())); ?>">
                                        <span class="error-message"><?= Validation::getError('peso_'.$ejercicio->getTbejerciciofuerzaid()) ?></span>
                                    </td>

                                    <td>
                                        <input type="number" name="descanso" min="0"
                                               value="<?php echo htmlspecialchars(Validation::getOldInput('descanso_'.$ejercicio->getTbejerciciofuerzaid(), $ejercicio->getTbejerciciofuerzadescanso())); ?>">
                                        <span class="error-message"><?= Validation::getError('descanso_'.$ejercicio->getTbejerciciofuerzaid()) ?></span>
                                    </td>

                                    <td class="actions-cell">
                                        <button type="submit" name="actualizar" title="Actualizar"
                                                onclick="return confirm('¿Estás seguro de actualizar este ejercicio?');">
                                            <i class="ph ph-pencil-simple"></i> Actualizar
                                        </button>
                                        <button type="submit" name="eliminar" title="Eliminar"
                                                onclick="return confirm('¿Estás seguro de eliminar este ejercicio?');">
                                            <i class="ph ph-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </form>
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