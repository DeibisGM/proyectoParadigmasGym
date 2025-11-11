<?php
session_start();
$tipoUsuario = $_SESSION['tipo_usuario'] ?? '';
if (!isset($_SESSION['usuario_id']) || !in_array($tipoUsuario, ['instructor', 'admin'], true)) {
    header('Location: loginView.php');
    exit();
}

include_once '../business/clienteBusiness.php';
$clienteBusiness = new ClienteBusiness();
$clientes = $clienteBusiness->getAllTBCliente();

$fechaHoy = new DateTimeImmutable('today');
$fechaInicioDefault = $fechaHoy->sub(new DateInterval('P30D'))->format('Y-m-d');
$fechaFinDefault = $fechaHoy->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Clientes</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Seguimiento integral de clientes</h2>

        </header>

        <main class="analytics-layout">
            <section class="analytics-panel">
                <h3><i class="ph ph-users-three"></i> Selección de cliente</h3>
                <div class="form-grid-container">
                    <div class="form-group">
                        <label for="cliente-select">Cliente</label>
                        <select id="cliente-select">
                            <option value="">Selecciona un cliente...</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente->getId(); ?>">
                                    <?php echo htmlspecialchars($cliente->getNombre()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                <div id="seguimiento-alert" class="analytics-alert" hidden></div>
            </section>

            <section class="analytics-panel" id="resultado-cobertura" style="display: none;">
                <h3><i class="ph ph-compass"></i> Resultado de cobertura corporal</h3>
                <div class="coverage-summary">
                    <div>
                        <h4>Resumen de Zonas</h4>
                        <ul id="resumen-zonas" class="coverage-list"></ul>
                    </div>
                </div>
                <script>
                    window.progresoData = {};
                </script>
                <?php
                $viewerId = 'seguimiento-body-viewer';
                $viewerPeriodButtons = [];
                $viewerDefaultPeriod = 'cobertura';
                include __DIR__ . '/body_viewer.php';
                ?>
            </section>

            <section class="analytics-panel" id="sugerencias-anteriores" style="display: none;">
                <h3><i class="ph ph-list-checks"></i> Sugerencias Anteriores</h3>
                <div id="lista-sugerencias"></div>
            </section>

            <section class="analytics-panel" id="crear-rutina" style="display: none;">
                <h3><i class="ph ph-note-pencil"></i> Sugerir Nueva Rutina</h3>
                <p class="text-muted">Diseña una rutina basada en las zonas que requieren atención. El cliente verá esta rutina en su panel personal.</p>
                <form action="../action/rutinaAction.php" method="POST" id="form-planificador">
                    <input type="hidden" name="sugerir_rutina" value="1">
                    <input type="hidden" name="cliente_id" id="rutina-cliente-id" value="">
                    <div class="form-grid-container">
                        <div class="form-group">
                            <label for="fecha-rutina">Fecha de Sugerencia</label>
                            <input type="date" id="fecha-rutina" name="fecha_rutina" value="<?php echo $fechaFinDefault; ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="observacion-rutina" style="margin-top: 1.5rem;">Observaciones generales</label>
                        <textarea id="observacion-rutina" name="observacion_rutina" placeholder="Notas importantes o indicaciones especiales" style="margin-bottom: 1.5rem;"></textarea>
                    </div>

                    <div class="planner-block">
                        <h4>Añadir ejercicio</h4>
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="tipo-ejercicio">Tipo de ejercicio</label>
                                <select id="tipo-ejercicio">
                                    <option value="">Seleccione...</option>
                                    <option value="fuerza">Fuerza</option>
                                    <option value="resistencia">Resistencia</option>
                                    <option value="equilibrio">Equilibrio</option>
                                    <option value="flexibilidad">Flexibilidad</option>
                                </select>
                            </div>
                            <div class="form-group" id="container-ejercicio-select" style="display:none;">
                                <label for="ejercicio-select" style="margin-bottom: 0.5rem;">Ejercicio</label>
                                <select id="ejercicio-select"></select>
                            </div>
                        </div>
                        <div id="campos-ejercicio" class="planner-fields"></div>
                        <button type="button" id="btn-agregar-ejercicio" class="btn-secondary" style="margin-top: 1rem;">
                            <i class="ph ph-plus"></i> Agregar a la rutina
                        </button>
                    </div>

                    <div class="planner-block">
                        <h4>Ejercicios incluidos</h4>
                        <div id="lista-ejercicios-agregados" class="planner-list">
                            <p class="planner-placeholder text-muted">Aún no se han añadido ejercicios.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="ph ph-floppy-disk"></i> Sugerir Rutina al Cliente
                    </button>
                </form>
            </section>
        </main>
    </div>

    <script>
        document.getElementById('fecha-rutina').addEventListener('click', function() {
            try {
                this.showPicker();
            } catch (error) {
                console.error("showPicker() is not supported by this browser.");
            }
        });

        const clienteSelect = document.getElementById('cliente-select');
        const alertSeguimiento = document.getElementById('seguimiento-alert');
        const resumenZonasList = document.getElementById('resumen-zonas');
        const rutinaClienteInput = document.getElementById('rutina-cliente-id');
        const formPlanificador = document.getElementById('form-planificador');
        const resultadoCoberturaSection = document.getElementById('resultado-cobertura');
        const crearRutinaSection = document.getElementById('crear-rutina');
        const sugerenciasAnterioresSection = document.getElementById('sugerencias-anteriores');
        const listaSugerencias = document.getElementById('lista-sugerencias');

        async function cargarSugerencias() {
            const clienteId = clienteSelect.value;
            if (!clienteId) {
                sugerenciasAnterioresSection.style.display = 'none';
                return;
            }

            try {
                const params = new URLSearchParams({
                    action: 'get_sugerencias_cliente',
                    cliente_id: clienteId
                });
                const response = await fetch(`../action/rutinaAction.php?${params.toString()}`);
                const data = await response.json();

                listaSugerencias.innerHTML = '';
                if (data.length > 0) {
                    const table = document.createElement('table');
                    table.className = 'table-clients';
                    table.innerHTML = `
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Observaciones</th>
                                <th>Ejercicios</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    `;
                    const tbody = table.querySelector('tbody');
                    data.forEach(rutina => {
                        const tr = document.createElement('tr');
                        let ejerciciosHtml = '<ul style="padding-left: 1rem; margin: 0;">';
                        rutina.ejercicios.forEach(ej => {
                            ejerciciosHtml += `<li><strong>${ej.nombreEjercicio}</strong></li>`;
                        });
                        ejerciciosHtml += '</ul>';

                        tr.innerHTML = `
                            <td data-label="Fecha">${new Date(rutina.tbrutinafecha).toLocaleDateString()}</td>
                            <td data-label="Observaciones">${rutina.tbrutinaobservacion || '-'}</td>
                            <td data-label="Ejercicios">${ejerciciosHtml}</td>
                            <td data-label="Acciones" class="actions">
                                <form action="../action/rutinaAction.php" method="POST" onsubmit="return confirm('¿Eliminar esta sugerencia?');" style="margin: 0;">
                                    <input type="hidden" name="delete_rutina" value="1">
                                    <input type="hidden" name="rutina_id" value="${rutina.tbrutinaid}">
                                    <button type="submit" class="btn-row btn-danger"><i class="ph ph-trash"></i></button>
                                </form>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                    listaSugerencias.appendChild(table);
                    sugerenciasAnterioresSection.style.display = 'block';
                } else {
                    sugerenciasAnterioresSection.style.display = 'none';
                }
            } catch (error) {
                console.error(error);
                setAlert('Ocurrió un error al cargar las sugerencias.', 'error');
            }
        }

        function setAlert(message, type = 'info') {
            if (!alertSeguimiento) return;
            alertSeguimiento.textContent = message;
            alertSeguimiento.dataset.type = type;
            alertSeguimiento.hidden = false;
        }

        function clearAlert() {
            if (alertSeguimiento) {
                alertSeguimiento.hidden = true;
            }
        }

        function resetCoverageLists() {
            resumenZonasList.innerHTML = '<li class="text-muted">Sin datos</li>';
        }

        function renderZonaList(container, zonas, emptyMessage) {
            if (!container) return;
            container.innerHTML = '';
            if (!zonas || zonas.length === 0) {
                const item = document.createElement('li');
                item.className = 'text-muted';
                item.textContent = emptyMessage;
                container.appendChild(item);
                return;
            }
            zonas.forEach(zona => {
                const item = document.createElement('li');
                item.className = 'coverage-list-item';
                item.classList.add(`is-categoria-${zona.categoria.toLowerCase()}`);
                const nombre = document.createElement('span');
                nombre.textContent = zona.nombre;
                const valor = document.createElement('span');
                valor.textContent = `${zona.score} ejercicios (${zona.categoria})`;
                item.appendChild(nombre);
                item.appendChild(valor);
                container.appendChild(item);
            });
        }

        async function cargarCobertura() {
            clearAlert();
            const clienteId = clienteSelect.value;
            if (!clienteId) {
                resultadoCoberturaSection.style.display = 'none';
                crearRutinaSection.style.display = 'none';
                sugerenciasAnterioresSection.style.display = 'none';
                return;
            }
            rutinaClienteInput.value = clienteId;

            const finDate = new Date();
            const inicioDate = new Date();
            inicioDate.setDate(finDate.getDate() - 30);

            const formatDate = (date) => date.toISOString().split('T')[0];
            const inicio = formatDate(inicioDate);
            const fin = formatDate(finDate);

            try {
                const params = new URLSearchParams({
                    action: 'get_cobertura_cliente',
                    clienteId,
                    inicio,
                    fin
                });
                const response = await fetch(`../action/progresoAction.php?${params.toString()}`);
                const data = await response.json();

                resultadoCoberturaSection.style.display = 'block';
                crearRutinaSection.style.display = 'block';

                if (!data.dataset) {
                    setAlert('No se pudieron recuperar los datos del cliente.', 'error');
                    resetCoverageLists();
                    return;
                }

                renderZonaList(resumenZonasList, data.resumenZonas || [], 'Sin registros en el rango seleccionado');

                if (window.bodyViewer) {
                    window.bodyViewer.setDataset({
                        cobertura: data.dataset
                    }, { defaultPeriod: 'cobertura' });
                }
            } catch (error) {
                console.error(error);
                setAlert('Ocurrió un error al analizar la cobertura. Intenta nuevamente.', 'error');
                resetCoverageLists();
            }
            
            cargarSugerencias();
        }

        clienteSelect.addEventListener('change', cargarCobertura);

        // Inicializar estado visual del planificador de rutinas
        (function inicializarPlanificador() {
            const tipoSelect = document.getElementById('tipo-ejercicio');
            const ejercicioSelectContainer = document.getElementById('container-ejercicio-select');
            const ejercicioSelect = document.getElementById('ejercicio-select');
            const camposContainer = document.getElementById('campos-ejercicio');
            const btnAgregar = document.getElementById('btn-agregar-ejercicio');
            const listaAgregados = document.getElementById('lista-ejercicios-agregados');
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
                        <div class="form-group" style="grid-column: span 2;"><label>Tiempo (seg):</label><input type="number" id="temp-tiempo" min="0"></div>
                    `;
                } else {
                    fieldsHtml += `
                        <div class="form-group" style="grid-column: span 2;"><label>Series:</label><input type="number" id="temp-series" min="1"></div>
                        <div class="form-group" style="grid-column: span 2;"><label>Tiempo (seg):</label><input type="number" id="temp-tiempo" min="0"></div>
                    `;
                }
                fieldsHtml += '<div class="form-group" style="grid-column: 1 / -1;"><label>Comentario:</label><textarea id="temp-comentario" rows="2" placeholder="Notas para este ejercicio"></textarea></div>';
                fieldsHtml += '</div>';
                camposContainer.innerHTML = fieldsHtml;
            });

            btnAgregar.addEventListener('click', function () {
                const tipo = tipoSelect.value;
                const ejercicioId = ejercicioSelect.value;
                const ejercicioNombre = ejercicioSelect.options[ejercicioSelect.selectedIndex]?.textContent || '';
                if (!tipo || !ejercicioId) {
                    alert('Selecciona un tipo de ejercicio y un ejercicio válido.');
                    return;
                }

                const getValue = (id) => document.getElementById(id)?.value || '';
                const series = getValue('temp-series');
                const repeticiones = getValue('temp-repeticiones');
                const peso = getValue('temp-peso');
                const tiempo = getValue('temp-tiempo');
                const descanso = getValue('temp-descanso');
                const comentario = getValue('temp-comentario');

                const contenedor = document.createElement('div');
                contenedor.className = 'planner-item';
                contenedor.innerHTML = `
                    <div>
                        <strong>${ejercicioNombre}</strong>
                        <p class="text-muted">${tipo.charAt(0).toUpperCase() + tipo.slice(1)}</p>
                        <p>
                            ${series ? `${series} series · ` : ''}
                            ${repeticiones ? `${repeticiones} reps · ` : ''}
                            ${peso ? `${peso} kg · ` : ''}
                            ${tiempo ? `${tiempo} seg · ` : ''}
                            ${descanso ? `Descanso ${descanso} seg` : ''}
                        </p>
                        ${comentario ? `<p class="text-muted">${comentario}</p>` : ''}
                    </div>
                    <button type="button" class="btn-row btn-danger" data-remove>
                        <i class="ph ph-trash"></i>
                    </button>
                `;

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `ejercicios[${ejercicioCounter}]`;
                input.value = JSON.stringify({
                    tipo,
                    id: ejercicioId,
                    series,
                    repeticiones,
                    peso,
                    tiempo,
                    descanso,
                    comentario
                });
                contenedor.appendChild(input);

                listaAgregados.appendChild(contenedor);
                const placeholder = listaAgregados.querySelector('.planner-placeholder');
                if (placeholder) {
                    placeholder.remove();
                }

                contenedor.querySelector('[data-remove]').addEventListener('click', () => {
                    contenedor.remove();
                    if (!listaAgregados.querySelector('.planner-item')) {
                        listaAgregados.innerHTML = '<p class="planner-placeholder text-muted">Aún no se han añadido ejercicios.</p>';
                    }
                });

                ejercicioCounter++;
                camposContainer.innerHTML = '';
                ejercicioSelectContainer.style.display = 'none';
                ejercicioSelect.innerHTML = '';
                tipoSelect.value = '';
            });

            formPlanificador.addEventListener('submit', () => {
                const entradas = formPlanificador.querySelectorAll('input[name^="ejercicios"]');
                entradas.forEach((input, index) => {
                    const datos = JSON.parse(input.value);
                    for (const [clave, valor] of Object.entries(datos)) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = `ejercicios[${index}][${clave}]`;
                        hidden.value = valor;
                        formPlanificador.appendChild(hidden);
                    }
                    input.remove();
                });
            });
        })();
    </script>
</body>
</html>
