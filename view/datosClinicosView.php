<?php

if (!class_exists('DatosClinicosBusiness')) {
    include_once '../business/datosClinicosBusiness.php';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Datos Clínicos</title>
    <script>
        function toggleConditionalField(checkboxId, divId) {
            const checkbox = document.getElementById(checkboxId);
            const div = document.getElementById(divId);

            if (checkbox.checked) {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
                const textarea = div.querySelector('textarea');
                if (textarea) {
                    textarea.value = '';
                }
            }
        }

        function submitForm(form, actionType) {
            const formData = new FormData(form);
            const loadingElement = document.getElementById('loading');
            const messageElement = document.getElementById('message');

            console.log('Enviando datos:', Object.fromEntries(formData));

            loadingElement.style.display = 'block';
            messageElement.innerHTML = '';

            fetch('../action/datosClinicosAction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loadingElement.style.display = 'none';

                console.log('Respuesta del servidor:', data);

                if (data.success) {
                    messageElement.innerHTML = '<div class="message success">' + data.message + '</div>';
                    if (actionType === 'create') {
                        form.reset();

                        document.getElementById('otraEnfermedadDiv').style.display = 'none';
                        document.getElementById('medicamentoDiv').style.display = 'none';
                        document.getElementById('descripcionLesionDiv').style.display = 'none';
                        document.getElementById('descripcionDiscapacidadDiv').style.display = 'none';
                    }
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    messageElement.innerHTML = '<div class="message error">' + data.message + '</div>';
                    if (data.debug) {
                        console.error('Debug info:', data.debug);
                    }
                }
            })
            .catch(error => {
                loadingElement.style.display = 'none';
                messageElement.innerHTML = '<div class="message error">Error de conexión: ' + error.message + '</div>';
                console.error('Error de fetch:', error);
            });

            return false;
        }

        function validarFormulario() {
            const clienteSelect = document.getElementById('clienteSelect');
            if (clienteSelect.value === '') {
                alert('Debe seleccionar un cliente.');
                return false;
            }
            return confirm('¿Estás seguro de que deseas crear este registro de datos clínicos?');
        }

        function confirmarAccion(mensaje) {
            return confirm(mensaje);
        }

        function handleTableFormSubmit(form, event) {
            event.preventDefault();

            const submitterName = event.submitter.name;

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = submitterName;
            actionInput.value = submitterName === 'update' ? 'Actualizar' : 'Eliminar';
            form.appendChild(actionInput);

            if (submitterName === 'update') {
                if (confirmarAccion('¿Estás seguro de que deseas actualizar este registro?')) {
                    return submitForm(form, 'update');
                }
            } else if (submitterName === 'delete') {
                if (confirmarAccion('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.')) {
                    return submitForm(form, 'delete');
                }
            }

            form.removeChild(actionInput);
            return false;
        }
    </script>
