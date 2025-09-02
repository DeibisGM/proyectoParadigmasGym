<?php
session_start();
include_once '../business/clienteBusiness.php';
include_once '../business/numeroEmergenciaBusiness.php';

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

$tipoUsuario = $_SESSION['tipo_usuario'];
$clienteId = $_SESSION['usuario_id'];

$clienteBusiness = new ClienteBusiness();
$numeroEmergenciaBusiness = new numeroEmergenciaBusiness();

if ($tipoUsuario === 'cliente') {
    $numeros = $numeroEmergenciaBusiness->getAllTBNumeroEmergenciaByClienteId($clienteId);
} else {
    $numeros = $numeroEmergenciaBusiness->getAllTBNumeroEmergencia();
    $clientes = $clienteBusiness->getAllTBCliente();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Números de Emergencia</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-phone-plus"></i>Gestión de Números de Emergencia</h2>

    </header>

    <main>
        <section>
            <h3><i class="ph ph-plus-circle"></i>Agregar Contacto</h3>
            <form method="post" action="../action/numeroEmergenciaAction.php">
                <?php if ($tipoUsuario !== 'cliente'): ?>
                    <label>Cliente:</label>
                    <select name="clienteId" required>
                        <option value="">Seleccione un cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente->getId(); ?>">
                                <?php echo $cliente->getNombre() . " - " . $cliente->getCarnet(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="hidden" name="clienteId" value="<?php echo $clienteId; ?>">
                <?php endif; ?>
                <label>Nombre Contacto:</label>
                <input type="text" name="nombre" placeholder="Nombre del Contacto" maxlength="50" required>
                <label>Teléfono:</label>
                <input type="text" name="telefono" placeholder="Teléfono" maxlength="8" required>
                <label>Relación:</label>
                <input type="text" name="relacion" placeholder="Relación" maxlength="30" required>
                <button type="submit" name="insertar"><i class="ph ph-plus"></i>Agregar</button>
            </form>
        </section>

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
                    <?php foreach ($numeros as $numero): ?>
                        <tr>
                            <form method="post" action="../action/numeroEmergenciaAction.php">
                                <td>
                                    <?php
                                    if ($tipoUsuario === 'cliente') {
                                        $clienteActual = $clienteBusiness->getClientePorId($clienteId);
                                        echo $clienteActual ? $clienteActual->getNombre() : 'Yo';
                                    } else {
                                        foreach ($clientes as $cliente) {
                                            if ($cliente->getId() == $numero->getClienteId()) {
                                                echo $cliente->getNombre() . " - " . $cliente->getCarnet();
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td><input type="text" name="nombre"
                                           value="<?php echo htmlspecialchars($numero->getNombre()); ?>"
                                           placeholder="Nombre del Contacto" maxlength="50" required></td>
                                <td><input type="text" name="telefono"
                                           value="<?php echo htmlspecialchars($numero->getTelefono()); ?>"
                                           placeholder="Teléfono" maxlength="8" required></td>
                                <td><input type="text" name="relacion"
                                           value="<?php echo htmlspecialchars($numero->getRelacion()); ?>"
                                           placeholder="Relación" maxlength="30" required></td>
                                <td class="actions-cell">
                                    <input type="hidden" name="id" value="<?php echo $numero->getId(); ?>">
                                    <input type="hidden" name="clienteId"
                                           value="<?php echo $numero->getClienteId(); ?>">
                                    <button type="submit" name="actualizar" title="Actualizar"><i
                                                class="ph ph-pencil-simple"></i> Actualizar
                                    </button>
                                    <button type="submit" name="eliminar"
                                            onclick="return confirm('¿Seguro que desea eliminar este número?');"
                                            title="Eliminar"><i class="ph ph-trash"></i> Eliminar
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
</body>
</html>