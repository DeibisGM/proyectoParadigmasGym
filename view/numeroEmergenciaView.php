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
    // Cliente solo ve sus números
    $numeros = $numeroEmergenciaBusiness->getAllTBNumeroEmergenciaByClienteId($clienteId);
} else {
    // Admin o instructor ve todos
    $numeros = $numeroEmergenciaBusiness->getAllTBNumeroEmergencia();
    $clientes = $clienteBusiness->getAllTBCliente();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Números de Emergencia</title>
</head>
<body>
<h1>Gestión de Números de Emergencia</h1>

<!-- Formulario -->
<form method="post" action="../action/numeroEmergenciaAction.php">
    <?php if ($tipoUsuario !== 'cliente'): ?>
        <label>Cliente:</label>
        <select name="clienteId" required>
            <option value="">Seleccione un cliente</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?php echo $cliente->getClienteId(); ?>">
                    <?php echo $cliente->getClienteNombre() . " - " . $cliente->getClienteCarnet(); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
    <?php else: ?>
        <!-- Cliente ya tiene su ID oculto -->
        <input type="hidden" name="clienteId" value="<?php echo $clienteId; ?>">
    <?php endif; ?>

    <label>Nombre Contacto:</label>
    <input type="text" name="nombre" maxlength="50" required><br><br>

    <label>Teléfono:</label>
    <input type="text" name="telefono" maxlength="8" required><br><br>

    <label>Relación:</label>
    <input type="text" name="relacion" maxlength="30" required><br><br>

    <input type="submit" name="insertar" value="Agregar">
</form>

<hr>

<!-- Tabla de Números de Emergencia -->
<table border="1">
    <tr>
        <th>Cliente</th>
        <th>Nombre Contacto</th>
        <th>Teléfono</th>
        <th>Relación</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($numeros as $numero): ?>
        <tr>
            <form method="post" action="../action/numeroEmergenciaAction.php">
                <td>
                    <?php
                    if ($tipoUsuario === 'cliente') {
                        echo "Yo mismo";
                    } else {
                        foreach ($clientes as $cliente) {
                            if ($cliente->getClienteId() == $numero->getClienteId()) {
                                echo $cliente->getClienteNombre() . " - " . $cliente->getClienteCarnet();
                            }
                        }
                    }
                    ?>
                </td>
                <td><input type="text" name="nombre" value="<?php echo htmlspecialchars($numero->getNombre()); ?>" maxlength="50" required></td>
                <td><input type="text" name="telefono" value="<?php echo htmlspecialchars($numero->getTelefono()); ?>" maxlength="8" required></td>
                <td><input type="text" name="relacion" value="<?php echo htmlspecialchars($numero->getRelacion()); ?>" maxlength="30" required></td>
                <td>
                    <input type="hidden" name="id" value="<?php echo $numero->getId(); ?>">
                    <input type="hidden" name="clienteId" value="<?php echo $numero->getClienteId(); ?>">
                    <input type="submit" name="actualizar" value="Actualizar">
                    <input type="submit" name="eliminar" value="Eliminar" onclick="return confirm('¿Seguro que desea eliminar este número?');">
                </td>
            </form>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
