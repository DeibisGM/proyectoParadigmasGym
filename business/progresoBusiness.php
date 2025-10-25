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

    public function getProgresoVisual($clienteId, $dias = 30) {
        $fechaFin = date('Y-m-d');
        $fechaInicio = date('Y-m-d', strtotime("-$dias days"));

        $rutinas = $this->rutinaData->getRutinasPorClienteEnRangoFechas($clienteId, $fechaInicio, $fechaFin);

        if (empty($rutinas)) {
            return [];
        }

        $subzonaCounts = [];
        $totalEjerciciosContados = 0;

        foreach ($rutinas as $rutina) {
            $ejercicios = $this->rutinaData->getEjerciciosPorRutinaId($rutina->getId());
            foreach ($ejercicios as $ejercicio) {
                $subzonasDelEjercicio = $this->ejercicioSubzonaData->getSubzonasPorEjercicio($ejercicio->getEjercicioId(), $ejercicio->getTipo());
                foreach ($subzonasDelEjercicio as $subzona) {
                    $subzonaIds = explode('$', $subzona->getSubzona());
                    foreach($subzonaIds as $szId) {
                        $szId = trim($szId);
                        if (empty($szId)) continue;

                        if (!isset($subzonaCounts[$szId])) {
                            $subzonaCounts[$szId] = 0;
                        }
                        $subzonaCounts[$szId]++;
                        $totalEjerciciosContados++;
                    }
                }
            }
        }

        if ($totalEjerciciosContados === 0) {
            return [];
        }

        $porcentajes = [];
        $totalHits = array_sum($subzonaCounts);
        if ($totalHits > 0) {
            foreach ($subzonaCounts as $subzonaId => $count) {
                // Se calcula el porcentaje y se multiplica por el número de subzonas distintas para magnificar la diferencia visual
                $porcentajes[$subzonaId] = ($count / $totalHits) * 100 * count($subzonaCounts);
            }
        }

        return $this->mapearSubzonasASVG($porcentajes);
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

        foreach ($resultadoSVG as $key => $value) {
            if ($value > 100) {
                $resultadoSVG[$key] = 100;
            }
        }

        return $resultadoSVG;
    }
}
?>