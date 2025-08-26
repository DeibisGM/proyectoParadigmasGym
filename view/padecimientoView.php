<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../view/login.php');
    exit();
}

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Gestión de Padecimientos</h1>

    <header>
        <a href="../index.php">Volver al Inicio</a>
    </header>

    <div id="mensaje" style="display: none; padding: 10px; margin: 10px 0; border: 1px solid; border-radius: 5px;"></div>

    <div id="formularioContainer">
        <h3 id="tituloFormulario">Registrar nuevo padecimiento</h3>

        <form id="formPadecimiento">
            <input type="hidden" id="accion" name="accion" value="create">
            <input type="hidden" id="padecimientoId" name="id" value="">

            <div>
                <label>Tipo de Padecimiento:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="Enfermedad">Enfermedad</option>
                    <option value="Lesión">Lesión</option>
                    <option value="Discapacidad">Discapacidad</option>
                    <option value="Trastorno">Trastorno</option>
                    <option value="Síndrome">Síndrome</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div>
                <label>Nombre:</label>
                <input type="text" id="nombre" name="nombre" required maxlength="100" placeholder="Nombre del padecimiento">
            </div>

            <div>
                <label>Descripción:</label>
                <textarea id="descripcion" name="descripcion" required maxlength="500" rows="3" placeholder="Descripción detallada del padecimiento"></textarea>
            </div>

            <div>
                <label>Forma de Actuar:</label>
                <textarea id="formaDeActuar" name="formaDeActuar" required maxlength="1000" rows="4" placeholder="Instrucciones sobre cómo actuar ante este padecimiento"></textarea>
            </div>

            <div>
                <button type="submit" id="btnSubmit">Registrar</button>
                <button type="button" onclick="limpiarFormulario()" id="btnCancelar" style="display: none;">Cancelar</button>
            </div>
        </form>
    </div>

    <hr>

    <div>
        <h3>Padecimientos registrados</h3>

        <table border="1" id="tablaPadecimientos">
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
            </tbody>
        </table>
    </div>

    <script>
        let padecimientos = [];

        document.addEventListener('DOMContentLoaded', function() {
            cargarPadecimientos();
        });

        document.getElementById('formPadecimiento').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validarFormulario()) {
                return;
            }

            const formData = new FormData(this);
            const accion = document.getElementById('accion').value;

            formData.append(accion, '1');

            fetch('../action/padecimientoAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    limpiarFormulario();
                    setTimeout(() => cargarPadecimientos(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión.', 'error');
            });
        });

        function cargarPadecimientos() {
            fetch('../action/padecimientoAction.php?getPadecimientos=1')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    padecimientos = data.data;
                    mostrarPadecimientos();
                } else {
                    mostrarMensaje('Error al cargar padecimientos: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al cargar padecimientos', 'error');
            });
        }

        function mostrarPadecimientos() {
            const tbody = document.getElementById('padecimientos-tbody');
            tbody.innerHTML = '';

            if (padecimientos.length === 0) {
                const row = tbody.insertRow();
                row.innerHTML = '<td colspan="5">No hay padecimientos registrados.</td>';
                return;
            }

            padecimientos.forEach(padecimiento => {
                const row = tbody.insertRow();
                row.setAttribute('data-id', padecimiento.id);
                row.innerHTML = `
                    <td class="tipo-cell">
                        <div class="tipo-display">
                            ${padecimiento.tipo}
                        </div>
                        <div class="tipo-edit" style="display: none;">
                            <select class="edit-tipo">
                                <option value="Enfermedad" ${padecimiento.tipo === 'Enfermedad' ? 'selected' : ''}>Enfermedad</option>
                                <option value="Lesión" ${padecimiento.tipo === 'Lesión' ? 'selected' : ''}>Lesión</option>
                                <option value="Discapacidad" ${padecimiento.tipo === 'Discapacidad' ? 'selected' : ''}>Discapacidad</option>
                                <option value="Trastorno" ${padecimiento.tipo === 'Trastorno' ? 'selected' : ''}>Trastorno</option>
                                <option value="Síndrome" ${padecimiento.tipo === 'Síndrome' ? 'selected' : ''}>Síndrome</option>
                                <option value="Otro" ${padecimiento.tipo === 'Otro' ? 'selected' : ''}>Otro</option>
                            </select>
                        </div>
                    </td>
                    <td class="nombre-cell">
                        <div class="nombre-display">
                            ${padecimiento.nombre}
                        </div>
                        <div class="nombre-edit" style="display: none;">
                            <input type="text" class="edit-nombre" value="${padecimiento.nombre}" maxlength="100">
                        </div>
                    </td>
                    <td class="descripcion-cell">
                        <div class="descripcion-display">
                            ${padecimiento.descripcion}
                        </div>
                        <div class="descripcion-edit" style="display: none;">
                            <textarea class="edit-descripcion" maxlength="500" rows="2">${padecimiento.descripcion}</textarea>
                        </div>
                    </td>
                    <td class="forma-cell">
                        <div class="forma-display">
                            ${padecimiento.formaDeActuar}
                        </div>
                        <div class="forma-edit" style="display: none;">
                            <textarea class="edit-forma" maxlength="1000" rows="2">${padecimiento.formaDeActuar}</textarea>
                        </div>
                    </td>
                    <td>
                        <button onclick="editarRegistro(${padecimiento.id})">Editar</button>
                        <button onclick="eliminarRegistro(${padecimiento.id})">Eliminar</button>
                        <button onclick="cancelarEdicion(${padecimiento.id})" style="display: none;" class="btn-cancelar-edicion">Cancelar</button>
                        <button onclick="guardarEdicion(${padecimiento.id})" style="display: none;" class="btn-guardar-edicion">Actualizar</button>
                    </td>
                `;
            });
        }

        function editarRegistro(id) {
            const fila = document.querySelector(`tr[data-id="${id}"]`);

            const displays = fila.querySelectorAll('.tipo-display, .nombre-display, .descripcion-display, .forma-display');
            const edits = fila.querySelectorAll('.tipo-edit, .nombre-edit, .descripcion-edit, .forma-edit');

            displays.forEach(display => display.style.display = 'none');
            edits.forEach(edit => edit.style.display = 'block');

            const btnEditar = fila.querySelector('button[onclick*="editarRegistro"]');
            const btnEliminar = fila.querySelector('button[onclick*="eliminarRegistro"]');
            const btnCancelar = fila.querySelector('.btn-cancelar-edicion');
            const btnGuardar = fila.querySelector('.btn-guardar-edicion');

            btnEditar.style.display = 'none';
            btnEliminar.style.display = 'none';
            btnCancelar.style.display = 'inline';
            btnGuardar.style.display = 'inline';
        }

        function cancelarEdicion(id) {
            const fila = document.querySelector(`tr[data-id="${id}"]`);

            const displays = fila.querySelectorAll('.tipo-display, .nombre-display, .descripcion-display, .forma-display');
            const edits = fila.querySelectorAll('.tipo-edit, .nombre-edit, .descripcion-edit, .forma-edit');

            displays.forEach(display => display.style.display = 'block');
            edits.forEach(edit => edit.style.display = 'none');

            const btnEditar = fila.querySelector('button[onclick*="editarRegistro"]');
            const btnEliminar = fila.querySelector('button[onclick*="eliminarRegistro"]');
            const btnCancelar = fila.querySelector('.btn-cancelar-edicion');
            const btnGuardar = fila.querySelector('.btn-guardar-edicion');

            btnEditar.style.display = 'inline';
            btnEliminar.style.display = 'inline';
            btnCancelar.style.display = 'none';
            btnGuardar.style.display = 'none';

            cargarPadecimientos();
        }

        function guardarEdicion(id) {
            const fila = document.querySelector(`tr[data-id="${id}"]`);

            const tipo = fila.querySelector('.edit-tipo').value;
            const nombre = fila.querySelector('.edit-nombre').value.trim();
            const descripcion = fila.querySelector('.edit-descripcion').value.trim();
            const formaDeActuar = fila.querySelector('.edit-forma').value.trim();

            if (!tipo || !nombre || !descripcion || !formaDeActuar) {
                mostrarMensaje('Todos los campos son obligatorios', 'error');
                return;
            }

            if (nombre.length < 3) {
                mostrarMensaje('El nombre debe tener al menos 3 caracteres', 'error');
                return;
            }

            if (descripcion.length < 10) {
                mostrarMensaje('La descripción debe tener al menos 10 caracteres', 'error');
                return;
            }

            if (formaDeActuar.length < 10) {
                mostrarMensaje('La forma de actuar debe tener al menos 10 caracteres', 'error');
                return;
            }

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
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    setTimeout(() => cargarPadecimientos(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión.', 'error');
            });
        }

        function eliminarRegistro(id) {
            const padecimiento = padecimientos.find(p => p.id == id);
            const nombrePadecimiento = padecimiento ? padecimiento.nombre : 'este padecimiento';

            if (confirm(`¿Está seguro de que desea eliminar "${nombrePadecimiento}"?`)) {
                const formData = new FormData();
                formData.append('delete', '1');
                formData.append('id', id);

                fetch('../action/padecimientoAction.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.requiereConfirmacion) {
                        if (confirm(data.message)) {
                            const confirmFormData = new FormData();
                            confirmFormData.append('confirmDelete', '1');
                            confirmFormData.append('id', id);

                            fetch('../action/padecimientoAction.php', {
                                method: 'POST',
                                body: confirmFormData
                            })
                            .then(response => response.json())
                            .then(confirmData => {
                                if (confirmData.success) {
                                    mostrarMensaje(confirmData.message, 'success');
                                    setTimeout(() => cargarPadecimientos(), 1500);
                                } else {
                                    mostrarMensaje(confirmData.message, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                mostrarMensaje('Error de conexión.', 'error');
                            });
                        }
                    } else {
                        if (data.success) {
                            mostrarMensaje(data.message, 'success');
                            setTimeout(() => cargarPadecimientos(), 1500);
                        } else {
                            mostrarMensaje(data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarMensaje('Error de conexión.', 'error');
                });
            }
        }

        function limpiarFormulario() {
            document.getElementById('formPadecimiento').reset();
            document.getElementById('accion').value = 'create';
            document.getElementById('padecimientoId').value = '';
            document.getElementById('tituloFormulario').textContent = 'Registrar nuevo padecimiento';
            document.getElementById('btnSubmit').textContent = 'Registrar';
            document.getElementById('btnCancelar').style.display = 'none';
        }

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

        function validarFormulario() {
            const tipo = document.getElementById('tipo').value.trim();
            const nombre = document.getElementById('nombre').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            const formaDeActuar = document.getElementById('formaDeActuar').value.trim();

            if (!tipo) {
                mostrarMensaje('Por favor seleccione un tipo de padecimiento', 'error');
                return false;
            }

            if (nombre.length < 3) {
                mostrarMensaje('El nombre debe tener al menos 3 caracteres', 'error');
                return false;
            }

            if (descripcion.length < 10) {
                mostrarMensaje('La descripción debe tener al menos 10 caracteres', 'error');
                return false;
            }

            if (formaDeActuar.length < 10) {
                mostrarMensaje('La forma de actuar debe tener al menos 10 caracteres', 'error');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>