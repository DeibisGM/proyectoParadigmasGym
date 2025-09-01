<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../view/login.php');
    exit();
}

$esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';

if (!$esAdmin) {
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <h2><i class="ph ph-bandaids"></i>Gestión de Padecimientos</h2>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a>
    </header>

    <main>
        <div id="mensaje"
             style="display: none; padding: 10px; margin: 10px 0; border: 1px solid; border-radius: 5px;"></div>
        <section>
            <h3 id="tituloFormulario"><i class="ph ph-plus-circle"></i>Registrar nuevo padecimiento</h3>
            <form id="formPadecimiento">
                <input type="hidden" id="accion" name="accion" value="create">
                <input type="hidden" id="padecimientoId" name="id" value="">

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

                <label>Nombre:</label>
                <input type="text" id="nombre" name="nombre" required maxlength="100"
                       placeholder="Nombre del padecimiento">
                <label>Descripción:</label>
                <textarea id="descripcion" name="descripcion" required maxlength="500"
                          placeholder="Descripción detallada"></textarea>
                <label>Forma de Actuar:</label>
                <textarea id="formaDeActuar" name="formaDeActuar" required maxlength="1000"
                          placeholder="Instrucciones sobre cómo actuar"></textarea>

                <button type="submit" id="btnSubmit"><i class="ph ph-plus"></i>Registrar</button>
                <button type="button" onclick="limpiarFormulario()" id="btnCancelar" style="display: none;"><i
                            class="ph ph-x-circle"></i>Cancelar
                </button>
            </form>
        </section>

        <section>
            <h3><i class="ph ph-list-bullets"></i>Padecimientos registrados</h3>
            <div style="overflow-x:auto;">
                <table id="tablaPadecimientos">
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
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
<script>
    let padecimientos = [];
    document.addEventListener('DOMContentLoaded', () => cargarPadecimientos());

    document.getElementById('formPadecimiento').addEventListener('submit', function (e) {
        e.preventDefault();
        if (!validarFormulario()) return;
        const formData = new FormData(this);
        const accion = document.getElementById('accion').value;
        formData.append(accion, '1');
        fetch('../action/padecimientoAction.php', {method: 'POST', body: formData})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    limpiarFormulario();
                    setTimeout(() => cargarPadecimientos(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            }).catch(error => mostrarMensaje('Error de conexión.', 'error'));
    });

    function cargarPadecimientos() {
        fetch('../action/padecimientoAction.php?getPadecimientos=1')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    padecimientos = data.data;
                    mostrarPadecimientos();
                } else {
                    mostrarMensaje('Error al cargar: ' + data.message, 'error');
                }
            }).catch(error => mostrarMensaje('Error al cargar padecimientos', 'error'));
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
            row.dataset.id = p.id;
            row.innerHTML = `
                <td><div class="display">${p.tipo}</div><div class="edit" style="display: none;"><select><option value="Enfermedad">Enfermedad</option><option value="Lesión">Lesión</option><option value="Discapacidad">Discapacidad</option><option value="Trastorno">Trastorno</option><option value="Síndrome">Síndrome</option><option value="Otro">Otro</option></select></div></td>
                <td><div class="display">${p.nombre}</div><div class="edit" style="display: none;"><input type="text" value="${p.nombre}" placeholder="Nombre" maxlength="100"></div></td>
                <td><div class="display">${p.descripcion}</div><div class="edit" style="display: none;"><textarea placeholder="Descripción" maxlength="500">${p.descripcion}</textarea></div></td>
                <td><div class="display">${p.formaDeActuar}</div><div class="edit" style="display: none;"><textarea placeholder="Forma de Actuar" maxlength="1000">${p.formaDeActuar}</textarea></div></td>
                <td class="actions-cell">
                    <button class="btn-edit" onclick="editarRegistro(${p.id})"><i class="ph ph-pencil-simple"></i> Editar</button>
                    <button class="btn-delete" onclick="eliminarRegistro(${p.id})"><i class="ph ph-trash"></i> Eliminar</button>
                    <button class="btn-cancel" style="display: none;" onclick="cancelarEdicion(${p.id})"><i class="ph ph-x-circle"></i> Cancelar</button>
                    <button class="btn-save" style="display: none;" onclick="guardarEdicion(${p.id})"><i class="ph ph-floppy-disk"></i> Guardar</button>
                </td>`;
            row.querySelector('select').value = p.tipo; // Set correct dropdown value
        });
    }

    function toggleEdit(id, isEditing) {
        const row = document.querySelector(`tr[data-id='${id}']`);
        row.querySelectorAll('.display').forEach(el => el.style.display = isEditing ? 'none' : 'block');
        row.querySelectorAll('.edit').forEach(el => el.style.display = isEditing ? 'block' : 'none');
        row.querySelector('.btn-edit').style.display = isEditing ? 'none' : 'inline-flex';
        row.querySelector('.btn-delete').style.display = isEditing ? 'none' : 'inline-flex';
        row.querySelector('.btn-cancel').style.display = isEditing ? 'inline-flex' : 'none';
        row.querySelector('.btn-save').style.display = isEditing ? 'inline-flex' : 'none';
    }

    function editarRegistro(id) {
        toggleEdit(id, true);
    }

    function cancelarEdicion(id) {
        toggleEdit(id, false);
        cargarPadecimientos();
    }

    function guardarEdicion(id) {
        const row = document.querySelector(`tr[data-id='${id}']`);
        const tipo = row.cells[0].querySelector('select').value;
        const nombre = row.cells[1].querySelector('input').value.trim();
        const descripcion = row.cells[2].querySelector('textarea').value.trim();
        const formaDeActuar = row.cells[3].querySelector('textarea').value.trim();

        if (!tipo || !nombre || !descripcion || !formaDeActuar || nombre.length < 3 || descripcion.length < 10 || formaDeActuar.length < 10) {
            mostrarMensaje('Datos inválidos. Verifique la información.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('update', '1');
        formData.append('id', id);
        formData.append('tipo', tipo);
        formData.append('nombre', nombre);
        formData.append('descripcion', descripcion);
        formData.append('formaDeActuar', formaDeActuar);

        fetch('../action/padecimientoAction.php', {method: 'POST', body: formData})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    setTimeout(() => cargarPadecimientos(), 1500);
                } else {
                    mostrarMensaje(data.message, 'error');
                }
            }).catch(error => mostrarMensaje('Error de conexión.', 'error'));
    }

    function eliminarRegistro(id) {
        const nombre = padecimientos.find(p => p.id == id)?.nombre || 'este padecimiento';
        if (!confirm(`¿Eliminar "${nombre}"?`)) return;

        const formData = new FormData();
        formData.append('delete', '1');
        formData.append('id', id);
        fetch('../action/padecimientoAction.php', {method: 'POST', body: formData})
            .then(response => response.json())
            .then(data => {
                if (data.requiereConfirmacion) {
                    if (confirm(data.message)) {
                        const confirmFormData = new FormData();
                        confirmFormData.append('confirmDelete', '1');
                        confirmFormData.append('id', id);
                        fetch('../action/padecimientoAction.php', {method: 'POST', body: confirmFormData})
                            .then(res => res.json()).then(confirmData => {
                            mostrarMensaje(confirmData.message, confirmData.success);
                            if (confirmData.success) setTimeout(() => cargarPadecimientos(), 1500);
                        });
                    }
                } else {
                    mostrarMensaje(data.message, data.success);
                    if (data.success) setTimeout(() => cargarPadecimientos(), 1500);
                }
            }).catch(error => mostrarMensaje('Error de conexión.', 'error'));
    }

    function limpiarFormulario() {
        document.getElementById('formPadecimiento').reset();
        document.getElementById('accion').value = 'create';
        document.getElementById('padecimientoId').value = '';
        document.getElementById('tituloFormulario').innerHTML = '<i class="ph ph-plus-circle"></i>Registrar nuevo padecimiento';
        document.getElementById('btnSubmit').innerHTML = '<i class="ph ph-plus"></i>Registrar';
        document.getElementById('btnCancelar').style.display = 'none';
    }

    function mostrarMensaje(mensaje, tipo) {
        const div = document.getElementById('mensaje');
        div.textContent = mensaje;
        div.style.display = 'block';
        div.style.backgroundColor = tipo === 'success' ? '#d4edda' : '#f8d7da';
        div.style.color = tipo === 'success' ? '#155724' : '#721c24';
        div.style.borderColor = tipo === 'success' ? '#c3e6cb' : '#f5c6cb';
        setTimeout(() => {
            div.style.display = 'none';
        }, 5000);
    }

    function validarFormulario() {
        if (!document.getElementById('tipo').value.trim()) {
            mostrarMensaje('Seleccione un tipo', 'error');
            return false;
        }
        if (document.getElementById('nombre').value.trim().length < 3) {
            mostrarMensaje('El nombre debe tener al menos 3 caracteres', 'error');
            return false;
        }
        if (document.getElementById('descripcion').value.trim().length < 10) {
            mostrarMensaje('La descripción debe tener al menos 10 caracteres', 'error');
            return false;
        }
        if (document.getElementById('formaDeActuar').value.trim().length < 10) {
            mostrarMensaje('La forma de actuar debe tener al menos 10 caracteres', 'error');
            return false;
        }
        return true;
    }
</script>
</body>
</html>