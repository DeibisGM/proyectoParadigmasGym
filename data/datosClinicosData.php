<?php
    include_once 'data.php';
    if (!class_exists('DatosClinicos')) {
        include_once '../domain/datosClinicos.php';
    }

    class DatosClinicosData extends Data {

        public function insertarTBDatosClinicos($datosClinicos){
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');

            $queryGetLastId = "SELECT MAX(idtbdatosclinicos) AS idtbdatosclinicos FROM tbdatosclinicos";
            $resultId = mysqli_query($conn, $queryGetLastId);
            $nextId = 1;

            if ($row = mysqli_fetch_row($resultId)) {
                if ($row[0] !== null) {
                    $nextId = (int)$row[0] + 1;
                }
            }

            $queryInsert = "INSERT INTO tbdatosclinicos (
                idtbdatosclinicos, tbdatosclinicosenfermedad, tbdatosclinicosotraenfermedad, tbdatosclinicostomamedicamento,
                tbdatosclinicosmedicamento, tbdatosclinicoslesion, tbdatosclinicosdescripcionlesion,
                tbdatosclinicosdiscapacidad, tbdatosclinicosdescripciondiscapacidad,
                tbdatosclinicosrestriccionmedica, tbdatosclinicosdescripcionrestriccionmedica,
                tbclientesid
            ) VALUES (
                " . $nextId . ",
                " . $datosClinicos->getTbdatosclinicosenfermedad() . ",
                '" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosotraenfermedad()) . "',
                " . $datosClinicos->getTbdatosclinicostomamedicamento() . ",
                '" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosmedicamento()) . "',
                " . $datosClinicos->getTbdatosclinicoslesion() . ",
                '" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosdescripcionlesion()) . "',
                " . $datosClinicos->getTbdatosclinicosdiscapacidad() . ",
                '" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosdescripciondiscapacidad()) . "',
                " . $datosClinicos->getTbdatosclinicosrestriccionmedica() . ",
                '" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosdescripcionrestriccionmedica()) . "',
                " . $datosClinicos->getTbclientesid() . "
            );";

            $result = mysqli_query($conn, $queryInsert);
            mysqli_close($conn);
            return $result;
        }

        public function actualizarTBDatosClinicos($datosClinicos){
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');

            $queryUpdate = "UPDATE tbdatosclinicos SET
                    tbdatosclinicosenfermedad=" . $datosClinicos->getTbdatosclinicosenfermedad() . ",
                    tbdatosclinicosotraenfermedad='" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosotraenfermedad()) . "',
                    tbdatosclinicostomamedicamento=" . $datosClinicos->getTbdatosclinicostomamedicamento() . ",
                    tbdatosclinicosmedicamento='" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosmedicamento()) . "',
                    tbdatosclinicoslesion=" . $datosClinicos->getTbdatosclinicoslesion() . ",
                    tbdatosclinicosdescripcionlesion='" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosdescripcionlesion()) . "',
                    tbdatosclinicosdiscapacidad=" . $datosClinicos->getTbdatosclinicosdiscapacidad() . ",
                    tbdatosclinicosdescripciondiscapacidad='" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosdescripciondiscapacidad()) . "',
                    tbdatosclinicosrestriccionmedica=" . $datosClinicos->getTbdatosclinicosrestriccionmedica() . ",
                    tbdatosclinicosdescripcionrestriccionmedica='" . mysqli_real_escape_string($conn, $datosClinicos->getTbdatosclinicosdescripcionrestriccionmedica()) . "'
                    WHERE idtbdatosclinicos=" . $datosClinicos->getTbdatosclinicosid() . ";";

            $result = mysqli_query($conn, $queryUpdate);
            mysqli_close($conn);
            return $result;
        }

        public function eliminarTBDatosClinicos($tbdatosclinicosId){
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');
            $queryDelete = "DELETE from tbdatosclinicos where idtbdatosclinicos=" . $tbdatosclinicosId . ";";
            $result = mysqli_query($conn, $queryDelete);
            mysqli_close($conn);
            return $result;
        }

        public function obtenerTBDatosClinicos() {//obtener todos los registros
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');

            $querySelect = "SELECT * FROM tbdatosclinicos";
            $result = mysqli_query($conn, $querySelect);

            $datosClinicos = [];
            while ($row = mysqli_fetch_array($result)) {

                $clienteId = $row['tbclientesid'];
                $queryCarnet = "SELECT tbclientescarnet FROM tbclientes WHERE tbclientesid = $clienteId";
                $resultCarnet = mysqli_query($conn, $queryCarnet);
                $carnetRow = mysqli_fetch_array($resultCarnet);

                $currentDatosClinicos = new DatosClinicos(
                    $row['idtbdatosclinicos'], $row['tbdatosclinicosenfermedad'], $row['tbdatosclinicosotraenfermedad'],
                    $row['tbdatosclinicostomamedicamento'], $row['tbdatosclinicosmedicamento'], $row['tbdatosclinicoslesion'],
                    $row['tbdatosclinicosdescripcionlesion'], $row['tbdatosclinicoscapacidad'], $row['tbdatosclinicosdescripciondiscapacidad'],
                    $row['tbdatosclinicosrestriccionmedica'], $row['tbdatosclinicosdescripcionrestriccionmedica'], $row['tbclientesid']
                );

                $currentDatosClinicos->carnet = $carnetRow['tbclientescarnet'] ?? '';

                array_push($datosClinicos, $currentDatosClinicos);
            }

            mysqli_close($conn);
            return $datosClinicos;
        }

        public function obtenerTBDatosClinicosPorCliente($tbclientesId) {//obtener datos de un cliente en especifico
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');
            $querySelect = "SELECT * FROM tbdatosclinicos WHERE tbclientesid=" . $tbclientesId . ";";
            $result = mysqli_query($conn, $querySelect);

            if ($row = mysqli_fetch_array($result)) {
                $datosClinicos = new DatosClinicos(
                    $row['idtbdatosclinicos'], $row['tbdatosclinicosenfermedad'], $row['tbdatosclinicosotraenfermedad'],
                    $row['tbdatosclinicostomamedicamento'], $row['tbdatosclinicosmedicamento'], $row['tbdatosclinicoslesion'],
                    $row['tbdatosclinicosdescripcionlesion'], $row['tbdatosclinicosdiscapacidad'], $row['tbdatosclinicosdescripciondiscapacidad'],
                    $row['tbdatosclinicosrestriccionmedica'], $row['tbdatosclinicosdescripcionrestriccionmedica'], $row['tbclientesid']
                );
                mysqli_close($conn);
                return $datosClinicos;
            }

            mysqli_close($conn);
            return null;
        }

        public function obtenerTodosLosClientes() {//obtiene clientes que no tienen datos clinicos
            $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
            $conn->set_charset('utf8');

            $queryClientes = "SELECT tbclientesid, tbclientescarnet, tbclientesnombre
                              FROM tbclientes
                              ORDER BY tbclientescarnet";

            $result = mysqli_query($conn, $queryClientes);
            $clientes = [];

            while ($row = mysqli_fetch_array($result)) {

                $clienteId = $row['tbclientesid'];
                $queryDatos = "SELECT COUNT(*) as total FROM tbdatosclinicos WHERE tbclientesid = $clienteId";
                $resultDatos = mysqli_query($conn, $queryDatos);
                $datosRow = mysqli_fetch_array($resultDatos);

                if ($datosRow['total'] == 0) {
                    $clientes[] = array(
                        'id' => $row['tbclientesid'],
                        'carnet' => $row['tbclientescarnet'],
                        'nombre' => $row['tbclientesnombre'],
                        'apellidos' => ''
                    );
                }
            }

            mysqli_close($conn);
            return $clientes;
        }
    }
?>