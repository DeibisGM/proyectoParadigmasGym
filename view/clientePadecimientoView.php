<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/loginView.php");
    exit();
}

include_once '../business/clientePadecimientoBusiness.php';
include_once '../business/padecimientoBusiness.php';
include_once '../business/PadecimientoDictamenBusiness.php';
include_once '../utility/Validation.php';

Validation::start();

$clientePadecimientoBusiness = new ClientePadecimientoBusiness();
$padecimientoBusiness = new PadecimientoBusiness();
$padecimientoDictamenBusiness = new PadecimientoDictamenBusiness();

$esUsuarioCliente = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cliente';
$esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
$esInstructor = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'instructor';

$padecimientosObj = $padecimientoBusiness->obtenerTbpadecimiento();
$tiposPadecimiento = $padecimientoBusiness->obtenerTiposPadecimiento();

$padecimientos = array();
foreach ($padecimientosObj as $padecimiento) {
    $padecimientos[] = array(
        'tbpadecimientoid' => $padecimiento->getTbpadecimientoid(),
        'tbpadecimientotipo' => $padecimiento->getTbpadecimientotipo(),
        'tbpadecimientonombre' => $padecimiento->getTbpadecimientonombre(),
    );
}

if ($esUsuarioCliente) {
    $clientePadecimientosObj = $clientePadecimientoBusiness->obtenerTodosTBClientePadecimientoPorCliente($_SESSION['usuario_id']);
} else {
    $clientePadecimientosObj = $clientePadecimientoBusiness->obtenerTBClientePadecimiento();
    $clientes = $clientePadecimientoBusiness->obtenerTodosLosClientes();
}

$clientePadecimientos = array();
foreach ($clientePadecimientosObj as $clienteObj) {
    $clientePadecimientos[] = array(
        'tbclientepadecimientoid' => $clienteObj->getTbclientepadecimientoid(),
        'tbclienteid' => $clienteObj->getTbclienteid(),
        'tbpadecimientoid' => $clienteObj->getTbpadecimientoid(),
        'tbpadecimientodictamenid' => $clienteObj->getTbpadecimientodictamenid(),
        'carnet' => $clienteObj->getCarnet(),
        'padecimientosNombres' => $clienteObj->getPadecimientosNombres()
    );
}

