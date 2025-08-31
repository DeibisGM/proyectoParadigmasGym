<?php

include_once '../data/horarioData.php';

class HorarioBusiness
{
    private $horarioData;

    public function __construct()
    {
        $this->horarioData = new HorarioData();
    }

    public function getAllHorarios()
    {
        $horarios = $this->horarioData->getAllHorarios();

        foreach ($horarios as $horario) {
            $bloqueosOriginales = $horario->getBloqueos();
            $bloqueosEstructurados = [];
            foreach ($bloqueosOriginales as $bloqueoStr) {
                $partes = explode('&', $bloqueoStr);
                if (count($partes) == 2) {
                    $bloqueosEstructurados[] = ['inicio' => $partes[0], 'fin' => $partes[1]];
                }
            }
            $horario->setBloqueos($bloqueosEstructurados);
        }
        return $horarios;
    }

    public function updateHorarios($postData)
    {
        for ($i = 1; $i <= 7; $i++) {
            $horario = new Horario($i, '', 0, null, null, '');

            $activo = isset($postData['activo'][$i]) ? 1 : 0;
            $horario->setActivo($activo);

            if ($activo) {
                $apertura = $postData['apertura'][$i] ?? null;
                $cierre = $postData['cierre'][$i] ?? null;
                $horario->setApertura($apertura);
                $horario->setCierre($cierre);

                $bloqueosArray = [];
                if (isset($postData['bloqueo_inicio'][$i])) {
                    for ($j = 0; $j < count($postData['bloqueo_inicio'][$i]); $j++) {
                        $inicio = $postData['bloqueo_inicio'][$i][$j];
                        $fin = $postData['bloqueo_fin'][$i][$j];
                        if (!empty($inicio) && !empty($fin)) {
                            $bloqueosArray[] = $inicio . '&' . $fin;
                        }
                    }
                }
                $horario->setBloqueos($bloqueosArray);
            }

            $this->horarioData->updateHorario($horario);
        }
        return true;
    }

    public function getHorarioDelDia($diaId)
    {
        $horario = $this->horarioData->getHorarioDelDia($diaId);
        if ($horario) {

            $bloqueosOriginales = $horario->getBloqueos();
            $bloqueosEstructurados = [];
            foreach ($bloqueosOriginales as $bloqueoStr) {
                $partes = explode('&', $bloqueoStr);
                if (count($partes) == 2) {
                    $bloqueosEstructurados[] = ['inicio' => $partes[0], 'fin' => $partes[1]];
                }
            }
            $horario->setBloqueos($bloqueosEstructurados);
        }
        return $horario;
    }

}