<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['tipo_usuario'], ['admin', 'instructor'])) {
    header("Location: ../index.php?error=unauthorized");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Padecimientos</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2><i class="ph ph-first-aid"></i> Gestión de Padecimientos</h2>
        </header>

        <main>
            <div id="mensaje" style="display: none;"></div>
            <section>
                <h3><i class="ph ph-plus-circle"></i>Registrar Padecimiento</h3>
                <form id="formPadecimiento">
                    <input type="hidden" id="accion" name="accion" value="create">
                    <input type="hidden" id="padecimientoId" name="id" value="">
                    <div class="form-grid-container">
                        <div class="form-group">
                            <label for="tipo">Tipo:</label>
                            <select id="tipo" name="tipo">
                                <option value="">Seleccione un tipo</option>
                                <option value="Enfermedad">Enfermedad</option>
                                <option value="Lesión">Lesión</option>
                                <option value="Discapacidad">Discapacidad</option>
                                <option value="Trastorno">Trastorno</option>
                                <option value="Síndrome">Síndrome</option>
                                <option value="Otro">Otro</option>
                            </select>
                            <span class="error-message" id="error-tipo"></span>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" maxlength="100"
                                placeholder="Nombre del padecimiento">
                            <span class="error-message" id="error-nombre"></span>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" maxlength="500"
                                placeholder="Descripción detallada"></textarea>
                            <span class="error-message" id="error-descripcion"></span>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="formaDeActuar">Forma de Actuar:</label>
                            <textarea id="formaDeActuar" name="formaDeActuar" maxlength="1000"
                                placeholder="Instrucciones sobre cómo actuar"></textarea>
                            <span class="error-message" id="error-formaDeActuar"></span>
                        </div>
                    </div>
                    <button type="submit" id="btnSubmit"><i class="ph ph-plus"></i>Registrar</button>
                    <button type="button" onclick="limpiarFormulario()" id="btnCancelar" class="btn-danger"
                        style="display: none;"><i class="ph ph-x-circle"></i>Cancelar</button>
                </form>
            </section>

            <section>
                <h3><i class="ph ph-list-bullets"></i>Padecimientos Registrados</h3>
                <div class="table-wrapper">
                    <table class="table-clients">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Forma de Actuar</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="padecimientos-tbody"></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script>
        let padecimientos = [];
        document.addEventListener('DOMContentLoaded', () => cargarPadecimientos());

        document.getElementById('formPadecimiento').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const accion = document.getElementById('accion').value;
            formData.append(accion, '1');
            fetch('../action/padecimientoAction.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        mostrarMensaje(data.message, 'success');
                        limpiarFormulario();
                        cargarPadecimientos();
                    } else {
                        mostrarMensaje(data.message, 'error');
                    }
                })
                .catch(() => mostrarMensaje('Error de conexión.', 'error'));
        });

        function cargarPadecimientos() {
            fetch('../action/padecimientoAction.php?getPadecimientos=1')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        padecimientos = data.data;
                        mostrarPadecimientos();
                    } else {
                        mostrarMensaje('Error al cargar: ' + data.message, 'error');
                    }
                })
                .catch(() => mostrarMensaje('Error al cargar padecimientos.', 'error'));
        }

        function mostrarPadecimientos() {
            const tbody = document.getElementById('padecimientos-tbody');
            tbody.innerHTML = '';
            if (padecimientos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">No hay padecimientos registrados.</td></tr>';
                return;
            }
            padecimientos.forEach(p => {
                const row = tbody.insertRow();
                row.innerHTML = `
                    <td data-label="Tipo">${p.tipo}</td>
                    <td data-label="Nombre">${p.nombre}</td>
                    <td data-label="Descripción">${p.descripcion}</td>
                    <td data-label="Forma de Actuar">${p.formaDeActuar}</td>
                    <td data-label="Acciones">
                        <div class="actions">
                            <button class="btn-row" onclick="editarRegistro(${p.id})"><i class="ph ph-pencil-simple"></i></button>
                            <button class="btn-row btn-danger" onclick="eliminarRegistro(${p.id})"><i class="ph ph-trash"></i></button>
                        </div>
                    </td>`;
            });
        }

        function editarRegistro(id) {
            const p = padecimientos.find(pad => pad.id == id);
            if (!p) return;
            document.getElementById('accion').value = 'update';
            document.getElementById('padecimientoId').value = p.id;
            document.getElementById('tipo').value = p.tipo;
            document.getElementById('nombre').value = p.nombre;
            document.getElementById('descripcion').value = p.descripcion;
            document.getElementById('formaDeActuar').value = p.formaDeActuar;
            document.getElementById('btnSubmit').innerHTML = '<i class="ph ph-pencil-simple"></i> Actualizar';
            document.getElementById('btnCancelar').style.display = 'inline-flex';
            document.querySelector('h3').scrollIntoView();
        }

        function eliminarRegistro(id) {
            const nombre = padecimientos.find(p => p.id == id)?.nombre || 'este padecimiento';
            if (!confirm(`¿Eliminar "${nombre}"?`)) return;

            const formData = new FormData();
            formData.append('delete', '1');
            formData.append('id', id);
            fetch('../action/padecimientoAction.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.requiereConfirmacion) {
                        if (confirm(data.message)) {
                            const confirmFormData = new FormData();
                            confirmFormData.append('confirmDelete', '1');
                            confirmFormData.append('id', id);
                            fetch('../action/padecimientoAction.php', { method: 'POST', body: confirmFormData })
                                .then(res => res.json()).then(confirmData => {
                                    mostrarMensaje(confirmData.message, confirmData.success ? 'success' : 'error');
                                    if (confirmData.success) cargarPadecimientos();
                                });
                        }
                    } else {
                        mostrarMensaje(data.message, data.success ? 'success' : 'error');
                        if (data.success) cargarPadecimientos();
                    }
                })
                .catch(() => mostrarMensaje('Error de conexión.', 'error'));
        }

        function limpiarFormulario() {
            document.getElementById('formPadecimiento').reset();
            document.getElementById('accion').value = 'create';
            document.getElementById('padecimientoId').value = '';
            document.getElementById('btnSubmit').innerHTML = '<i class="ph ph-plus"></i>Registrar';
            document.getElementById('btnCancelar').style.display = 'none';
        }

        function mostrarMensaje(mensaje, tipo) {
            const div = document.getElementById('mensaje');
            div.textContent = mensaje;
            div.style.display = 'block';
            div.className = tipo === 'success' ? 'success-message flash-msg' : 'error-message flash-msg';
            setTimeout(() => { div.style.display = 'none'; }, 5000);
        }
    </script>
</body>

</html>