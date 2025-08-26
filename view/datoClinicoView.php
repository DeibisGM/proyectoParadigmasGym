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
    $datosClinicos = $datoClinicoBusiness->obtenerTodosTBDatoClinicoPorCliente($_SESSION['usuario_id']);
} else {
    $datosClinicos = $datoClinicoBusiness->obtenerTBDatoClinico();
    $clientes = $datoClinicoBusiness->obtenerTodosLosClientes();
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
            <tbody>
                <?php foreach ($datosClinicos as $dato): ?>
                <tr data-id="<?php echo $dato->getTbdatoclinicoid(); ?>" data-cliente-id="<?php echo $dato->getTbclienteid(); ?>">
                    <?php if (!$esUsuarioCliente): ?>
                        <td><?php echo htmlspecialchars($dato->getCarnet()); ?></td>
                    <?php endif; ?>
                    <td class="padecimiento-cell">
                        <div class="padecimiento-display">
                            <?php echo htmlspecialchars($dato->getPadecimientosNombresString()); ?>
                        </div>
                        <div class="padecimiento-edit" style="display: none;">
                        </div>
                    </td>
                    <td>
                        <button onclick="editarRegistro(<?php echo $dato->getTbdatoclinicoid(); ?>)">Editar</button>
                        <button onclick="cancelarEdicion(<?php echo $dato->getTbdatoclinicoid(); ?>)" style="display: none;" class="btn-cancelar-edicion">Cancelar</button>
                        <button onclick="guardarEdicion(<?php echo $dato->getTbdatoclinicoid(); ?>)" style="display: none;" class="btn-guardar-edicion">Guardar</button>
                        <?php if ($esAdmin): ?>
                        <button onclick="eliminarRegistro(<?php echo $dato->getTbdatoclinicoid(); ?>)" class="btn-eliminar">Eliminar</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($datosClinicos)): ?>
        <p><?php echo $esUsuarioCliente ? 'No tiene datos clínicos registrados.' : 'No hay datos clínicos registrados.'; ?></p>
        <?php endif; ?>
    </div>

    <script>
        let padecimientosData = <?php echo json_encode($padecimientos); ?>;
        let esUsuarioCliente = <?php echo $esUsuarioCliente ? 'true' : 'false'; ?>;

        function cargarPadecimientosPorTipo() {
            const tipoSeleccionado = document.getElementById('tipoPadecimiento').value;
            const selectPadecimiento = document.getElementById('padecimiento');

            console.log('Tipo seleccionado:', tipoSeleccionado);
            console.log('Padecimientos disponibles:', padecimientosData);

            selectPadecimiento.innerHTML = '<option value="">Seleccione un padecimiento</option>';

            if (tipoSeleccionado) {
                const padecimientosFiltrados = padecimientosData.filter(p =>
                    p.tbpadecimientotipo === tipoSeleccionado
                );

                console.log('Padecimientos filtrados:', padecimientosFiltrados);

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

            // Agregar el padecimiento como array para compatibilidad con el backend
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

        let padecimientoSeleccionadoEdicion = {};

        function editarRegistro(id) {
            const fila = document.querySelector(`tr[data-id="${id}"]`);
            const padecimientoDisplay = fila.querySelector('.padecimiento-display');
            const padecimientoEdit = fila.querySelector('.padecimiento-edit');

            const btnEditar = fila.querySelector('button[onclick*="editarRegistro"]');
            const btnCancelar = fila.querySelector('.btn-cancelar-edicion');
            const btnGuardar = fila.querySelector('.btn-guardar-edicion');
            const btnEliminar = fila.querySelector('.btn-eliminar');

            btnEditar.style.display = 'none';
            btnCancelar.style.display = 'inline';
            btnGuardar.style.display = 'inline';
            if (btnEliminar) btnEliminar.style.display = 'none';

            padecimientoDisplay.style.display = 'none';

            padecimientoEdit.innerHTML = `
                <div>
                    <label>Tipo:</label>
                    <select id="tipo-edit-${id}" onchange="cargarPadecimientosPorTipoEdicion(${id})">
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
                    <select id="padecimiento-edit-${id}" disabled>
                        <option value="">Primero seleccione un tipo</option>
                    </select>
                </div>
            `;

            padecimientoEdit.style.display = 'block';
        }

        function cargarPadecimientosPorTipoEdicion(id) {
            const tipoSeleccionado = document.getElementById(`tipo-edit-${id}`).value;
            const selectPadecimiento = document.getElementById(`padecimiento-edit-${id}`);

            console.log('Cargando padecimientos para edición, tipo:', tipoSeleccionado);

            selectPadecimiento.innerHTML = '<option value="">Seleccione un padecimiento</option>';

            if (tipoSeleccionado) {
                const padecimientosFiltrados = padecimientosData.filter(p =>
                    p.tbpadecimientotipo === tipoSeleccionado
                );

                console.log('Padecimientos filtrados para edición:', padecimientosFiltrados);

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

        function guardarEdicion(id) {
            const padecimientoSeleccionado = document.getElementById(`padecimiento-edit-${id}`).value;

            if (!padecimientoSeleccionado) {
                mostrarMensaje('Error: Debe seleccionar un padecimiento.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('update', '1');
            formData.append('id', id);

            if (esUsuarioCliente) {
                formData.append('clienteId', '<?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0; ?>');
            } else {
                formData.append('clienteId', obtenerClienteIdDelRegistro(id));
            }

            formData.append('padecimientosIds[]', padecimientoSeleccionado);

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

        function obtenerClienteIdDelRegistro(id) {
            const fila = document.querySelector(`tr[data-id="${id}"]`);
            return fila.getAttribute('data-cliente-id');
        }

        function cancelarEdicion(id) {
            const fila = document.querySelector(`tr[data-id="${id}"]`);
            const padecimientoDisplay = fila.querySelector('.padecimiento-display');
            const padecimientoEdit = fila.querySelector('.padecimiento-edit');

            const btnEditar = fila.querySelector('button[onclick*="editarRegistro"]');
            const btnCancelar = fila.querySelector('.btn-cancelar-edicion');
            const btnGuardar = fila.querySelector('.btn-guardar-edicion');
            const btnEliminar = fila.querySelector('.btn-eliminar');

            btnEditar.style.display = 'inline';
            btnCancelar.style.display = 'none';
            btnGuardar.style.display = 'none';
            if (btnEliminar) btnEliminar.style.display = 'inline';

            padecimientoDisplay.style.display = 'block';
            padecimientoEdit.style.display = 'none';
            padecimientoEdit.innerHTML = '';
        }

        function eliminarRegistro(id) {
            if (!confirm('¿Está seguro de que desea eliminar este registro? Esta acción no se puede deshacer.')) {
                return;
            }

            const formData = new FormData();
            formData.append('delete', '1');
            formData.append('id', id);

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
                    const padecimientos = filas[i].cells[1].textContent.toLowerCase();
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