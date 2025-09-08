<?php

    if (!class_exists('ClientePadecimientoData')) {
        include_once '../data/clientePadecimientoData.php';
    }

    class ClientePadecimientoBusiness {

        private $clientePadecimientoData;

        public function __construct() {
            $this->clientePadecimientoData = new ClientePadecimientoData();
        }

        public function insertarTBClientePadecimiento($clientePadecimiento) {
            return $this->clientePadecimientoData->insertarTBClientePadecimiento($clientePadecimiento);
        }

        public function actualizarTBClientePadecimiento($clientePadecimiento) {
            return $this->clientePadecimientoData->actualizarTBClientePadecimiento($clientePadecimiento);
        }

        public function eliminarTBClientePadecimiento($tbclientepadecimientoid) {
            return $this->clientePadecimientoData->eliminarTBClientePadecimiento($tbclientepadecimientoid);
        }

        public function obtenerTBClientePadecimiento() {
            return $this->clientePadecimientoData->obtenerTBClientePadecimiento();
        }

        public function obtenerTBClientePadecimientoPorCliente($tbclienteid) {
            return $this->clientePadecimientoData->obtenerTBClientePadecimientoPorCliente($tbclienteid);
        }

        public function obtenerTodosTBClientePadecimientoPorCliente($tbclienteid) {
            return $this->clientePadecimientoData->obtenerTodosTBClientePadecimientoPorCliente($tbclienteid);
        }

        public function obtenerTodosLosClientes() {
            return $this->clientePadecimientoData->obtenerTodosLosClientes();
        }

        public function obtenerPadecimientos() {
            return $this->clientePadecimientoData->obtenerPadecimientos();
        }

        public function existenClientePadecimientosPorCliente($tbclienteid) {
            $datos = $this->clientePadecimientoData->obtenerTBClientePadecimientoPorCliente($tbclienteid);
            return $datos !== null;
        }

        public function validarClientePadecimiento($tbclienteid, $tbpadecimientoid) {
            $errores = array();

            if (empty($tbclienteid) || $tbclienteid <= 0) {
                $errores[] = "Debe seleccionar un cliente válido";
            }

            if (empty($tbpadecimientoid)) {
                $errores[] = "Debe seleccionar al menos un padecimiento";
            } else {
                if (!$this->clientePadecimientoData->validarPadecimientosExisten($tbpadecimientoid)) {
                    $errores[] = "Uno o más padecimientos seleccionados no son válidos";
                }
            }

            return $errores;
        }

        public function obtenerTBClientePadecimientoPorId($registroId) {
            return $this->clientePadecimientoData->obtenerTBClientePadecimientoPorId($registroId);
        }

        public function actualizarPadecimientoIndividual($registroId, $padecimientoIdAntiguo, $padecimientoIdNuevo) {

            $registroActual = $this->obtenerTBClientePadecimientoPorId($registroId);
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

            $clientePadecimientoActualizado = new ClientePadecimiento(
                $registroActual->getTbclientepadecimientoid(),
                $registroActual->getTbclienteid(),
                $nuevosIdsString,
                $registroActual->getTbpadecimientodictamenid()
            );

            return $this->clientePadecimientoData->actualizarTBClientePadecimiento($clientePadecimientoActualizado);
        }

        public function eliminarPadecimientoIndividual($registroId, $padecimientoId) {

            $registroActual = $this->obtenerTBClientePadecimientoPorId($registroId);
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
                $resultado = $this->clientePadecimientoData->eliminarTBClientePadecimiento($registroId);
                if ($resultado) {
                    return array('success' => true, 'message' => 'Éxito: Padecimiento eliminado. Como era el único padecimiento, se eliminó todo el registro.');
                } else {
                    return array('success' => false, 'message' => 'Error: No se pudo eliminar el registro.');
                }
            }

            $nuevosIdsString = implode('$', $nuevosIds);

            $clientePadecimientoActualizado = new ClientePadecimiento(
                $registroActual->getTbclientepadecimientoid(),
                $registroActual->getTbclienteid(),
                $nuevosIdsString,
                $registroActual->getTbpadecimientodictamenid()
            );

            $resultado = $this->clientePadecimientoData->actualizarTBClientePadecimiento($clientePadecimientoActualizado);
            if ($resultado) {
                return array('success' => true, 'message' => 'Éxito: Padecimiento eliminado correctamente.');
            } else {
                return array('success' => false, 'message' => 'Error: No se pudo actualizar el registro.');
            }
        }

        public function limpiarPadecimientosEliminados() {
            $todosClientePadecimientos = $this->clientePadecimientoData->obtenerTBClientePadecimiento();
            $actualizacionesRealizadas = 0;

            foreach ($todosClientePadecimientos as $clientePadecimiento) {
                $padecimientosOriginales = $clientePadecimiento->getPadecimientosIds();
                $padecimientosValidos = array();

                foreach ($padecimientosOriginales as $padecimientoId) {
                    if (!empty($padecimientoId) && is_numeric($padecimientoId)) {
                        if ($this->clientePadecimientoData->validarPadecimientosExisten($padecimientoId)) {
                            $padecimientosValidos[] = $padecimientoId;
                        }
                    }
                }

                if (count($padecimientosValidos) !== count($padecimientosOriginales)) {
                    if (empty($padecimientosValidos)) {
                        $this->clientePadecimientoData->eliminarTBClientePadecimiento($clientePadecimiento->getTbclientepadecimientoid());
                    } else {
                        // FIX: Corrected implode syntax
                        $nuevosIds = implode('$', $padecimientosValidos);
                        $clientePadecimientoActualizado = new ClientePadecimiento(
                            $clientePadecimiento->getTbclientepadecimientoid(),
                            $clientePadecimiento->getTbclienteid(),
                            $nuevosIds,
                            $clientePadecimiento->getTbpadecimientodictamenid()
                        );
                        $this->clientePadecimientoData->actualizarTBClientePadecimiento($clientePadecimientoActualizado);
                    }
                    $actualizacionesRealizadas++;
                }
            }

            return $actualizacionesRealizadas;
        }

        public function modificarPadecimientoEnRegistros($padecimientoIdAntiguo, $padecimientoIdNuevo) {
            $todosClientePadecimientos = $this->clientePadecimientoData->obtenerTBClientePadecimiento();
            $actualizacionesRealizadas = 0;

            foreach ($todosClientePadecimientos as $clientePadecimiento) {
                $padecimientosIds = $clientePadecimiento->getPadecimientosIds();
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
                    // FIX: Corrected implode syntax
                    $nuevosIdsString = implode('$', $nuevosIds);
                    $clientePadecimientoActualizado = new ClientePadecimiento(
                        $clientePadecimiento->getTbclientepadecimientoid(),
                        $clientePadecimiento->getTbclienteid(),
                        $nuevosIdsString,
                        $clientePadecimiento->getTbpadecimientodictamenid()
                    );

                    $this->clientePadecimientoData->actualizarTBClientePadecimiento($clientePadecimientoActualizado);
                    $actualizacionesRealizadas++;
                }
            }

            return $actualizacionesRealizadas;
        }

        public function padecimientoEnUso($padecimientoId) {
            $todosClientePadecimientos = $this->clientePadecimientoData->obtenerTBClientePadecimiento();
            $clientesAfectados = array();

            foreach ($todosClientePadecimientos as $clientePadecimiento) {
                $padecimientosIds = $clientePadecimiento->getPadecimientosIds();

                if (in_array($padecimientoId, $padecimientosIds)) {
                    $clientesAfectados[] = array(
                        'clienteId' => $clientePadecimiento->getTbclienteid(),
                        'carnet' => $clientePadecimiento->getCarnet(),
                        'registroId' => $clientePadecimiento->getTbclientepadecimientoid()
                    );
                }
            }

            return $clientesAfectados;
        }

        public function eliminarPadecimientoDeRegistros($padecimientoId) {
            $todosClientePadecimientos = $this->clientePadecimientoData->obtenerTBClientePadecimiento();
            $actualizacionesRealizadas = 0;
            $registrosEliminados = 0;

            foreach ($todosClientePadecimientos as $clientePadecimiento) {
                $padecimientosIds = $clientePadecimiento->getPadecimientosIds();
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
                        $this->clientePadecimientoData->eliminarTBClientePadecimiento($clientePadecimiento->getTbclientepadecimientoid());
                        $registrosEliminados++;
                    } else {
                        // FIX: Corrected implode syntax
                        $nuevosIdsString = implode('$', $nuevosIds);
                        $clientePadecimientoActualizado = new ClientePadecimiento(
                            $clientePadecimiento->getTbclientepadecimientoid(),
                            $clientePadecimiento->getTbclienteid(),
                            $nuevosIdsString,
                            $clientePadecimiento->getTbpadecimientodictamenid()
                        );
                        $this->clientePadecimientoData->actualizarTBClientePadecimiento($clientePadecimientoActualizado);
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