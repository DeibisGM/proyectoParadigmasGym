<?php
    include_once 'data.php';
    if (!class_exists('ClientePadecimiento')) {
        include_once '../domain/clientePadecimiento.php';
    }
    if (!class_exists('PadecimientoDictamenData')) {
        include_once 'PadecimientoDictamenData.php';
    }
    class ClientePadecimientoData extends Data {

        public function insertarTBClientePadecimiento($clientePadecimiento) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return false;
            }
            $conn->set_charset('utf8');

            $queryExiste = "SELECT tbclientepadecimientoid, tbpadecimientoid FROM tbclientepadecimiento WHERE tbclienteid = ?";
            $stmtExiste = mysqli_prepare($conn, $queryExiste);
            mysqli_stmt_bind_param($stmtExiste, "i", $clientePadecimiento->getTbclienteid());
            mysqli_stmt_execute($stmtExiste);
            $resultExiste = mysqli_stmt_get_result($stmtExiste);
            $result = false;

            if ($rowExiste = mysqli_fetch_assoc($resultExiste)) {

                $registroExistenteId = $rowExiste['tbclientepadecimientoid'];
                $padecimientosActuales = $rowExiste['tbpadecimientoid'];
                $nuevosPadecimientos = $clientePadecimiento->getTbpadecimientoid();

                $actualesArray = empty($padecimientosActuales) ? [] : explode('$', $padecimientosActuales);
                $nuevosArray = empty($nuevosPadecimientos) ? [] : explode('$', $nuevosPadecimientos);
                $todosLosPadecimientos = array_unique(array_merge($actualesArray, $nuevosArray));
                $padecimientosConcatenados = implode('$', array_filter($todosLosPadecimientos));

                if ($clientePadecimiento->getTbpadecimientodictamenid() !== null) {
                    $queryUpdate = "UPDATE tbclientepadecimiento SET tbpadecimientoid = ?, tbpadecimientodictamenid = ? WHERE tbclientepadecimientoid = ?";
                    $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
                    mysqli_stmt_bind_param($stmtUpdate, "sii", $padecimientosConcatenados, $clientePadecimiento->getTbpadecimientodictamenid(), $registroExistenteId);
                    $result = mysqli_stmt_execute($stmtUpdate);
                    mysqli_stmt_close($stmtUpdate);
                } else {
                    $queryUpdate = "UPDATE tbclientepadecimiento SET tbpadecimientoid = ? WHERE tbclientepadecimientoid = ?";
                    $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
                    mysqli_stmt_bind_param($stmtUpdate, "si", $padecimientosConcatenados, $registroExistenteId);
                    $result = mysqli_stmt_execute($stmtUpdate);
                    mysqli_stmt_close($stmtUpdate);
                }
            } else {
                $queryGetLastId = "SELECT MAX(tbclientepadecimientoid) AS tbclientepadecimientoid FROM tbclientepadecimiento";
                $resultId = mysqli_query($conn, $queryGetLastId);
                $nextId = 1;
                $row = mysqli_fetch_row($resultId);
                if ($row) {
                    if ($row[0] !== null) {
                        $nextId = (int)$row[0] + 1;
                    }
                }

                $queryInsert = "INSERT INTO tbclientepadecimiento (tbclientepadecimientoid, tbclienteid, tbpadecimientoid, tbpadecimientodictamenid) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $queryInsert);

                if ($stmt) {
                    $dictamenId = $clientePadecimiento->getTbpadecimientodictamenid();
                    $dictamenId = $dictamenId === null ? null : (int)$dictamenId;
                    mysqli_stmt_bind_param($stmt, "iisi", $nextId, $clientePadecimiento->getTbclienteid(), $clientePadecimiento->getTbpadecimientoid(), $dictamenId);
                    $result = mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }

            mysqli_stmt_close($stmtExiste);
            mysqli_close($conn);
            return $result;
        }

        public function actualizarTBClientePadecimiento($clientePadecimiento) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return false;
            }
            $conn->set_charset('utf8');

            $queryUpdate = "UPDATE tbclientepadecimiento SET tbclienteid=?, tbpadecimientoid=?, tbpadecimientodictamenid=? WHERE tbclientepadecimientoid=?";
            $stmt = mysqli_prepare($conn, $queryUpdate);

            if ($stmt) {
                 $dictamenId = $clientePadecimiento->getTbpadecimientodictamenid();
                $dictamenId = $dictamenId === null ? null : (int)$dictamenId;
                mysqli_stmt_bind_param($stmt, "isii", $clientePadecimiento->getTbclienteid(), $clientePadecimiento->getTbpadecimientoid(), $dictamenId, $clientePadecimiento->getTbclientepadecimientoid());
                $result = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                $result = false;
            }

            mysqli_close($conn);
            return $result;
        }

        public function eliminarTBClientePadecimiento($tbclientepadecimientoid) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return false;
            }
            $conn->set_charset('utf8');

            // Primero obtenemos el registro para saber qué dictámenes eliminar
            $querySelect = "SELECT tbpadecimientodictamenid FROM tbclientepadecimiento WHERE tbclientepadecimientoid=?";
            $stmtSelect = mysqli_prepare($conn, $querySelect);
            mysqli_stmt_bind_param($stmtSelect, "i", $tbclientepadecimientoid);
            mysqli_stmt_execute($stmtSelect);
            $result = mysqli_stmt_get_result($stmtSelect);

            $dictamenesIds = null;
            if ($row = mysqli_fetch_assoc($result)) {
                $dictamenesIds = $row['tbpadecimientodictamenid'];
            }
            mysqli_stmt_close($stmtSelect);

            // Si hay dictámenes asociados, los eliminamos
            if (!empty($dictamenesIds)) {
                $padecimientoDictamenData = new PadecimientoDictamenData();
                $idsArray = explode('$', $dictamenesIds);

                foreach ($idsArray as $dictamenId) {
                    $dictamenId = intval($dictamenId);
                    if ($dictamenId > 0) {
                        $padecimientoDictamenData->eliminarTBPadecimientoDictamen($dictamenId);
                    }
                }
            }

            // Ahora eliminamos el registro de clientepadecimiento
            $queryDelete = "DELETE FROM tbclientepadecimiento WHERE tbclientepadecimientoid=?";
            $stmt = mysqli_prepare($conn, $queryDelete);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $tbclientepadecimientoid);
                $result = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                $result = false;
            }

            mysqli_close($conn);
            return $result;
        }

        // Método modificado para eliminar todos los padecimientos de un cliente
        public function eliminarTBClientePadecimientoPorCliente($tbclienteid) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return false;
            }
            $conn->set_charset('utf8');

            // Primero obtenemos todos los registros del cliente para saber qué dictámenes eliminar
            $querySelect = "SELECT tbpadecimientodictamenid FROM tbclientepadecimiento WHERE tbclienteid=?";
            $stmtSelect = mysqli_prepare($conn, $querySelect);
            mysqli_stmt_bind_param($stmtSelect, "i", $tbclienteid);
            mysqli_stmt_execute($stmtSelect);
            $result = mysqli_stmt_get_result($stmtSelect);

            $todosDictamenes = array();
            while ($row = mysqli_fetch_assoc($result)) {
                if (!empty($row['tbpadecimientodictamenid'])) {
                    $idsArray = explode('$', $row['tbpadecimientodictamenid']);
                    $todosDictamenes = array_merge($todosDictamenes, $idsArray);
                }
            }
            mysqli_stmt_close($stmtSelect);

            // Eliminamos los dictámenes únicos
            if (!empty($todosDictamenes)) {
                $dictamenesUnicos = array_unique($todosDictamenes);
                $padecimientoDictamenData = new PadecimientoDictamenData();

                foreach ($dictamenesUnicos as $dictamenId) {
                    $dictamenId = intval($dictamenId);
                    if ($dictamenId > 0) {
                        $padecimientoDictamenData->eliminarTBPadecimientoDictamen($dictamenId);
                    }
                }
            }

            // Ahora eliminamos todos los registros del cliente
            $queryDelete = "DELETE FROM tbclientepadecimiento WHERE tbclienteid=?";
            $stmt = mysqli_prepare($conn, $queryDelete);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $tbclienteid);
                $result = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                $result = false;
            }

            mysqli_close($conn);
            return $result;
        }
        public function obtenerTBClientePadecimiento() {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return array();
            }
            $conn->set_charset('utf8');

            $querySelect = "SELECT cp.*, c.tbclientecarnet FROM tbclientepadecimiento cp
                            LEFT JOIN tbcliente c ON cp.tbclienteid = c.tbclienteid
                            ORDER BY cp.tbclienteid, cp.tbclientepadecimientoid";
            $result = mysqli_query($conn, $querySelect);

            if (!$result) {
                mysqli_close($conn);
                return array();
            }

            $clientePadecimientos = array();
            while ($row = mysqli_fetch_array($result)) {
                $currentClientePadecimiento = new ClientePadecimiento(
                    $row['tbclientepadecimientoid'],
                    $row['tbclienteid'],
                    $row['tbpadecimientoid'],
                    $row['tbpadecimientodictamenid']
                );

                $currentClientePadecimiento->setCarnet($row['tbclientecarnet'] ?? '');

                $padecimientosNombres = $this->obtenerNombresPadecimientos($row['tbpadecimientoid']);
                $currentClientePadecimiento->setPadecimientosNombres($padecimientosNombres);

                array_push($clientePadecimientos, $currentClientePadecimiento);
            }

            mysqli_close($conn);
            return $clientePadecimientos;
        }

        public function obtenerTBClientePadecimientoPorCliente($tbclienteid) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return null;
            }
            $conn->set_charset('utf8');

            $querySelect = "SELECT * FROM tbclientepadecimiento WHERE tbclienteid=? ORDER BY tbclientepadecimientoid LIMIT 1";
            $stmt = mysqli_prepare($conn, $querySelect);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $tbclienteid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($row = mysqli_fetch_array($result)) {
                    $clientePadecimiento = new ClientePadecimiento(
                        $row['tbclientepadecimientoid'],
                        $row['tbclienteid'],
                        $row['tbpadecimientoid'],
                        $row['tbpadecimientodictamenid']
                    );

                    $padecimientosNombres = $this->obtenerNombresPadecimientos($row['tbpadecimientoid']);
                    $clientePadecimiento->setPadecimientosNombres($padecimientosNombres);

                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);
                    return $clientePadecimiento;
                }

                mysqli_stmt_close($stmt);
            }

            mysqli_close($conn);
            return null;
        }

        public function obtenerTodosTBClientePadecimientoPorCliente($tbclienteid) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return array();
            }
            $conn->set_charset('utf8');

            $querySelect = "SELECT cp.*, c.tbclientecarnet FROM tbclientepadecimiento cp
                            LEFT JOIN tbcliente c ON cp.tbclienteid = c.tbclienteid
                            WHERE cp.tbclienteid=? ORDER BY cp.tbclientepadecimientoid";
            $stmt = mysqli_prepare($conn, $querySelect);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $tbclienteid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                $clientePadecimientos = array();
                while ($row = mysqli_fetch_array($result)) {
                    $clientePadecimiento = new ClientePadecimiento(
                        $row['tbclientepadecimientoid'],
                        $row['tbclienteid'],
                        $row['tbpadecimientoid'],
                        $row['tbpadecimientodictamenid']
                    );

                    $clientePadecimiento->setCarnet($row['tbclientecarnet'] ?? '');

                    $padecimientosNombres = $this->obtenerNombresPadecimientos($row['tbpadecimientoid']);
                    $clientePadecimiento->setPadecimientosNombres($padecimientosNombres);

                    array_push($clientePadecimientos, $clientePadecimiento);
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $clientePadecimientos;
            }

            mysqli_close($conn);
            return array();
        }

        public function obtenerTodosLosClientes() {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return array();
            }
            $conn->set_charset('utf8');

            $queryClientes = "SELECT tbclienteid, tbclientecarnet, tbclientenombre
                              FROM tbcliente
                              ORDER BY tbclientecarnet";

            $result = mysqli_query($conn, $queryClientes);
            if (!$result) {
                mysqli_close($conn);
                return array();
            }

            $clientes = array();
            while ($row = mysqli_fetch_array($result)) {
                $clientes[] = array(
                    'id' => $row['tbclienteid'],
                    'carnet' => $row['tbclientecarnet'],
                    'nombre' => $row['tbclientenombre'],
                    'apellidos' => ''
                );
            }

            mysqli_close($conn);
            return $clientes;
        }

        public function obtenerTBClientePadecimientoPorId($registroId) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return null;
            }
            $conn->set_charset('utf8');

            $querySelect = "SELECT * FROM tbclientepadecimiento WHERE tbclientepadecimientoid=?";
            $stmt = mysqli_prepare($conn, $querySelect);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $registroId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($row = mysqli_fetch_array($result)) {
                    $clientePadecimiento = new ClientePadecimiento(
                        $row['tbclientepadecimientoid'],
                        $row['tbclienteid'],
                        $row['tbpadecimientoid'],
                        $row['tbpadecimientodictamenid']
                    );

                    $padecimientosNombres = $this->obtenerNombresPadecimientos($row['tbpadecimientoid']);
                    $clientePadecimiento->setPadecimientosNombres($padecimientosNombres);

                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);
                    return $clientePadecimiento;
                }

                mysqli_stmt_close($stmt);
            }

            mysqli_close($conn);
            return null;
        }

        public function obtenerPadecimientos() {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return array();
            }
            $conn->set_charset('utf8');

            $querySelect = "SELECT * FROM tbpadecimiento ORDER BY tbpadecimientonombre";
            $result = mysqli_query($conn, $querySelect);

            if (!$result) {
                mysqli_close($conn);
                return array();
            }

            $padecimientos = array();
            while ($row = mysqli_fetch_array($result)) {
                $padecimientos[] = array(
                    'id' => $row['tbpadecimientoid'],
                    'nombre' => $row['tbpadecimientonombre']
                );
            }

            mysqli_close($conn);
            return $padecimientos;
        }

        private function obtenerNombresPadecimientos($padecimientosIds) {
            if (empty($padecimientosIds)) {
                return array();
            }

            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return array();
            }
            $conn->set_charset('utf8');

            $idsArray = explode('$', $padecimientosIds);
            $nombres = array();

            foreach ($idsArray as $id) {
                $id = intval($id);
                if ($id > 0) {
                    $queryNombre = "SELECT tbpadecimientonombre FROM tbpadecimiento WHERE tbpadecimientoid = ?";
                    $stmt = mysqli_prepare($conn, $queryNombre);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        mysqli_stmt_execute($stmt);
                        $resultNombre = mysqli_stmt_get_result($stmt);

                        if ($rowNombre = mysqli_fetch_array($resultNombre)) {
                            $nombres[] = $rowNombre['tbpadecimientonombre'];
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
            }

            mysqli_close($conn);
            return $nombres;
        }

        public function validarPadecimientosExisten($padecimientosIds) {
            if (empty($padecimientosIds)) {
                return false;
            }

            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return false;
            }
            $conn->set_charset('utf8');

            $idsArray = explode('$', $padecimientosIds);
            $idsValidos = 0;

            foreach ($idsArray as $id) {
                $id = intval($id);
                if ($id > 0) {
                    $queryValidar = "SELECT COUNT(*) as total FROM tbpadecimiento WHERE tbpadecimientoid = ?";
                    $stmt = mysqli_prepare($conn, $queryValidar);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $id);
                        mysqli_stmt_execute($stmt);
                        $resultValidar = mysqli_stmt_get_result($stmt);
                        $rowValidar = mysqli_fetch_array($resultValidar);
                        mysqli_stmt_close($stmt);

                        if ($rowValidar['total'] > 0) {
                            $idsValidos++;
                        }
                    }
                }
            }

            mysqli_close($conn);
            return $idsValidos === count($idsArray);
        }
    }
?>