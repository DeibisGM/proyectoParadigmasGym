<?php
include_once __DIR__ . '/../data/rutinaData.php';
include_once __DIR__ . '/../data/ejercicioSubzonaData.php';
include_once __DIR__ . '/../data/cuerpoZonaData.php';

class ProgresoBusiness {
    private const ZONA_TRABAJADA_THRESHOLD = 5;
    private $rutinaData;
    private $ejercicioSubzonaData;
    private $cuerpoZonaData;
    private $subzonaSvgMap = [
        '29' => ['pectoralmayor'],
        '30' => ['pectoralmayor'],
        '31' => ['pectoralmayor'],
        '25' => ['gemelosfrontal', 'gemelostrasero'],
        '28' => ['cuadriceps'],
        '27' => ['Isquiotibiales'],
        '26' => ['gluteos'],
        '23' => ['abductores'],
        '24' => ['abductores'],
        '8'  => ['abdominales'],
        '10' => ['abdominales'],
        '9'  => ['oblicuosfrontales', 'oblicuotrasero'],
        '11' => ['dorsalancho'],
        '14' => ['dorsalancho'],
        '12' => ['trapeciofrontal', 'trapeciotrasero'],
        '13' => ['Infraespinoso'],
        '16' => ['hombrofrontal'],
        '18' => ['hombrotrasero'],
        '17' => ['hombrofrontal', 'hombrotrasero'],
        '22' => ['biceps'],
        '21' => ['triceps'],
        '20' => ['Braquiorradialfrontal', 'Braquiorradialtrasero', 'flexordedos', 'extensordedos'],
        '15' => ['serratoanterior'],
        '37' => ['abdominales', 'oblicuosfrontales', 'oblicuotrasero']
    ];

    public function __construct() {
        $this->rutinaData = new RutinaData();
        $this->ejercicioSubzonaData = new ejercicioSubzonaData();
        $this->cuerpoZonaData = new CuerpoZonaData();
    }

    /**
     * Mantiene compatibilidad con la firma original devolviendo únicamente la distribución
     * porcentual para un rango en días específico.
     */
    public function getProgresoVisual($clienteId, $dias = 30) {
        $fechaFin = new DateTimeImmutable('today');
        $fechaInicio = $fechaFin->modify("-$dias days");

        $resultado = $this->calcularProgresoEntreFechas($clienteId, $fechaInicio, $fechaFin);
        return $resultado['porcentajes'];
    }

    /**
     * Retorna la información necesaria para el visor corporal agrupada por periodo (día, semana y mes).
     */
    public function getProgresoPorPeriodos($clienteId) {
        $periodos = ['daily', 'weekly', 'monthly', 'all'];
        $respuesta = [];

        $hoy = new DateTimeImmutable('today');
        $ultimaFechaRegistro = $this->rutinaData->getUltimaFechaRutinaPorCliente($clienteId);
        $primeraFechaRegistro = method_exists($this->rutinaData, 'getPrimeraFechaRutinaPorCliente')
            ? $this->rutinaData->getPrimeraFechaRutinaPorCliente($clienteId)
            : null;

        $fechaUltimaRutina = $ultimaFechaRegistro
            ? DateTimeImmutable::createFromFormat('Y-m-d', $ultimaFechaRegistro) ?: $hoy
            : null;
        $fechaPrimeraRutina = $primeraFechaRegistro
            ? DateTimeImmutable::createFromFormat('Y-m-d', $primeraFechaRegistro) ?: $fechaUltimaRutina
            : null;

        $labels = [
            'daily' => 'Últimas 24 horas',
            'weekly' => 'Semana reciente',
            'monthly' => 'Último mes',
            'all' => 'Historial completo'
        ];

        foreach ($periodos as $periodo) {
            [$inicio, $fin] = $this->obtenerRangoFechasPorPeriodo(
                $periodo,
                $hoy,
                $fechaPrimeraRutina,
                $fechaUltimaRutina
            );
            $resultado = $this->calcularProgresoEntreFechas($clienteId, $inicio, $fin);

            $respuesta[$periodo] = [
                'fechaInicio' => $inicio->format('Y-m-d'),
                'fechaFin' => $fin->format('Y-m-d'),
                'rutinas' => $resultado['rutinas'],
                'totalInstancias' => $resultado['totalInstancias'],
                'porcentajes' => $resultado['porcentajes'],
                'metricas' => $resultado['metricas'],
                'zonas' => $resultado['zonas'],
                'label' => $labels[$periodo] ?? ucfirst($periodo),
                'mode' => 'intensity'
            ];
        }

        return $respuesta;
    }

