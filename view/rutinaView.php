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
    <title>Mis Rutinas de Entrenamiento</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .ejercicio-campos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .ejercicio-campos-grid .form-group {
            margin-bottom: 0;
        }
        .rutina-card ul {
            list-style-type: disc;
            padding-left: 20px;
        }
        .rutina-card li {
            margin-bottom: 8px;
        }
    </style>
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
                    <input type="date" id="fecha_rutina" name="fecha_rutina" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div id="panel-agregar-ejercicio" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <h4>Añadir Ejercicio</h4>
                    <div class="form-group">
                        <label for="tipo-ejercicio">Tipo de Ejercicio:</label>
                        <select id="tipo-ejercicio">
                            <option value="">Seleccione un tipo...</option>
                            <option value="fuerza">Fuerza</option>
                            <option value="resistencia">Resistencia</option>
                            <option value="equilibrio">Equilibrio</option>
                        </select>
                    </div>
                    <div class="form-group" id="container-ejercicio-select" style="display:none;">
                        <label for="ejercicio-select">Ejercicio:</label>
                        <select id="ejercicio-select"></select>
                    </div>

                    <div id="campos-ejercicio"></div>

                    <button type="button" id="btn-agregar-ejercicio">
                        <i class="ph ph-plus"></i> Añadir a la Rutina
                    </button>
                </div>

                <h4>Ejercicios en la Rutina de Hoy</h4>
                <div id="lista-ejercicios-agregados" style="min-height: 50px; border: 1px solid #eee; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <p style="color: #6c757d; font-style: italic;">Aún no has añadido ejercicios.</p>
                </div>

                <div class="form-group">
                    <label for="observacion_rutina">Observaciones Generales:</label>
                    <textarea id="observacion_rutina" name="observacion_rutina" placeholder="¿Cómo te sentiste? ¿Alguna nota importante?"></textarea>
                </div>

                <button type="submit">
                    <i class="ph ph-floppy-disk"></i> Guardar Rutina Completa
                </button>
            </form>
        </section>
        <hr>
        <section>
            <h3><i class="ph ph-list-checks"></i> Historial de Rutinas</h3>
            <?php if (empty($misRutinas)): ?>
                <p>No tienes rutinas registradas.</p>
            <?php else: ?>
                <?php foreach($misRutinas as $rutina): ?>
                    <div class="rutina-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                        <div style="display:flex; justify-content: space-between; align-items: center;">
                            <h4>Fecha: <?php echo date('d/m/Y', strtotime($rutina->getFecha())); ?></h4>
                            <form action="../action/rutinaAction.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta rutina?');" style="margin-bottom: 0;">
                                <input type="hidden" name="delete_rutina" value="1">
                                <input type="hidden" name="rutina_id" value="<?php echo $rutina->getId(); ?>">
                                <button type="submit" class="btn-danger" style="background: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; max-width: 40px; justify-content: center;">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </form>
                        </div>
                        <?php if(!empty($rutina->getObservacion())): ?>
                            <p><strong>Observaciones:</strong> <?php echo htmlspecialchars($rutina->getObservacion()); ?></p>
                        <?php endif; ?>
                        <ul>
                            <?php foreach($rutina->getEjercicios() as $ej): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($ej->getNombreEjercicio()); ?>:</strong>
                                    <?php
                                    $detalles = [];
                                    if($ej->getSeries()) $detalles[] = $ej->getSeries() . ' series';
                                    if($ej->getRepeticiones()) $detalles[] = $ej->getRepeticiones() . ' reps';
                                    if($ej->getPeso() !== null && $ej->getPeso() != '') $detalles[] = $ej->getPeso() . ' kg';
                                    if($ej->getTiempo()) $detalles[] = $ej->getTiempo() . ' seg';
                                    if($ej->getDescanso()) $detalles[] = 'desc. ' . $ej->getDescanso() . ' seg';
                                    echo implode(' &times; ', $detalles);
                                    if(!empty($ej->getComentario())) echo ' - <em>' . htmlspecialchars($ej->getComentario()) . '</em>';
                                    ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo-ejercicio');
        const ejercicioSelectContainer = document.getElementById('container-ejercicio-select');
        const ejercicioSelect = document.getElementById('ejercicio-select');
        const camposContainer = document.getElementById('campos-ejercicio');
        const btnAgregar = document.getElementById('btn-agregar-ejercicio');
        const listaAgregados = document.getElementById('lista-ejercicios-agregados');
        const formPrincipal = document.getElementById('form-rutina-principal');
        let ejercicioCounter = 0;

        tipoSelect.addEventListener('change', function() {
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

        ejercicioSelect.addEventListener('change', function() {
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
                fieldsHtml += `
                    <div class="form-group"><label>Duración (seg):</label><input type="number" id="temp-tiempo" min="1"></div>
                `;
            }
            fieldsHtml += '</div>';
            fieldsHtml += '<div class="form-group" style="margin-top: 1rem;"><label>Comentario:</label><input type="text" id="temp-comentario" placeholder="Opcional"></div>';
            camposContainer.innerHTML = fieldsHtml;
        });

        btnAgregar.addEventListener('click', function() {
            const tipo = tipoSelect.value;
            const ejercicioId = ejercicioSelect.value;
            const ejercicioNombre = ejercicioSelect.options[ejercicioSelect.selectedIndex].text;

            if (!tipo || !ejercicioId) {
                alert('Debe seleccionar un tipo y un ejercicio.');
                return;
            }

            if(listaAgregados.querySelector('p')) {
                listaAgregados.innerHTML = '';
            }

            let displayHtml = `<strong>${ejercicioNombre}:</strong> `;
            let hiddenInputs = `
                <input type="hidden" name="ejercicios[${ejercicioCounter}][tipo]" value="${tipo}">
                <input type="hidden" name="ejercicios[${ejercicioCounter}][id]" value="${ejercicioId}">
            `;

            const comentario = document.getElementById('temp-comentario').value;
            let detalles = [];

            if (tipo === 'fuerza' || tipo === 'resistencia') {
                const series = document.getElementById('temp-series')?.value || 0;
                const repeticiones = document.getElementById('temp-repeticiones')?.value || 0;
                const peso = document.getElementById('temp-peso')?.value || '';
                const descanso = document.getElementById('temp-descanso')?.value || 0;
                const tiempo = document.getElementById('temp-tiempo')?.value || 0;

                if(series > 0) detalles.push(`${series} series`);
                if(repeticiones > 0) detalles.push(`${repeticiones} reps`);
                if(peso !== '') detalles.push(`${peso} kg`);
                if(tiempo > 0) detalles.push(`${tiempo} seg`);
                if(descanso > 0) detalles.push(`desc. ${descanso}s`);

                hiddenInputs += `
                    <input type="hidden" name="ejercicios[${ejercicioCounter}][series]" value="${series}">
                    <input type="hidden" name="ejercicios[${ejercicioCounter}][repeticiones]" value="${repeticiones}">
                    <input type="hidden" name="ejercicios[${ejercicioCounter}][peso]" value="${peso}">
                    <input type="hidden" name="ejercicios[${ejercicioCounter}][descanso]" value="${descanso}">
                    <input type="hidden" name="ejercicios[${ejercicioCounter}][tiempo]" value="${tiempo}">
                `;
            } else if (tipo === 'equilibrio') {
                const tiempo = document.getElementById('temp-tiempo').value || 0;
                if(tiempo > 0) detalles.push(`${tiempo} seg`);
                hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][tiempo]" value="${tiempo}">`;
            }

            displayHtml += detalles.join(' &times; ');
            if(comentario) displayHtml += ` - <em>(${comentario})</em>`;
            hiddenInputs += `<input type="hidden" name="ejercicios[${ejercicioCounter}][comentario]" value="${comentario}">`;

            const div = document.createElement('div');
            div.id = `ejercicio-item-${ejercicioCounter}`;
            div.style.marginBottom = '10px';
            div.innerHTML = displayHtml;

            const btnEliminar = document.createElement('button');
            btnEliminar.type = 'button';
            btnEliminar.textContent = 'Quitar';
            btnEliminar.style.cssText = 'margin-left: 10px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; max-width: 80px; justify-content: center;';
            const currentCounter = ejercicioCounter;
            btnEliminar.onclick = function() {
                document.getElementById(`ejercicio-item-${currentCounter}`).remove();
                document.getElementById(`hidden-inputs-${currentCounter}`).remove();
                if(listaAgregados.children.length === 0) {
                    listaAgregados.innerHTML = '<p style="color: #6c757d; font-style: italic;">Aún no has añadido ejercicios.</p>';
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