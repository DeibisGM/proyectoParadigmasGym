<?php
session_start();
include_once '../business/ejercicioEquilibrioBusiness.php';
include_once '../utility/Validation.php';

Validation::start();

$ejercicioBusiness = new EjercicioEquilibrioBusiness();
$ejercicios = $ejercicioBusiness->obtenerTbejercicioequilibrio();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ejercicios de Equilibrio/Coordinación</title>
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
        table textarea,
        table select {
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
                <i class="ph ph-balance"></i>
                Gestión de Ejercicios de Equilibrio/Coordinación
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
                <h3><i class="ph ph-plus-circle"></i> Registrar Ejercicio de Equilibrio/Coordinación</h3>
                <form name="ejercicioForm" method="post" action="../action/ejercicioEquilibrioAction.php">
                    <div class="form-group">
                        <label>Nombre del Ejercicio:</label>
                        <span class="error-message"><?= Validation::getError('nombre') ?></span>
                        <input type="text" name="nombre" placeholder="Ej: Postura del árbol"
                               value="<?= htmlspecialchars(Validation::getOldInput('nombre')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Descripción:</label>
                        <span class="error-message"><?= Validation::getError('descripcion') ?></span>
                        <textarea name="descripcion" placeholder="Descripción del ejercicio y cómo realizarlo" rows="3"><?= htmlspecialchars(Validation::getOldInput('descripcion')) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Dificultad:</label>
                        <span class="error-message"><?= Validation::getError('dificultad') ?></span>
                        <select name="dificultad">
                            <option value="">Seleccione la dificultad</option>
                            <option value="Principiante" <?= Validation::getOldInput('dificultad') == 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                            <option value="Intermedio" <?= Validation::getOldInput('dificultad') == 'Intermedio' ? 'selected' : '' ?>>Intermedio</option>
                            <option value="Avanzado" <?= Validation::getOldInput('dificultad') == 'Avanzado' ? 'selected' : '' ?>>Avanzado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Duración (segundos):</label>
                        <span class="error-message"><?= Validation::getError('duracion') ?></span>
                        <input type="number" name="duracion" placeholder="Ej: 30" min="1"
                               value="<?= htmlspecialchars(Validation::getOldInput('duracion')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Materiales (opcional):</label>
                        <input type="text" name="materiales" placeholder="Ej: Bosu, colchoneta, silla"
                               value="<?= htmlspecialchars(Validation::getOldInput('materiales')) ?>"/>
                    </div>

                    <div class="form-group">
                        <label>Postura/Posición:</label>
                        <span class="error-message"><?= Validation::getError('postura') ?></span>
                        <select name="postura">
                            <option value="">Seleccione la postura</option>
                            <option value="De pie" <?= Validation::getOldInput('postura') == 'De pie' ? 'selected' : '' ?>>De pie</option>
                            <option value="Sentado" <?= Validation::getOldInput('postura') == 'Sentado' ? 'selected' : '' ?>>Sentado</option>
                            <option value="En el suelo" <?= Validation::getOldInput('postura') == 'En el suelo' ? 'selected' : '' ?>>En el suelo</option>
                            <option value="En movimiento" <?= Validation::getOldInput('postura') == 'En movimiento' ? 'selected' : '' ?>>En movimiento</option>
                        </select>
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
                                <th>Dificultad</th>
                                <th>Duración (seg)</th>
                                <th>Materiales</th>
                                <th>Postura</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ejercicios as $ejercicio): ?>
                            <tr>
                                <form method="post" action="../action/ejercicioEquilibrioAction.php">
                                    <input type="hidden" name="id" value="<?php echo $ejercicio->getTbejercicioequilibrioid(); ?>">

                                    <td>
                                        <input type="text" name="nombre"
                                               value="<?php echo htmlspecialchars(Validation::getOldInput('nombre_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibrionombre())); ?>">
                                        <span class="error-message"><?= Validation::getError('nombre_'.$ejercicio->getTbejercicioequilibrioid()) ?></span>
                                    </td>

                                    <td>
                                        <textarea name="descripcion" rows="2"><?php echo htmlspecialchars(Validation::getOldInput('descripcion_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibriodescripcion())); ?></textarea>
                                        <span class="error-message"><?= Validation::getError('descripcion_'.$ejercicio->getTbejercicioequilibrioid()) ?></span>
                                    </td>

                                    <td>
                                        <select name="dificultad">
                                            <option value="Principiante" <?= Validation::getOldInput('dificultad_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibriodificultad()) == 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                                            <option value="Intermedio" <?= Validation::getOldInput('dificultad_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibriodificultad()) == 'Intermedio' ? 'selected' : '' ?>>Intermedio</option>
                                            <option value="Avanzado" <?= Validation::getOldInput('dificultad_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibriodificultad()) == 'Avanzado' ? 'selected' : '' ?>>Avanzado</option>
                                        </select>
                                        <span class="error-message"><?= Validation::getError('dificultad_'.$ejercicio->getTbejercicioequilibrioid()) ?></span>
                                    </td>

                                    <td>
                                        <input type="number" name="duracion" min="1"
                                               value="<?php echo htmlspecialchars(Validation::getOldInput('duracion_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibrioduracion())); ?>">
                                        <span class="error-message"><?= Validation::getError('duracion_'.$ejercicio->getTbejercicioequilibrioid()) ?></span>
                                    </td>

                                    <td>
                                        <input type="text" name="materiales"
                                               value="<?php echo htmlspecialchars(Validation::getOldInput('materiales_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibriomateriales())); ?>">
                                    </td>

                                    <td>
                                        <select name="postura">
                                            <option value="De pie" <?= Validation::getOldInput('postura_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibriopostura()) == 'De pie' ? 'selected' : '' ?>>De pie</option>
                                            <option value="Sentado" <?= Validation::getOldInput('postura_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibriopostura()) == 'Sentado' ? 'selected' : '' ?>>Sentado</option>
                                            <option value="En el suelo" <?= Validation::getOldInput('postura_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibriopostura()) == 'En el suelo' ? 'selected' : '' ?>>En el suelo</option>
                                            <option value="En movimiento" <?= Validation::getOldInput('postura_'.$ejercicio->getTbejercicioequilibrioid(), $ejercicio->getTbejercicioequilibriopostura()) == 'En movimiento' ? 'selected' : '' ?>>En movimiento</option>
                                        </select>
                                        <span class="error-message"><?= Validation::getError('postura_'.$ejercicio->getTbejercicioequilibrioid()) ?></span>
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