<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'cliente') {
    header('Location: loginView.php');
    exit();
}
$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Cliente';
$clienteId = (int) $_SESSION['usuario_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparativa de Progreso</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="container">
        <header>
            <a href="../index.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Comparativa de Progreso Corporal</h2>
        </header>

        <main class="analytics-layout">
            <section class="analytics-panel">
                <h3><i class="ph ph-sliders"></i> Selecciona los periodos a comparar</h3>
                <p class="text-muted">Elige dos periodos de tu historial para evaluar el progreso de tu entrenamiento por zonas del cuerpo.</p>
                <div class="form-grid-container">
                    <div class="form-group">
                        <label for="granularidad">Tipo de periodo</label>
                        <select id="granularidad">
                            <option value="week">Semanas</option>
                            <option value="month">Meses</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="periodo-a">Periodo A</label>
                        <select id="periodo-a"></select>
                    </div>
                    <div class="form-group">
                        <label for="periodo-b">Periodo B</label>
                        <select id="periodo-b"></select>
                    </div>
                </div>
                <div class="analytics-actions">
                    <button id="btn-cargar-periodos" class="btn-secondary">
                        <i class="ph ph-arrows-clockwise"></i> Actualizar periodos
                    </button>
                    <button id="btn-comparar" class="btn-primary">
                        <i class="ph ph-graph"></i> Comparar periodos
                    </button>
                </div>
                <div id="comparativa-alert" class="analytics-alert" hidden></div>
            </section>

            <section class="analytics-panel">
                <h3><i class="ph ph-pulse"></i> Resumen de métricas</h3>
                <div class="analytics-summary-grid">
                    <div class="analytics-card" data-summary-card="A">
                        <div class="analytics-card-header">
                            <h4>Periodo A</h4>
                            <p data-summary-range="A" class="analytics-card-range">-</p>
                        </div>
                        <ul class="analytics-card-metrics">
                            <li><span>Rutinas</span><span data-summary-value="A-rutinas">0</span></li>
                            <li><span>Ejercicios</span><span data-summary-value="A-ejercicios">0</span></li>
                            <li><span>Series</span><span data-summary-value="A-series">0</span></li>
                            <li><span>Repeticiones</span><span data-summary-value="A-repeticiones">0</span></li>
                            <li><span>Carga acumulada</span><span data-summary-value="A-peso">0 kg</span></li>
                        </ul>
                    </div>
                    <div class="analytics-card" data-summary-card="B">
                        <div class="analytics-card-header">
                            <h4>Periodo B</h4>
                            <p data-summary-range="B" class="analytics-card-range">-</p>
                        </div>
                        <ul class="analytics-card-metrics">
                            <li><span>Rutinas</span><span data-summary-value="B-rutinas">0</span></li>
                            <li><span>Ejercicios</span><span data-summary-value="B-ejercicios">0</span></li>
                            <li><span>Series</span><span data-summary-value="B-series">0</span></li>
                            <li><span>Repeticiones</span><span data-summary-value="B-repeticiones">0</span></li>
                            <li><span>Carga acumulada</span><span data-summary-value="B-peso">0 kg</span></li>
                        </ul>
                    </div>
                    <div class="analytics-card analytics-card--delta" data-summary-card="diff">
                        <div class="analytics-card-header">
                            <h4>Diferencia (B - A)</h4>
                            <p data-summary-range="diff" class="analytics-card-range">Selecciona periodos</p>
                        </div>
                        <ul class="analytics-card-metrics">
                            <li><span>Rutinas</span><span data-summary-value="diff-rutinas">0</span></li>
                            <li><span>Ejercicios</span><span data-summary-value="diff-ejercicios">0</span></li>
                            <li><span>Series</span><span data-summary-value="diff-series">0</span></li>
                            <li><span>Repeticiones</span><span data-summary-value="diff-repeticiones">0</span></li>
                            <li><span>Carga acumulada</span><span data-summary-value="diff-peso">0 kg</span></li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="analytics-panel">
                <h3><i class="ph ph-person-simple-run"></i> Visualización corporal</h3>
                <p class="text-muted">Alterna entre los periodos y su diferencia desde los botones del visor. En modo diferencia, los tonos verdes indican más trabajo en el periodo B, los rojos señalan retrocesos y los contornos punteados marcan zonas sin cambios relevantes.</p>
                <script>
                    window.progresoData = {};
                </script>
                <?php
                $viewerId = 'comparativa-body-viewer';
                $viewerPeriodButtons = [
                    ['key' => 'periodoA', 'label' => 'Periodo A'],
                    ['key' => 'periodoB', 'label' => 'Periodo B'],
                    ['key' => 'diferencia', 'label' => 'Diferencia']
                ];
                $viewerDefaultPeriod = 'diferencia';
                include __DIR__ . '/body_viewer.php';
                ?>
            </section>
        </main>
    </div>

    <script>
        const periodSelectA = document.getElementById('periodo-a');
        const periodSelectB = document.getElementById('periodo-b');
        const granularidadSelect = document.getElementById('granularidad');
        const btnComparar = document.getElementById('btn-comparar');
        const btnRefrescar = document.getElementById('btn-cargar-periodos');
        const alertBox = document.getElementById('comparativa-alert');
        const metricKeys = ['rutinas', 'ejercicios', 'series', 'repeticiones', 'peso'];
        let periodosDisponibles = [];

        const numberFormatters = {
            entero: new Intl.NumberFormat('es-CR', { maximumFractionDigits: 0 }),
            decimal: new Intl.NumberFormat('es-CR', { maximumFractionDigits: 1 })
        };

        function showAlert(message, type = 'info') {
            if (!alertBox) return;
            alertBox.textContent = message;
            alertBox.dataset.type = type;
            alertBox.hidden = false;
        }

        function hideAlert() {
            if (alertBox) {
                alertBox.hidden = true;
            }
        }

        function fillSelectOptions() {
            const makeOption = (periodo) => {
                const option = document.createElement('option');
                option.value = `${periodo.fechaInicio}|${periodo.fechaFin}`;
                option.textContent = `${periodo.label} · ${periodo.rutinas} ${periodo.rutinas === 1 ? 'rutina' : 'rutinas'}`;
                option.dataset.inicio = periodo.fechaInicio;
                option.dataset.fin = periodo.fechaFin;
                return option;
            };

            [periodSelectA, periodSelectB].forEach(select => {
                select.innerHTML = '';
            });

            periodosDisponibles.forEach(periodo => {
                periodSelectA.appendChild(makeOption(periodo));
                periodSelectB.appendChild(makeOption(periodo).cloneNode(true));
            });

            if (periodosDisponibles.length > 0) {
                periodSelectA.selectedIndex = 0;
            }
            if (periodosDisponibles.length > 1) {
                periodSelectB.selectedIndex = 1;
            }
        }

        async function cargarPeriodos() {
            hideAlert();
            const granularidad = granularidadSelect.value;
            try {
                const response = await fetch(`../action/progresoAction.php?action=get_periodos&granularidad=${encodeURIComponent(granularidad)}`);
                const data = await response.json();
                if (Array.isArray(data.periodos) && data.periodos.length > 0) {
                    periodosDisponibles = data.periodos;
                    fillSelectOptions();
                } else {
                    periodosDisponibles = [];
                    fillSelectOptions();
                    showAlert('No se encontraron periodos con rutinas registradas en tu historial.', 'warning');
                }
            } catch (error) {
                showAlert('Ocurrió un error al cargar los periodos disponibles. Intenta más tarde.', 'error');
            }
        }

        function obtenerPeriodoDesdeSelect(select) {
            const value = select.value;
            if (!value) {
                return null;
            }
            const [inicio, fin] = value.split('|');
            const periodo = periodosDisponibles.find(item => item.fechaInicio === inicio && item.fechaFin === fin);
            return periodo || { fechaInicio: inicio, fechaFin: fin, label: 'Personalizado', rutinas: 0 };
        }

        async function obtenerDataset(periodo, etiqueta) {
            const params = new URLSearchParams({
                action: 'get_progreso_rango',
                inicio: periodo.fechaInicio,
                fin: periodo.fechaFin
            });
            const response = await fetch(`../action/progresoAction.php?${params.toString()}`);
            const data = await response.json();
            if (!data.dataset) {
                throw new Error('Respuesta inválida del servidor');
            }
            const dataset = data.dataset;
            dataset.label = etiqueta;
            dataset.meta = dataset.meta || {};
            dataset.meta.rutinasTexto = `${dataset.rutinas} ${dataset.rutinas === 1 ? 'rutina' : 'rutinas'}`;
            return dataset;
        }

        function calcularDiferencia(datasetA, datasetB) {
            const diff = {
                label: 'Diferencia B - A',
                mode: 'delta',
                fechaInicio: datasetA.fechaInicio,
                fechaFin: datasetB.fechaFin,
                rutinas: `A: ${datasetA.rutinas} | B: ${datasetB.rutinas}`,
                metricas: {},
                porcentajes: {},
                meta: { rutinasTexto: `A: ${datasetA.rutinas} · B: ${datasetB.rutinas}` }
            };

            const metricasKeys = Object.keys(datasetA.metricas || {}).concat(Object.keys(datasetB.metricas || {}));
            const metricasUnicas = Array.from(new Set(metricasKeys));
            metricasUnicas.forEach(key => {
                const valorA = datasetA.metricas?.[key] ?? 0;
                const valorB = datasetB.metricas?.[key] ?? 0;
                diff.metricas[key] = valorB - valorA;
            });

            const zonas = new Set([
                ...Object.keys(datasetA.porcentajes || {}),
                ...Object.keys(datasetB.porcentajes || {})
            ]);
            zonas.forEach(zonaId => {
                const valorA = datasetA.porcentajes?.[zonaId] ?? 0;
                const valorB = datasetB.porcentajes?.[zonaId] ?? 0;
                diff.porcentajes[zonaId] = valorB - valorA;
            });

            return diff;
        }

        function formatMetricValue(key, value, mode = 'intensity') {
            if (value == null) {
                return '0';
            }
            const delta = mode === 'delta';
            const numeric = Number(value);
            if (!Number.isFinite(numeric)) {
                return String(value);
            }
            const sign = delta && numeric !== 0 ? (numeric > 0 ? '+' : '−') : '';
            const absolute = delta ? Math.abs(numeric) : numeric;
            switch (key) {
                case 'peso':
                    return `${sign}${numberFormatters.decimal.format(absolute)} kg`;
                default:
                    return `${sign}${numberFormatters.entero.format(absolute)}`;
            }
        }

        function actualizarTarjetaResumen(clave, dataset) {
            const card = document.querySelector(`[data-summary-card="${clave}"]`);
            if (!card || !dataset) {
                return;
            }
            const rangeEl = card.querySelector(`[data-summary-range="${clave}"]`);
            if (rangeEl) {
                rangeEl.textContent = dataset.fechaInicio && dataset.fechaFin
                    ? `${dataset.fechaInicio} → ${dataset.fechaFin}`
                    : '-';
            }
            metricKeys.forEach(metricKey => {
                const valueEl = card.querySelector(`[data-summary-value="${clave}-${metricKey}"]`);
                if (!valueEl) {
                    return;
                }
                const valorBase = metricKey === 'rutinas' ? dataset.rutinas : dataset.metricas?.[metricKey];
                valueEl.textContent = formatMetricValue(metricKey, valorBase, dataset.mode || 'intensity');
                valueEl.classList.toggle('is-positive', (dataset.mode === 'delta') && Number(valorBase) > 0);
                valueEl.classList.toggle('is-negative', (dataset.mode === 'delta') && Number(valorBase) < 0);
            });
        }

        async function ejecutarComparativa() {
            hideAlert();
            const periodoA = obtenerPeriodoDesdeSelect(periodSelectA);
            const periodoB = obtenerPeriodoDesdeSelect(periodSelectB);

            if (!periodoA || !periodoB) {
                showAlert('Selecciona dos periodos válidos para comparar.', 'warning');
                return;
            }

            if (periodoA.fechaInicio === periodoB.fechaInicio && periodoA.fechaFin === periodoB.fechaFin) {
                showAlert('Selecciona periodos distintos para obtener una comparación.', 'warning');
                return;
            }

            try {
                const [datasetA, datasetB] = await Promise.all([
                    obtenerDataset(periodoA, 'Periodo A'),
                    obtenerDataset(periodoB, 'Periodo B')
                ]);

                actualizarTarjetaResumen('A', datasetA);
                actualizarTarjetaResumen('B', datasetB);

                const diff = calcularDiferencia(datasetA, datasetB);
                actualizarTarjetaResumen('diff', diff);

                if (window.bodyViewer) {
                    window.bodyViewer.setDataset({
                        periodoA: datasetA,
                        periodoB: datasetB,
                        diferencia: diff
                    }, { defaultPeriod: 'diferencia' });
                }
            } catch (error) {
                console.error(error);
                showAlert('No fue posible generar la comparativa. Intenta nuevamente.', 'error');
            }
        }

        btnComparar.addEventListener('click', ejecutarComparativa);
        btnRefrescar.addEventListener('click', cargarPeriodos);
        granularidadSelect.addEventListener('change', cargarPeriodos);

        const iniciarComparativa = () => {
            cargarPeriodos().then(() => {
                if (periodosDisponibles.length >= 2) {
                    ejecutarComparativa();
                }
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', iniciarComparativa);
        } else {
            iniciarComparativa();
        }
    </script>
</body>
</html>