    /**
     * Recupera los indicadores de progreso para un rango personalizado.
     */
    public function getProgresoPorRango($clienteId, $fechaInicio, $fechaFin) {
        $inicio = $this->parseFecha($fechaInicio) ?? new DateTimeImmutable($fechaInicio);
        $fin = $this->parseFecha($fechaFin) ?? new DateTimeImmutable($fechaFin);

        if ($fin < $inicio) {
            [$inicio, $fin] = [$fin, $inicio];
        }

        $resultado = $this->calcularProgresoEntreFechas($clienteId, $inicio, $fin);

        $resultado['fechaInicio'] = $inicio->format('Y-m-d');
        $resultado['fechaFin'] = $fin->format('Y-m-d');
        $resultado['mode'] = 'intensity';

        return $resultado;
    }

    /**
     * Lista los periodos disponibles (semanas o meses) con actividad registrada.
     */
    public function getPeriodosDisponibles($clienteId, $granularidad = 'week', $limite = 12) {
        $rutinas = $this->rutinaData->getRutinasPorCliente($clienteId);
        if (empty($rutinas)) {
            return [];
        }

        $granularidad = strtolower($granularidad);
        $buckets = [];

        foreach ($rutinas as $rutina) {
            $fecha = $this->parseFecha($rutina->getFecha());
            if (!$fecha) {
                continue;
            }

            if ($granularidad === 'month') {
                $inicio = $fecha->modify('first day of this month');
                $fin = $fecha->modify('last day of this month');
                $clave = $inicio->format('Y-m');
                $label = sprintf('Mes %s/%s', $inicio->format('m'), $inicio->format('Y'));
            } else {
                $inicio = $fecha->modify('monday this week');
                $fin = $fecha->modify('sunday this week');
                $clave = sprintf('%s-W%s', $inicio->format('o'), $inicio->format('W'));
                $label = sprintf('Semana %s (%s - %s)',
                    $inicio->format('W'),
                    $inicio->format('d/m'),
                    $fin->format('d/m')
                );
            }

            if (!isset($buckets[$clave])) {
                $buckets[$clave] = [
                    'clave' => $clave,
                    'fechaInicio' => $inicio->format('Y-m-d'),
                    'fechaFin' => $fin->format('Y-m-d'),
                    'label' => $label,
                    'rutinas' => 0
                ];
            }

            $buckets[$clave]['rutinas']++;
        }

        $periodos = array_values($buckets);
        usort($periodos, function ($a, $b) {
            return strcmp($b['fechaInicio'], $a['fechaInicio']);
        });

        if ($limite > 0) {
            $periodos = array_slice($periodos, 0, $limite);
        }

        return $periodos;
    }

