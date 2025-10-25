<?php
include_once __DIR__ . '/../data/rutinaData.php';
include_once __DIR__ . '/ejercicioFuerzaBusiness.php';
include_once __DIR__ . '/ejercicioResistenciaBusiness.php';

class RutinaBusiness
{
    private $rutinaData;
    private $ejercicioFuerzaBusiness;
    private $ejercicioResistenciaBusiness;

    public function __construct()
    {
        $this->rutinaData = new RutinaData();
        $this->ejercicioFuerzaBusiness = new EjercicioFuerzaBusiness();
        $this->ejercicioResistenciaBusiness = new ejercicioResistenciaBusiness();
    }

    public function crearRutinaCompleta($rutina)
    {
        $rutinaId = $this->rutinaData->insertarRutina($rutina);
        if ($rutinaId > 0) {
            foreach ($rutina->getEjercicios() as $ejercicio) {
                $ejercicio->setRutinaId($rutinaId);
                $this->rutinaData->insertarRutinaEjercicio($ejercicio);
            }
            return true;
        }
        return false;
    }

    public function obtenerRutinasConEjercicios($clienteId)
    {
        $rutinas = $this->rutinaData->getRutinasPorCliente($clienteId);
        foreach ($rutinas as $rutina) {
            $ejercicios = $this->rutinaData->getEjerciciosPorRutinaId($rutina->getId());
            foreach ($ejercicios as $ejercicio) {
                $nombre = $this->obtenerNombreEjercicio($ejercicio->getTipo(), $ejercicio->getEjercicioId());
                $ejercicio->setNombreEjercicio($nombre);
            }
            $rutina->setEjercicios($ejercicios);
        }
        return $rutinas;
    }

    private function obtenerNombreEjercicio($tipo, $id)
    {
        switch ($tipo) {
            case 'fuerza':
                $ejercicios = $this->ejercicioFuerzaBusiness->obtenerTbejerciciofuerza();
                foreach($ejercicios as $ejer){
                    if($ejer->getTbejerciciofuerzaid() == $id){
                        return $ejer->getTbejerciciofuerzanombre();
                    }
                }
                break;
            case 'resistencia':
                $ejercicios = $this->ejercicioResistenciaBusiness->getAllTBEjercicioResistecia();
                foreach($ejercicios as $ejer){
                    if($ejer->getId() == $id){
                        return $ejer->getNombre();
                    }
                }
                break;
        }
        return 'Ejercicio Desconocido';
    }

    public function eliminarRutinaCompleta($rutinaId)
    {
        return $this->rutinaData->eliminarRutina($rutinaId);
    }

    public function getEjerciciosPorTipo($tipo) {
        $ejercicios = [];
        switch ($tipo) {
            case 'fuerza':
                $ejerciciosData = $this->ejercicioFuerzaBusiness->obtenerTbejerciciofuerza();
                foreach ($ejerciciosData as $ej) {
                    $ejercicios[] = ['id' => $ej->getTbejerciciofuerzaid(), 'nombre' => $ej->getTbejerciciofuerzanombre()];
                }
                break;
            case 'resistencia':
                $ejerciciosData = $this->ejercicioResistenciaBusiness->getAllTBEjercicioResistecia();
                foreach ($ejerciciosData as $ej) {
                    if($ej->getActivo()){
                        $ejercicios[] = ['id' => $ej->getId(), 'nombre' => $ej->getNombre()];
                    }
                }
                break;
        }
        return $ejercicios;
    }
}
?>