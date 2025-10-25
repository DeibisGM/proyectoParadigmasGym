<?php
// Debug script to check progress calculation
include_once 'business/progresoBusiness.php';

// Test with a sample client ID (you may need to adjust this)
$clienteId = 1; // Adjust this ID based on your actual data
$progresoBusiness = new ProgresoBusiness();

echo "<h2>Debug - Progreso Visual</h2>";
echo "<h3>Cliente ID: $clienteId</h3>";

// Get the progress data
$progresoData = $progresoBusiness->getProgresoVisual($clienteId, 30);

echo "<h4>Resultado final:</h4>";
echo "<pre>";
print_r($progresoData);
echo "</pre>";

// Let's also debug the internal calculation by modifying the method temporarily
// We'll add debug output to see what's happening inside
class DebugProgresoBusiness extends ProgresoBusiness {
    public function debugGetProgresoVisual($clienteId, $dias = 30) {
        $fechaFin = date('Y-m-d');
        $fechaInicio = date('Y-m-d', strtotime("-$dias days"));
        
        echo "<h4>Fechas de consulta:</h4>";
        echo "Desde: $fechaInicio hasta: $fechaFin<br>";

        $rutinaData = new RutinaData();
        $ejercicioSubzonaData = new ejercicioSubzonaData();
        
        $rutinas = $rutinaData->getRutinasPorClienteEnRangoFechas($clienteId, $fechaInicio, $fechaFin);
        
        echo "<h4>Rutinas encontradas: " . count($rutinas) . "</h4>";

        if (empty($rutinas)) {
            echo "No se encontraron rutinas para este cliente en el rango de fechas.<br>";
            return [];
        }

        $subzonaCounts = [];
        $totalEjerciciosRegistrados = 0;

        foreach ($rutinas as $rutina) {
            echo "<br>Rutina ID: " . $rutina->getId() . "<br>";
            $ejercicios = $rutinaData->getEjerciciosPorRutinaId($rutina->getId());
            echo "Ejercicios en esta rutina: " . count($ejercicios) . "<br>";
            
            foreach ($ejercicios as $ejercicio) {
                $totalEjerciciosRegistrados++;
                echo "  Ejercicio ID: " . $ejercicio->getEjercicioId() . ", Tipo: " . $ejercicio->getTipo() . "<br>";

                $subzonasDelEjercicio = $ejercicioSubzonaData->getSubzonasPorEjercicio($ejercicio->getEjercicioId(), $ejercicio->getTipo());
                foreach ($subzonasDelEjercicio as $subzona) {
                    $subzonaIds = explode('$', $subzona->getSubzona());
                    echo "    Subzonas: " . $subzona->getSubzona() . "<br>";
                    foreach($subzonaIds as $szId) {
                        $szId = trim($szId);
                        if (empty($szId)) continue;

                        if (!isset($subzonaCounts[$szId])) {
                            $subzonaCounts[$szId] = 0;
                        }
                        $subzonaCounts[$szId]++;
                        echo "      Subzona $szId: count = " . $subzonaCounts[$szId] . "<br>";
                    }
                }
            }
        }

        echo "<h4>Total de ejercicios registrados: $totalEjerciciosRegistrados</h4>";
        echo "<h4>Conteo por subzonas:</h4>";
        echo "<pre>";
        print_r($subzonaCounts);
        echo "</pre>";

        $porcentajes = [];
        if ($totalEjerciciosRegistrados > 0) {
            foreach ($subzonaCounts as $subzonaId => $count) {
                $porcentajes[$subzonaId] = ($count / $totalEjerciciosRegistrados) * 100;
                echo "Subzona $subzonaId: ($count / $totalEjerciciosRegistrados) * 100 = " . $porcentajes[$subzonaId] . "%<br>";
            }
        }

        echo "<h4>Porcentajes calculados:</h4>";
        echo "<pre>";
        print_r($porcentajes);
        echo "</pre>";

        // Manually implement the SVG mapping since the method is private
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
                        echo "Mapeando subzona $subzonaId ($porcentaje%) a parte SVG: $parteSVG (total acumulado: {$resultadoSVG[$parteSVG]}%)<br>";
                    }
                } else {
                    $resultadoSVG[$mapa[$subzonaId]] = ($resultadoSVG[$mapa[$subzonaId]] ?? 0) + $porcentaje;
                    echo "Mapeando subzona $subzonaId ($porcentaje%) a parte SVG: {$mapa[$subzonaId]} (total: {$resultadoSVG[$mapa[$subzonaId]]}%)<br>";
                }
            }
        }

        // Cap at 100%
        foreach ($resultadoSVG as $key => $value) {
            if ($value > 100) {
                $resultadoSVG[$key] = 100;
            }
        }

        return $resultadoSVG;
    }
}

$debugBusiness = new DebugProgresoBusiness();
echo "<h3>========== DEBUG DETALLADO ==========</h3>";
$debugResult = $debugBusiness->debugGetProgresoVisual($clienteId, 30);

echo "<h4>Resultado después del mapeo SVG:</h4>";
echo "<pre>";
print_r($debugResult);
echo "</pre>";
?>