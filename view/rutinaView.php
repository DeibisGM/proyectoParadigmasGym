<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: loginView.php");
    exit();
}
include_once '../business/rutinaBusiness.php';
$rutinaBusiness = new RutinaBusiness();
$rutinasSugeridas = $rutinaBusiness->obtenerRutinasSugeridasConEjercicios($_SESSION['usuario_id']);
$rutinasCompletadas = $rutinaBusiness->obtenerRutinasCompletadasConEjercicios($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Rutinas de Entrenamiento</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Mis Rutinas</h2>
        </header>
        <main>
            <section>
                <h3><i class="ph ph-plus-circle"></i> Registrar Rutina del Día</h3>
                <form action="../action/rutinaAction.php" method="POST" id="form-rutina-principal">
                    <input type="hidden" name="create_rutina" value="1">
                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label for="fecha_rutina">Fecha del Entrenamiento:</label>
                        <input type="date" id="fecha_rutina" name="fecha_rutina" class="date-input" value="<?php echo date('Y-m-d'); ?>"
                            required>
                    </div>

                    <h4 style="margin-top: 2rem;">Añadir Ejercicio</h4>
                    <div id="panel-agregar-ejercicio"
                        style="border: 1px solid var(--color-border); padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius-md);">
                        
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="tipo-ejercicio">Tipo:</label>
                                <select id="tipo-ejercicio">
                                    <option value="">Seleccione...</option>
                                    <option value="fuerza">Fuerza</option>
                                    <option value="resistencia">Resistencia</option>
                                    <option value="equilibrio">Equilibrio</option>
                                    <option value="flexibilidad">Flexibilidad</option>
                                </select>
                            </div>
                            <div class="form-group" id="container-ejercicio-select" style="display:none;">
                                <label for="ejercicio-select" style="margin-bottom: 0.5rem;">Ejercicio:</label>
                                <select id="ejercicio-select"></select>
                            </div>
                        </div>
                        <div id="campos-ejercicio" style="margin-top: 1rem;"></div>
                        <button type="button" id="btn-agregar-ejercicio" style="margin-top: 1rem;">
                            <i class="ph ph-plus"></i> Añadir a la Rutina
                        </button>
                    </div>

                    <h4>Ejercicios en la Rutina de Hoy</h4>
                    <div id="lista-ejercicios-agregados"
                        style="min-height: 50px; border: 1px solid var(--color-border); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
                        <p style="color: var(--color-text-muted);">Aún no has añadido ejercicios.</p>
                    </div>

                    <div class="form-group">
                        <label for="observacion_rutina">Observaciones Generales:</label>
                        <textarea id="observacion_rutina" name="observacion_rutina"
                            placeholder="¿Cómo te sentiste? ¿Alguna nota importante?"></textarea>
                    </div>

                    <button type="submit" style="margin-top: 1rem;">
                        <i class="ph ph-floppy-disk"></i> Guardar Rutina Completa
                    </button>
                </form>
            </section>

            <section>
                <h3><i class="ph ph-star"></i> Rutinas Sugeridas</h3>
                <?php if (empty($rutinasSugeridas)): ?>
                    <p>No tienes rutinas sugeridas por un instructor.</p>
                <?php else: ?>
                    <div class="menu-grid-2-cols">
                        <?php foreach ($rutinasSugeridas as $rutina): ?>
                            <details class="rutina-card">
                                <summary><h4>Sugerencia del <?php echo date('d/m/Y', strtotime($rutina['tbrutinafecha'])); ?></h4></summary>
                                <p><?php echo !empty($rutina['tbrutinaobservacion']) ? htmlspecialchars($rutina['tbrutinaobservacion']) : 'Sin observaciones.'; ?></p>
                                <ul>
                                    <?php foreach ($rutina['ejercicios'] as $ej): ?>
                                        <li>
                                            <strong><?php echo htmlspecialchars($ej['nombreEjercicio']); ?>:</strong>
                                            <?php
                                            $detalles = [];
                                            if ($ej['tbrutinaejercicioseries']) $detalles[] = $ej['tbrutinaejercicioseries'] . ' series';
                                            if ($ej['tbrutinaejerciciorepeticiones']) $detalles[] = $ej['tbrutinaejerciciorepeticiones'] . ' reps';
                                            if ($ej['tbrutinaejerciciopeso'] !== null && $ej['tbrutinaejerciciopeso'] != '') $detalles[] = $ej['tbrutinaejerciciopeso'] . ' kg';
                                            if ($ej['tbrutinaejerciciotiempo_seg']) $detalles[] = $ej['tbrutinaejerciciotiempo_seg'] . ' seg';
                                            if ($ej['tbrutinaejerciciodescanso_seg']) $detalles[] = 'desc. ' . $ej['tbrutinaejerciciodescanso_seg'] . ' seg';
                                            echo implode(' &times; ', $detalles);
                                            ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="rutina-card-actions">
                                    <button class="btn-realizar-rutina" data-rutina-id="<?php echo $rutina['tbrutinaid']; ?>">
                                        <i class="ph ph-play"></i> Realizar Rutina
                                    </button>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section>
                <h3><i class="ph ph-list-checks"></i> Historial de Rutinas Completadas</h3>
                <?php if (empty($rutinasCompletadas)): ?>
                    <p>No tienes rutinas completadas.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table-clients">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Observaciones</th>
                                    <th>Ejercicios</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rutinasCompletadas as $rutina): ?>
                                    <tr>
                                        <td data-label="Fecha">
                                            <?php echo date('d/m/Y', strtotime($rutina['tbrutinafecha'])); ?>
                                        </td>
                                        <td data-label="Observaciones">
                                            <?php echo !empty($rutina['tbrutinaobservacion']) ? htmlspecialchars($rutina['tbrutinaobservacion']) : '-'; ?>
                                        </td>
                                        <td data-label="Ejercicios">
                                            <ul style="padding-left: 1rem; margin: 0;">
                                                <?php foreach ($rutina['ejercicios'] as $ej): ?>
                                                    <li>
                                                        <strong>
                                                            <?php echo htmlspecialchars($ej['nombreEjercicio']); ?>:
                                                        </strong>
                                                        <?php
                                                        $detalles = [];
                                                        if ($ej['tbrutinaejercicioseries'])
                                                            $detalles[] = $ej['tbrutinaejercicioseries'] . ' series';
                                                        if ($ej['tbrutinaejerciciorepeticiones'])
                                                            $detalles[] = $ej['tbrutinaejerciciorepeticiones'] . ' reps';
                                                        if ($ej['tbrutinaejerciciopeso'] !== null && $ej['tbrutinaejerciciopeso'] != '')
                                                            $detalles[] = $ej['tbrutinaejerciciopeso'] . ' kg';
                                                        if ($ej['tbrutinaejerciciotiempo_seg'])
                                                            $detalles[] = $ej['tbrutinaejerciciotiempo_seg'] . ' seg';
                                                        if ($ej['tbrutinaejerciciodescanso_seg'])
                                                            $detalles[] = 'desc. ' . $ej['tbrutinaejerciciodescanso_seg'] . ' seg';
                                                        echo implode(' &times; ', $detalles);
                                                        if (!empty($ej['tbrutinaejerciciocomentario']))
                                                            echo ' - <em>' . htmlspecialchars($ej['tbrutinaejerciciocomentario']) . '</em>';
                                                        ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                        <td data-label="Acciones" class="actions">
                                            <form action="../action/rutinaAction.php" method="POST"
                                                onsubmit="return confirm('¿Eliminar esta rutina?');" style="margin: 0;">
                                                <input type="hidden" name="delete_rutina" value="1">
                                                <input type="hidden" name="rutina_id"
                                                    value="<?php echo $rutina['tbrutinaid']; ?>">
                                                <button type="submit" class="btn-row btn-danger">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const realizarButtons = document.querySelectorAll('.btn-realizar-rutina');
            const rutinasSugeridas = <?php echo json_encode($rutinasSugeridas); ?>;

            const formPrincipal = document.getElementById('form-rutina-principal');
            const listaAgregados = document.getElementById('lista-ejercicios-agregados');
            const fechaRutinaInput = document.getElementById('fecha_rutina');
            const observacionRutinaInput = document.getElementById('observacion_rutina');
            let ejercicioCounter = 0;

            realizarButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const rutinaId = this.dataset.rutinaId;
                    const rutina = rutinasSugeridas.find(r => r.tbrutinaid == rutinaId);

                    if (rutina) {
                        // Clear existing exercises from the form
                        listaAgregados.innerHTML = '';
                        const existingHiddenInputs = formPrincipal.querySelectorAll('div[id^="hidden-inputs-"]');
                        existingHiddenInputs.forEach(div => div.remove());
                        ejercicioCounter = 0;

                        // Set date and observation
                        fechaRutinaInput.value = new Date().toISOString().split('T')[0];
                        observacionRutinaInput.value = "Basado en la sugerencia del " + new Date(rutina.tbrutinafecha).toLocaleDateString() + ".\n" + rutina.tbrutinaobservacion;

                        // Add exercises to the form
                        rutina.ejercicios.forEach(ej => {
                            let displayHtml = `<strong>${ej.nombreEjercicio}:</strong> `;
                            let hiddenInputs = `<input type="hidden" name="ejercicios[${ejercicioCounter}][tipo]" value="${ej.tbrutinaejerciciotipo}"><input type="hidden" name="ejercicios[${ejercicioCounter}][id]" value="${ej.tbejercicioid}">`;
                            let detalles = [];

                            if (ej.tbrutinaejercicioseries) {
                                detalles.push(`${ej.tbrutinaejercicioseries} series`);
                                hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][series]" value="${ej.tbrutinaejercicioseries}">`;
                            }
                            if (ej.tbrutinaejerciciorepeticiones) {
                                detalles.push(`${ej.tbrutinaejerciciorepeticiones} reps`);
                                hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][repeticiones]" value="${ej.tbrutinaejerciciorepeticiones}">`;
                            }
                            if (ej.tbrutinaejerciciopeso) {
                                detalles.push(`${ej.tbrutinaejerciciopeso} kg`);
                                hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][peso]" value="${ej.tbrutinaejerciciopeso}">`;
                            }
                            if (ej.tbrutinaejerciciotiempo_seg) {
                                detalles.push(`${ej.tbrutinaejerciciotiempo_seg} seg`);
                                hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][tiempo]" value="${ej.tbrutinaejerciciotiempo_seg}">`;
                            }
                            if (ej.tbrutinaejerciciodescanso_seg) {
                                detalles.push(`desc. ${ej.tbrutinaejerciciodescanso_seg} seg`);
                                hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][descanso]" value="${ej.tbrutinaejerciciodescanso_seg}">`;
                            }
                            if (ej.tbrutinaejerciciocomentario) {
                                hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][comentario]" value="${ej.tbrutinaejerciciocomentario}">`;
                            }

                            displayHtml += detalles.join(' &times; ');
                            if (ej.tbrutinaejerciciocomentario) displayHtml += ` - <em>(${ej.tbrutinaejerciciocomentario})</em>`;

                            const div = document.createElement('div');
                            div.id = `ejercicio-item-${ejercicioCounter}`;
                            div.innerHTML = displayHtml;
                            const btnEliminar = document.createElement('button');
                            btnEliminar.type = 'button';
                            btnEliminar.innerHTML = '<i class="ph ph-x"></i>';
                            btnEliminar.className = 'btn-row btn-danger';
                            const currentCounter = ejercicioCounter;
                            btnEliminar.onclick = function () {
                                document.getElementById(`ejercicio-item-${currentCounter}`).remove();
                                document.getElementById(`hidden-inputs-${currentCounter}`).remove();
                                if (listaAgregados.children.length === 0) {
                                    listaAgregados.innerHTML = '<p style="color: var(--color-text-muted);">Aún no has añadido ejercicios.</p>';
                                }
                            };
                            div.appendChild(btnEliminar);

                            const hiddenDiv = document.createElement('div');
                            hiddenDiv.id = `hidden-inputs-${ejercicioCounter}`;
                            hiddenDiv.innerHTML = hiddenInputs;

                            listaAgregados.appendChild(div);
                            formPrincipal.appendChild(hiddenDiv);

                            ejercicioCounter++;
                        });

                        // Scroll to the form
                        formPrincipal.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });

            const tipoSelect = document.getElementById('tipo-ejercicio');
            const ejercicioSelectContainer = document.getElementById('container-ejercicio-select');
            const ejercicioSelect = document.getElementById('ejercicio-select');
            const camposContainer = document.getElementById('campos-ejercicio');
            const btnAgregar = document.getElementById('btn-agregar-ejercicio');
            
            tipoSelect.addEventListener('change', function () {
                const tipo = this.value;
                ejercicioSelect.innerHTML = '<option value="">Cargando...</option>';
                camposContainer.innerHTML = '';
                if (!tipo) {
                    ejercicioSelectContainer.style.display = 'none';
                    return;
                }
                fetch(`../action/rutinaAction.php?action=get_ejercicios_por_tipo&tipo=${tipo}`)
                    .then(response => response.json())
                    .then(data => {
                        ejercicioSelect.innerHTML = '<option value="">Seleccione un ejercicio...</option>';
                        data.forEach(ej => {
                            ejercicioSelect.innerHTML += `<option value="${ej.id}">${ej.nombre}</option>`;
                        });
                        ejercicioSelectContainer.style.display = 'block';
                    });
            });

            ejercicioSelect.addEventListener('change', function () {
                const tipo = tipoSelect.value;
                camposContainer.innerHTML = '';
                if (!this.value) return;

                let fieldsHtml = '<div class="ejercicio-campos-grid">';
                if (tipo === 'fuerza') {
                    fieldsHtml += `
                    <div class="form-group"><label>Series:</label><input type="number" id="temp-series" min="1"></div>
                    <div class="form-group"><label>Repeticiones:</label><input type="number" id="temp-repeticiones" min="1"></div>
                    <div class="form-group"><label>Peso (kg):</label><input type="number" id="temp-peso" min="0" step="0.5"></div>
                    <div class="form-group"><label>Descanso (seg):</label><input type="number" id="temp-descanso" min="0"></div>
                `;
                } else if (tipo === 'resistencia') {
                    fieldsHtml += `
                    <div class="form-group"><label>Series:</label><input type="number" id="temp-series" min="1"></div>
                    <div class="form-group"><label>Repeticiones:</label><input type="number" id="temp-repeticiones" min="1"></div>
                    <div class="form-group"><label>Tiempo (seg):</label><input type="number" id="temp-tiempo" min="0"></div>
                    <div class="form-group"><label>Peso (kg):</label><input type="number" id="temp-peso" min="0" step="0.5"></div>
                    <div class="form-group"><label>Descanso (seg):</label><input type="number" id="temp-descanso" min="0"></div>
                `;
                } else if (tipo === 'equilibrio') {
                    fieldsHtml += `<div class="form-group"><label>Duración (seg):</label><input type="number" id="temp-tiempo" min="1"></div>`;
                } else if (tipo === 'flexibilidad') {
                    fieldsHtml += `
                    <div class="form-group"><label>Series:</label><input type="number" id="temp-series" min="1"></div>
                    <div class="form-group"><label>Duración (seg):</label><input type="number" id="temp-tiempo" min="1"></div>
                `;
                }
                fieldsHtml += '</div>';
                fieldsHtml += '<div class="form-group" style="margin-top: 1rem;"><label>Comentario:</label><input type="text" id="temp-comentario" placeholder="Opcional"></div>';
                camposContainer.innerHTML = fieldsHtml;
            });

            btnAgregar.addEventListener('click', function () {
                const tipo = tipoSelect.value;
                const ejercicioId = ejercicioSelect.value;
                const ejercicioNombre = ejercicioSelect.options[ejercicioSelect.selectedIndex].text;

                if (!tipo || !ejercicioId) {
                    alert('Debe seleccionar un tipo y un ejercicio.');
                    return;
                }

                if (listaAgregados.querySelector('p')) {
                    listaAgregados.innerHTML = '';
                }

                let displayHtml = `<strong>${ejercicioNombre}:</strong> `;
                let hiddenInputs = `<input type="hidden" name="ejercicios[${ejercicioCounter}][tipo]" value="${tipo}"><input type="hidden" name="ejercicios[${ejercicioCounter}][id]" value="${ejercicioId}">`;
                const comentario = document.getElementById('temp-comentario').value;
                let detalles = [];

                ['series', 'repeticiones', 'peso', 'descanso', 'tiempo'].forEach(field => {
                    const input = document.getElementById(`temp-${field}`);
                    if (input) {
                        const value = input.value || (field === 'peso' ? '' : 0);
                        if (value > 0 || (field === 'peso' && value !== '')) {
                            detalles.push(`${value} ${field === 'descanso' ? 's desc' : field}`);
                            hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][${field}]" value="${value}">`;
                        }
                    }
                });

                displayHtml += detalles.join(' &times; ');
                if (comentario) displayHtml += ` - <em>(${comentario})</em>`;
                hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][comentario]" value="${comentario}">`;

                const div = document.createElement('div');
                div.id = `ejercicio-item-${ejercicioCounter}`;
                div.innerHTML = displayHtml;
                const btnEliminar = document.createElement('button');
                btnEliminar.type = 'button';
                btnEliminar.innerHTML = '<i class="ph ph-x"></i>';
                btnEliminar.className = 'btn-row btn-danger';
                const currentCounter = ejercicioCounter;
                btnEliminar.onclick = function () {
                    document.getElementById(`ejercicio-item-${currentCounter}`).remove();
                    document.getElementById(`hidden-inputs-${currentCounter}`).remove();
                    if (listaAgregados.children.length === 0) {
                        listaAgregados.innerHTML = '<p style="color: var(--color-text-muted);">Aún no has añadido ejercicios.</p>';
                    }
                };
                div.appendChild(btnEliminar);

                const hiddenDiv = document.createElement('div');
                hiddenDiv.id = `hidden-inputs-${ejercicioCounter}`;
                hiddenDiv.innerHTML = hiddenInputs;

                listaAgregados.appendChild(div);
                formPrincipal.appendChild(hiddenDiv);

                ejercicioCounter++;
                resetearPanelAgregar();
            });

            function resetearPanelAgregar() {
                tipoSelect.value = '';
                ejercicioSelectContainer.style.display = 'none';
                ejercicioSelect.innerHTML = '';
                camposContainer.innerHTML = '';
            }

            // Abrir el date picker al hacer clic en el campo
            document.getElementById('fecha_rutina').addEventListener('click', function() {
                try {
                    this.showPicker();
                } catch (error) {
                    console.error("showPicker() is not supported by this browser.");
                }
            });
        });
    </script>
</body>
</html>