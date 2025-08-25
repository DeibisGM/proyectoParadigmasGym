<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../view/login.php');
    exit();
}

// Verificar permisos - Solo administradores pueden gestionar padecimientos
$esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';

if (!$esAdmin) {
    echo "<h1>Acceso Denegado</h1>";
    echo "<p>Solo los administradores pueden gestionar padecimientos.</p>";
    echo "<p><a href='../index.php'>Volver al Inicio</a></p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Padecimientos</title>
</head>
<body>
    <h1>Gestión de Padecimientos - Panel de Administrador</h1>

    <header>
        <a href="../index.php">Volver al Inicio</a>
    </header>

    <!-- Sección de registro de padecimientos -->
    <div id="registroSection">
        <h2>Registrar Nuevo Padecimiento</h2>
        <form id="padecimientoForm">
            <table border="1">
                <tr>
                    <td><label for="tipo">Tipo de Padecimiento:</label></td>
                    <td>
                        <select id="tipo" name="tipo" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="Enfermedad">Enfermedad</option>
                            <option value="Lesión">Lesión</option>
                            <option value="Discapacidad">Discapacidad</option>
                            <option value="Condición médica">Condición médica</option>
                            <option value="Trastorno">Trastorno</option>
                            <option value="Síndrome">Síndrome</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="nombre">Nombre:</label></td>
                    <td><input type="text" id="nombre" name="nombre" required maxlength="100" placeholder="Nombre del padecimiento"></td>
                </tr>
                <tr>
                    <td><label for="descripcion">Descripción:</label></td>
                    <td><textarea id="descripcion" name="descripcion" required maxlength="500" rows="3" placeholder="Descripción detallada del padecimiento"></textarea></td>
                </tr>
                <tr>
                    <td><label for="formaDeActuar">Forma de Actuar:</label></td>
                    <td><textarea id="formaDeActuar" name="formaDeActuar" required maxlength="1000" rows="4" placeholder="Instrucciones sobre cómo actuar ante este padecimiento"></textarea></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button type="submit">Registrar Padecimiento</button>
                        <button type="button" onclick="limpiarFormulario()">Limpiar</button>
                    </td>
                </tr>
            </table>
        </form>
        <div id="mensaje"></div>
    </div>

    <hr>

    <!-- Sección de tabla de padecimientos -->
    <div id="tablaSection">
        <h2>Padecimientos Registrados</h2>
        <button onclick="cargarPadecimientos()">Actualizar Lista</button>
        <br><br>
        <table border="1" id="tablaPadecimientos" width="100%">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Forma de Actuar</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="padecimientos-tbody">
                <!-- Los datos se cargarán dinámicamente -->
            </tbody>
        </table>
    </div>

    <script>
        // Variables globales
        let padecimientos = [];
        let modoEdicion = false;
        let idEditando = null;

        // Cargar padecimientos al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarPadecimientos();
        });

        // Manejar envío del formulario
        document.getElementById('padecimientoForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validarFormulario()) {
                return;
            }

            if (modoEdicion) {
                actualizarPadecimiento();
            } else {
                registrarPadecimiento();
            }
        });

        // Función para registrar nuevo padecimiento
        function registrarPadecimiento() {
            const formData = new FormData();
            formData.append('create', '1');
            formData.append('tipo', document.getElementById('tipo').value);
            formData.append('nombre', document.getElementById('nombre').value);
            formData.append('descripcion', document.getElementById('descripcion').value);
            formData.append('formaDeActuar', document.getElementById('formaDeActuar').value);

            fetch('../action/padecimientoAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarMensaje(data.message, data.success);
                if (data.success) {
                    limpiarFormulario();
                    cargarPadecimientos();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al procesar la solicitud', false);
            });
        }

        // Función para actualizar padecimiento
        function actualizarPadecimiento() {
            const formData = new FormData();
            formData.append('update', '1');
            formData.append('id', idEditando);
            formData.append('tipo', document.getElementById('tipo').value);
            formData.append('nombre', document.getElementById('nombre').value);
            formData.append('descripcion', document.getElementById('descripcion').value);
            formData.append('formaDeActuar', document.getElementById('formaDeActuar').value);

            fetch('../action/padecimientoAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarMensaje(data.message, data.success);
                if (data.success) {
                    cancelarEdicionFormulario();
                    cargarPadecimientos();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al procesar la solicitud', false);
            });
        }

        // Función para cargar padecimientos
        function cargarPadecimientos() {
            fetch('../action/padecimientoAction.php?getPadecimientos=1')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    padecimientos = data.data;
                    mostrarPadecimientos();
                } else {
                    mostrarMensaje('Error al cargar padecimientos: ' + data.message, false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al cargar padecimientos', false);
            });
        }

        // Función para mostrar padecimientos en la tabla
        function mostrarPadecimientos() {
            const tbody = document.getElementById('padecimientos-tbody');
            tbody.innerHTML = '';

            if (padecimientos.length === 0) {
                const row = tbody.insertRow();
                row.innerHTML = '<td colspan="5">No hay padecimientos registrados</td>';
                return;
            }

            padecimientos.forEach(padecimiento => {
                const row = tbody.insertRow();
                row.setAttribute('data-id', padecimiento.id);
                row.innerHTML = `
                    <td>
                        <select class="edit-tipo" disabled>
                            <option value="Enfermedad" ${padecimiento.tipo === 'Enfermedad' ? 'selected' : ''}>Enfermedad</option>
                            <option value="Lesión" ${padecimiento.tipo === 'Lesión' ? 'selected' : ''}>Lesión</option>
                            <option value="Discapacidad" ${padecimiento.tipo === 'Discapacidad' ? 'selected' : ''}>Discapacidad</option>
                            <option value="Condición médica" ${padecimiento.tipo === 'Condición médica' ? 'selected' : ''}>Condición médica</option>
                            <option value="Trastorno" ${padecimiento.tipo === 'Trastorno' ? 'selected' : ''}>Trastorno</option>
                            <option value="Síndrome" ${padecimiento.tipo === 'Síndrome' ? 'selected' : ''}>Síndrome</option>
                            <option value="Otro" ${padecimiento.tipo === 'Otro' ? 'selected' : ''}>Otro</option>
                        </select>
                    </td>
                    <td><input type="text" class="edit-nombre" value="${padecimiento.nombre}" disabled maxlength="100"></td>
                    <td><textarea class="edit-descripcion" disabled maxlength="500" rows="2">${padecimiento.descripcion}</textarea></td>
                    <td><textarea class="edit-forma" disabled maxlength="1000" rows="2">${padecimiento.formaDeActuar}</textarea></td>
                    <td>
                        <button onclick="habilitarEdicion(${padecimiento.id})">Editar</button>
                        <button onclick="guardarCambios(${padecimiento.id})" style="display: none;">Guardar</button>
                        <button onclick="cancelarEdicion(${padecimiento.id})" style="display: none;">Cancelar</button>
                        <br>
                        <button onclick="eliminarPadecimiento(${padecimiento.id})">Eliminar</button>
                        <button onclick="editarEnFormulario(${padecimiento.id})">Editar en Formulario</button>
                    </td>
                `;
            });
        }

        // Función para habilitar edición en la tabla
        function habilitarEdicion(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (!row) return;

            // Habilitar campos de edición
            const tipo = row.querySelector('.edit-tipo');
            const nombre = row.querySelector('.edit-nombre');
            const descripcion = row.querySelector('.edit-descripcion');
            const forma = row.querySelector('.edit-forma');

            tipo.disabled = false;
            nombre.disabled = false;
            descripcion.disabled = false;
            forma.disabled = false;

            // Mostrar/ocultar botones
            const botones = row.querySelectorAll('button');
            botones[0].style.display = 'none'; // Editar
            botones[1].style.display = 'inline'; // Guardar
            botones[2].style.display = 'inline'; // Cancelar
            botones[3].disabled = true; // Deshabilitar eliminar mientras edita
            botones[4].disabled = true; // Deshabilitar "Editar en Formulario" mientras edita
        }

        // Función para editar en el formulario principal
        function editarEnFormulario(id) {
            const padecimiento = padecimientos.find(p => p.id == id);
            if (!padecimiento) return;

            // Llenar el formulario
            document.getElementById('tipo').value = padecimiento.tipo;
            document.getElementById('nombre').value = padecimiento.nombre;
            document.getElementById('descripcion').value = padecimiento.descripcion;
            document.getElementById('formaDeActuar').value = padecimiento.formaDeActuar;

            // Cambiar a modo edición
            modoEdicion = true;
            idEditando = id;

            // Cambiar el botón y título
            const submitBtn = document.querySelector('#padecimientoForm button[type="submit"]');
            submitBtn.textContent = 'Actualizar Padecimiento';

            // Cambiar título
            document.querySelector('#registroSection h2').textContent = 'Actualizar Padecimiento';

            // Agregar botón cancelar
            if (!document.getElementById('cancelBtn')) {
                const cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.id = 'cancelBtn';
                cancelBtn.textContent = 'Cancelar Edición';
                cancelBtn.onclick = cancelarEdicionFormulario;
                submitBtn.parentNode.appendChild(cancelBtn);
            }

            // Scroll al formulario
            document.getElementById('registroSection').scrollIntoView({ behavior: 'smooth' });
        }

        // Función para guardar cambios desde la tabla
        function guardarCambios(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (!row) return;

            const tipo = row.querySelector('.edit-tipo').value;
            const nombre = row.querySelector('.edit-nombre').value.trim();
            const descripcion = row.querySelector('.edit-descripcion').value.trim();
            const formaDeActuar = row.querySelector('.edit-forma').value.trim();

            // Validar datos
            if (!tipo || !nombre || !descripcion || !formaDeActuar) {
                mostrarMensaje('Todos los campos son obligatorios', false);
                return;
            }

            if (nombre.length < 3) {
                mostrarMensaje('El nombre debe tener al menos 3 caracteres', false);
                return;
            }

            if (descripcion.length < 10) {
                mostrarMensaje('La descripción debe tener al menos 10 caracteres', false);
                return;
            }

            if (formaDeActuar.length < 10) {
                mostrarMensaje('La forma de actuar debe tener al menos 10 caracteres', false);
                return;
            }

            // Enviar actualización
            const formData = new FormData();
            formData.append('update', '1');
            formData.append('id', id);
            formData.append('tipo', tipo);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('formaDeActuar', formaDeActuar);

            fetch('../action/padecimientoAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarMensaje(data.message, data.success);
                if (data.success) {
                    cargarPadecimientos(); // Recargar tabla
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al procesar la solicitud', false);
            });
        }

        // Función para cancelar edición en la tabla
        function cancelarEdicion(id) {
            cargarPadecimientos(); // Simplemente recargar para restaurar valores originales
        }

        // Función para cancelar edición del formulario
        function cancelarEdicionFormulario() {
            modoEdicion = false;
            idEditando = null;
            limpiarFormulario();

            // Restaurar botón
            const submitBtn = document.querySelector('#padecimientoForm button[type="submit"]');
            submitBtn.textContent = 'Registrar Padecimiento';

            // Remover botón cancelar
            const cancelBtn = document.getElementById('cancelBtn');
            if (cancelBtn) {
                cancelBtn.remove();
            }

            // Restaurar título
            document.querySelector('#registroSection h2').textContent = 'Registrar Nuevo Padecimiento';
        }

        // Función para eliminar padecimiento
        function eliminarPadecimiento(id) {
            const padecimiento = padecimientos.find(p => p.id == id);
            const nombrePadecimiento = padecimiento ? padecimiento.nombre : 'este padecimiento';

            if (confirm(`¿Está seguro de que desea eliminar "${nombrePadecimiento}"?\n\nEsta acción no se puede deshacer y puede afectar a los datos clínicos que referencien este padecimiento.`)) {
                const formData = new FormData();
                formData.append('delete', '1');
                formData.append('id', id);

                fetch('../action/padecimientoAction.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    mostrarMensaje(data.message, data.success);
                    if (data.success) {
                        cargarPadecimientos();
                        // Si estaba editando este elemento, cancelar edición
                        if (modoEdicion && idEditando == id) {
                            cancelarEdicionFormulario();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarMensaje('Error al procesar la solicitud', false);
                });
            }
        }

        // Función para limpiar formulario
        function limpiarFormulario() {
            document.getElementById('padecimientoForm').reset();
            document.getElementById('mensaje').innerHTML = '';
            if (modoEdicion) {
                cancelarEdicionFormulario();
            }
        }

        // Función para mostrar mensajes
        function mostrarMensaje(mensaje, esExito) {
            const mensajeDiv = document.getElementById('mensaje');
            mensajeDiv.innerHTML = `<div>${mensaje}</div>`;

            // Ocultar mensaje después de 5 segundos
            setTimeout(() => {
                mensajeDiv.innerHTML = '';
            }, 5000);
        }

        // Función para validar formulario antes del envío
        function validarFormulario() {
            const tipo = document.getElementById('tipo').value.trim();
            const nombre = document.getElementById('nombre').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            const formaDeActuar = document.getElementById('formaDeActuar').value.trim();

            if (!tipo) {
                mostrarMensaje('Por favor seleccione un tipo de padecimiento', false);
                return false;
            }

            if (nombre.length < 3) {
                mostrarMensaje('El nombre debe tener al menos 3 caracteres', false);
                return false;
            }

            if (descripcion.length < 10) {
                mostrarMensaje('La descripción debe tener al menos 10 caracteres', false);
                return false;
            }

            if (formaDeActuar.length < 10) {
                mostrarMensaje('La forma de actuar debe tener al menos 10 caracteres', false);
                return false;
            }

            return true;
        }
    </script>
</body>
</html>