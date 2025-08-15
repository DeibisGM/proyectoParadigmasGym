<?php
    include_once 'data.php';
    if (!class_exists('DatoClinico')) {
        include_once '../domain/datoClinico.php';
    }

    class DatoClinicoData extends Data {

        public function insertarTBDatoClinico($datoClinico){
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');

            $queryGetLastId = "SELECT MAX(tbdatoclinicoid) AS tbdatoclinicoid FROM tbdatoclinico";
            $resultId = mysqli_query($conn, $queryGetLastId);
            $nextId = 1;

            if ($row = mysqli_fetch_row($resultId)) {
                if ($row[0] !== null) {
                    $nextId = (int)$row[0] + 1;
                }
            }

            $queryInsert = "INSERT INTO tbdatoclinico (
                tbdatoclinicoid, tbdatoclinicoenfermedad, tbdatoclinicoenfermedaddescripcion, tbdatoclinicomedicamento,
                tbdatoclinicomedicamentodescripcion, tbdatoclinicolesion, tbdatoclinicolesiondescripcion,
                tbdatoclinicodiscapacidad, tbdatoclinicodiscapacidaddescripcion,
                tbdatoclinicorestriccionmedica, tbdatoclinicorestriccionmedicadescripcion,
                tbclienteid
            ) VALUES (
                " . $nextId . ",
                " . $datoClinico->getTbdatoclinicoenfermedad() . ",
                '" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicoenfermedaddescripcion()) . "',
                " . $datoClinico->getTbdatoclinicomedicamento() . ",
                '" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicomedicamentodescripcion()) . "',
                " . $datoClinico->getTbdatoclinicolesion() . ",
                '" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicolesiondescripcion()) . "',
                " . $datoClinico->getTbdatoclinicodiscapacidad() . ",
                '" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicodiscapacidaddescripcion()) . "',
                " . $datoClinico->getTbdatoclinicorestriccionmedica() . ",
                '" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicorestriccionmedicadescripcion()) . "',
                " . $datoClinico->getTbclienteid() . "
            );";

            $result = mysqli_query($conn, $queryInsert);
            mysqli_close($conn);
            return $result;
        }

        public function actualizarTBDatoClinico($datoClinico){
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');

            $queryUpdate = "UPDATE tbdatoclinico SET
                    tbdatoclinicoenfermedad=" . $datoClinico->getTbdatoclinicoenfermedad() . ",
                    tbdatoclinicoenfermedaddescripcion='" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicoenfermedaddescripcion()) . "',
                    tbdatoclinicomedicamento=" . $datoClinico->getTbdatoclinicomedicamento() . ",
                    tbdatoclinicomedicamentodescripcion='" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicomedicamentodescripcion()) . "',
                    tbdatoclinicolesion=" . $datoClinico->getTbdatoclinicolesion() . ",
                    tbdatoclinicolesiondescripcion='" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicolesiondescripcion()) . "',
                    tbdatoclinicodiscapacidad=" . $datoClinico->getTbdatoclinicodiscapacidad() . ",
                    tbdatoclinicodiscapacidaddescripcion='" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicodiscapacidaddescripcion()) . "',
                    tbdatoclinicorestriccionmedica=" . $datoClinico->getTbdatoclinicorestriccionmedica() . ",
                    tbdatoclinicorestriccionmedicadescripcion='" . mysqli_real_escape_string($conn, $datoClinico->getTbdatoclinicorestriccionmedicadescripcion()) . "'
                    WHERE tbdatoclinicoid=" . $datoClinico->getTbdatoclinicoid() . ";";

            $result = mysqli_query($conn, $queryUpdate);
            mysqli_close($conn);
            return $result;
        }

        public function eliminarTBDatoClinico($tbdatoclinicoid){
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');
            $queryDelete = "DELETE from tbdatoclinico where tbdatoclinicoid=" . $tbdatoclinicoid . ";";
            $result = mysqli_query($conn, $queryDelete);
            mysqli_close($conn);
            return $result;
        }

        public function obtenerTBDatoClinico() {//obtener todos los registros
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');

            $querySelect = "SELECT * FROM tbdatoclinico";
            $result = mysqli_query($conn, $querySelect);

            $datosClinicos = [];
            while ($row = mysqli_fetch_array($result)) {

                $clienteId = $row['tbclienteid'];
                $queryCarnet = "SELECT tbclientecarnet FROM tbcliente WHERE tbclienteid = $clienteId";
                $resultCarnet = mysqli_query($conn, $queryCarnet);
                $carnetRow = mysqli_fetch_array($resultCarnet);

                $currentDatoClinico = new DatoClinico(
                    $row['tbdatoclinicoid'], $row['tbclienteid'], $row['tbdatoclinicoenfermedad'],
                    $row['tbdatoclinicoenfermedaddescripcion'], $row['tbdatoclinicomedicamento'], $row['tbdatoclinicomedicamentodescripcion'],
                    $row['tbdatoclinicolesion'], $row['tbdatoclinicolesiondescripcion'], $row['tbdatoclinicodiscapacidad'],
                    $row['tbdatoclinicodiscapacidaddescripcion'], $row['tbdatoclinicorestriccionmedica'], $row['tbdatoclinicorestriccionmedicadescripcion']
                );

                $currentDatoClinico->setCarnet($carnetRow['tbclientecarnet'] ?? '');

                array_push($datosClinicos, $currentDatoClinico);
            }

            mysqli_close($conn);
            return $datosClinicos;
        }

        public function obtenerTBDatoClinicoPorCliente($tbclienteid) {//obtener datos de un cliente en especifico
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');
            $querySelect = "SELECT * FROM tbdatoclinico WHERE tbclienteid=" . $tbclienteid . ";";
            $result = mysqli_query($conn, $querySelect);

            if ($row = mysqli_fetch_array($result)) {
                $datoClinico = new DatoClinico(
                    $row['tbdatoclinicoid'], $row['tbclienteid'], $row['tbdatoclinicoenfermedad'],
                    $row['tbdatoclinicoenfermedaddescripcion'], $row['tbdatoclinicomedicamento'], $row['tbdatoclinicomedicamentodescripcion'],
                    $row['tbdatoclinicolesion'], $row['tbdatoclinicolesiondescripcion'], $row['tbdatoclinicodiscapacidad'],
                    $row['tbdatoclinicodiscapacidaddescripcion'], $row['tbdatoclinicorestriccionmedica'], $row['tbdatoclinicorestriccionmedicadescripcion']
                );
                mysqli_close($conn);
                return $datoClinico;
            }

            mysqli_close($conn);
            return null;
        }

        public function obtenerTodosLosClientes() {//obtiene clientes que no tienen datos clinicos
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');

            $queryClientes = "SELECT tbclienteid, tbclientecarnet, tbclientenombre
                              FROM tbcliente
                              ORDER BY tbclientecarnet";

            $result = mysqli_query($conn, $queryClientes);
            $clientes = [];

            while ($row = mysqli_fetch_array($result)) {

                $clienteId = $row['tbclienteid'];
                $queryDatos = "SELECT COUNT(*) as total FROM tbdatoclinico WHERE tbclienteid = $clienteId";
                $resultDatos = mysqli_query($conn, $queryDatos);
                $datosRow = mysqli_fetch_array($resultDatos);

                if ($datosRow['total'] == 0) {
                    $clientes[] = array(
                        'id' => $row['tbclienteid'],
                        'carnet' => $row['tbclientecarnet'],
                        'nombre' => $row['tbclientenombre'],
                        'apellidos' => ''
                    );
                }
            }

            mysqli_close($conn);
            return $clientes;
        }
    }
?>