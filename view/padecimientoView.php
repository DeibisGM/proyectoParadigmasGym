<?php
session_start();
include_once '../utility/Validation.php';

Validation::start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../view/login.php');
    exit();
}

$esAdmin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';
$esInstruct = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'instructor';

if (!$esAdmin && !$esInstruct) {
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
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <h2><i class="ph ph-bandaids"></i>Gestión de Padecimientos</h2>
    </header>

    <main>
        <div id="mensaje" class="" style="display: none;"></div>

        <section>
            <h3 id="tituloFormulario"><i class="ph ph-plus-circle"></i>Registrar nuevo padecimiento</h3>
            <form id="formPadecimiento">
                <input type="hidden" id="accion" name="accion" value="create">
                <input type="hidden" id="padecimientoId" name="id" value="">

                <div class="form-group">
                    <label>Tipo de Padecimiento:</label>
                    <span class="error-message" id="error-tipo"></span>
                    <select id="tipo" name="tipo">
                        <option value="">Seleccione un tipo</option>
                        <option value="Enfermedad">Enfermedad</option>
                        <option value="Lesión">Lesión</option>
                        <option value="Discapacidad">Discapacidad</option>
                        <option value="Trastorno">Trastorno</option>
                        <option value="Síndrome">Síndrome</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nombre:</label>
                    <span class="error-message" id="error-nombre"></span>
                    <input type="text" id="nombre" name="nombre" maxlength="100"
                           placeholder="Nombre del padecimiento">
                </div>

                <div class="form-group">
                    <label>Descripción:</label>
                    <span class="error-message" id="error-descripcion"></span>
                    <textarea id="descripcion" name="descripcion" maxlength="500"
                              placeholder="Descripción detallada"></textarea>
                </div>

                <div class="form-group">
                    <label>Forma de Actuar:</label>
                    <span class="error-message" id="error-formaDeActuar"></span>
                    <textarea id="formaDeActuar" name="formaDeActuar" maxlength="1000"
                              placeholder="Instrucciones sobre cómo actuar"></textarea>
                </div>

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
<?php Validation::clear(); ?>
<script>
    let padecimientos = [];
    document.addEventListener('DOMContentLoaded', () => cargarPadecimientos());

    document.getElementById('formPadecimiento').addEventListener('submit', function (e) {
        e.preventDefault();
        limpiarErrores();
        const formData = new FormData(this);
        const accion = document.getElementById('accion').value;
        formData.append(accion, '1');
        fetch('../action/padecimientoAction.php', {method: 'POST', body: formData})
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                const data = JSON.parse(text);
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    limpiarFormulario();
                    setTimeout(() => cargarPadecimientos(), 1500);
                } else {
                    if (data.errors) {
                        mostrarErroresValidacion(data.errors);
                    }
                    mostrarMensaje(data.message, 'error');
                }
            }).catch(error => {
                console.error('Error completo:', error);
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
                <td>
                    <div class="display">${p.tipo}</div>
                    <div class="edit" style="display: none;">
                        <select>
                            <option value="">Seleccione un tipo</option>
                            <option value="Enfermedad">Enfermedad</option>
                            <option value="Lesión">Lesión</option>
                            <option value="Discapacidad">Discapacidad</option>
                            <option value="Trastorno">Trastorno</option>
                            <option value="Síndrome">Síndrome</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <span class="error-message" id="error-tipo_${p.id}"></span>
                    </div>
                </td>
                <td>
                    <div class="display">${p.nombre}</div>
                    <div class="edit" style="display: none;">
                        <input type="text" value="${p.nombre}" placeholder="Nombre" maxlength="100">
                        <span class="error-message" id="error-nombre_${p.id}"></span>
                    </div>
                </td>
                <td>
                    <div class="display">${p.descripcion}</div>
                    <div class="edit" style="display: none;">
                        <textarea placeholder="Descripción" maxlength="500">${p.descripcion}</textarea>
                        <span class="error-message" id="error-descripcion_${p.id}"></span>
                    </div>
                </td>
                <td>
                    <div class="display">${p.formaDeActuar}</div>
                    <div class="edit" style="display: none;">
                        <textarea placeholder="Forma de Actuar" maxlength="1000">${p.formaDeActuar}</textarea>
                        <span class="error-message" id="error-formaDeActuar_${p.id}"></span>
                    </div>
                </td>
                <td class="actions-cell">
                    <button class="btn-edit" onclick="editarRegistro(${p.id})"><i class="ph ph-pencil-simple"></i> Editar</button>
                    <button class="btn-delete" onclick="eliminarRegistro(${p.id})"><i class="ph ph-trash"></i> Eliminar</button>
                    <button class="btn-cancel" style="display: none;" onclick="cancelarEdicion(${p.id})"><i class="ph ph-x-circle"></i> Cancelar</button>
                    <button class="btn-save" style="display: none;" onclick="guardarEdicion(${p.id})"><i class="ph ph-floppy-disk"></i> Guardar</button>
                </td>`;
            row.querySelector('select').value = p.tipo;
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

        if (!isEditing) {
            limpiarErroresInline(id);
        }
    }

    function editarRegistro(id) {
        toggleEdit(id, true);
    }

    function cancelarEdicion(id) {
        toggleEdit(id, false);
        cargarPadecimientos();
    }

    function guardarEdicion(id) {
        limpiarErroresInline(id);
        const row = document.querySelector(`tr[data-id='${id}']`);
        const tipo = row.cells[0].querySelector('select').value;
        const nombre = row.cells[1].querySelector('input').value.trim();
        const descripcion = row.cells[2].querySelector('textarea').value.trim();
        const formaDeActuar = row.cells[3].querySelector('textarea').value.trim();

        const formData = new FormData();
        formData.append('update', '1');
        formData.append('id', id);
        formData.append('tipo', tipo);
        formData.append('nombre', nombre);
        formData.append('descripcion', descripcion);
        formData.append('formaDeActuar', formaDeActuar);

        fetch('../action/padecimientoAction.php', {method: 'POST', body: formData})
            .then(response => {
                console.log('Update response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Update response text:', text);
                const data = JSON.parse(text);
                if (data.success) {
                    mostrarMensaje(data.message, 'success');
                    toggleEdit(id, false);
                    setTimeout(() => cargarPadecimientos(), 1500);
                } else {
                    if (data.errors) {
                        mostrarErroresInline(data.errors, id);
                    }
                    mostrarMensaje(data.message, 'error');
                }
            }).catch(error => {
                console.error('Update error completo:', error);
                mostrarMensaje('Error de conexión.', 'error');
            });
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
                            mostrarMensaje(confirmData.message, confirmData.success ? 'success' : 'error');
                            if (confirmData.success) setTimeout(() => cargarPadecimientos(), 1500);
                        });
                    }
                } else {
                    mostrarMensaje(data.message, data.success ? 'success' : 'error');
                    if (data.success) setTimeout(() => cargarPadecimientos(), 1500);
                }
            }).catch(error => mostrarMensaje('Error de conexión.', 'error'));
    }

    function limpiarErrores() {
        document.querySelectorAll('#formPadecimiento .error-message').forEach(el => el.textContent = '');
    }

    function limpiarErroresInline(id) {
        document.querySelectorAll(`[id^="error-"][id$="_${id}"]`).forEach(el => el.textContent = '');
    }

    function mostrarErroresValidacion(errors) {
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById('error-' + field);
            if (errorElement) {
                errorElement.textContent = errors[field];
            }
        });
    }

    function mostrarErroresInline(errors, id) {
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById('error-' + field);
            if (errorElement) {
                errorElement.textContent = errors[field];
            }
        });
    }

    function limpiarFormulario() {
        document.getElementById('formPadecimiento').reset();
        document.getElementById('accion').value = 'create';
        document.getElementById('padecimientoId').value = '';
        document.getElementById('tituloFormulario').innerHTML = '<i class="ph ph-plus-circle"></i>Registrar nuevo padecimiento';
        document.getElementById('btnSubmit').innerHTML = '<i class="ph ph-plus"></i>Registrar';
        document.getElementById('btnCancelar').style.display = 'none';
        limpiarErrores();
    }

    function mostrarMensaje(mensaje, tipo) {
        const div = document.getElementById('mensaje');
        div.textContent = mensaje;
        div.style.display = 'block';
        div.className = tipo === 'success' ? 'success-message flash-msg' : 'error-message flash-msg';
        setTimeout(() => {
            div.style.display = 'none';
        }, 5000);
    }
</script>
</body>
</html>