<?php
    session_start();

    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
        header('Location: ../view/loginView.php');
        exit();
    }

    if ($_SESSION['tipo_usuario'] !== 'cliente') {
        header("Location: ../index.php?error=acceso_denegado");
        exit();
    }

    $clienteId = $_SESSION['usuario_id'];

    if (!class_exists('DatosClinicosBusiness')) {
        include_once '../business/datosClinicosBusiness.php';
    }

    $datosClinicosBusiness = new DatosClinicosBusiness();

    $datosExistentes = $datosClinicosBusiness->obtenerTBDatosClinicosPorCliente($clienteId);
    $tieneRegistro = ($datosExistentes !== null);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mis Datos Clínicos</title>
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

                if (actionType === 'create' && !formData.has('create')) {
                    formData.append('create', '1');
                }
                if (actionType === 'update' && !formData.has('update')) {
                    formData.append('update', '1');
                }

                loadingElement.style.display = 'block';
                messageElement.innerHTML = '';

                fetch('../action/datosClinicosAction.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    loadingElement.style.display = 'none';

                    if (data.success) {
                        messageElement.innerHTML = '<div class="message success">' + data.message + '</div>';
                        if (actionType === 'create') {
                            form.reset();
                            document.getElementById('otraEnfermedadDiv').style.display = 'none';
                            document.getElementById('medicamentoDiv').style.display = 'none';
                            document.getElementById('descripcionLesionDiv').style.display = 'none';
                            document.getElementById('descripcionDiscapacidadDiv').style.display = 'none';
                            document.getElementById('descripcionrestriccionmedica').style.display = 'none';
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
                return confirm('¿Estás seguro de que deseas guardar estos datos clínicos?');
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
                    if (confirmarAccion('¿Estás seguro de que deseas actualizar tu información clínica?')) {
                        return submitForm(form, 'update');
                    }
                }

                form.removeChild(actionInput);
                return false;
            }
        </script>
    </head>
    <body>

        <header>
            <h2>Mis Datos Clínicos</h2>
            <a href="../index.php">Volver al Inicio</a>
        </header>

        <hr>

        <main>
            <?php if (!$tieneRegistro): ?>
            <h2>Registrar Mis Datos Clínicos</h2>

            <div id="loading" style="display: none;">Procesando...</div>
            <div id="message"></div>

            <form id="createForm" onsubmit="if(validarFormulario()) { return submitForm(this, 'create'); } else { return false; }">
                <input type="hidden" name="create" value="1">
                <input type="hidden" name="clienteId" value="<?php echo $clienteId; ?>">

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
                    <input type="checkbox" id="restriccionMedica" name="restriccionMedica" onchange="toggleConditionalField('restriccionMedica', 'descripcionrestriccionmedica')">
                    ¿Posee restricción médica?
                </label><br>

                <div id="descripcionrestriccionmedica" style="margin-left: 30px; display: none;">
                    <label>Describa la restricción médica:</label><br>
                    <textarea name="descripcionrestriccionmedica" placeholder="Describa la restricción médica..."></textarea><br>
                </div><br>

                <input type="submit" value="Registrar Datos Clínicos">
            </form>

            <?php else: ?>
            <h2>Mis Datos Clínicos Registrados</h2>

            <div id="loading" style="display: none;">Procesando...</div>
            <div id="message"></div>

            <table border="1" style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 8px; text-align: left;">Enfermedad</th>
                        <th style="padding: 8px; text-align: left;">Medicamento</th>
                        <th style="padding: 8px; text-align: left;">Lesión</th>
                        <th style="padding: 8px; text-align: left;">Discapacidad</th>
                        <th style="padding: 8px; text-align: left;">Restricción Médica</th>
                        <th style="padding: 8px; text-align: left;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <form class="table-form" onsubmit="return handleTableFormSubmit(this, event);">
                            <input type="hidden" name="id" value="<?php echo $datosExistentes->getTbdatosclinicosid(); ?>">
                            <input type="hidden" name="clienteId" value="<?php echo $clienteId; ?>">

                            <td style="padding: 8px;">
                                <input type="checkbox" name="enfermedad" <?php echo $datosExistentes->getTbdatosclinicosenfermedad() ? 'checked' : ''; ?>> Sí<br>
                                <textarea name="otraEnfermedad" style="width: 95%; margin-top: 5px;" placeholder="Describa..."><?php echo htmlspecialchars($datosExistentes->getTbdatosclinicosotraenfermedad()); ?></textarea>
                            </td>

                            <td style="padding: 8px;">
                                <input type="checkbox" name="tomaMedicamento" <?php echo $datosExistentes->getTbdatosclinicostomamedicamento() ? 'checked' : ''; ?>> Sí<br>
                                <textarea name="medicamento" style="width: 95%; margin-top: 5px;" placeholder="Describa..."><?php echo htmlspecialchars($datosExistentes->getTbdatosclinicosmedicamento()); ?></textarea>
                            </td>

                            <td style="padding: 8px;">
                                <input type="checkbox" name="lesion" <?php echo $datosExistentes->getTbdatosclinicoslesion() ? 'checked' : ''; ?>> Sí<br>
                                <textarea name="descripcionLesion" style="width: 95%; margin-top: 5px;" placeholder="Describa..."><?php echo htmlspecialchars($datosExistentes->getTbdatosclinicosdescripcionlesion()); ?></textarea>
                            </td>

                            <td style="padding: 8px;">
                                <input type="checkbox" name="discapacidad" <?php echo $datosExistentes->getTbdatosclinicosdiscapacidad() ? 'checked' : ''; ?>> Sí<br>
                                <textarea name="descripcionDiscapacidad" style="width: 95%; margin-top: 5px;" placeholder="Describa..."><?php echo htmlspecialchars($datosExistentes->getTbdatosclinicosdescripciondiscapacidad()); ?></textarea>
                            </td>

                            <td style="padding: 8px;">
                                <input type="checkbox" name="restriccionMedica" <?php echo $datosExistentes->getTbdatosclinicosrestriccionmedica() ? 'checked' : ''; ?>> Sí<br>
                                <textarea name="descripcionrestriccionmedica" style="width: 95%; margin-top: 5px;" placeholder="Describa..."><?php echo htmlspecialchars($datosExistentes->getTbdatosclinicosdescripcionrestriccionmedica()); ?></textarea>
                            </td>

                            <td style="padding: 8px;">
                                <input type="submit" value="Actualizar" name="update">
                            </td>
                        </form>
                    </tr>
                </tbody>
            </table>
            <?php endif; ?>

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
                        echo '<div class="message success">Éxito: Sus datos clínicos se registraron correctamente.</div>';
                    } else if ($success == "updated") {
                        echo '<div class="message success">Éxito: Sus datos clínicos se actualizaron correctamente.</div>';
                    }
                }
                ?>
            </div>
        </main>

        <hr>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> - Sistema de Gestión Gimnasio</p>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php if (!$tieneRegistro): ?>
                toggleConditionalField('enfermedad', 'otraEnfermedadDiv');
                toggleConditionalField('tomaMedicamento', 'medicamentoDiv');
                toggleConditionalField('lesion', 'descripcionLesionDiv');
                toggleConditionalField('discapacidad', 'descripcionDiscapacidadDiv');
                toggleConditionalField('restriccionMedica', 'descripcionrestriccionmedica');
                <?php endif; ?>
            });
        </script>

    </body>
</html>