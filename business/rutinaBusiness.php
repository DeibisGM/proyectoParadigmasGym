<?php
include_once '../data/rutinaData.php';
include_once '../business/ejercicioFuerzaBusiness.php';
include_once '../business/ejercicioResistenciaBusiness.php';
// Nota: Cuando crees el business de Equilibrio, inclúyelo aquí.
// include_once '../business/ejercicioEquilibrioBusiness.php';

class RutinaBusiness
{
    private $rutinaData;
    private $ejercicioFuerzaBusiness;
    private $ejercicioResistenciaBusiness;
    // private $ejercicioEquilibrioBusiness;

    public function __construct()
    {
        $this->rutinaData = new RutinaData();
        $this->ejercicioFuerzaBusiness = new EjercicioFuerzaBusiness();
        $this->ejercicioResistenciaBusiness = new ejercicioResistenciaBusiness();
        // $this->ejercicioEquilibrioBusiness = new EjercicioEquilibrioBusiness();
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
            case 'equilibrio':
                // Implementar cuando tengas el business de Equilibrio
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
            case 'equilibrio':
                // Aquí iría la lógica para cargar ejercicios de equilibrio
                // $ejerciciosData = $this->ejercicioEquilibrioBusiness->getAll...();
                // foreach ($ejerciciosData as $ej) { ... }
                break;
        }
        return $ejercicios;
    }
}
?>
