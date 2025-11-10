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
            <p class="page-subtitle">Visualiza las zonas trabajadas por tus clientes y diseña nuevas rutinas equilibradas.</p>
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
                    <div class="form-group">
                        <label for="fecha-inicio">Fecha inicio</label>
                        <input type="date" id="fecha-inicio" value="<?php echo $fechaInicioDefault; ?>">
                    </div>
                    <div class="form-group">
                        <label for="fecha-fin">Fecha fin</label>
                        <input type="date" id="fecha-fin" value="<?php echo $fechaFinDefault; ?>">
                    </div>
                </div>
                <div class="analytics-actions">
                    <button id="btn-analizar" class="btn-primary">
                        <i class="ph ph-activity"></i> Analizar cobertura
                    </button>
                </div>
                <div id="seguimiento-alert" class="analytics-alert" hidden></div>
            </section>

            <section class="analytics-panel">
                <h3><i class="ph ph-compass"></i> Resultado de cobertura corporal</h3>
                <div class="coverage-summary">
                    <div>
                        <h4>Zonas trabajadas</h4>
                        <ul id="zonas-trabajadas" class="coverage-list"></ul>
                    </div>
                    <div>
                        <h4>Zonas pendientes</h4>
                        <ul id="zonas-faltantes" class="coverage-list"></ul>
                    </div>
                </div>
                <script>
                    window.progresoData = {};
                </script>
                <?php
                $viewerId = 'seguimiento-body-viewer';
                $viewerPeriodButtons = [
                    ['key' => 'cobertura', 'label' => 'Cobertura']
                ];
                $viewerDefaultPeriod = 'cobertura';
                include __DIR__ . '/body_viewer.php';
                ?>
            </section>

            <section class="analytics-panel">
                <h3><i class="ph ph-note-pencil"></i> Crear rutina personalizada</h3>
                <p class="text-muted">Diseña una rutina basada en las zonas que requieren atención. El cliente verá esta rutina en su panel personal.</p>
                <form action="../action/rutinaAction.php" method="POST" id="form-planificador">
                    <input type="hidden" name="create_rutina" value="1">
                    <input type="hidden" name="cliente_id" id="rutina-cliente-id" value="">
                    <div class="form-grid-container">
                        <div class="form-group">
                            <label for="fecha-rutina">Fecha</label>
                            <input type="date" id="fecha-rutina" name="fecha_rutina" value="<?php echo $fechaFinDefault; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="observacion-rutina">Observaciones generales</label>
                            <textarea id="observacion-rutina" name="observacion_rutina" placeholder="Notas importantes o indicaciones especiales"></textarea>
                        </div>
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
                                <label for="ejercicio-select">Ejercicio</label>
                                <select id="ejercicio-select"></select>
                            </div>
                        </div>
                        <div id="campos-ejercicio" class="planner-fields"></div>
                        <button type="button" id="btn-agregar-ejercicio" class="btn-secondary">
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
                        <i class="ph ph-floppy-disk"></i> Guardar rutina para el cliente
                    </button>
                </form>
            </section>
        </main>
    </div>

    <script>
        const clienteSelect = document.getElementById('cliente-select');
        const fechaInicioInput = document.getElementById('fecha-inicio');
        const fechaFinInput = document.getElementById('fecha-fin');
        const btnAnalizar = document.getElementById('btn-analizar');
        const alertSeguimiento = document.getElementById('seguimiento-alert');
        const zonasTrabajadasList = document.getElementById('zonas-trabajadas');
        const zonasFaltantesList = document.getElementById('zonas-faltantes');
        const rutinaClienteInput = document.getElementById('rutina-cliente-id');
        const formPlanificador = document.getElementById('form-planificador');

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
            zonasTrabajadasList.innerHTML = '<li class="text-muted">Sin datos</li>';
            zonasFaltantesList.innerHTML = '<li class="text-muted">Sin datos</li>';
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
                const nombre = document.createElement('span');
                nombre.textContent = zona.nombre;
                const valor = document.createElement('span');
                valor.textContent = `${Math.round(zona.porcentaje)}%`;
                item.appendChild(nombre);
                item.appendChild(valor);
                container.appendChild(item);
            });
        }

        async function cargarCobertura() {
            clearAlert();
            const clienteId = clienteSelect.value;
            if (!clienteId) {
                setAlert('Selecciona un cliente para analizar la cobertura.', 'warning');
                return;
            }
            rutinaClienteInput.value = clienteId;

            const inicio = fechaInicioInput.value;
            const fin = fechaFinInput.value;
            if (!inicio || !fin) {
                setAlert('Selecciona un rango de fechas válido.', 'warning');
                return;
            }

            try {
                const params = new URLSearchParams({
                    action: 'get_cobertura_cliente',
                    clienteId,
                    inicio,
                    fin
                });
                const response = await fetch(`../action/progresoAction.php?${params.toString()}`);
                const data = await response.json();
                if (!data.dataset) {
                    setAlert('No se pudieron recuperar los datos del cliente.', 'error');
                    resetCoverageLists();
                    return;
                }

                renderZonaList(zonasTrabajadasList, data.resumenZonas?.trabajadas || [], 'Sin registros en el rango seleccionado');
                renderZonaList(zonasFaltantesList, data.resumenZonas?.faltantes || [], 'Todas las zonas tienen actividad');

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
        }

        btnAnalizar.addEventListener('click', cargarCobertura);
        clienteSelect.addEventListener('change', () => {
            rutinaClienteInput.value = clienteSelect.value || '';
        });

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
                        <div class="form-group"><label>Tiempo (seg):</label><input type="number" id="temp-tiempo" min="0"></div>
                    `;
                } else {
                    fieldsHtml += `
                        <div class="form-group"><label>Series:</label><input type="number" id="temp-series" min="1"></div>
                        <div class="form-group"><label>Tiempo (seg):</label><input type="number" id="temp-tiempo" min="0"></div>
                    `;
                }
                fieldsHtml += '<div class="form-group"><label>Comentario:</label><textarea id="temp-comentario" rows="2" placeholder="Notas para este ejercicio"></textarea></div>';
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
