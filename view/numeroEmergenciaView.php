<?php
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
    <meta charset="UTF-8"/>
    <title>Gestión de Números de Emergencia</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-phone-plus"></i>Números de Emergencia</h2>
    </header>

    <main>
        <?php
        // Mensajes de error o éxito
        $generalError = Validation::getError('general');
        if ($generalError) {
            echo '<p class="error-message"><b>Error: '.htmlspecialchars($generalError).'</b></p>';
        } else if (isset($_GET['error'])) {
            $error = $_GET['error'];
            echo '<p class="error-message"><b>Error: ';
            if ($error == "datos_faltantes") echo 'Datos incompletos.';
            else if ($error == "insertar") echo 'No se pudo insertar el número.';
            else if ($error == "actualizar") echo 'No se pudo actualizar el número.';
            else if ($error == "eliminar") echo 'No se pudo eliminar el número.';
            else echo 'Acción no válida.';
            echo '</b></p>';
        } else if (isset($_GET['success'])) {
            $success = $_GET['success'];
            echo '<p class="success-message"><b>Éxito: ';
            if ($success == "insertado") echo 'Número insertado correctamente.';
            else if ($success == "actualizado") echo 'Número actualizado correctamente.';
            else if ($success == "eliminado") echo 'Número eliminado correctamente.';
            echo '</b></p>';
        }
        ?>

        <!-- Formulario de registro -->
        <section>
            <h3><i class="ph ph-plus-circle"></i>Agregar Contacto</h3>
            <form method="post" action="../action/numeroEmergenciaAction.php">
                <?php if ($tipoUsuario !== 'cliente'): ?>
                    <div class="form-group">
                        <label>Cliente:</label>
                        <span class="error-message"><?= Validation::getError('clienteId') ?></span>
                        <select name="clienteId">
                            <option value="">Seleccione un cliente</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c->getId() ?>" <?= Validation::getOldInput('clienteId') == $c->getId() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c->getNombre().' - '.$c->getCarnet()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="clienteId" value="<?= $usuarioId ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Nombre Contacto:</label>
                    <span class="error-message"><?= Validation::getError('nombre') ?></span>
                    <input type="text" name="nombre" maxlength="50" placeholder="Nombre del Contacto" value="<?= Validation::getOldInput('nombre') ?>">
                </div>
                <div class="form-group">
                    <label>Teléfono:</label>
                    <span class="error-message"><?= Validation::getError('telefono') ?></span>
                    <input type="text" name="telefono" maxlength="8" placeholder="Teléfono" value="<?= Validation::getOldInput('telefono') ?>">
                </div>
                <div class="form-group">
                    <label>Relación:</label>
                    <span class="error-message"><?= Validation::getError('relacion') ?></span>
                    <input type="text" name="relacion" maxlength="30" placeholder="Relación" value="<?= Validation::getOldInput('relacion') ?>">
                </div>
                <button type="submit" name="insertar"><i class="ph ph-plus"></i>Agregar</button>
            </form>
        </section>

        <!-- Tabla de contactos -->
        <section>
            <h3><i class="ph ph-list-bullets"></i>Contactos Registrados</h3>
            <div style="overflow-x:auto;">
                <table>
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
                            <form method="post" action="../action/numeroEmergenciaAction.php">
                                <input type="hidden" name="id" value="<?= $n->getId() ?>">
                                <input type="hidden" name="clienteId" value="<?= $n->getClienteId() ?>">
                                <td>
                                    <?php
                                    if ($tipoUsuario === 'cliente') {
                                        echo htmlspecialchars($cliente->getNombre());
                                    } else {
                                        foreach ($clientes as $c) {
                                            if ($c->getId() == $n->getClienteId()) {
                                                echo htmlspecialchars($c->getNombre().' - '.$c->getCarnet());
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td><input type="text" name="nombre" maxlength="50" value="<?= htmlspecialchars($n->getNombre()) ?>"></td>
                                <td><input type="text" name="telefono" maxlength="8" value="<?= htmlspecialchars($n->getTelefono()) ?>"></td>
                                <td><input type="text" name="relacion" maxlength="30" value="<?= htmlspecialchars($n->getRelacion()) ?>"></td>
                                <td>
                                    <button type="submit" name="actualizar"><i class="ph ph-pencil-simple"></i>Actualizar</button>
                                    <button type="submit" name="eliminar" onclick="return confirm('¿Seguro que desea eliminar este número?');"><i class="ph ph-trash"></i>Eliminar</button>
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
        <p>&copy; <?= date("Y") ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
<?php Validation::clear(); ?>
</body>
</html>