$dictamenes = array();
$dictamenesObj = $padecimientoDictamenBusiness->getAllTBPadecimientoDictamen();
foreach ($dictamenesObj as $dictamen) {
    $dictamenes[$dictamen->getPadecimientodictamenid()] = $dictamen->getPadecimientodictamenentidademision();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Padecimientos del Cliente</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2><i class="ph ph-first-aid-kit"></i>Gestión de Padecimientos del Cliente</h2>
        </header>

        <main>
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message flash-msg">
                    <?php
                    if ($_GET['success'] === 'created')
                        echo 'Registro creado con éxito.';
                    elseif ($_GET['success'] === 'updated')
                        echo 'Registro actualizado con éxito.';
                    elseif ($_GET['success'] === 'deleted')
                        echo 'Registro eliminado con éxito.';
                    ?>
                </p>
            <?php endif; ?>
            <?php if (Validation::getError('general')): ?>
                <p class="error-message flash-msg">
                    <?php echo Validation::getError('general'); ?>
                </p>
            <?php endif; ?>

            <section>
                <h3><i class="ph ph-plus-circle"></i>
                    <?php echo $esUsuarioCliente ? 'Registrar Padecimiento' : 'Registrar Padecimiento de Cliente'; ?>
                </h3>

                <form id="formClientePadecimiento" action="../action/clientePadecimientoAction.php" method="POST">
                    <input type="hidden" id="accion" name="accion" value="create">
                    <input type="hidden" id="clientePadecimientoId" name="id" value="">
                    <input type="hidden" id="dictamenIdHidden" name="dictamenId"
                        value="<?php echo Validation::getOldInput('dictamenId'); ?>">

                    <div class="form-grid-container">
                        <?php if (!$esUsuarioCliente): ?>
                            <div class="form-group">
                                <label for="clienteId">Cliente:</label>
                                <?php if ($error = Validation::getError('clienteId')): ?><span
                                    class="error-message"><?php echo $error; ?></span><?php endif; ?>
                                <select id="clienteId" name="clienteId">
                                    <option value="">Seleccione un cliente</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?php echo $cliente['id']; ?>" <?php echo (Validation::getOldInput('clienteId') == $cliente['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cliente['carnet'] . ' - ' . $cliente['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="tipoPadecimiento">Tipo de padecimiento:</label>
                            <?php if ($error = Validation::getError('tipoPadecimiento')): ?><span
                                class="error-message"><?php echo $error; ?></span><?php endif; ?>
                            <select id="tipoPadecimiento" name="tipoPadecimiento">
                                <option value="">Seleccione un tipo</option>
                                <?php foreach ($tiposPadecimiento as $tipo): ?>
                                    <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo (Validation::getOldInput('tipoPadecimiento') == $tipo) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipo); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="padecimiento">Padecimiento:</label>
                            <?php if ($error = Validation::getError('padecimientos')): ?><span
                                class="error-message"><?php echo $error; ?></span><?php endif; ?>
                            <select id="padecimiento" name="padecimientosIds[]" disabled>
                                <option value="">Primero seleccione un tipo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Dictamen (opcional):</label>
                            <span id="errorDictamen" class="error-message" style="display: none;"></span>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" id="dictamenDisplay" readonly
                                    placeholder="No se ha registrado dictamen" style="flex: 1;"
                                    value="<?php echo Validation::getOldInput('dictamenEntidad'); ?>">
                                <button type="button" class="btn-row" onclick="abrirRegistroDictamen()"
                                    title="Registrar Dictamen"><i class="ph ph-file-plus"></i></button>
                                <button type="button" class="btn-row btn-danger" onclick="limpiarDictamen()"
                                    title="Quitar Dictamen" <?php echo !Validation::getOldInput('dictamenId') ? 'style="display: none;"' : ''; ?>
                                    id="btnLimpiarDictamen">
                                    <i class="ph ph-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="btnSubmit" name="create"><i class="ph ph-plus"></i>Registrar</button>
                </form>
            </section>


            <section>
                <h3><i class="ph ph-list-bullets"></i>
                    <?php echo $esUsuarioCliente ? 'Mis Padecimientos Registrados' : 'Padecimientos de Clientes'; ?>
                </h3>
                <div class="table-wrapper">
                    <table id="tablaClientePadecimientos" class="table-clients">
                        <thead>
                            <tr>
                                <?php if (!$esUsuarioCliente): ?>
                                    <th>Carnet</th>
                                <?php endif; ?>
                                <th>Padecimiento</th>
                                <th>Entidad Dictamen</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaBody"></tbody>
                    </table>
                </div>
                <div id="mensajeVacio" style="display: none; padding: 1rem; text-align: center;">
                    <p>
                        <?php echo $esUsuarioCliente ? 'No tiene padecimientos registrados.' : 'No hay padecimientos registrados.'; ?>
                    </p>
                </div>
            </section>
        </main>
    </div>
