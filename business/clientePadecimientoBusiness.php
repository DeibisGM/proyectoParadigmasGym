<?php

if (!class_exists('ClientePadecimientoData')) {
    include_once '../data/clientePadecimientoData.php';
}
include_once '../business/PadecimientoDictamenBusiness.php';
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
    public function eliminarRelacionPorDictamenId($dictamenId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("No se pudo conectar a la base de datos para eliminar relación");
            return false;
        }
        $conn->set_charset('utf8');

        try {
            // Buscar registros que tengan este dictamen
            $querySelect = "SELECT tbclientepadecimientoid, tbpadecimientodictamenid FROM tbclientepadecimiento WHERE tbpadecimientodictamenid LIKE ?";
            $searchPattern = "%$dictamenId%";
            $stmt = mysqli_prepare($conn, $querySelect);
            mysqli_stmt_bind_param($stmt, "s", $searchPattern);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($result)) {
                $registroId = $row['tbclientepadecimientoid'];
                $dictamenesActuales = $row['tbpadecimientodictamenid'];

                // Remover el dictamen de la cadena - MEJORADO
                $dictamenesArray = array_filter(explode('$', $dictamenesActuales), function($id) {
                    return !empty(trim($id));
                });

                $nuevosDictamenes = array_filter($dictamenesArray, function($id) use ($dictamenId) {
                    return trim($id) != trim($dictamenId);
                });

                $nuevaCadena = implode('$', $nuevosDictamenes);

                // Actualizar o eliminar según el caso
                if (empty($nuevaCadena)) {
                    // Si no quedan dictámenes, eliminar el registro completo
                    $queryDelete = "DELETE FROM tbclientepadecimiento WHERE tbclientepadecimientoid = ?";
                    $stmtDelete = mysqli_prepare($conn, $queryDelete);
                    mysqli_stmt_bind_param($stmtDelete, "i", $registroId);
                    $deleteResult = mysqli_stmt_execute($stmtDelete);
                    mysqli_stmt_close($stmtDelete);

                    if (!$deleteResult) {
                        error_log("Error al eliminar registro completo de cliente-padecimiento: " . mysqli_error($conn));
                    }
                } else {
                    // Actualizar con la nueva cadena
                    $queryUpdate = "UPDATE tbclientepadecimiento SET tbpadecimientodictamenid = ? WHERE tbclientepadecimientoid = ?";
                    $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
                    mysqli_stmt_bind_param($stmtUpdate, "si", $nuevaCadena, $registroId);
                    $updateResult = mysqli_stmt_execute($stmtUpdate);
                    mysqli_stmt_close($stmtUpdate);

                    if (!$updateResult) {
                        error_log("Error al actualizar cadena de dictámenes: " . mysqli_error($conn));
                    }
                }
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return true;

        } catch (Exception $e) {
            error_log("Exception en eliminarRelacionPorDictamenId: " . $e->getMessage());
            mysqli_close($conn);
            return false;
        }
    }
    // Método agregado para usar el que existe en Data
    public function eliminarTBClientePadecimientoPorCliente($tbclienteid) {
        return $this->clientePadecimientoData->eliminarTBClientePadecimientoPorCliente($tbclienteid);
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

    // MÉTODO CORREGIDO: Validación individual de padecimientos
    public function limpiarPadecimientosEliminados() {
        $todosClientePadecimientos = $this->clientePadecimientoData->obtenerTBClientePadecimiento();
        $actualizacionesRealizadas = 0;

        foreach ($todosClientePadecimientos as $clientePadecimiento) {
            $padecimientosOriginales = $clientePadecimiento->getPadecimientosIds();
            $padecimientosValidos = array();

            foreach ($padecimientosOriginales as $padecimientoId) {
                if (!empty($padecimientoId) && is_numeric($padecimientoId)) {
                    // CORREGIDO: Validar cada padecimiento individualmente
                    if ($this->validarPadecimientoIndividualExiste($padecimientoId)) {
                        $padecimientosValidos[] = $padecimientoId;
                    }
                }
            }

            if (count($padecimientosValidos) !== count($padecimientosOriginales)) {
                if (empty($padecimientosValidos)) {
                    $this->clientePadecimientoData->eliminarTBClientePadecimiento($clientePadecimiento->getTbclientepadecimientoid());
                } else {
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

    // NUEVO MÉTODO: Para validar un padecimiento individual
    private function validarPadecimientoIndividualExiste($padecimientoId) {
        return $this->clientePadecimientoData->validarPadecimientosExisten($padecimientoId);
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

    // MÉTODOS ADICIONALES SUGERIDOS

    public function validarClienteExiste($clienteId) {
        $clientes = $this->obtenerTodosLosClientes();
        foreach ($clientes as $cliente) {
            if ($cliente['id'] == $clienteId) {
                return true;
            }
        }
        return false;
    }

    public function contarPadecimientosPorCliente($clienteId) {
        $registros = $this->obtenerTodosTBClientePadecimientoPorCliente($clienteId);
        $totalPadecimientos = 0;

        foreach ($registros as $registro) {
            $totalPadecimientos += $registro->contarPadecimientos();
        }

        return $totalPadecimientos;
    }

    public function obtenerEstadisticasPadecimientos() {
        $todosRegistros = $this->obtenerTBClientePadecimiento();
        $estadisticas = array(
            'totalRegistros' => count($todosRegistros),
            'clientesConPadecimientos' => 0,
            'padecimientosMasComunes' => array()
        );

        $clientesUnicos = array();
        $contadorPadecimientos = array();

        foreach ($todosRegistros as $registro) {
            $clientesUnicos[$registro->getTbclienteid()] = true;

            $padecimientosIds = $registro->getPadecimientosIds();
            foreach ($padecimientosIds as $padecimientoId) {
                if (!isset($contadorPadecimientos[$padecimientoId])) {
                    $contadorPadecimientos[$padecimientoId] = 0;
                }
                $contadorPadecimientos[$padecimientoId]++;
            }
        }

        $estadisticas['clientesConPadecimientos'] = count($clientesUnicos);
        arsort($contadorPadecimientos);
        $estadisticas['padecimientosMasComunes'] = array_slice($contadorPadecimientos, 0, 10, true);

        return $estadisticas;
    }

        /**
     * Obtiene todos los dictámenes asociados a un cliente
     */
    public function obtenerDictamenesPorCliente($clienteId) {
        $registros = $this->clientePadecimientoData->obtenerTodosTBClientePadecimientoPorCliente($clienteId);
        $dictamenes = array();

        foreach ($registros as $registro) {
            $dictamenId = $registro->getTbpadecimientodictamenid();
            if (!empty($dictamenId)) {
                $dictamenesIds = explode('$', $dictamenId);
                $dictamenes = array_merge($dictamenes, $dictamenesIds);
            }
        }

        return array_unique(array_filter($dictamenes));
    }

    /**
     * Valida si un cliente puede tener un dictamen asociado
     */
    public function validarAsociacionClienteDictamen($clienteId, $dictamenId) {
        $errores = array();

        // Validar que el cliente existe
        if (!$this->validarClienteExiste($clienteId)) {
            $errores[] = "El cliente especificado no existe";
        }

        // Validar que el dictamen ID es válido
        if (empty($dictamenId) || !is_numeric($dictamenId) || $dictamenId <= 0) {
            $errores[] = "El ID del dictamen no es válido";
        }

        return $errores;
    }

    /**
     * Actualiza las relaciones de dictámenes para un cliente específico
     */
    public function actualizarDictamenesCliente($clienteId, $dictamenesIds) {
        if (!$this->validarClienteExiste($clienteId)) {
            return false;
        }

        // Convertir array a string si es necesario
        if (is_array($dictamenesIds)) {
            $dictamenesString = implode('$', array_filter($dictamenesIds));
        } else {
            $dictamenesString = $dictamenesIds;
        }

        // Obtener o crear registro existente
        $registroExistente = $this->clientePadecimientoData->obtenerTBClientePadecimientoPorCliente($clienteId);

        if ($registroExistente) {
            // Actualizar registro existente
            $registroExistente->setTbpadecimientodictamenid($dictamenesString);
            return $this->clientePadecimientoData->actualizarTBClientePadecimiento($registroExistente);
        } else {
            // Crear nuevo registro
            $nuevoRegistro = new ClientePadecimiento(0, $clienteId, '', $dictamenesString);
            return $this->clientePadecimientoData->insertarTBClientePadecimiento($nuevoRegistro);
        }
    }
    public function eliminarTBClientePadecimientoConDictamenes($tbclientepadecimientoid) {
        // 1. Obtener el registro antes de eliminarlo para saber qué dictámenes tiene
        $registro = $this->obtenerTBClientePadecimientoPorId($tbclientepadecimientoid);

        if (!$registro) {
            return false;
        }

        $dictamenId = $registro->getTbpadecimientodictamenid();

        // 2. Eliminar el registro de cliente-padecimiento
        $eliminacionExitosa = $this->clientePadecimientoData->eliminarTBClientePadecimiento($tbclientepadecimientoid);

        if ($eliminacionExitosa && !empty($dictamenId)) {
            // 3. Si tenía dictamen, eliminarlo también
            include_once '../business/PadecimientoDictamenBusiness.php';
            $dictamenBusiness = new PadecimientoDictamenBusiness();

            // Verificar si este dictamen está siendo usado por otros clientes
            if (!$this->dictamenEnUsoPorOtrosClientes($dictamenId, $tbclientepadecimientoid)) {
                $dictamenBusiness->eliminarTBPadecimientoDictamen($dictamenId);
            }
        }

        return $eliminacionExitosa;
    }

    /**
     * Verifica si un dictamen está siendo usado por otros clientes
     */
    private function dictamenEnUsoPorOtrosClientes($dictamenId, $registroIdExcluir = null) {
        $todosRegistros = $this->clientePadecimientoData->obtenerTBClientePadecimiento();

        foreach ($todosRegistros as $registro) {
            // Saltar el registro que estamos eliminando
            if ($registroIdExcluir && $registro->getTbclientepadecimientoid() == $registroIdExcluir) {
                continue;
            }

            $dictamenesIds = $registro->getTbpadecimientodictamenid();
            if (!empty($dictamenesIds)) {
                $dictamenesArray = explode('$', $dictamenesIds);
                if (in_array($dictamenId, $dictamenesArray)) {
                    return true; // El dictamen está siendo usado por otro cliente
                }
            }
        }

        return false; // El dictamen no está siendo usado por otros
    }

    /**
     * Obtiene el cliente ID asociado a un dictamen específico
     */
    public function obtenerClienteIdPorDictamenId($dictamenId) {
        $todosRegistros = $this->clientePadecimientoData->obtenerTBClientePadecimiento();

        foreach ($todosRegistros as $registro) {
            $dictamenesIds = $registro->getTbpadecimientodictamenid();
            if (!empty($dictamenesIds)) {
                $dictamenesArray = explode('$', $dictamenesIds);
                if (in_array($dictamenId, $dictamenesArray)) {
                    return $registro->getTbclienteid();
                }
            }
        }

        return null;
    }

    /**
     * Asocia un dictamen a un cliente específico
     */
    public function asociarDictamenACliente($clienteId, $dictamenId) {
        // Buscar si ya existe un registro para este cliente
        $registroExistente = $this->clientePadecimientoData->obtenerTBClientePadecimientoPorCliente($clienteId);

        if ($registroExistente) {
            // Ya existe: agregar el dictamen a los existentes
            $dictamenesActuales = $registroExistente->getTbpadecimientodictamenid();
            $dictamenesArray = empty($dictamenesActuales) ? [] : explode('$', $dictamenesActuales);

            if (!in_array($dictamenId, $dictamenesArray)) {
                $dictamenesArray[] = $dictamenId;
                $nuevaCadena = implode('$', array_filter($dictamenesArray));

                $registroExistente->setTbpadecimientodictamenid($nuevaCadena);
                return $this->clientePadecimientoData->actualizarTBClientePadecimiento($registroExistente);
            }

            return true; // Ya estaba asociado
        } else {
            // No existe: crear nuevo registro solo con el dictamen
            $nuevoRegistro = new ClientePadecimiento(0, $clienteId, '', $dictamenId);
            return $this->clientePadecimientoData->insertarTBClientePadecimiento($nuevoRegistro);
        }
    }
}

?>