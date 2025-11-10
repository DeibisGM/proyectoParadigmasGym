<?php
include_once __DIR__ . '/../data/rutinaData.php';
include_once __DIR__ . '/../data/ejercicioSubzonaData.php';
include_once __DIR__ . '/../data/cuerpoZonaData.php';

class ProgresoBusiness {
    private $rutinaData;
    private $ejercicioSubzonaData;
    private $cuerpoZonaData;

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
                'porcentajes' => $resultado['porcentajes']
            ];
        }

        return $respuesta;
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
            'porcentajes' => $distribucion['porcentajes']
        ];
    }

    private function calcularDistribucionSubzonas($rutinas) {
        if (empty($rutinas)) {
            return [
                'totalInstancias' => 0,
                'porcentajes' => []
            ];
        }

        $subzonaCounts = [];
        $totalInstanciasSubzonas = 0;

        foreach ($rutinas as $rutina) {
            $ejercicios = $this->rutinaData->getEjerciciosPorRutinaId($rutina->getId());
            foreach ($ejercicios as $ejercicio) {
                $subzonasDelEjercicio = $this->ejercicioSubzonaData->getSubzonasPorEjercicio(
                    $ejercicio->getEjercicioId(),
                    $ejercicio->getTipo()
                );
                foreach ($subzonasDelEjercicio as $subzona) {
                    $subzonaIds = explode('$', $subzona->getSubzona());
                    foreach ($subzonaIds as $szId) {
                        $szId = trim($szId);
                        if (empty($szId)) {
                            continue;
                        }

                        if (!isset($subzonaCounts[$szId])) {
                            $subzonaCounts[$szId] = 0;
                        }
                        $subzonaCounts[$szId]++;
                        $totalInstanciasSubzonas++;
                    }
                }
            }
        }

        if ($totalInstanciasSubzonas === 0) {
            return [
                'totalInstancias' => 0,
                'porcentajes' => []
            ];
        }

        $porcentajes = [];
        foreach ($subzonaCounts as $subzonaId => $count) {
            $porcentajes[$subzonaId] = ($count / $totalInstanciasSubzonas) * 100;
        }

        return [
            'totalInstancias' => $totalInstanciasSubzonas,
            'porcentajes' => $this->mapearSubzonasASVG($porcentajes)
        ];
    }

    private function obtenerRangoFechasPorPeriodo(
        $periodo,
        DateTimeImmutable $hoy,
        ?DateTimeImmutable $primeraRutina = null,
        ?DateTimeImmutable $ultimaRutina = null
    )
    {
        $periodo = strtolower($periodo);
        $inicio = $hoy;
        $fin = $hoy;

        switch ($periodo) {
            case 'daily':
                // Mantener el día actual como referencia visual, incluso si no hay rutinas hoy.
                break;
            case 'weekly':
                // Contemplar los últimos 7 días completos (hoy y los 6 anteriores).
                $inicio = $hoy->sub(new DateInterval('P7D'));
                break;
            case 'monthly':
                // Desde el mismo día del mes anterior hasta hoy.
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

    private function mapearSubzonasASVG($porcentajes) {
        $mapa = [
            '29' => 'pectoralmayor', '30' => 'pectoralmayor', '31' => 'pectoralmayor',
            '25' => ['gemelosfrontal', 'gemelostrasero'],
            '28' => 'cuadriceps', '27' => 'Isquiotibiales', '26' => 'gluteos',
            '23' => 'abductores', '24' => 'abductores',
            '8'  => 'abdominales', '10' => 'abdominales',
            '9'  => ['oblicuosfrontales', 'oblicuotrasero'],
            '11' => 'dorsalancho', '14' => 'dorsalancho',
            '12' => ['trapeciofrontal', 'trapeciotrasero'],
            '13' => 'Infraespinoso',
            '16' => 'hombrofrontal', '18' => 'hombrotrasero',
            '17' => ['hombrofrontal', 'hombrotrasero'],
            '22' => 'biceps', '21' => 'triceps',
            '20' => ['Braquiorradialfrontal', 'Braquiorradialtrasero', 'flexordedos', 'extensordedos'],
            '15' => 'serratoanterior',
            '37' => ['abdominales', 'oblicuosfrontales', 'oblicuotrasero']
        ];

        $resultadoSVG = [];
        foreach ($porcentajes as $subzonaId => $porcentaje) {
            if (isset($mapa[$subzonaId])) {
                if (is_array($mapa[$subzonaId])) {
                    foreach ($mapa[$subzonaId] as $parteSVG) {
                        $resultadoSVG[$parteSVG] = ($resultadoSVG[$parteSVG] ?? 0) + $porcentaje;
                    }
                } else {
                    $resultadoSVG[$mapa[$subzonaId]] = ($resultadoSVG[$mapa[$subzonaId]] ?? 0) + $porcentaje;
                }
            }
        }

        // CAMBIO: Ahora no es necesario normalizar a 100, pero se mantiene como seguridad.
        // La normalización real ocurre en el Javascript del visor para ajustar la escala de colores.
        foreach ($resultadoSVG as $key => $value) {
            if ($value > 100) {
                $resultadoSVG[$key] = 100;
            }
        }

        return $resultadoSVG;
    }
}
?>