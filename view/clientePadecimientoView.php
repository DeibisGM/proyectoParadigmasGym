<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/loginView.php");
    exit();
}

include_once '../business/clientePadecimientoBusiness.php';
include_once '../business/padecimientoBusiness.php';
include_once '../business/PadecimientoDictamenBusiness.php';

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
        'tbpadecimientodescripcion' => $padecimiento->getTbpadecimientodescripcion(),
        'tbpadecimientoformadeactuar' => $padecimiento->getTbpadecimientoformadeactuar()
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

$formData = array();
if (isset($_SESSION['temp_form_data'])) {
    $formData = $_SESSION['temp_form_data'];
    unset($_SESSION['temp_form_data']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cliente Padecimiento</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-first-aid-kit"></i>Gestión de Cliente Padecimiento</h2>
    </header>

    <main>
        <div id="mensaje" style="display: none;"></div>

        <?php if (!$esUsuarioCliente): ?>
            <section>
                <h3><i class="ph ph-funnel"></i>Filtros de búsqueda</h3>
                <label for="tipoBusqueda">Buscar por:</label>
                <select id="tipoBusqueda">
                    <option value="todos">Todos los registros</option>
                    <option value="cliente">Por cliente</option>
                    <option value="padecimiento">Por padecimiento</option>
                </select>

                <div id="filtroCliente" style="display: none;">
                    <label for="buscarCliente">Cliente:</label>
                    <input type="text" id="buscarCliente" placeholder="Escriba el carnet del cliente">
                </div>

                <div id="filtroPadecimiento" style="display: none;">
                    <label for="buscarPadecimiento">Padecimiento:</label>
                    <select id="buscarPadecimiento">
                        <option value="">Seleccione un padecimiento</option>
                        <?php foreach ($padecimientos as $padecimiento): ?>
                            <option value="<?php echo htmlspecialchars($padecimiento['tbpadecimientonombre']); ?>">
                                <?php echo htmlspecialchars($padecimiento['tbpadecimientonombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" onclick="aplicarFiltros()"><i class="ph ph-funnel-simple"></i>Aplicar Filtro</button>
                <button type="button" onclick="limpiarFiltros()"><i class="ph ph-x-circle"></i>Limpiar</button>
            </section>
        <?php endif; ?>

        <section>
            <h3 id="tituloFormulario"><i class="ph ph-plus-circle"></i>
                <?php echo $esUsuarioCliente ? 'Registrar nuevo cliente padecimiento' : 'Registrar cliente padecimiento'; ?>
            </h3>

            <form id="formClientePadecimiento">
                <input type="hidden" id="accion" name="accion" value="create">
                <input type="hidden" id="clientePadecimientoId" name="id" value="">
                <input type="hidden" id="dictamenIdHidden" name="dictamenId" value="<?php echo isset($formData['dictamenId']) ? $formData['dictamenId'] : ''; ?>">

                <?php if (!$esUsuarioCliente): ?>
                    <div>
                        <label for="clienteId">Cliente:</label>
                        <select id="clienteId" name="clienteId" required>
                            <option value="">Seleccione un cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>"
                                    <?php echo (isset($formData['clienteId']) && $formData['clienteId'] == $cliente['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cliente['carnet'] . ' - ' . $cliente['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="tipoPadecimiento">Tipo de padecimiento:</label>
                    <select id="tipoPadecimiento" onchange="cargarPadecimientosPorTipo()">
                        <option value="">Seleccione un tipo</option>
                        <?php foreach ($tiposPadecimiento as $tipo): ?>
                            <option value="<?php echo htmlspecialchars($tipo); ?>"
                                <?php echo (isset($formData['tipoPadecimiento']) && $formData['tipoPadecimiento'] == $tipo) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tipo); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="padecimiento">Padecimiento:</label>
                    <select id="padecimiento" name="padecimientoId" disabled required>
                        <option value="">Primero seleccione un tipo</option>
                    </select>
                </div>

                <div>
                    <label>Dictamen (opcional):</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" id="dictamenDisplay" readonly
                               placeholder="No se ha registrado dictamen"
                               style="flex: 1;"
                               value="<?php echo isset($formData['dictamenEntidad']) ? $formData['dictamenEntidad'] : ''; ?>">
                        <button type="button" onclick="abrirRegistroDictamen()" style="white-space: nowrap;">
                            <i class="ph ph-file-plus"></i> Registrar Dictamen
                        </button>
                        <button type="button" onclick="limpiarDictamen()" style="white-space: nowrap;"
                                <?php echo !isset($formData['dictamenId']) ? 'style="display: none;"' : ''; ?> id="btnLimpiarDictamen">
                            <i class="ph ph-x-circle"></i> Quitar
                        </button>
                    </div>
                </div>

                <div>
                    <button type="submit" id="btnSubmit"><i class="ph ph-plus"></i>Registrar</button>
                    <button type="button" onclick="limpiarFormulario()" id="btnCancelar" style="display: none;"><i class="ph ph-x-circle"></i>Cancelar</button>
                </div>
            </form>
        </section>

        <div id="modalDictamen" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="ph ph-file-plus"></i> Registrar Nuevo Dictamen</h3>
                    <button type="button" class="close" onclick="cerrarModalDictamen()">&times;</button>
                </div>
                <div id="mensajeModal" style="display: none; margin-bottom: 15px;"></div>
                <form id="formDictamen" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="fechaemisionModal">Fecha de Emisión:</label>
                        <input type="date" id="fechaemisionModal" name="fechaemision" required>
                        <small>La fecha no puede ser futura</small>
                    </div>
                    <div class="form-group">
                        <label for="entidademisionModal">Entidad de Emisión:</label>
                        <input type="text" id="entidademisionModal" name="entidademision"
                            placeholder="Nombre de la entidad" required>
                    </div>
                    <div class="form-group">
                        <label for="imagenesModal">Imágenes del Dictamen:</label>
                        <input type="file" id="imagenesModal" name="imagenes[]" multiple accept="image/*">
                        <small>Formatos aceptados: JPG, PNG, WebP. Máximo 5MB por imagen.</small>
                    </div>
                    <div class="modal-buttons">
                        <button type="button" onclick="cerrarModalDictamen()">
                            <i class="ph ph-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" id="btnGuardarDictamen">
                            <i class="ph ph-plus"></i> Guardar Dictamen
                        </button>
                    </div>
                </form>
                <div id="loadingDictamen" style="display: none; text-align: center; padding: 20px;">
                    <p>Procesando dictamen...</p>
                </div>
            </div>
        </div>

        <section>
            <h3>
                <i class="ph ph-list-bullets"></i><?php echo $esUsuarioCliente ? 'Mis cliente padecimientos' : 'Cliente padecimientos de todos los clientes'; ?>
            </h3>
            <div style="overflow-x:auto;">
                <table id="tablaClientePadecimientos">
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
                    <tbody id="tablaBody">
                    </tbody>
                </table>
            </div>
            <div id="mensajeVacio" style="display: none;">
                <p><?php echo $esUsuarioCliente ? 'No tiene cliente padecimientos registrados.' : 'No hay cliente padecimientos registrados.'; ?></p>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>

<script>
    let padecimientosData = <?php echo json_encode($padecimientos); ?>;
    let esUsuarioCliente = <?php echo $esUsuarioCliente ? 'true' : 'false'; ?>;
    let esAdmin = <?php echo $esAdmin ? 'true' : 'false'; ?>;
    let clientePadecimientos = <?php echo json_encode($clientePadecimientos); ?>;
    let dictamenes = <?php echo json_encode($dictamenes); ?>;
    let modalDictamenAbierto = false;

    let clientePad = [];
    clientePadecimientos.forEach(cliente => {
        let padecimientosString = cliente.tbpadecimientoid || '';
        let padecimientosIds = [];
        if (padecimientosString && padecimientosString.trim() !== '') {
            padecimientosIds = padecimientosString.split('$').filter(id => id && id.trim() !== '');
        }
        clientePad.push({
            id: cliente.tbclientepadecimientoid,
            clienteId: cliente.tbclienteid,
            carnet: cliente.carnet || '',
            padecimientos: padecimientosString,
            padecimientosIds: padecimientosIds,
            padecimientosNombres: cliente.padecimientosNombres || [],
            dictamenId: cliente.tbpadecimientodictamenid
        });
    });

    window.onload = function () {
        cargarDatosEnTabla();

        <?php if (isset($formData['padecimientoId'])): ?>
            setTimeout(() => {
                document.getElementById('padecimiento').value = '<?php echo $formData['padecimientoId']; ?>';
            }, 100);
        <?php endif; ?>
    };

    function abrirRegistroDictamen() {
        if (!esUsuarioCliente) {
            const clienteId = document.getElementById('clienteId').value;
            if (!clienteId) {
                mostrarMensaje('Error: Primero debe seleccionar un cliente para poder registrar un dictamen.', 'error');
                return;
            }
        }

        const hoy = new Date().toISOString().split('T')[0];
        document.getElementById('fechaemisionModal').max = hoy;
        document.getElementById('fechaemisionModal').value = hoy;
        document.getElementById('formDictamen').reset();
        document.getElementById('mensajeModal').style.display = 'none';
        document.getElementById('modalDictamen').style.display = 'block';
        modalDictamenAbierto = true;
    }

    function limpiarDictamen() {
        document.getElementById('dictamenIdHidden').value = '';
        document.getElementById('dictamenDisplay').value = '';
        document.getElementById('btnLimpiarDictamen').style.display = 'none';
    }

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
        if (clientePad.length === 0) {
            document.getElementById('mensajeVacio').style.display = 'block';
            return;
        }
        document.getElementById('mensajeVacio').style.display = 'none';

        clientePad.forEach((cliente, clienteIndex) => {
            if (!cliente.padecimientosIds || cliente.padecimientosIds.length === 0) return;

            cliente.padecimientosIds.forEach((padecimientoId, index) => {
                if (padecimientoId && padecimientoId.trim() !== '') {
                    const padecimientoObj = padecimientosData.find(p => p.tbpadecimientoid == padecimientoId);
                    const nombrePadecimiento = padecimientoObj ? padecimientoObj.tbpadecimientonombre : `ID Desconocido: ${padecimientoId}`;
                    const entidadDictamen = cliente.dictamenId && dictamenes[cliente.dictamenId] ? dictamenes[cliente.dictamenId] : 'Sin dictamen';

                    const fila = document.createElement('tr');
                    fila.setAttribute('data-registro-id', cliente.id);
                    fila.setAttribute('data-cliente-id', cliente.clienteId);
                    fila.setAttribute('data-padecimiento-id', padecimientoId);
                    fila.setAttribute('data-padecimiento-index', index);

                    let contenidoFila = '';
                    if (!esUsuarioCliente) {
                        contenidoFila += `<td>${cliente.carnet}</td>`;
                    }
                    contenidoFila += `
                        <td class="padecimiento-cell">
                            <div class="padecimiento-display">${nombrePadecimiento}</div>
                            <div class="padecimiento-edit" style="display: none;">
                                <div>
                                    <label>Tipo:</label>
                                    <select id="tipo-edit-${cliente.id}-${index}" onchange="cargarPadecimientosPorTipoEdicion(${cliente.id}, ${index})">
                                        <option value="">Seleccione un tipo</option>
                                        <?php foreach ($tiposPadecimiento as $tipo): ?>
                                            <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label>Padecimiento:</label>
                                    <select id="padecimiento-edit-${cliente.id}-${index}" disabled><option value="">Primero seleccione un tipo</option></select>
                                </div>
                            </div>
                        </td>
                        <td>${entidadDictamen}</td>
                        <td class="actions-cell">
                            <button onclick="editarPadecimientoIndividual(${cliente.id}, ${index}, '${padecimientoId}')" class="btn-editar" title="Editar"><i class="ph ph-pencil-simple"></i> Editar</button>
                            <button onclick="cancelarEdicionIndividual(${cliente.id}, ${index})" style="display: none;" class="btn-cancelar-edicion" title="Cancelar"><i class="ph ph-x-circle"></i> Cancelar</button>
                            <button onclick="guardarEdicionIndividual(${cliente.id}, ${index}, '${padecimientoId}')" style="display: none;" class="btn-guardar-edicion" title="Guardar"><i class="ph ph-floppy-disk"></i> Guardar</button>
                    `;
                    if (esAdmin) {
                        contenidoFila += `<button onclick="eliminarPadecimientoIndividual(${cliente.id}, '${padecimientoId}', '${nombrePadecimiento}')" class="btn-eliminar" title="Eliminar"><i class="ph ph-trash"></i> Eliminar</button>`;
                    }
                    contenidoFila += `</td>`;
                    fila.innerHTML = contenidoFila;
                    tbody.appendChild(fila);
                }
            });
        });
    }

    function editarPadecimientoIndividual(registroId, index, padecimientoId) {
        const fila = document.querySelector(`tr[data-registro-id="${registroId}"][data-padecimiento-index="${index}"]`);
        fila.querySelector('.padecimiento-display').style.display = 'none';
        fila.querySelector('.padecimiento-edit').style.display = 'block';
        fila.querySelector('.btn-editar').style.display = 'none';
        fila.querySelector('.btn-cancelar-edicion').style.display = 'inline-flex';
        fila.querySelector('.btn-guardar-edicion').style.display = 'inline-flex';
        const btnEliminar = fila.querySelector('.btn-eliminar');
        if (btnEliminar) btnEliminar.style.display = 'none';

        const padecimientoActual = padecimientosData.find(p => p.tbpadecimientoid == padecimientoId);
        if (padecimientoActual) {
            const tipoSelect = document.getElementById(`tipo-edit-${registroId}-${index}`);
            tipoSelect.value = padecimientoActual.tbpadecimientotipo;
            cargarPadecimientosPorTipoEdicion(registroId, index);
            setTimeout(() => {
                document.getElementById(`padecimiento-edit-${registroId}-${index}`).value = padecimientoId;
            }, 100);
        }
    }

    function cargarPadecimientosPorTipoEdicion(registroId, index) {
        const tipoSeleccionado = document.getElementById(`tipo-edit-${registroId}-${index}`).value;
        const selectPadecimiento = document.getElementById(`padecimiento-edit-${registroId}-${index}`);
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

    function guardarEdicionIndividual(registroId, index, padecimientoIdAntiguo) {
        const nuevoPadecimientoId = document.getElementById(`padecimiento-edit-${registroId}-${index}`).value;
        if (!nuevoPadecimientoId) {
            mostrarMensaje('Error: Debe seleccionar un padecimiento.', 'error');
            return;
        }
        const formData = new FormData();
        formData.append('updateIndividual', '1');
        formData.append('registroId', registroId);
        formData.append('padecimientoIdAntiguo', padecimientoIdAntiguo);
        formData.append('padecimientoIdNuevo', nuevoPadecimientoId);
        formData.append('clienteId', obtenerClienteIdDelRegistro(registroId));

        fetch('../action/clientePadecimientoAction.php', {method: 'POST', body: formData})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            }).catch(error => mostrarMensaje('Error de conexión.', 'error'));
    }

    function cancelarEdicionIndividual(registroId, index) {
        const fila = document.querySelector(`tr[data-registro-id="${registroId}"][data-padecimiento-index="${index}"]`);
        fila.querySelector('.padecimiento-display').style.display = 'block';
        fila.querySelector('.padecimiento-edit').style.display = 'none';
        fila.querySelector('.btn-editar').style.display = 'inline-flex';
        fila.querySelector('.btn-cancelar-edicion').style.display = 'none';
        fila.querySelector('.btn-guardar-edicion').style.display = 'none';
        const btnEliminar = fila.querySelector('.btn-eliminar');
        if (btnEliminar) btnEliminar.style.display = 'inline-flex';
    }

    function eliminarPadecimientoIndividual(registroId, padecimientoId, nombrePadecimiento) {
        if (!confirm(`¿Eliminar el padecimiento "${nombrePadecimiento}" de este registro?`)) return;
        const formData = new FormData();
        formData.append('deleteIndividual', '1');
        formData.append('registroId', registroId);
        formData.append('padecimientoId', padecimientoId);
        fetch('../action/clientePadecimientoAction.php', {method: 'POST', body: formData})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            }).catch(error => mostrarMensaje('Error de conexión.', 'error'));
    }

    function obtenerClienteIdDelRegistro(registroId) {
        const fila = document.querySelector(`tr[data-registro-id="${registroId}"]`);
        return fila ? fila.getAttribute('data-cliente-id') : null;
    }

    function limpiarFormulario() {
        document.getElementById('formClientePadecimiento').reset();
        document.getElementById('accion').value = 'create';
        document.getElementById('clientePadecimientoId').value = '';
        document.getElementById('dictamenIdHidden').value = '';
        document.getElementById('dictamenDisplay').value = '';
        document.getElementById('btnLimpiarDictamen').style.display = 'none';
        document.getElementById('tituloFormulario').innerHTML = `<i class="ph ph-plus-circle"></i> ${esUsuarioCliente ? 'Registrar nuevo cliente padecimiento' : 'Registrar cliente padecimiento'}`;
        document.getElementById('btnSubmit').innerHTML = '<i class="ph ph-plus"></i>Registrar';
        document.getElementById('btnCancelar').style.display = 'none';
        document.getElementById('padecimiento').disabled = true;
    }

    document.getElementById('formClientePadecimiento').addEventListener('submit', function (e) {
        e.preventDefault();
        const padecimientoSeleccionado = document.getElementById('padecimiento').value;
        if (!padecimientoSeleccionado) {
            mostrarMensaje('Error: Debe seleccionar un padecimiento.', 'error');
            return;
        }
        const formData = new FormData(this);
        const accion = document.getElementById('accion').value;
        formData.delete('padecimientoId');
        formData.append('padecimientosIds[]', padecimientoSeleccionado);
        formData.append(accion, '1');
        fetch('../action/clientePadecimientoAction.php', {method: 'POST', body: formData})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    limpiarFormulario();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            }).catch(error => mostrarMensaje('Error de conexión.', 'error'));
    });

    function mostrarMensaje(mensaje, tipo) {
        const divMensaje = document.getElementById('mensaje');
        divMensaje.textContent = mensaje;
        divMensaje.style.display = 'block';
        divMensaje.className = tipo === 'success' ? 'success' : 'error';
        setTimeout(() => {
            divMensaje.style.display = 'none';
        }, 5000);
    }

    <?php if (!$esUsuarioCliente): ?>
    document.getElementById('tipoBusqueda').addEventListener('change', function () {
        const tipo = this.value;
        document.getElementById('filtroCliente').style.display = tipo === 'cliente' ? 'block' : 'none';
        document.getElementById('filtroPadecimiento').style.display = tipo === 'padecimiento' ? 'block' : 'none';
    });

    function aplicarFiltros() {
        const tipoBusqueda = document.getElementById('tipoBusqueda').value;
        const filas = document.getElementById('tablaClientePadecimientos').getElementsByTagName('tbody')[0].rows;
        for (let fila of filas) {
            let mostrar = true;
            if (tipoBusqueda === 'cliente') {
                const textoBusqueda = document.getElementById('buscarCliente').value.toLowerCase();
                const carnet = fila.cells[0].textContent.toLowerCase();
                mostrar = carnet.includes(textoBusqueda);
            } else if (tipoBusqueda === 'padecimiento') {
                const padecimientoBuscado = document.getElementById('buscarPadecimiento').value.value.toLowerCase();
                const padecimientos = fila.cells[1].textContent.toLowerCase();
                mostrar = padecimientos.includes(padecimientoBuscado);
            }
            fila.style.display = mostrar ? '' : 'none';
        }
    }

    function limpiarFiltros() {
        document.getElementById('tipoBusqueda').value = 'todos';
        document.getElementById('buscarCliente').value = '';
        document.getElementById('buscarPadecimiento').value = '';
        document.getElementById('filtroCliente').style.display = 'none';
        document.getElementById('filtroPadecimiento').style.display = 'none';
        const filas = document.getElementById('tablaClientePadecimientos').getElementsByTagName('tbody')[0].rows;
        for (let fila of filas) {
            fila.style.display = '';
        }
    }
    <?php endif; ?>

    function cerrarModalDictamen() {
        if (confirm('¿Está seguro que desea cerrar? Se perderán los datos no guardados del dictamen.')) {
            document.getElementById('modalDictamen').style.display = 'none';
            modalDictamenAbierto = false;
        }
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modalDictamen');
        if (event.target == modal && modalDictamenAbierto) {
            cerrarModalDictamen();
        }
    }

    document.getElementById('formDictamen').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const btnGuardar = document.getElementById('btnGuardarDictamen');
        const loading = document.getElementById('loadingDictamen');
        const mensajeModal = document.getElementById('mensajeModal');

        const fecha = document.getElementById('fechaemisionModal').value;
        const entidad = document.getElementById('entidademisionModal').value.trim();

        if (!fecha || !entidad) {
            mostrarMensajeModal('Error: Todos los campos son obligatorios.', 'error');
            return;
        }

        const hoy = new Date().toISOString().split('T')[0];
        if (fecha > hoy) {
            mostrarMensajeModal('Error: La fecha de emisión no puede ser futura.', 'error');
            return;
        }

        let clienteId;
        if (esUsuarioCliente) {
            clienteId = '<?php echo $_SESSION['usuario_id']; ?>';
        } else {
            clienteId = document.getElementById('clienteId').value;
            if (!clienteId) {
                mostrarMensajeModal('Error: Debe seleccionar un cliente.', 'error');
                return;
            }
        }

        formData.append('clienteId', clienteId);

        btnGuardar.disabled = true;
        loading.style.display = 'block';
        mensajeModal.style.display = 'none';

        formData.append('accion', 'guardar');
        formData.append('ajax_request', '1');

        fetch('../action/PadecimientoDictamenAction.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('dictamenIdHidden').value = data.dictamenId || data.padecimiento?.id || '';
                document.getElementById('dictamenDisplay').value = data.entidadEmision || data.padecimiento?.entidademision || entidad;
                document.getElementById('btnLimpiarDictamen').style.display = 'inline-block';

                mostrarMensajeModal('Éxito: Dictamen registrado correctamente.', 'success');

                setTimeout(() => {
                    document.getElementById('modalDictamen').style.display = 'none';
                    modalDictamenAbierto = false;
                    mostrarMensaje('Dictamen registrado. Ahora puede completar el registro del cliente padecimiento.', 'success');
                }, 2000);

            } else {
                mostrarMensajeModal('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensajeModal('Error: Error de conexión. Intente nuevamente.', 'error');
        })
        .finally(() => {
            btnGuardar.disabled = false;
            loading.style.display = 'none';
        });
    });

    function mostrarMensajeModal(mensaje, tipo) {
        const mensajeModal = document.getElementById('mensajeModal');
        mensajeModal.textContent = mensaje;
        mensajeModal.className = tipo === 'success' ? 'success' : 'error';
        mensajeModal.style.display = 'block';

        if (tipo !== 'success') {
            setTimeout(() => {
                mensajeModal.style.display = 'none';
            }, 5000);
        }
    }

    document.getElementById('fechaemisionModal').addEventListener('change', function() {
        const hoy = new Date().toISOString().split('T')[0];
        if (this.value > hoy) {
            mostrarMensajeModal('La fecha de emisión no puede ser futura.', 'error');
            this.value = hoy;
        }
    });
</script>
</body>
</html>