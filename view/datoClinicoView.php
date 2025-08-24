<?php
session_start();

// Verificar que el usuario esté logueado
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

// Obtener datos necesarios
$padecimientosObj = $padecimientoBusiness->obtenerTbpadecimiento();
$tiposPadecimiento = $padecimientoBusiness->obtenerTiposPadecimiento();

// Convertir objetos Padecimiento a array para JavaScript
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
    $datosClinicos = array();
    $datoExistente = $datoClinicoBusiness->obtenerTBDatoClinicoPorCliente($_SESSION['usuario_id']);
    if ($datoExistente) {
        $datosClinicos = array($datoExistente);
    }
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
    <!-- Mensajes de respuesta -->
    <div id="mensaje" style="display: none; padding: 10px; margin: 10px 0; border: 1px solid; border-radius: 5px;"></div>

    <?php if (!$esUsuarioCliente): ?>
    <!-- Filtros para Admin e Instructor -->
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

    <!-- Formulario para registrar/actualizar datos clínicos -->
    <div id="formularioContainer">
        <h3 id="tituloFormulario">
            <?php echo $esUsuarioCliente ? 'Registrar mis datos clínicos' : 'Registrar datos clínicos'; ?>
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
                <label>Padecimientos:</label>
                <select id="padecimiento" onchange="agregarPadecimiento()" disabled>
                    <option value="">Primero seleccione un tipo</option>
                </select>
            </div>

            <!-- Lista de padecimientos seleccionados -->
            <div id="padecimientosSeleccionados">
                <h4>Padecimientos seleccionados:</h4>
                <ul id="listaPadecimientos"></ul>
                <input type="hidden" id="padecimientosIds" name="padecimientosIds">
            </div>

            <div>
                <button type="submit" id="btnSubmit">Registrar</button>
                <button type="button" onclick="limpiarFormulario()" id="btnCancelar" style="display: none;">Cancelar</button>
            </div>
        </form>
    </div>

    <hr>

    <!-- Tabla de datos clínicos -->
    <div>
        <h3><?php echo $esUsuarioCliente ? 'Mis datos clínicos' : 'Datos clínicos de todos los clientes'; ?></h3>

        <table border="1" id="tablaDatosClinicos">
            <thead>
                <tr>
                    <?php if (!$esUsuarioCliente): ?>
                        <th>Carnet</th>
                    <?php endif; ?>
                    <th>Padecimientos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datosClinicos as $dato): ?>
                <tr data-id="<?php echo $dato->getTbdatoclinicoid(); ?>" data-cliente-id="<?php echo $dato->getTbclienteid(); ?>">
                    <?php if (!$esUsuarioCliente): ?>
                        <td><?php echo htmlspecialchars($dato->getCarnet()); ?></td>
                    <?php endif; ?>
                    <td class="padecimientos-cell">
                        <div class="padecimientos-display">
                            <?php echo htmlspecialchars($dato->getPadecimientosNombresString()); ?>
                        </div>
                        <div class="padecimientos-edit" style="display: none;">
                            <!-- Aquí se cargará dinámicamente el formulario de edición -->
                        </div>
                    </td>
                    <td>
                        <button onclick="editarRegistro(<?php echo $dato->getTbdatoclinicoid(); ?>)">Editar</button>
                        <?php if ($esAdmin): ?>
                        <button onclick="eliminarRegistro(<?php echo $dato->getTbdatoclinicoid(); ?>)">Eliminar</button>
                        <?php endif; ?>
                        <button onclick="cancelarEdicion(<?php echo $dato->getTbdatoclinicoid(); ?>)" style="display: none;" class="btn-cancelar-edicion">Cancelar</button>
                        <button onclick="guardarEdicion(<?php echo $dato->getTbdatoclinicoid(); ?>)" style="display: none;" class="btn-guardar-edicion">Guardar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($datosClinicos)): ?>
        <p>No hay datos clínicos registrados.</p>
        <?php endif; ?>
    </div>

    <script>
        let padecimientosData = <?php echo json_encode($padecimientos); ?>;
        let padecimientosSeleccionados = [];
        let esUsuarioCliente = <?php echo $esUsuarioCliente ? 'true' : 'false'; ?>;

        // Función para cargar padecimientos por tipo
        function cargarPadecimientosPorTipo() {
            const tipoSeleccionado = document.getElementById('tipoPadecimiento').value;
            const selectPadecimiento = document.getElementById('padecimiento');

            console.log('Tipo seleccionado:', tipoSeleccionado);
            console.log('Padecimientos disponibles:', padecimientosData);

            // Limpiar opciones
            selectPadecimiento.innerHTML = '<option value="">Seleccione un padecimiento</option>';

            if (tipoSeleccionado) {
                // Filtrar padecimientos por tipo
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

        // Función para agregar padecimiento a la lista
        function agregarPadecimiento() {
            const selectPadecimiento = document.getElementById('padecimiento');
            const padecimientoId = selectPadecimiento.value;

            if (padecimientoId && !padecimientosSeleccionados.includes(padecimientoId)) {
                const nombrePadecimiento = selectPadecimiento.options[selectPadecimiento.selectedIndex].text;

                padecimientosSeleccionados.push(padecimientoId);

                // Agregar a la lista visual
                const lista = document.getElementById('listaPadecimientos');
                const li = document.createElement('li');
                li.innerHTML = `
                    ${nombrePadecimiento}
                    <button type="button" onclick="removerPadecimiento('${padecimientoId}', this)">Quitar</button>
                `;
                lista.appendChild(li);

                // Actualizar input hidden
                document.getElementById('padecimientosIds').value = padecimientosSeleccionados.join(',');

                // Resetear select
                selectPadecimiento.value = '';
            }
        }

        // Función para remover padecimiento de la lista
        function removerPadecimiento(padecimientoId, button) {
            const index = padecimientosSeleccionados.indexOf(padecimientoId);
            if (index > -1) {
                padecimientosSeleccionados.splice(index, 1);
                button.parentElement.remove();
                document.getElementById('padecimientosIds').value = padecimientosSeleccionados.join(',');
            }
        }

        // Función para limpiar el formulario
        function limpiarFormulario() {
            document.getElementById('formDatoClinico').reset();
            document.getElementById('accion').value = 'create';
            document.getElementById('datoClinicoId').value = '';
            document.getElementById('tituloFormulario').textContent = esUsuarioCliente ? 'Registrar mis datos clínicos' : 'Registrar datos clínicos';
            document.getElementById('btnSubmit').textContent = 'Registrar';
            document.getElementById('btnCancelar').style.display = 'none';

            padecimientosSeleccionados = [];
            document.getElementById('listaPadecimientos').innerHTML = '';
            document.getElementById('padecimientosIds').value = '';
            document.getElementById('padecimiento').disabled = true;
        }

        // Manejar envío del formulario
        document.getElementById('formDatoClinico').addEventListener('submit', function(e) {
            e.preventDefault();

            if (padecimientosSeleccionados.length === 0) {
                mostrarMensaje('Error: Debe seleccionar al menos un padecimiento.', 'error');
                return;
            }

            const formData = new FormData(this);
            const accion = document.getElementById('accion').value;

            // Agregar los padecimientos como array
            formData.delete('padecimientosIds');
            padecimientosSeleccionados.forEach(id => {
                formData.append('padecimientosIds[]', id);
            });

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

        // Función para mostrar mensajes
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

        // Variables para manejar edición
        let padecimientosSeleccionadosEdicion = {};

        // Función para editar registro desde la tabla
        function editarRegistro(id) {
            const fila = document.querySelector(`tr[data-id="${id}"]`);
            const padecimientosDisplay = fila.querySelector('.padecimientos-display');
            const padecimientosEdit = fila.querySelector('.padecimientos-edit');

            // Ocultar botón editar, mostrar botones guardar y cancelar
            const btnEditar = fila.querySelector('button[onclick*="editarRegistro"]');
            const btnCancelar = fila.querySelector('.btn-cancelar-edicion');
            const btnGuardar = fila.querySelector('.btn-guardar-edicion');

            btnEditar.style.display = 'none';
            btnCancelar.style.display = 'inline';
            btnGuardar.style.display = 'inline';

            // Cambiar a modo edición
            padecimientosDisplay.style.display = 'none';

            // Inicializar array de padecimientos para este registro
            padecimientosSeleccionadosEdicion[id] = [];

            // Crear formulario de edición
            padecimientosEdit.innerHTML = `
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
                    <select id="padecimiento-edit-${id}" onchange="agregarPadecimientoEdicion(${id})" disabled>
                        <option value="">Primero seleccione un tipo</option>
                    </select>
                </div>
                <div>
                    <h5>Padecimientos seleccionados:</h5>
                    <ul id="lista-padecimientos-edit-${id}"></ul>
                </div>
            `;

            padecimientosEdit.style.display = 'block';
        }

        // Función para cargar padecimientos por tipo en edición
        function cargarPadecimientosPorTipoEdicion(id) {
            const tipoSeleccionado = document.getElementById(`tipo-edit-${id}`).value;
            const selectPadecimiento = document.getElementById(`padecimiento-edit-${id}`);

            console.log('Cargando padecimientos para edición, tipo:', tipoSeleccionado);

            // Limpiar opciones
            selectPadecimiento.innerHTML = '<option value="">Seleccione un padecimiento</option>';

            if (tipoSeleccionado) {
                // Filtrar padecimientos por tipo
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

        // Función para agregar padecimiento en edición
        function agregarPadecimientoEdicion(id) {
            const selectPadecimiento = document.getElementById(`padecimiento-edit-${id}`);
            const padecimientoId = selectPadecimiento.value;

            if (padecimientoId && !padecimientosSeleccionadosEdicion[id].includes(padecimientoId)) {
                const nombrePadecimiento = selectPadecimiento.options[selectPadecimiento.selectedIndex].text;

                padecimientosSeleccionadosEdicion[id].push(padecimientoId);

                // Agregar a la lista visual
                const lista = document.getElementById(`lista-padecimientos-edit-${id}`);
                const li = document.createElement('li');
                li.innerHTML = `
                    ${nombrePadecimiento}
                    <button type="button" onclick="removerPadecimientoEdicion(${id}, '${padecimientoId}', this)">Quitar</button>
                `;
                lista.appendChild(li);

                // Resetear select
                selectPadecimiento.value = '';
            }
        }

        // Función para remover padecimiento en edición
        function removerPadecimientoEdicion(id, padecimientoId, button) {
            const index = padecimientosSeleccionadosEdicion[id].indexOf(padecimientoId);
            if (index > -1) {
                padecimientosSeleccionadosEdicion[id].splice(index, 1);
                button.parentElement.remove();
            }
        }

        // Función para guardar edición
        function guardarEdicion(id) {
            if (!padecimientosSeleccionadosEdicion[id] || padecimientosSeleccionadosEdicion[id].length === 0) {
                mostrarMensaje('Error: Debe seleccionar al menos un padecimiento.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('update', '1');
            formData.append('id', id);

            // Si es cliente, obtener su ID de la sesión
            if (esUsuarioCliente) {
                formData.append('clienteId', '<?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0; ?>');
            } else {
                // Para admin/instructor, necesitaríamos obtener el clienteId del registro actual
                // Por ahora asumimos que se mantiene el mismo cliente
                formData.append('clienteId', obtenerClienteIdDelRegistro(id));
            }

            // Agregar padecimientos como array
            padecimientosSeleccionadosEdicion[id].forEach(padecimientoId => {
                formData.append('padecimientosIds[]', padecimientoId);
            });

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

        // Función auxiliar para obtener el ID del cliente de un registro (para admin/instructor)
        function obtenerClienteIdDelRegistro(id) {
            const fila = document.querySelector(`tr[data-id="${id}"]`);
            return fila.getAttribute('data-cliente-id');
        }

        // Función para cancelar edición
        function cancelarEdicion(id) {
            const fila = document.querySelector(`tr[data-id="${id}"]`);
            const padecimientosDisplay = fila.querySelector('.padecimientos-display');
            const padecimientosEdit = fila.querySelector('.padecimientos-edit');

            // Mostrar botón editar, ocultar botones guardar y cancelar
            const btnEditar = fila.querySelector('button[onclick*="editarRegistro"]');
            const btnCancelar = fila.querySelector('.btn-cancelar-edicion');
            const btnGuardar = fila.querySelector('.btn-guardar-edicion');

            btnEditar.style.display = 'inline';
            btnCancelar.style.display = 'none';
            btnGuardar.style.display = 'none';

            // Restaurar vista normal
            padecimientosDisplay.style.display = 'block';
            padecimientosEdit.style.display = 'none';
            padecimientosEdit.innerHTML = '';

            // Limpiar array de padecimientos de edición
            if (padecimientosSeleccionadosEdicion[id]) {
                delete padecimientosSeleccionadosEdicion[id];
            }
        }

        // Función para eliminar registro
        function eliminarRegistro(id) {
            if (confirm('¿Está seguro de que desea eliminar este registro?')) {
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
        }

        // Funciones para filtros (solo para admin e instructor)
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