    /**
     * Genera un resumen de cobertura corporal para un cliente en un rango concreto.
     */
    public function getCoberturaCliente($clienteId, DateTimeImmutable $inicio, DateTimeImmutable $fin) {
        if ($fin < $inicio) {
            [$inicio, $fin] = [$fin, $inicio];
        }

        $resultado = $this->calcularProgresoEntreFechas($clienteId, $inicio, $fin);
        $resultado['fechaInicio'] = $inicio->format('Y-m-d');
        $resultado['fechaFin'] = $fin->format('Y-m-d');
        $resultado['label'] = 'Cobertura seleccionada';
        $resultado['mode'] = 'intensity';

        $zonasStats = $resultado['zonas'];
        $zonasActivas = $this->cuerpoZonaData->getActiveTBCuerpoZona();

        $resumenZonas = [];

        foreach ($zonasActivas as $zona) {
            $subzonasRaw = $this->cuerpoZonaData->getCuerpoZonaSubZonaId($zona->getIdCuerpoZona());
            $subzonasIds = array_filter(array_map('trim', explode('$', (string) $subzonasRaw)));
            $svgIds = [];
            foreach ($subzonasIds as $subzonaId) {
                $svgIds = array_merge($svgIds, $this->resolveSvgTargets($subzonaId));
            }
            $svgIds = array_values(array_unique($svgIds));

            $ejercicioCount = 0;
            foreach ($svgIds as $svgId) {
                $ejercicioCount += $zonasStats[$svgId]['instancias'] ?? 0;
            }
            $ejercicioCount = round($ejercicioCount);

            $categoria = 'Pendiente';
            if ($ejercicioCount > 0) {
                if ($ejercicioCount <= 2) {
                    $categoria = 'Bajo';
                } elseif ($ejercicioCount <= 4) {
                    $categoria = 'Medio';
                } else {
                    $categoria = 'Alto';
                }
            }

            $registro = [
                'id' => $zona->getIdCuerpoZona(),
                'nombre' => $zona->getNombreCuerpoZona(),
                'score' => $ejercicioCount,
                'categoria' => $categoria,
                'componentes' => $svgIds
            ];

            $resumenZonas[] = $registro;
        }

        usort($resumenZonas, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return [
            'dataset' => $resultado,
            'resumenZonas' => $resumenZonas
        ];
    }

    private function parseFecha($fecha) {
        if ($fecha instanceof DateTimeImmutable) {
            return $fecha;
        }
        if ($fecha instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($fecha);
        }
        if (is_string($fecha)) {
            $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $fecha);
            if ($parsed !== false) {
                return $parsed;
            }
        }
        return null;
    }

    private function calcularProgresoEntreFechas($clienteId, DateTimeImmutable $inicio, DateTimeImmutable $fin) {
        $rutinas = $this->rutinaData->getRutinasPorClienteEnRangoFechas(
            $clienteId,
            $inicio->format('Y-m-d'),
            $fin->format('Y-m-d')
        );

        $distribucion = $this->calcularDistribucionSubzonas($rutinas);

        return [
            'rutinas' => count($rutinas),
            'totalInstancias' => $distribucion['totalInstancias'],
            'porcentajes' => $distribucion['porcentajes'],
            'zonas' => $distribucion['zonas'],
            'metricas' => $distribucion['metricas'],
            'mode' => 'intensity'
        ];
    }

