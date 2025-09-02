<?php

    if (!class_exists('DatoClinicoData')) {
        include_once '../data/datoClinicoData.php';
    }

    class DatoClinicoBusiness {

        private $datoClinicoData;

        public function __construct() {
            $this->datoClinicoData = new DatoClinicoData();
        }

        public function insertarTBDatoClinico($datoClinico) {
            return $this->datoClinicoData->insertarTBDatoClinico($datoClinico);
        }

        public function actualizarTBDatoClinico($datoClinico) {
            return $this->datoClinicoData->actualizarTBDatoClinico($datoClinico);
        }

        public function eliminarTBDatoClinico($tbdatoclinicoid) {
            return $this->datoClinicoData->eliminarTBDatoClinico($tbdatoclinicoid);
        }

        public function obtenerTBDatoClinico() {
            return $this->datoClinicoData->obtenerTBDatoClinico();
        }

        public function obtenerTBDatoClinicoPorCliente($tbclienteid) {
            return $this->datoClinicoData->obtenerTBDatoClinicoPorCliente($tbclienteid);
        }

        public function obtenerTodosTBDatoClinicoPorCliente($tbclienteid) {
            return $this->datoClinicoData->obtenerTodosTBDatoClinicoPorCliente($tbclienteid);
        }

        public function obtenerTodosLosClientes() {
            return $this->datoClinicoData->obtenerTodosLosClientes();
        }

        public function obtenerPadecimientos() {
            return $this->datoClinicoData->obtenerPadecimientos();
        }

        public function existenDatoClinicosPorCliente($tbclienteid) {
            $datos = $this->datoClinicoData->obtenerTBDatoClinicoPorCliente($tbclienteid);
            return $datos !== null;
        }

        public function validarDatoClinico($tbclienteid, $tbpadecimientoid) {
            $errores = array();

            if (empty($tbclienteid) || $tbclienteid <= 0) {
                $errores[] = "Debe seleccionar un cliente válido";
            }

            if (empty($tbpadecimientoid)) {
                $errores[] = "Debe seleccionar al menos un padecimiento";
            } else {
                if (!$this->datoClinicoData->validarPadecimientosExisten($tbpadecimientoid)) {
                    $errores[] = "Uno o más padecimientos seleccionados no son válidos";
                }
            }

            return $errores;
        }

        public function obtenerTBDatoClinicoPorId($registroId) {
            return $this->datoClinicoData->obtenerTBDatoClinicoPorId($registroId);
        }

        public function actualizarPadecimientoIndividual($registroId, $padecimientoIdAntiguo, $padecimientoIdNuevo) {

            $registroActual = $this->obtenerTBDatoClinicoPorId($registroId);
            if (!$registroActual) {
                return false;
            }

            $padecimientosIds = $registroActual->getPadecimientosIds();
            $nuevosIds = array();

            $seEncontro = false;
            foreach ($padecimientosIds as $id) {
                if ($id == $padecimientoIdAntiguo) {
                    $nuevosIds[] = $padecimientoIdNuevo;
                    $seEncontro = true;
                } else {
                    $nuevosIds[] = $id;
                }
            }

            if (!$seEncontro) {
                return false;
            }

            $nuevosIds = array_unique($nuevosIds);
            $nuevosIdsString = implode('$', $nuevosIds);

            $datoClinicoActualizado = new DatoClinico(
                $registroActual->getTbdatoclinicoid(),
                $registroActual->getTbclienteid(),
                $nuevosIdsString
            );

            return $this->datoClinicoData->actualizarTBDatoClinico($datoClinicoActualizado);
        }

        public function eliminarPadecimientoIndividual($registroId, $padecimientoId) {

            $registroActual = $this->obtenerTBDatoClinicoPorId($registroId);
            if (!$registroActual) {
                return array('success' => false, 'message' => 'Error: Registro no encontrado.');
            }

            $padecimientosIds = $registroActual->getPadecimientosIds();
            $nuevosIds = array();

            $seEncontro = false;
            foreach ($padecimientosIds as $id) {
                if ($id != $padecimientoId) {
                    $nuevosIds[] = $id;
                } else {
                    $seEncontro = true;
                }
            }

            if (!$seEncontro) {
                return array('success' => false, 'message' => 'Error: El padecimiento no se encontró en el registro.');
            }

            if (empty($nuevosIds)) {
                $resultado = $this->datoClinicoData->eliminarTBDatoClinico($registroId);
                if ($resultado) {
                    return array('success' => true, 'message' => 'Éxito: Padecimiento eliminado. Como era el único padecimiento, se eliminó todo el registro.');
                } else {
                    return array('success' => false, 'message' => 'Error: No se pudo eliminar el registro.');
                }
            }

            $nuevosIdsString = implode('$', $nuevosIds);

            $datoClinicoActualizado = new DatoClinico(
                $registroActual->getTbdatoclinicoid(),
                $registroActual->getTbclienteid(),
                $nuevosIdsString
            );

            $resultado = $this->datoClinicoData->actualizarTBDatoClinico($datoClinicoActualizado);
            if ($resultado) {
                return array('success' => true, 'message' => 'Éxito: Padecimiento eliminado correctamente.');
            } else {
                return array('success' => false, 'message' => 'Error: No se pudo actualizar el registro.');
            }
        }

        public function limpiarPadecimientosEliminados() {
            $todosDatosClinicos = $this->datoClinicoData->obtenerTBDatoClinico();
            $actualizacionesRealizadas = 0;

            foreach ($todosDatosClinicos as $datoClinico) {
                $padecimientosOriginales = $datoClinico->getPadecimientosIds();
                $padecimientosValidos = array();

                foreach ($padecimientosOriginales as $padecimientoId) {
                    if (!empty($padecimientoId) && is_numeric($padecimientoId)) {
                        if ($this->datoClinicoData->validarPadecimientosExisten($padecimientoId)) {
                            $padecimientosValidos[] = $padecimientoId;
                        }
                    }
                }

                if (count($padecimientosValidos) !== count($padecimientosOriginales)) {
                    if (empty($padecimientosValidos)) {
                        $this->datoClinicoData->eliminarTBDatoClinico($datoClinico->getTbdatoclinicoid());
                    } else {
                        $nuevosIds = implode('$', $padecimientosValidos);
                        $datoClinicoActualizado = new DatoClinico(
                            $datoClinico->getTbdatoclinicoid(),
                            $datoClinico->getTbclienteid(),
                            $nuevosIds
                        );
                        $this->datoClinicoData->actualizarTBDatoClinico($datoClinicoActualizado);
                    }
                    $actualizacionesRealizadas++;
                }
            }

            return $actualizacionesRealizadas;
        }

        public function modificarPadecimientoEnRegistros($padecimientoIdAntiguo, $padecimientoIdNuevo) {
            $todosDatosClinicos = $this->datoClinicoData->obtenerTBDatoClinico();
            $actualizacionesRealizadas = 0;

            foreach ($todosDatosClinicos as $datoClinico) {
                $padecimientosIds = $datoClinico->getPadecimientosIds();
                $seModifico = false;
                $nuevosIds = array();

                foreach ($padecimientosIds as $id) {
                    if ($id == $padecimientoIdAntiguo) {
                        $nuevosIds[] = $padecimientoIdNuevo;
                        $seModifico = true;
                    } else {
                        $nuevosIds[] = $id;
                    }
                }

                if ($seModifico) {
                    $nuevosIdsString = implode('$', $nuevosIds);
                    $datoClinicoActualizado = new DatoClinico(
                        $datoClinico->getTbdatoclinicoid(),
                        $datoClinico->getTbclienteid(),
                        $nuevosIdsString
                    );

                    $this->datoClinicoData->actualizarTBDatoClinico($datoClinicoActualizado);
                    $actualizacionesRealizadas++;
                }
            }

            return $actualizacionesRealizadas;
        }

        public function padecimientoEnUso($padecimientoId) {
            $todosDatosClinicos = $this->datoClinicoData->obtenerTBDatoClinico();
            $clientesAfectados = array();

            foreach ($todosDatosClinicos as $datoClinico) {
                $padecimientosIds = $datoClinico->getPadecimientosIds();

                if (in_array($padecimientoId, $padecimientosIds)) {
                    $clientesAfectados[] = array(
                        'clienteId' => $datoClinico->getTbclienteid(),
                        'carnet' => $datoClinico->getCarnet(),
                        'registroId' => $datoClinico->getTbdatoclinicoid()
                    );
                }
            }

            return $clientesAfectados;
        }

        public function eliminarPadecimientoDeRegistros($padecimientoId) {
            $todosDatosClinicos = $this->datoClinicoData->obtenerTBDatoClinico();
            $actualizacionesRealizadas = 0;
            $registrosEliminados = 0;

            foreach ($todosDatosClinicos as $datoClinico) {
                $padecimientosIds = $datoClinico->getPadecimientosIds();
                $nuevosIds = array();
                $seElimino = false;

                foreach ($padecimientosIds as $id) {
                    if ($id != $padecimientoId) {
                        $nuevosIds[] = $id;
                    } else {
                        $seElimino = true;
                    }
                }

                if ($seElimino) {
                    if (empty($nuevosIds)) {
                        $this->datoClinicoData->eliminarTBDatoClinico($datoClinico->getTbdatoclinicoid());
                        $registrosEliminados++;
                    } else {
                        $nuevosIdsString = implode('$', $nuevosIds);
                        $datoClinicoActualizado = new DatoClinico(
                            $datoClinico->getTbdatoclinicoid(),
                            $datoClinico->getTbclienteid(),
                            $nuevosIdsString
                        );
                        $this->datoClinicoData->actualizarTBDatoClinico($datoClinicoActualizado);
                        $actualizacionesRealizadas++;
                    }
                }
            }

            return array(
                'registrosActualizados' => $actualizacionesRealizadas,
                'registrosEliminados' => $registrosEliminados
            );
        }
    }
?>