</body>
<script>
    let padecimientosData = <?php echo json_encode($padecimientos); ?>;
    let esUsuarioCliente = <?php echo $esUsuarioCliente ? 'true' : 'false'; ?>;
    let esAdmin = <?php echo $esAdmin ? 'true' : 'false'; ?>;
    let clientePadecimientos = <?php echo json_encode($clientePadecimientos); ?>;
    let dictamenes = <?php echo json_encode($dictamenes); ?>;

    function cargarPadecimientosPorTipo() {
        const tipoSeleccionado = document.getElementById('tipoPadecimiento').value;
        const selectPadecimiento = document.getElementById('padecimiento');
        selectPadecimiento.innerHTML = '<option value="">Seleccione un padecimiento</option>';
        if (tipoSeleccionado) {
            const padecimientosFiltrados = padecimientosData.filter(p => p.tbpadecimientotipo === tipoSeleccionado);
            padecimientosFiltrados.forEach(padecimiento => {
                const option = document.createElement('option');
                option.value = padecimiento.tbpadecimientoid;
                option.textContent = padecimiento.tbpadecimientonombre;
                selectPadecimiento.appendChild(option);
            });
            selectPadecimiento.disabled = false;
        } else {
            selectPadecimiento.disabled = true;
        }
    }

    function cargarDatosEnTabla() {
        const tbody = document.getElementById('tablaBody');
        tbody.innerHTML = '';
        if (clientePadecimientos.length === 0) {
            document.getElementById('mensajeVacio').style.display = 'block';
            return;
        }
        document.getElementById('mensajeVacio').style.display = 'none';

        clientePadecimientos.forEach(registro => {
            const padecimientosIds = registro.tbpadecimientoid ? registro.tbpadecimientoid.split('$').filter(id => id) : [];
            const padecimientosNombres = registro.padecimientosNombres ? registro.padecimientosNombres.split('$').filter(name => name) : [];

            padecimientosIds.forEach((padecimientoId, index) => {
                const nombrePadecimiento = padecimientosNombres[index] || `ID: ${padecimientoId}`;
                const entidadDictamen = registro.tbpadecimientodictamenid && dictamenes[registro.tbpadecimientodictamenid] ? dictamenes[registro.tbpadecimientodictamenid] : 'Sin dictamen';
                const fila = document.createElement('tr');
                let contenidoFila = '';
                if (!esUsuarioCliente) {
                    contenidoFila += `<td data-label="Carnet">${registro.carnet}</td>`;
                }
                contenidoFila += `<td data-label="Padecimiento">${nombrePadecimiento}</td>`;
                contenidoFila += `<td data-label="Entidad Dictamen">${entidadDictamen}</td>`;
                contenidoFila += `<td data-label="Acciones"><div class="actions">`;

                if (esAdmin || esInstructor) {
                    contenidoFila += `<button onclick="eliminarPadecimientoIndividual(${registro.tbclientepadecimientoid}, '${padecimientoId}', '${nombrePadecimiento}')" class="btn-row btn-danger" title="Eliminar"><i class="ph ph-trash"></i></button>`;
                }
                contenidoFila += `</div></td>`;
                fila.innerHTML = contenidoFila;
                tbody.appendChild(fila);
            });
        });
    }
    function eliminarPadecimientoIndividual(registroId, padecimientoId, nombrePadecimiento) {
        if (!confirm(`¿Está seguro de eliminar el padecimiento "${nombrePadecimiento}" de este registro?`)) return;

        const formData = new FormData();
        formData.append('deleteIndividual', '1');
        formData.append('registroId', registroId);
        formData.append('padecimientoId', padecimientoId);

        fetch('../action/clientePadecimientoAction.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = window.location.pathname + '?success=deleted';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => alert('Error de conexión.'));
    }


    document.addEventListener('DOMContentLoaded', function () {
        cargarDatosEnTabla();
        document.getElementById('tipoPadecimiento').addEventListener('change', cargarPadecimientosPorTipo);
        <?php
        $oldPadecimientoId = Validation::getOldInput('padecimientosIds');
        if ($oldPadecimientoId && is_array($oldPadecimientoId) && count($oldPadecimientoId) > 0) {
            $selectedId = $oldPadecimientoId[0];
            $selectedPadecimiento = null;
            foreach ($padecimientosObj as $p) {
                if ($p->getTbpadecimientoid() == $selectedId) {
                    $selectedPadecimiento = $p;
                    break;
                }
            }
            if ($selectedPadecimiento) {
                echo "document.getElementById('tipoPadecimiento').value = '" . $selectedPadecimiento->getTbpadecimientotipo() . "';";
                echo "cargarPadecimientosPorTipo();";
                echo "setTimeout(() => { document.getElementById('padecimiento').value = '" . $selectedId . "'; }, 100);";
            }
        }
        ?>
    });
</script>


</html>
<?php
Validation::clear();
?>