    private function calcularDistribucionSubzonas($rutinas) {
        $resultado = [
            'totalInstancias' => 0,
            'porcentajes' => [],
            'zonas' => [],
            'metricas' => [
                'ejercicios' => 0,
                'series' => 0,
                'repeticiones' => 0,
                'peso' => 0.0,
                'tiempo' => 0,
                'volumen' => 0.0
            ]
        ];

        if (empty($rutinas)) {
            return $resultado;
        }

        $svgStats = [];
        $totalVolumenReferencia = 0.0;

        foreach ($rutinas as $rutina) {
            $ejercicios = $this->rutinaData->getEjerciciosPorRutinaId($rutina->getId());
            foreach ($ejercicios as $ejercicio) {
                $metricas = $this->calcularMetricasEjercicio($ejercicio);
                $volumenBase = $metricas['volumen'];
                if ($volumenBase <= 0) {
                    $volumenBase = max($metricas['series'], 1);
                }

                $resultado['metricas']['ejercicios']++;
                $resultado['metricas']['series'] += $metricas['series'];
                $resultado['metricas']['repeticiones'] += $metricas['repeticiones'];
                $resultado['metricas']['peso'] += $metricas['peso'];
                $resultado['metricas']['tiempo'] += $metricas['tiempo'];
                $resultado['metricas']['volumen'] += $volumenBase;

                $subzonasDelEjercicio = $this->ejercicioSubzonaData->getSubzonasPorEjercicio(
                    $ejercicio->getEjercicioId(),
                    $ejercicio->getTipo()
                );

                if (empty($subzonasDelEjercicio)) {
                    continue;
                }

                foreach ($subzonasDelEjercicio as $subzona) {
                    $subzonaIds = array_filter(array_map('trim', explode('$', $subzona->getSubzona())));
                    if (empty($subzonaIds)) {
                        continue;
                    }

                    foreach ($subzonaIds as $subzonaId) {
                        $targets = $this->resolveSvgTargets($subzonaId);
                        if (empty($targets)) {
                            continue;
                        }

                        $division = max(count($subzonaIds) * count($targets), 1);
                        $aporteVolumen = $volumenBase / $division;
                        $aporteSeries = $metricas['series'] / $division;
                        $aporteRepeticiones = $metricas['repeticiones'] / $division;
                        $aportePeso = $metricas['peso'] / $division;
                        $aporteTiempo = $metricas['tiempo'] / $division;

                        foreach ($targets as $targetId) {
                            if (!isset($svgStats[$targetId])) {
                                $svgStats[$targetId] = [
                                    'valor' => 0.0,
                                    'series' => 0.0,
                                    'repeticiones' => 0.0,
                                    'peso' => 0.0,
                                    'tiempo' => 0.0,
                                    'instancias' => 0.0
                                ];
                            }
                            $svgStats[$targetId]['valor'] += $aporteVolumen;
                            $svgStats[$targetId]['series'] += $aporteSeries;
                            $svgStats[$targetId]['repeticiones'] += $aporteRepeticiones;
                            $svgStats[$targetId]['peso'] += $aportePeso;
                            $svgStats[$targetId]['tiempo'] += $aporteTiempo;
                            $svgStats[$targetId]['instancias'] += 1 / max(count($targets), 1);
                        }

                        $totalVolumenReferencia += $aporteVolumen;
                    }
                }
            }
        }

        if ($totalVolumenReferencia <= 0) {
            $totalVolumenReferencia = 1;
        }

        $porcentajesCrudos = [];
        foreach ($svgStats as $zonaId => $stats) {
            $porcentaje = ($stats['valor'] / $totalVolumenReferencia) * 100;
            $porcentajesCrudos[$zonaId] = $porcentaje;
            $svgStats[$zonaId]['valor'] = round($stats['valor'], 2);
            $svgStats[$zonaId]['series'] = round($stats['series'], 2);
            $svgStats[$zonaId]['repeticiones'] = round($stats['repeticiones'], 2);
            $svgStats[$zonaId]['peso'] = round($stats['peso'], 2);
            $svgStats[$zonaId]['tiempo'] = round($stats['tiempo'], 2);
            $svgStats[$zonaId]['instancias'] = round($stats['instancias'], 2);
        }

        $porcentajes = $this->normalizarPorcentajes($porcentajesCrudos, 1);

        foreach ($porcentajes as $zonaId => $porcentaje) {
            if (isset($svgStats[$zonaId])) {
                $svgStats[$zonaId]['porcentaje'] = $porcentaje;
            }
        }

        $resultado['totalInstancias'] = (int) round(array_sum(array_column($svgStats, 'instancias')));
        if ($resultado['totalInstancias'] === 0) {
            $resultado['totalInstancias'] = $resultado['metricas']['ejercicios'];
        }

        $resultado['porcentajes'] = $porcentajes;
        $resultado['zonas'] = $svgStats;
        $resultado['metricas']['peso'] = round($resultado['metricas']['peso'], 2);
        $resultado['metricas']['volumen'] = round($resultado['metricas']['volumen'], 2);

        return $resultado;
    }

    private function calcularMetricasEjercicio($ejercicio) {
        $series = max((int) $ejercicio->getSeries(), 0);
        $repeticiones = max((int) $ejercicio->getRepeticiones(), 0);
        $peso = (float) $ejercicio->getPeso();
        $tiempo = max((int) $ejercicio->getTiempo(), 0);
        $tipo = strtolower($ejercicio->getTipo());

        $totalRepeticiones = $series > 0 && $repeticiones > 0 ? $series * $repeticiones : $repeticiones;
        if ($totalRepeticiones === 0 && $repeticiones > 0) {
            $totalRepeticiones = $repeticiones;
        }

        $totalTiempo = $series > 0 && $tiempo > 0 ? $series * $tiempo : $tiempo;

        $volumen = 0.0;
        switch ($tipo) {
            case 'fuerza':
                $baseSeries = $series > 0 ? $series : 1;
                $baseReps = $repeticiones > 0 ? $repeticiones : 1;
                $basePeso = $peso > 0 ? $peso : 1;
                $volumen = $baseSeries * $baseReps * $basePeso;
                break;
            case 'resistencia':
                $baseSeries = $series > 0 ? $series : 1;
                if ($tiempo > 0) {
                    $volumen = $baseSeries * $tiempo;
                } elseif ($repeticiones > 0) {
                    $volumen = $baseSeries * $repeticiones;
                } else {
                    $volumen = $baseSeries;
                }
                break;
            case 'equilibrio':
            case 'flexibilidad':
                $baseSeries = $series > 0 ? $series : 1;
                $volumen = $baseSeries * ($tiempo > 0 ? $tiempo : 60);
                break;
            default:
                $baseSeries = $series > 0 ? $series : 1;
                $volumen = $baseSeries * ($repeticiones > 0 ? $repeticiones : ($tiempo > 0 ? $tiempo : 1));
                break;
        }

        if ($volumen <= 0) {
            $volumen = max($series, 1);
        }

        $tonelaje = 0.0;
        if ($peso > 0 && $totalRepeticiones > 0) {
            $tonelaje = $peso * $totalRepeticiones;
        }

        return [
            'series' => $series,
            'repeticiones' => $totalRepeticiones,
            'peso' => $tonelaje,
            'tiempo' => $totalTiempo,
            'volumen' => $volumen
        ];
    }

