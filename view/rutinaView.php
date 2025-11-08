<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header("Location: loginView.php");
    exit();
}
include_once '../business/rutinaBusiness.php';
$rutinaBusiness = new RutinaBusiness();
$misRutinas = $rutinaBusiness->obtenerRutinasConEjercicios($_SESSION['usuario_id']);
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
            <h2><i class="ph ph-notebook"></i> Mis Rutinas</h2>
        </header>
        <main>
            <section>
                <h3><i class="ph ph-plus-circle"></i> Registrar Rutina del Día</h3>
                <form action="../action/rutinaAction.php" method="POST" id="form-rutina-principal">
                    <input type="hidden" name="create_rutina" value="1">
                    <div class="form-group">
                        <label for="fecha_rutina">Fecha del Entrenamiento:</label>
                        <input type="date" id="fecha_rutina" name="fecha_rutina" value="<?php echo date('Y-m-d'); ?>"
                            required>
                    </div>

                    <div id="panel-agregar-ejercicio"
                        style="border: 1px solid var(--color-border); padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius-md);">
                        <h4>Añadir Ejercicio</h4>
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
                                <label for="ejercicio-select">Ejercicio:</label>
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

                    <button type="submit">
                        <i class="ph ph-floppy-disk"></i> Guardar Rutina Completa
                    </button>
                </form>
            </section>

            <section>
                <h3><i class="ph ph-list-checks"></i> Historial de Rutinas</h3>
                <?php if (empty($misRutinas)): ?>
                    <p>No tienes rutinas registradas.</p>
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
                                <?php foreach ($misRutinas as $rutina): ?>
                                    <tr>
                                        <td data-label="Fecha">
                                            <?php echo date('d/m/Y', strtotime($rutina->getFecha())); ?>
                                        </td>
                                        <td data-label="Observaciones">
                                            <?php echo htmlspecialchars($rutina->getObservacion()); ?>
                                        </td>
                                        <td data-label="Ejercicios">
                                            <ul style="padding-left: 1rem; margin: 0;">
                                                <?php foreach ($rutina->getEjercicios() as $ej): ?>
                                                    <li>
                                                        <strong>
                                                            <?php echo htmlspecialchars($ej->getNombreEjercicio()); ?>:
                                                        </strong>
                                                        <?php
                                                        $detalles = [];
                                                        if ($ej->getSeries())
                                                            $detalles[] = $ej->getSeries() . ' series';
                                                        if ($ej->getRepeticiones())
                                                            $detalles[] = $ej->getRepeticiones() . ' reps';
                                                        if ($ej->getPeso() !== null && $ej->getPeso() != '')
                                                            $detalles[] = $ej->getPeso() . ' kg';
                                                        if ($ej->getTiempo())
                                                            $detalles[] = $ej->getTiempo() . ' seg';
                                                        if ($ej->getDescanso())
                                                            $detalles[] = 'desc. ' . $ej->getDescanso() . ' seg';
                                                        echo implode(' &times; ', $detalles);
                                                        if (!empty($ej->getComentario()))
                                                            echo ' - <em>' . htmlspecialchars($ej->getComentario()) . '</em>';
                                                        ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                        <td data-label="Acciones">
                                            <form action="../action/rutinaAction.php" method="POST"
                                                onsubmit="return confirm('¿Eliminar esta rutina?');">
                                                <input type="hidden" name="delete_rutina" value="1">
                                                <input type="hidden" name="rutina_id"
                                                    value="<?php echo $rutina->getId(); ?>">
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
            const tipoSelect = document.getElementById('tipo-ejercicio');
            const ejercicioSelectContainer = document.getElementById('container-ejercicio-select');
            const ejercicioSelect = document.getElementById('ejercicio-select');
            const camposContainer = document.getElementById('campos-ejercicio');
            const btnAgregar = document.getElementById('btn-agregar-ejercicio');
            const listaAgregados = document.getElementById('lista-ejercicios-agregados');
            const formPrincipal = document.getElementById('form-rutina-principal');
            let ejercicioCounter = 0;

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
        });
    </script>
</body>

</html>