</head>
<body>

    <header>
        <h2>Gym - Datos Clínicos</h2>
        <a href="/proyectoParadigmasGym/index.php">Volver al Inicio</a>
    </header>

    <hr>

    <main>
        <h2>Registrar Datos Clínicos</h2>

        <div id="loading" style="display: none;">Procesando...</div>
        <div id="message"></div>

        <form id="createForm" onsubmit="if(validarFormulario()) { return submitForm(this, 'create'); } else { return false; }">
            <input type="hidden" name="create" value="1">

            <label>Cliente:</label><br>
            <select id="clienteSelect" name="clienteId" required>
                <option value="">Seleccione un cliente</option>
                <?php
                try {
                    $datosClinicosBusiness = new DatosClinicosBusiness();
                    $clientesDisponibles = $datosClinicosBusiness->obtenerTodosLosClientes();

                    if (empty($clientesDisponibles)) {
                        echo '<option value="" disabled>No hay clientes disponibles</option>';
                    } else {
                        foreach ($clientesDisponibles as $cliente) {
                            echo '<option value="' . htmlspecialchars($cliente['id']) . '">' .
                                 htmlspecialchars($cliente['carnet']) . ' - ' .
                                 htmlspecialchars($cliente['nombre']) .
                                 '</option>';
                        }
                    }
                } catch (Exception $e) {
                    echo '<option value="" disabled>Error al cargar clientes</option>';
                }
                ?>
            </select><br><br>

            <label>
                <input type="checkbox" id="enfermedad" name="enfermedad" onchange="toggleConditionalField('enfermedad', 'otraEnfermedadDiv')">
                ¿Posee alguna enfermedad?
            </label><br>
            <div id="otraEnfermedadDiv" style="margin-left: 30px; display: none;">
                <label>Especifique la enfermedad:</label><br>
                <textarea name="otraEnfermedad" placeholder="Describa la enfermedad..."></textarea><br>
            </div><br>

            <label>
                <input type="checkbox" id="tomaMedicamento" name="tomaMedicamento" onchange="toggleConditionalField('tomaMedicamento', 'medicamentoDiv')">
                ¿Toma algún medicamento?
            </label><br>
            <div id="medicamentoDiv" style="margin-left: 30px; display: none;">
                <label>Especifique el medicamento:</label><br>
                <textarea name="medicamento" placeholder="Describa el medicamento..."></textarea><br>
            </div><br>

            <label>
                <input type="checkbox" id="lesion" name="lesion" onchange="toggleConditionalField('lesion', 'descripcionLesionDiv')">
                ¿Posee alguna lesión?
            </label><br>
            <div id="descripcionLesionDiv" style="margin-left: 30px; display: none;">
                <label>Describa la lesión:</label><br>
                <textarea name="descripcionLesion" placeholder="Describa la lesión..."></textarea><br>
            </div><br>

            <label>
                <input type="checkbox" id="discapacidad" name="discapacidad" onchange="toggleConditionalField('discapacidad', 'descripcionDiscapacidadDiv')">
                ¿Posee alguna discapacidad?
            </label><br>
            <div id="descripcionDiscapacidadDiv" style="margin-left: 30px; display: none;">
                <label>Describa la discapacidad:</label><br>
                <textarea name="descripcionDiscapacidad" placeholder="Describa la discapacidad..."></textarea><br>
            </div><br>

            <label>
                <input type="checkbox" name="restriccionMedica">
                ¿Posee restricción médica?
            </label><br><br>

            <input type="submit" value="Registrar Datos Clínicos">
        </form>

        <br><br>

        <h2>Datos Clínicos Registrados</h2>

        <table border="1" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 8px; text-align: left;">Carnet Cliente</th>
                    <th style="padding: 8px; text-align: left;">Enfermedad</th>
                    <th style="padding: 8px; text-align: left;">Medicamento</th>
                    <th style="padding: 8px; text-align: left;">Lesión</th>
                    <th style="padding: 8px; text-align: left;">Discapacidad</th>
                    <th style="padding: 8px; text-align: left;">Restricción Médica</th>
                    <th style="padding: 8px; text-align: left;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $allDatosClinicos = $datosClinicosBusiness->obtenerTBDatosClinicos();

                    if (empty($allDatosClinicos)) {
                        echo '<tr><td colspan="7" style="padding: 8px; text-align: center;">No hay datos clínicos registrados</td></tr>';
                    } else {
                        foreach ($allDatosClinicos as $current) {
                            $id = $current->getTbdatosclinicosid();
                            echo '<tr>';
                            echo '<form class="table-form" onsubmit="return handleTableFormSubmit(this, event);">';
                            echo '<input type="hidden" name="id" value="' . $id . '">';
                            echo '<input type="hidden" name="clienteId" value="' . $current->getTbclientesid() . '">';

                            echo '<td style="padding: 8px;">';
                            echo '<strong>' . htmlspecialchars(isset($current->carnet) ? $current->carnet : 'N/A') . '</strong>';
                            echo '</td>';

                            echo '<td style="padding: 8px;">';
                            echo '<input type="checkbox" name="enfermedad" ' . ($current->getTbdatosclinicosenfermedad() ? 'checked' : '') . '> Sí<br>';
                            echo '<textarea name="otraEnfermedad" style="width: 95%; margin-top: 5px;" placeholder="Describa...">' . htmlspecialchars($current->getTbdatosclinicosotraenfermedad()) . '</textarea>';
                            echo '</td>';

                            echo '<td style="padding: 8px;">';
                            echo '<input type="checkbox" name="tomaMedicamento" ' . ($current->getTbdatosclinicostomamedicamento() ? 'checked' : '') . '> Sí<br>';
                            echo '<textarea name="medicamento" style="width: 95%; margin-top: 5px;" placeholder="Describa...">' . htmlspecialchars($current->getTbdatosclinicosmedicamento()) . '</textarea>';
                            echo '</td>';

                            echo '<td style="padding: 8px;">';
                            echo '<input type="checkbox" name="lesion" ' . ($current->getTbdatosclinicoslesion() ? 'checked' : '') . '> Sí<br>';
                            echo '<textarea name="descripcionLesion" style="width: 95%; margin-top: 5px;" placeholder="Describa...">' . htmlspecialchars($current->getTbdatosclinicosdescripcionlesion()) . '</textarea>';
                            echo '</td>';

                            echo '<td style="padding: 8px;">';
                            echo '<input type="checkbox" name="discapacidad" ' . ($current->getTbdatosclinicosdiscapacidad() ? 'checked' : '') . '> Sí<br>';
                            echo '<textarea name="descripcionDiscapacidad" style="width: 95%; margin-top: 5px;" placeholder="Describa...">' . htmlspecialchars($current->getTbdatosclinicosdescripciondiscapacidad()) . '</textarea>';
                            echo '</td>';

                            echo '<td style="padding: 8px;">';
                            echo '<input type="checkbox" name="restriccionMedica" ' . ($current->getTbdatosclinicosrestriccionmedica() ? 'checked' : '') . '> Sí';
                            echo '</td>';

                            echo '<td style="padding: 8px;">';
                            echo '<input type="submit" value="Actualizar" name="update"> ';
                            echo '<input type="submit" value="Eliminar" name="delete">';
                            echo '</td>';

                            echo '</form>';
                            echo '</tr>';
                        }
                    }
                } catch (Exception $e) {
                    echo '<tr><td colspan="7" style="padding: 8px; text-align: center;">Error al cargar datos: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <br>

        <div>
            <?php
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                if ($error == "emptyField") {
                    echo '<div class="message error">Error: Hay campos vacíos.</div>';
                } else if ($error == "dbError") {
                    echo '<div class="message error">Error: No se pudo procesar la transacción en la base de datos.</div>';
                } else {
                    echo '<div class="message error">Error: ' . htmlspecialchars($error) . '</div>';
                }
            } else if (isset($_GET['success'])) {
                $success = $_GET['success'];
                if ($success == "inserted") {
                    echo '<div class="message success">Éxito: Registro insertado correctamente.</div>';
                } else if ($success == "updated") {
                    echo '<div class="message success">Éxito: Registro actualizado correctamente.</div>';
                } else if ($success == "deleted") {
                    echo '<div class="message success">Éxito: Registro eliminado correctamente.</div>';
                }
            }
            ?>
        </div>
    </main>

    <hr>

    <footer>
        <p>Fin de la página.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toggleConditionalField('enfermedad', 'otraEnfermedadDiv');
            toggleConditionalField('tomaMedicamento', 'medicamentoDiv');
            toggleConditionalField('lesion', 'descripcionLesionDiv');
            toggleConditionalField('discapacidad', 'descripcionDiscapacidadDiv');
        });
    </script>

</body>
</html>