<?php
    include_once 'data.php';
    if (!class_exists('DatoClinico')) {
        include_once '../domain/datoClinico.php';
    }

    class DatoClinicoData extends Data {

        public function insertarTBDatoClinico($datoClinico) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return false;
            }
            $conn->set_charset('utf8');

            // Verificar si ya existe un registro para este cliente
            $queryExiste = "SELECT * FROM tbdatoclinico WHERE tbclienteid = ?";
            $stmtExiste = mysqli_prepare($conn, $queryExiste);
            mysqli_stmt_bind_param($stmtExiste, "i", $datoClinico->getTbclienteid());
            mysqli_stmt_execute($stmtExiste);
            $resultExiste = mysqli_stmt_get_result($stmtExiste);

            if ($rowExiste = mysqli_fetch_assoc($resultExiste)) {
                // YA EXISTE: Concatenar padecimientos
                $padecimientosActuales = $rowExiste['tbpadecimientoid'];
                $nuevosPadecimientos = $datoClinico->getTbpadecimientoid();

                // Convertir a arrays, unir y eliminar duplicados
                $actualesArray = empty($padecimientosActuales) ? array() : explode('$', $padecimientosActuales);
                $nuevosArray = empty($nuevosPadecimientos) ? array() : explode('$', $nuevosPadecimientos);
                $todosLosPadecimientos = array_unique(array_merge($actualesArray, $nuevosArray));
                $padecimientosConcatenados = implode('$', array_filter($todosLosPadecimientos));

                // Actualizar registro existente
                $queryUpdate = "UPDATE tbdatoclinico SET tbpadecimientoid = ? WHERE tbclienteid = ?";
                $stmtUpdate = mysqli_prepare($conn, $queryUpdate);
                mysqli_stmt_bind_param($stmtUpdate, "si", $padecimientosConcatenados, $datoClinico->getTbclienteid());
                $result = mysqli_stmt_execute($stmtUpdate);
                mysqli_stmt_close($stmtUpdate);

            } else {
                // NO EXISTE: Crear nuevo registro
                $queryGetLastId = "SELECT MAX(tbdatoclinicoid) AS tbdatoclinicoid FROM tbdatoclinico";
                $resultId = mysqli_query($conn, $queryGetLastId);
                $nextId = 1;

                if ($row = mysqli_fetch_row($resultId)) {
                    if ($row[0] !== null) {
                        $nextId = (int)$row[0] + 1;
                    }
                }

                $queryInsert = "INSERT INTO tbdatoclinico (tbdatoclinicoid, tbclienteid, tbpadecimientoid) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $queryInsert);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "iis", $nextId, $datoClinico->getTbclienteid(), $datoClinico->getTbpadecimientoid());
                    $result = mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    $result = false;
                }
            }

            mysqli_stmt_close($stmtExiste);
            mysqli_close($conn);
            return $result;
        }

        public function actualizarTBDatoClinico($datoClinico) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return false;
            }
            $conn->set_charset('utf8');

            $queryUpdate = "UPDATE tbdatoclinico SET tbclienteid=?, tbpadecimientoid=? WHERE tbdatoclinicoid=?";
            $stmt = mysqli_prepare($conn, $queryUpdate);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "isi", $datoClinico->getTbclienteid(), $datoClinico->getTbpadecimientoid(), $datoClinico->getTbdatoclinicoid());
                $result = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                $result = false;
            }

            mysqli_close($conn);
            return $result;
        }

        public function eliminarTBDatoClinico($tbdatoclinicoid) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return false;
            }
            $conn->set_charset('utf8');

            $queryDelete = "DELETE FROM tbdatoclinico WHERE tbdatoclinicoid=?";
            $stmt = mysqli_prepare($conn, $queryDelete);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $tbdatoclinicoid);
                $result = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                $result = false;
            }

            mysqli_close($conn);
            return $result;
        }
        public function eliminarTBDatoClinicoPorCliente($tbclienteid) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return false;
            }
            $conn->set_charset('utf8');

            $queryDelete = "DELETE FROM tbdatoclinico WHERE tbclienteid=?";
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

        public function obtenerTBDatoClinico() {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return array();
            }
            $conn->set_charset('utf8');

            $querySelect = "SELECT dc.*, c.tbclientecarnet FROM tbdatoclinico dc
                            LEFT JOIN tbcliente c ON dc.tbclienteid = c.tbclienteid
                            ORDER BY dc.tbclienteid, dc.tbdatoclinicoid";
            $result = mysqli_query($conn, $querySelect);

            if (!$result) {
                mysqli_close($conn);
                return array();
            }

            $datosClinicos = array();
            while ($row = mysqli_fetch_array($result)) {
                $currentDatoClinico = new DatoClinico(
                    $row['tbdatoclinicoid'],
                    $row['tbclienteid'],
                    $row['tbpadecimientoid']
                );

                // Establecer el carnet
                $currentDatoClinico->setCarnet($row['tbclientecarnet'] ?? '');

                // Obtener nombres de padecimientos
                $padecimientosNombres = $this->obtenerNombresPadecimientos($row['tbpadecimientoid']);
                $currentDatoClinico->setPadecimientosNombres($padecimientosNombres);

                array_push($datosClinicos, $currentDatoClinico);
            }

            mysqli_close($conn);
            return $datosClinicos;
        }

        public function obtenerTBDatoClinicoPorCliente($tbclienteid) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return null;
            }
            $conn->set_charset('utf8');

            $querySelect = "SELECT * FROM tbdatoclinico WHERE tbclienteid=? ORDER BY tbdatoclinicoid LIMIT 1";
            $stmt = mysqli_prepare($conn, $querySelect);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $tbclienteid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if ($row = mysqli_fetch_array($result)) {
                    $datoClinico = new DatoClinico(
                        $row['tbdatoclinicoid'],
                        $row['tbclienteid'],
                        $row['tbpadecimientoid']
                    );

                    $padecimientosNombres = $this->obtenerNombresPadecimientos($row['tbpadecimientoid']);
                    $datoClinico->setPadecimientosNombres($padecimientosNombres);

                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);
                    return $datoClinico;
                }

                mysqli_stmt_close($stmt);
            }

            mysqli_close($conn);
            return null;
        }

        public function obtenerTodosTBDatoClinicoPorCliente($tbclienteid) {
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            if (!$conn) {
                return array();
            }
            $conn->set_charset('utf8');

            $querySelect = "SELECT dc.*, c.tbclientecarnet FROM tbdatoclinico dc
                            LEFT JOIN tbcliente c ON dc.tbclienteid = c.tbclienteid
                            WHERE dc.tbclienteid=? ORDER BY dc.tbdatoclinicoid";
            $stmt = mysqli_prepare($conn, $querySelect);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $tbclienteid);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                $datosClinicos = array();
                while ($row = mysqli_fetch_array($result)) {
                    $datoClinico = new DatoClinico(
                        $row['tbdatoclinicoid'],
                        $row['tbclienteid'],
                        $row['tbpadecimientoid']
                    );

                    // Establecer el carnet
                    $datoClinico->setCarnet($row['tbclientecarnet'] ?? '');

                    // Obtener nombres de padecimientos
                    $padecimientosNombres = $this->obtenerNombresPadecimientos($row['tbpadecimientoid']);
                    $datoClinico->setPadecimientosNombres($padecimientosNombres);

                    array_push($datosClinicos, $datoClinico);
                }

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $datosClinicos;
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

    public function obtenerTBDatoClinicoPorId($registroId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            return null;
        }
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbdatoclinico WHERE tbdatoclinicoid=?";
        $stmt = mysqli_prepare($conn, $querySelect);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $registroId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_array($result)) {
                $datoClinico = new DatoClinico(
                    $row['tbdatoclinicoid'],
                    $row['tbclienteid'],
                    $row['tbpadecimientoid']
                );

                $padecimientosNombres = $this->obtenerNombresPadecimientos($row['tbpadecimientoid']);
                $datoClinico->setPadecimientosNombres($padecimientosNombres);

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $datoClinico;
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