    private function normalizarPorcentajes(array $valores, int $precision = 1): array {
        $valoresFiltrados = array_filter($valores, static function ($valor) {
            return is_numeric($valor) && $valor > 0;
        });

        if (empty($valoresFiltrados)) {
            return array_fill_keys(array_keys($valores), 0.0);
        }

        $suma = array_sum($valoresFiltrados);
        if ($suma <= 0) {
            return array_fill_keys(array_keys($valores), 0.0);
        }

        $factor = 10 ** max($precision, 0);
        $porcentajes = [];
        $residuos = [];

        foreach ($valores as $clave => $valor) {
            $bruto = max((float) $valor, 0.0);
            $proporcion = ($bruto / $suma) * 100;
            $escalado = $proporcion * $factor;
            $entero = floor($escalado + 1e-9);
            $porcentajes[$clave] = $entero / $factor;
            $residuos[$clave] = $escalado - $entero;
        }

        $objetivo = (int) round(100 * $factor);
        $actual = (int) round(array_sum($porcentajes) * $factor);
        $diferencia = $objetivo - $actual;

        if ($diferencia !== 0) {
            $orden = array_keys($residuos);
            usort($orden, function ($a, $b) use ($residuos, $diferencia) {
                if ($diferencia > 0) {
                    return $residuos[$b] <=> $residuos[$a];
                }
                return $residuos[$a] <=> $residuos[$b];
            });

            $paso = $diferencia > 0 ? 1 : -1;
            $diferencia = abs($diferencia);
            $totalElementos = max(count($orden), 1);

            for ($i = 0; $i < $diferencia; $i++) {
                $indice = $orden[$i % $totalElementos];
                $porcentajes[$indice] = ($porcentajes[$indice] * $factor + $paso) / $factor;
            }
        }

        foreach ($porcentajes as $clave => $valor) {
            $porcentajes[$clave] = round($valor, $precision);
        }

        return $porcentajes;
    }

    private function resolveSvgTargets($subzonaId) {
        $targets = $this->subzonaSvgMap[$subzonaId] ?? [];
        if (!is_array($targets)) {
            $targets = [$targets];
        }
        return array_filter(array_map('strval', $targets));
    }

    private function obtenerRangoFechasPorPeriodo(
        $periodo,
        DateTimeImmutable $hoy,
        ?DateTimeImmutable $primeraRutina = null,
        ?DateTimeImmutable $ultimaRutina = null
    ) {
        $periodo = strtolower($periodo);
        $inicio = $hoy;
        $fin = $hoy;

        switch ($periodo) {
            case 'daily':
                break;
            case 'weekly':
                $inicio = $hoy->modify('-6 days');
                break;
            case 'monthly':
                $inicio = $hoy->sub(new DateInterval('P1M'));
                break;
            case 'all':
                if ($primeraRutina) {
                    $inicio = $primeraRutina;
                }
                $fin = $ultimaRutina ?: $hoy;
                break;
            default:
                $inicio = $hoy->sub(new DateInterval('P29D'));
                break;
        }

        if ($periodo === 'all') {
            if ($fin > $hoy) {
                $fin = $hoy;
            }
            if ($primeraRutina && $inicio > $fin) {
                $inicio = $fin;
            }
        } else {
            $fin = $hoy;
        }

        if ($fin < $inicio) {
            $fin = $inicio;
        }

        return [$inicio, $fin];
    }
}
?>
