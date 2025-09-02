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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Gestión de Datos Clínicos</h1>
<header>
    <a href="../index.php">Volver al Inicio</a>
</header>

    <div id="mensaje" style="display: none; padding: 10px; margin: 10px 0; border: 1px solid; border-radius: 5px;"></div>

    <?php if (!$esUsuarioCliente): ?>

    <div>
        <h3>Filtros de búsqueda</h3>
        <label>Buscar por:</label>
        <select id="tipoBusqueda">
            <option value="todos">Todos los registros</option>
            <option value="cliente">Por cliente</option>
            <option value="padecimiento">Por padecimiento</option>
        </select>

        <div id="filtroCliente" style="display: none;">
            <label>Cliente:</label>
            <input type="text" id="buscarCliente" placeholder="Escriba el nombre o carnet del cliente">
        </div>

        <div id="filtroPadecimiento" style="display: none;">
            <label>Padecimiento:</label>
            <select id="buscarPadecimiento">
                <option value="">Seleccione un padecimiento</option>
                <?php foreach ($padecimientos as $padecimiento): ?>
                    <option value="<?php echo htmlspecialchars($padecimiento['tbpadecimientonombre']); ?>">
                        <?php echo htmlspecialchars($padecimiento['tbpadecimientonombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="button" onclick="aplicarFiltros()">Aplicar Filtro</button>
        <button type="button" onclick="limpiarFiltros()">Limpiar Filtros</button>
    </div>
    <hr>
    <?php endif; ?>

    <div id="formularioContainer">
        <h3 id="tituloFormulario">
            <?php echo $esUsuarioCliente ? 'Registrar nuevo dato clínico' : 'Registrar dato clínico'; ?>
        </h3>

        <form id="formDatoClinico">
            <input type="hidden" id="accion" name="accion" value="create">
            <input type="hidden" id="datoClinicoId" name="id" value="">

            <?php if (!$esUsuarioCliente): ?>
            <div>
                <label>Cliente:</label>
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
                <label>Tipo de padecimiento:</label>
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
                <label>Padecimiento:</label>
                <select id="padecimiento" name="padecimientoId" disabled required>
                    <option value="">Primero seleccione un tipo</option>
                </select>
            </div>

            <div>
                <button type="submit" id="btnSubmit">Registrar</button>
                <button type="button" onclick="limpiarFormulario()" id="btnCancelar" style="display: none;">Cancelar</button>
            </div>
        </form>
    </div>

    <hr>

    <div>
        <h3><?php echo $esUsuarioCliente ? 'Mis datos clínicos' : 'Datos clínicos de todos los clientes'; ?></h3>

        <table border="1" id="tablaDatosClinicos">
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

            </tbody>
        </table>

        <div id="mensajeVacio" style="display: none;">
            <p><?php echo $esUsuarioCliente ? 'No tiene datos clínicos registrados.' : 'No hay datos clínicos registrados.'; ?></p>
        </div>
    </div>

    <script>
        let padecimientosData = <?php echo json_encode($padecimientos); ?>;
        let esUsuarioCliente = <?php echo $esUsuarioCliente ? 'true' : 'false'; ?>;
        let esAdmin = <?php echo $esAdmin ? 'true' : 'false'; ?>;
        let datosClinicos = <?php echo json_encode($datosClinicos); ?>;

        console.log('Datos clínicos recibidos de PHP:', datosClinicos);
        console.log('Padecimientos recibidos:', padecimientosData);

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

        console.log('Datos procesados para JavaScript:', datosCli);

        // Inicializar la interfaz al cargar la página
        window.onload = function() {
            cargarDatosEnTabla();
        };

        function cargarPadecimientosPorTipo() {
            const tipoSeleccionado = document.getElementById('tipoPadecimiento').value;
            const selectPadecimiento = document.getElementById('padecimiento');

            selectPadecimiento.innerHTML = '<option value="">Seleccione un padecimiento</option>';

            if (tipoSeleccionado) {
                const padecimientosFiltrados = padecimientosData.filter(p =>
                    p.tbpadecimientotipo === tipoSeleccionado
                );

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
            console.log('Iniciando carga de datos en tabla...');
            const tbody = document.getElementById('tablaBody');
            tbody.innerHTML = '';

            if (datosCli.length === 0) {
                console.log('No hay datos para mostrar');
                document.getElementById('mensajeVacio').style.display = 'block';
                return;
            }

            document.getElementById('mensajeVacio').style.display = 'none';

            datosCli.forEach((dato, datoIndex) => {
                console.log(`Procesando dato ${datoIndex}:`, dato);

                if (!dato.padecimientosIds || dato.padecimientosIds.length === 0) {
                    console.warn(`Registro ${datoIndex} sin padecimientos:`, dato);
                    return;
                }

                dato.padecimientosIds.forEach((padecimientoId, index) => {
                    if (padecimientoId && padecimientoId.trim() !== '') {
                        console.log(`Creando fila para padecimiento ${padecimientoId}`);

                        const padecimientoObj = padecimientosData.find(p => p.tbpadecimientoid == padecimientoId);
                        const nombrePadecimiento = padecimientoObj ? padecimientoObj.tbpadecimientonombre : `ID: ${padecimientoId}`;

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
                                <div class="padecimiento-display">
                                    ${nombrePadecimiento}
                                </div>
                                <div class="padecimiento-edit" style="display: none;">
                                    <div>
                                        <label>Tipo:</label>
                                        <select id="tipo-edit-${dato.id}-${index}" onchange="cargarPadecimientosPorTipoEdicion(${dato.id}, ${index})">
                                            <option value="">Seleccione un tipo</option>
                                            <?php foreach ($tiposPadecimiento as $tipo): ?>
                                                <option value="<?php echo htmlspecialchars($tipo); ?>">
                                                    <?php echo htmlspecialchars($tipo); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label>Padecimiento:</label>
                                        <select id="padecimiento-edit-${dato.id}-${index}" disabled>
                                            <option value="">Primero seleccione un tipo</option>
                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button onclick="editarPadecimientoIndividual(${dato.id}, ${index}, '${padecimientoId}')" class="btn-editar">Editar</button>
                                <button onclick="cancelarEdicionIndividual(${dato.id}, ${index})" style="display: none;" class="btn-cancelar-edicion">Cancelar</button>
                                <button onclick="guardarEdicionIndividual(${dato.id}, ${index}, '${padecimientoId}')" style="display: none;" class="btn-guardar-edicion">Guardar</button>
                        `;

                        if (esAdmin) {
                            contenidoFila += `<button onclick="eliminarPadecimientoIndividual(${dato.id}, '${padecimientoId}', '${nombrePadecimiento}')" class="btn-eliminar">Eliminar</button>`;
                        }

                        contenidoFila += `</td>`;
                        fila.innerHTML = contenidoFila;
                        tbody.appendChild(fila);

                        console.log('Fila agregada a la tabla');
                    }
                });
            });

            console.log('Finalizada carga de datos en tabla');
        }

        function editarPadecimientoIndividual(registroId, index, padecimientoId) {
            const fila = document.querySelector(`tr[data-registro-id="${registroId}"][data-padecimiento-index="${index}"]`);
            const padecimientoDisplay = fila.querySelector('.padecimiento-display');
            const padecimientoEdit = fila.querySelector('.padecimiento-edit');

            const btnEditar = fila.querySelector('.btn-editar');
            const btnCancelar = fila.querySelector('.btn-cancelar-edicion');
            const btnGuardar = fila.querySelector('.btn-guardar-edicion');
            const btnEliminar = fila.querySelector('.btn-eliminar');

            btnEditar.style.display = 'none';
            btnCancelar.style.display = 'inline';
            btnGuardar.style.display = 'inline';
            if (btnEliminar) btnEliminar.style.display = 'none';

            padecimientoDisplay.style.display = 'none';
            padecimientoEdit.style.display = 'block';

            const padecimientoActual = padecimientosData.find(p => p.tbpadecimientoid == padecimientoId);
            if (padecimientoActual) {
                const tipoSelect = document.getElementById(`tipo-edit-${registroId}-${index}`);
                tipoSelect.value = padecimientoActual.tbpadecimientotipo;
                cargarPadecimientosPorTipoEdicion(registroId, index);

                setTimeout(() => {
                    const padecimientoSelect = document.getElementById(`padecimiento-edit-${registroId}-${index}`);
                    padecimientoSelect.value = padecimientoId;
                }, 100);
            }
        }

        function cargarPadecimientosPorTipoEdicion(registroId, index) {
            const tipoSeleccionado = document.getElementById(`tipo-edit-${registroId}-${index}`).value;
            const selectPadecimiento = document.getElementById(`padecimiento-edit-${registroId}-${index}`);

            selectPadecimiento.innerHTML = '<option value="">Seleccione un padecimiento</option>';

            if (tipoSeleccionado) {
                const padecimientosFiltrados = padecimientosData.filter(p =>
                    p.tbpadecimientotipo === tipoSeleccionado
                );

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

            if (esUsuarioCliente) {
                formData.append('clienteId', '<?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0; ?>');
            } else {
                formData.append('clienteId', obtenerClienteIdDelRegistro(registroId));
            }

            fetch('../action/datoClinicoAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión.', 'error');
            });
        }

        function cancelarEdicionIndividual(registroId, index) {
            const fila = document.querySelector(`tr[data-registro-id="${registroId}"][data-padecimiento-index="${index}"]`);
            const padecimientoDisplay = fila.querySelector('.padecimiento-display');
            const padecimientoEdit = fila.querySelector('.padecimiento-edit');

            const btnEditar = fila.querySelector('.btn-editar');
            const btnCancelar = fila.querySelector('.btn-cancelar-edicion');
            const btnGuardar = fila.querySelector('.btn-guardar-edicion');
            const btnEliminar = fila.querySelector('.btn-eliminar');

            btnEditar.style.display = 'inline';
            btnCancelar.style.display = 'none';
            btnGuardar.style.display = 'none';
            if (btnEliminar) btnEliminar.style.display = 'inline';

            padecimientoDisplay.style.display = 'block';
            padecimientoEdit.style.display = 'none';
        }

        function eliminarPadecimientoIndividual(registroId, padecimientoId, nombrePadecimiento) {
            if (!confirm(`¿Está seguro de que desea eliminar el padecimiento "${nombrePadecimiento}"? Esta acción no se puede deshacer.`)) {
                return;
            }

            const formData = new FormData();
            formData.append('deleteIndividual', '1');
            formData.append('registroId', registroId);
            formData.append('padecimientoId', padecimientoId);

            fetch('../action/datoClinicoAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión.', 'error');
            });
        }

        function obtenerClienteIdDelRegistro(registroId) {
            const fila = document.querySelector(`tr[data-registro-id="${registroId}"]`);
            return fila.getAttribute('data-cliente-id');
        }

        function limpiarFormulario() {
            document.getElementById('formDatoClinico').reset();
            document.getElementById('accion').value = 'create';
            document.getElementById('datoClinicoId').value = '';
            document.getElementById('tituloFormulario').textContent = esUsuarioCliente ? 'Registrar nuevo dato clínico' : 'Registrar dato clínico';
            document.getElementById('btnSubmit').textContent = 'Registrar';
            document.getElementById('btnCancelar').style.display = 'none';

            document.getElementById('padecimiento').disabled = true;
        }

        document.getElementById('formDatoClinico').addEventListener('submit', function(e) {
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

            fetch('../action/datoClinicoAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    limpiarFormulario();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión.', 'error');
            });
        });

        function mostrarMensaje(mensaje, tipo) {
            const divMensaje = document.getElementById('mensaje');
            divMensaje.textContent = mensaje;
            divMensaje.style.display = 'block';

            if (tipo === 'success') {
                divMensaje.style.backgroundColor = '#d4edda';
                divMensaje.style.color = '#155724';
                divMensaje.style.borderColor = '#c3e6cb';
            } else {
                divMensaje.style.backgroundColor = '#f8d7da';
                divMensaje.style.color = '#721c24';
                divMensaje.style.borderColor = '#f5c6cb';
            }

            setTimeout(() => {
                divMensaje.style.display = 'none';
            }, 5000);
        }

        <?php if (!$esUsuarioCliente): ?>
        document.getElementById('tipoBusqueda').addEventListener('change', function() {
            const tipo = this.value;
            document.getElementById('filtroCliente').style.display = tipo === 'cliente' ? 'block' : 'none';
            document.getElementById('filtroPadecimiento').style.display = tipo === 'padecimiento' ? 'block' : 'none';
        });

        function aplicarFiltros() {
            const tipoBusqueda = document.getElementById('tipoBusqueda').value;
            const tabla = document.getElementById('tablaDatosClinicos').getElementsByTagName('tbody')[0];
            const filas = tabla.getElementsByTagName('tr');

            for (let i = 0; i < filas.length; i++) {
                let mostrar = true;

                if (tipoBusqueda === 'cliente') {
                    const textoBusqueda = document.getElementById('buscarCliente').value.toLowerCase();
                    const carnet = filas[i].cells[0].textContent.toLowerCase();
                    mostrar = carnet.includes(textoBusqueda);
                } else if (tipoBusqueda === 'padecimiento') {
                    const padecimientoBuscado = document.getElementById('buscarPadecimiento').value.toLowerCase();
                    const padecimientos = filas[i].cells[esUsuarioCliente ? 0 : 1].textContent.toLowerCase();
                    mostrar = padecimientos.includes(padecimientoBuscado);
                }

                filas[i].style.display = mostrar ? '' : 'none';
            }
        }

        function limpiarFiltros() {
            document.getElementById('tipoBusqueda').value = 'todos';
            document.getElementById('buscarCliente').value = '';
            document.getElementById('buscarPadecimiento').value = '';
            document.getElementById('filtroCliente').style.display = 'none';
            document.getElementById('filtroPadecimiento').style.display = 'none';

            const tabla = document.getElementById('tablaDatosClinicos').getElementsByTagName('tbody')[0];
            const filas = tabla.getElementsByTagName('tr');

            for (let i = 0; i < filas.length; i++) {
                filas[i].style.display = '';
            }
        }
        <?php endif; ?>
    </script>
</body>
</html>