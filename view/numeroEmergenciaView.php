<?php
session_start();
include_once '../business/clienteBusiness.php';
include_once '../business/numeroEmergenciaBusiness.php';
include_once '../utility/Validation.php';

Validation::start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: loginView.php");
    exit();
}

$usuarioId = $_SESSION['usuario_id'];
$tipoUsuario = $_SESSION['tipo_usuario'];

$clienteBusiness = new ClienteBusiness();
$numeroEmergenciaBusiness = new numeroEmergenciaBusiness();

if ($tipoUsuario == 'cliente') {
    $cliente = $clienteBusiness->getClientePorId($usuarioId);
    $numeros = $numeroEmergenciaBusiness->getAllTBNumeroEmergenciaByClienteId($usuarioId);
} else {
    $clientes = $clienteBusiness->getAllTBCliente();
    $numeros = $numeroEmergenciaBusiness->getAllTBNumeroEmergencia();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Números de Emergencia</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2><i class="ph ph-phone-plus"></i> Gestión de Números de Emergencia</h2>
        </header>

        <main>
            <?php
            $generalError = Validation::getError('general');
            if ($generalError) {
                echo '<p class="error-message flash-msg"><b>Error: ' . htmlspecialchars($generalError) . '</b></p>';
            } else if (isset($_GET['error'])) {
                $error = $_GET['error'];
                echo '<p class="error-message flash-msg"><b>Error: ';
                if ($error == "datos_faltantes")
                    echo 'Datos incompletos.';
                else if ($error == "insertar")
                    echo 'No se pudo insertar el número.';
                else if ($error == "actualizar")
                    echo 'No se pudo actualizar el número.';
                else if ($error == "eliminar")
                    echo 'No se pudo eliminar el número.';
                else
                    echo 'Acción no válida.';
                echo '</b></p>';
            } else if (isset($_GET['success'])) {
                $success = $_GET['success'];
                echo '<p class="success-message flash-msg"><b>Éxito: ';
                if ($success == "insertado")
                    echo 'Número insertado correctamente.';
                else if ($success == "actualizado")
                    echo 'Número actualizado correctamente.';
                else if ($success == "eliminado")
                    echo 'Número eliminado correctamente.';
                echo '</b></p>';
            }
            ?>

            <section>
                <h3><i class="ph ph-plus-circle"></i>Agregar Contacto</h3>
                <form method="post" action="../action/numeroEmergenciaAction.php">
                    <div class="form-grid-container">
                        <?php if ($tipoUsuario !== 'cliente'): ?>
                            <div class="form-group">
                                <label for="clienteId">Cliente:</label>
                                <?php if ($error = Validation::getError('clienteId')): ?><span class="error-message">
                                        <?= $error ?>
                                    </span><?php endif; ?>
                                <select id="clienteId" name="clienteId">
                                    <option value="">Seleccione un cliente</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <option value="<?= $c->getId() ?>"
                                            <?= Validation::getOldInput('clienteId') == $c->getId() ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c->getNombre() . ' - ' . $c->getCarnet()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="clienteId" value="<?= $usuarioId ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="nombre">Nombre Contacto:</label>
                            <?php if ($error = Validation::getError('nombre')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <input type="text" id="nombre" name="nombre" maxlength="50" placeholder="Nombre del Contacto"
                                value="<?= Validation::getOldInput('nombre') ?>">
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <?php if ($error = Validation::getError('telefono')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <input type="text" id="telefono" name="telefono" maxlength="8" placeholder="Teléfono"
                                value="<?= Validation::getOldInput('telefono') ?>">
                        </div>
                        <div class="form-group">
                            <label for="relacion">Relación:</label>
                            <?php if ($error = Validation::getError('relacion')): ?><span class="error-message">
                                    <?= $error ?>
                                </span><?php endif; ?>
                            <input type="text" id="relacion" name="relacion" maxlength="30" placeholder="Relación"
                                value="<?= Validation::getOldInput('relacion') ?>">
                        </div>
                    </div>
                    <button type="submit" name="insertar"><i class="ph ph-plus"></i>Agregar</button>
                </form>
            </section>

            <section>
                <h3><i class="ph ph-list-bullets"></i>Contactos Registrados</h3>
                <div class="table-wrapper">
                    <table class="table-clients">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Nombre Contacto</th>
                                <th>Teléfono</th>
                                <th>Relación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($numeros as $n): ?>
                                <tr>
                                    <form id="form-<?= $n->getId() ?>" method="post"
                                        action="../action/numeroEmergenciaAction.php"></form>
                                    <input type="hidden" name="id" value="<?= $n->getId() ?>"
                                        form="form-<?= $n->getId() ?>">
                                    <input type="hidden" name="clienteId" value="<?= $n->getClienteId() ?>"
                                        form="form-<?= $n->getId() ?>">

                                    <td data-label="Cliente">
                                        <?php
                                        if ($tipoUsuario === 'cliente') {
                                            echo htmlspecialchars($cliente->getNombre());
                                        } else {
                                            foreach ($clientes as $c) {
                                                if ($c->getId() == $n->getClienteId()) {
                                                    echo htmlspecialchars($c->getNombre() . ' - ' . $c->getCarnet());
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td data-label="Nombre Contacto">
                                        <input type="text" name="nombre" maxlength="50"
                                            value="<?= htmlspecialchars(Validation::getOldInput('nombre_' . $n->getId(), $n->getNombre())) ?>"
                                            form="form-<?= $n->getId() ?>">
                                        <?php if ($error = Validation::getError('nombre_' . $n->getId())): ?><span
                                            class="error-message">
                                                <?= $error ?>
                                            </span><?php endif; ?>
                                    </td>
                                    <td data-label="Teléfono">
                                        <input type="text" name="telefono" maxlength="8"
                                            value="<?= htmlspecialchars(Validation::getOldInput('telefono_' . $n->getId(), $n->getTelefono())) ?>"
                                            form="form-<?= $n->getId() ?>">
                                        <?php if ($error = Validation::getError('telefono_' . $n->getId())): ?><span
                                            class="error-message">
                                                <?= $error ?>
                                            </span><?php endif; ?>
                                    </td>
                                    <td data-label="Relación">
                                        <input type="text" name="relacion" maxlength="30"
                                            value="<?= htmlspecialchars(Validation::getOldInput('relacion_' . $n->getId(), $n->getRelacion())) ?>"
                                            form="form-<?= $n->getId() ?>">
                                        <?php if ($error = Validation::getError('relacion_' . $n->getId())): ?><span
                                            class="error-message">
                                                <?= $error ?>
                                            </span><?php endif; ?>
                                    </td>
                                    <td data-label="Acciones">
                                        <div class="actions">
                                            <button type="submit" name="actualizar" class="btn-row" title="Actualizar"
                                                form="form-<?= $n->getId() ?>"><i
                                                    class="ph ph-pencil-simple"></i></button>
                                            <button type="submit" name="eliminar" class="btn-row btn-danger"
                                                onclick="return confirm('¿Seguro que desea eliminar?');"
                                                title="Eliminar" form="form-<?= $n->getId() ?>"><i
                                                    class="ph ph-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <?php Validation::clear(); ?>
</body>

</html>