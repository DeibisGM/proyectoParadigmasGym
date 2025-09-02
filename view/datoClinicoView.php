<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/loginView.php");
    exit();
}

include_once '../business/datoClinicoBusiness.php';
include_once '../business/padecimientoBusiness.php';

$datoClinicoBusiness = new DatoClinicoBusiness();
$padecimientoBusiness = new PadecimientoBusiness();

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
    $datosClinicosObj = $datoClinicoBusiness->obtenerTodosTBDatoClinicoPorCliente($_SESSION['usuario_id']);
} else {
    $datosClinicosObj = $datoClinicoBusiness->obtenerTBDatoClinico();
    $clientes = $datoClinicoBusiness->obtenerTodosLosClientes();
}

$datosClinicos = array();
foreach ($datosClinicosObj as $datoObj) {
    $datosClinicos[] = array(
            'tbdatoclinicoid' => $datoObj->getTbdatoclinicoid(),
            'tbclienteid' => $datoObj->getTbclienteid(),
            'tbpadecimientoid' => $datoObj->getTbpadecimientoid(),
            'carnet' => $datoObj->getCarnet(),
            'padecimientosNombres' => $datoObj->getPadecimientosNombres()
    );
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Datos Clínicos</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-first-aid-kit"></i>Gestión de Datos Clínicos</h2>

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
                <button type="button" onclick="aplicarFiltros()"><i class="ph ph-funnel-simple"></i>Aplicar Filtro
                </button>
                <button type="button" onclick="limpiarFiltros()"><i class="ph ph-x-circle"></i>Limpiar</button>
            </section>
        <?php endif; ?>

        <section>
            <h3 id="tituloFormulario"><i class="ph ph-plus-circle"></i>
                <?php echo $esUsuarioCliente ? 'Registrar nuevo dato clínico' : 'Registrar dato clínico'; ?>
            </h3>

            <form id="formDatoClinico">
                <input type="hidden" id="accion" name="accion" value="create">
                <input type="hidden" id="datoClinicoId" name="id" value="">

                <?php if (!$esUsuarioCliente): ?>
                    <div>
                        <label for="clienteId">Cliente:</label>
                        <select id="clienteId" name="clienteId" required>
                            <option value="">Seleccione un cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>">
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
                            <option value="<?php echo htmlspecialchars($tipo); ?>">
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
                    <button type="submit" id="btnSubmit"><i class="ph ph-plus"></i>Registrar</button>
                    <button type="button" onclick="limpiarFormulario()" id="btnCancelar" style="display: none;"><i
                                class="ph ph-x-circle"></i>Cancelar
                    </button>
                </div>
            </form>
        </section>

        <section>
            <h3>
                <i class="ph ph-list-bullets"></i><?php echo $esUsuarioCliente ? 'Mis datos clínicos' : 'Datos clínicos de todos los clientes'; ?>
            </h3>
            <div style="overflow-x:auto;">
                <table id="tablaDatosClinicos">
                    <thead>
                    <tr>
                        <?php if (!$esUsuarioCliente): ?>
                            <th>Carnet</th>
                        <?php endif; ?>
                        <th>Padecimiento</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="tablaBody">
                    <!-- El contenido se cargará dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div id="mensajeVacio" style="display: none;">
                <p><?php echo $esUsuarioCliente ? 'No tiene datos clínicos registrados.' : 'No hay datos clínicos registrados.'; ?></p>
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
    let datosClinicos = <?php echo json_encode($datosClinicos); ?>;

    let datosCli = [];
    datosClinicos.forEach(dato => {
        let padecimientosString = dato.tbpadecimientoid || '';
        let padecimientosIds = [];
        if (padecimientosString && padecimientosString.trim() !== '') {
            padecimientosIds = padecimientosString.split('$').filter(id => id && id.trim() !== '');
        }
        datosCli.push({
            id: dato.tbdatoclinicoid,
            clienteId: dato.tbclienteid,
            carnet: dato.carnet || '',
            padecimientos: padecimientosString,
            padecimientosIds: padecimientosIds,
            padecimientosNombres: dato.padecimientosNombres || []
        });
    });

    window.onload = function () {
        cargarDatosEnTabla();
    };

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
        if (datosCli.length === 0) {
            document.getElementById('mensajeVacio').style.display = 'block';
            return;
        }
        document.getElementById('mensajeVacio').style.display = 'none';

        datosCli.forEach((dato, datoIndex) => {
            if (!dato.padecimientosIds || dato.padecimientosIds.length === 0) return;

            dato.padecimientosIds.forEach((padecimientoId, index) => {
                if (padecimientoId && padecimientoId.trim() !== '') {
                    const padecimientoObj = padecimientosData.find(p => p.tbpadecimientoid == padecimientoId);
                    const nombrePadecimiento = padecimientoObj ? padecimientoObj.tbpadecimientonombre : `ID Desconocido: ${padecimientoId}`;
                    const fila = document.createElement('tr');
                    fila.setAttribute('data-registro-id', dato.id);
                    fila.setAttribute('data-cliente-id', dato.clienteId);
                    fila.setAttribute('data-padecimiento-id', padecimientoId);
                    fila.setAttribute('data-padecimiento-index', index);

                    let contenidoFila = '';
                    if (!esUsuarioCliente) {
                        contenidoFila += `<td>${dato.carnet}</td>`;
                    }
                    contenidoFila += `
                        <td class="padecimiento-cell">
                            <div class="padecimiento-display">${nombrePadecimiento}</div>
                            <div class="padecimiento-edit" style="display: none;">
                                <div>
                                    <label>Tipo:</label>
                                    <select id="tipo-edit-${dato.id}-${index}" onchange="cargarPadecimientosPorTipoEdicion(${dato.id}, ${index})">
                                        <option value="">Seleccione un tipo</option>
                                        <?php foreach ($tiposPadecimiento as $tipo): ?>
                                            <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label>Padecimiento:</label>
                                    <select id="padecimiento-edit-${dato.id}-${index}" disabled><option value="">Primero seleccione un tipo</option></select>
                                </div>
                            </div>
                        </td>
                        <td class="actions-cell">
                            <button onclick="editarPadecimientoIndividual(${dato.id}, ${index}, '${padecimientoId}')" class="btn-editar" title="Editar"><i class="ph ph-pencil-simple"></i> Editar</button>
                            <button onclick="cancelarEdicionIndividual(${dato.id}, ${index})" style="display: none;" class="btn-cancelar-edicion" title="Cancelar"><i class="ph ph-x-circle"></i> Cancelar</button>
                            <button onclick="guardarEdicionIndividual(${dato.id}, ${index}, '${padecimientoId}')" style="display: none;" class="btn-guardar-edicion" title="Guardar"><i class="ph ph-floppy-disk"></i> Guardar</button>
                    `;
                    if (esAdmin) {
                        contenidoFila += `<button onclick="eliminarPadecimientoIndividual(${dato.id}, '${padecimientoId}', '${nombrePadecimiento}')" class="btn-eliminar" title="Eliminar"><i class="ph ph-trash"></i> Eliminar</button>`;
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

        fetch('../action/datoClinicoAction.php', {method: 'POST', body: formData})
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
        fetch('../action/datoClinicoAction.php', {method: 'POST', body: formData})
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
        document.getElementById('formDatoClinico').reset();
        document.getElementById('accion').value = 'create';
        document.getElementById('datoClinicoId').value = '';
        document.getElementById('tituloFormulario').innerHTML = `<i class="ph ph-plus-circle"></i> ${esUsuarioCliente ? 'Registrar nuevo dato clínico' : 'Registrar dato clínico'}`;
        document.getElementById('btnSubmit').innerHTML = '<i class="ph ph-plus"></i>Registrar';
        document.getElementById('btnCancelar').style.display = 'none';
        document.getElementById('padecimiento').disabled = true;
    }

    document.getElementById('formDatoClinico').addEventListener('submit', function (e) {
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
        fetch('../action/datoClinicoAction.php', {method: 'POST', body: formData})
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
        const filas = document.getElementById('tablaDatosClinicos').getElementsByTagName('tbody')[0].rows;
        for (let fila of filas) {
            let mostrar = true;
            if (tipoBusqueda === 'cliente') {
                const textoBusqueda = document.getElementById('buscarCliente').value.toLowerCase();
                const carnet = fila.cells[0].textContent.toLowerCase();
                mostrar = carnet.includes(textoBusqueda);
            } else if (tipoBusqueda === 'padecimiento') {
                const padecimientoBuscado = document.getElementById('buscarPadecimiento').value.toLowerCase();
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
        const filas = document.getElementById('tablaDatosClinicos').getElementsByTagName('tbody')[0].rows;
        for (let fila of filas) {
            fila.style.display = '';
        }
    }
    <?php endif; ?>
</script>
</body>
</html>