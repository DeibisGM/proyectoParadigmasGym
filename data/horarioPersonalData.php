<?php
include_once 'data.php';
include_once '../domain/horarioPersonal.php';

class HorarioPersonalData extends Data
{
    public function insertarHorarioPersonal($horarioPersonal)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("Error de conexión a la base de datos");
            return false;
        }
        $conn->set_charset('utf8');

        // DEBUG: Log de los datos recibidos
        error_log("DEBUG insertarHorarioPersonal - Datos recibidos:");
        error_log("Fecha: " . $horarioPersonal->getFecha());
        error_log("Hora: " . $horarioPersonal->getHora());
        error_log("Instructor ID: " . $horarioPersonal->getInstructorId());
        error_log("Duración: " . $horarioPersonal->getDuracion());

        // Primero verificar si ya existe un horario para este instructor en la misma fecha y hora
        $checkQuery = "SELECT tbhorariopersonalid FROM tbhorariopersonal
                       WHERE tbinstructorid = ? AND tbhorariopersonalfecha = ? AND tbhorariopersonalhora = ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "sss",
            $horarioPersonal->getInstructorId(),
            $horarioPersonal->getFecha(),
            $horarioPersonal->getHora()
        );
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            error_log("Ya existe un horario para este instructor en la misma fecha y hora");
            mysqli_stmt_close($checkStmt);
            mysqli_close($conn);
            return false;
        }
        mysqli_stmt_close($checkStmt);

        // Insertar nuevo horario
        $query = "INSERT INTO tbhorariopersonal
                  (tbhorariopersonalfecha, tbhorariopersonalhora, tbinstructorid, tbclienteid,
                   tbhorariopersonalestado, tbhorariopersonalduracion, tbhorariopersonaltipo)
                  VALUES (?, ?, ?, NULL, 'disponible', ?, 'personal')";

        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            error_log("Error preparando statement: " . mysqli_error($conn));
            mysqli_close($conn);
            return false;
        }

        mysqli_stmt_bind_param($stmt, "sssi",
            $horarioPersonal->getFecha(),
            $horarioPersonal->getHora(),
            $horarioPersonal->getInstructorId(),
            $horarioPersonal->getDuracion()
        );

        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            error_log("Error en la inserción: " . mysqli_error($conn));
            error_log("Query: " . $query);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return false;
        }

        $lastId = mysqli_insert_id($conn);
        error_log("✅ Horario insertado exitosamente con ID: " . $lastId);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return $lastId;
    }

    public function getHorariosPorRangoFechas($fechaInicio, $fechaFin, $instructorId = null)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("Error de conexión en getHorariosPorRangoFechas");
            return [];
        }
        $conn->set_charset('utf8');

        // Query corregida - SOLO horarios disponibles
        $query = "SELECT
                    hp.tbhorariopersonalid,
                    hp.tbhorariopersonalfecha,
                    hp.tbhorariopersonalhora,
                    hp.tbinstructorid,
                    hp.tbclienteid,
                    hp.tbhorariopersonalestado,
                    hp.tbhorariopersonalduracion,
                    hp.tbhorariopersonaltipo,
                    i.tbinstructornombre,
                    c.tbclientenombre
                  FROM tbhorariopersonal hp
                  LEFT JOIN tbinstructor i ON hp.tbinstructorid = i.tbinstructorid
                  LEFT JOIN tbcliente c ON hp.tbclienteid = c.tbclienteid
                  WHERE hp.tbhorariopersonalfecha BETWEEN ? AND ?
                  AND hp.tbhorariopersonalestado = 'disponible'
                  AND hp.tbclienteid IS NULL";

        if ($instructorId) {
            $query .= " AND hp.tbinstructorid = ?";
        }

        $query .= " ORDER BY hp.tbhorariopersonalfecha, hp.tbhorariopersonalhora";

        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            error_log("Error preparando statement: " . mysqli_error($conn));
            mysqli_close($conn);
            return [];
        }

        if ($instructorId) {
            mysqli_stmt_bind_param($stmt, "sss", $fechaInicio, $fechaFin, $instructorId);
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $fechaInicio, $fechaFin);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $horarios = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // DEBUG: Log de cada fila obtenida
            error_log("✅ Fila obtenida - ID: " . $row['tbhorariopersonalid'] . ", Fecha: " . $row['tbhorariopersonalfecha'] . ", Hora: " . $row['tbhorariopersonalhora']);

            $horario = new HorarioPersonal(
                $row['tbhorariopersonalid'],
                $row['tbhorariopersonalfecha'],
                $row['tbhorariopersonalhora'],
                $row['tbinstructorid'],
                $row['tbclienteid'],
                $row['tbhorariopersonalestado'],
                $row['tbhorariopersonalduracion'],
                $row['tbhorariopersonaltipo']
            );

            if (isset($row['tbinstructornombre'])) {
                $horario->setInstructorNombre($row['tbinstructornombre']);
            }
            if (isset($row['tbclientenombre'])) {
                $horario->setClienteNombre($row['tbclientenombre']);
            }

            $horarios[] = $horario;
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        error_log("✅ Total de horarios disponibles obtenidos: " . count($horarios));
        return $horarios;
    }

    public function reservarHorarioPersonal($horarioId, $clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("Error de conexión en reservarHorarioPersonal");
            return false;
        }
        $conn->set_charset('utf8');

        // Primero verificar que el horario existe y está disponible
        $checkQuery = "SELECT tbhorariopersonalid FROM tbhorariopersonal
                       WHERE tbhorariopersonalid = ?
                       AND tbhorariopersonalestado = 'disponible'
                       AND tbclienteid IS NULL";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "i", $horarioId);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) === 0) {
            error_log("❌ Horario no disponible o no encontrado - ID: " . $horarioId);
            mysqli_stmt_close($checkStmt);
            mysqli_close($conn);
            return false;
        }
        mysqli_stmt_close($checkStmt);

        // Actualizar el horario
        $query = "UPDATE tbhorariopersonal
                  SET tbclienteid = ?, tbhorariopersonalestado = 'reservado'
                  WHERE tbhorariopersonalid = ?
                  AND tbhorariopersonalestado = 'disponible'
                  AND tbclienteid IS NULL";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $clienteId, $horarioId);
        $result = mysqli_stmt_execute($stmt);

        $affectedRows = mysqli_affected_rows($conn);
        error_log("✅ Filas afectadas en reserva: " . $affectedRows . " para horario ID: " . $horarioId);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return $result && $affectedRows > 0;
    }

    public function cancelarReservaPersonal($horarioId, $clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("Error de conexión en cancelarReservaPersonal");
            return false;
        }
        $conn->set_charset('utf8');

        $query = "UPDATE tbhorariopersonal
                  SET tbclienteid = NULL, tbhorariopersonalestado = 'disponible'
                  WHERE tbhorariopersonalid = ? AND tbclienteid = ?";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $horarioId, $clienteId);
        $result = mysqli_stmt_execute($stmt);

        $affectedRows = mysqli_affected_rows($conn);
        error_log("✅ Filas afectadas en cancelación: " . $affectedRows);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return $result && $affectedRows > 0;
    }

    public function eliminarHorarioPersonal($horarioId, $instructorId = null)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("Error de conexión en eliminarHorarioPersonal");
            return false;
        }
        $conn->set_charset('utf8');

        if ($instructorId) {
            $query = "DELETE FROM tbhorariopersonal
                      WHERE tbhorariopersonalid = ?
                      AND tbinstructorid = ?
                      AND tbhorariopersonalestado = 'disponible'
                      AND tbclienteid IS NULL";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "is", $horarioId, $instructorId);
        } else {
            $query = "DELETE FROM tbhorariopersonal
                      WHERE tbhorariopersonalid = ?
                      AND tbhorariopersonalestado = 'disponible'
                      AND tbclienteid IS NULL";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $horarioId);
        }

        $result = mysqli_stmt_execute($stmt);
        $affectedRows = mysqli_affected_rows($conn);

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return $result && $affectedRows > 0;
    }

    public function getReservasPorCliente($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("Error de conexión en getReservasPorCliente");
            return [];
        }
        $conn->set_charset('utf8');

        $query = "SELECT
                    hp.tbhorariopersonalid,
                    hp.tbhorariopersonalfecha,
                    hp.tbhorariopersonalhora,
                    hp.tbinstructorid,
                    hp.tbclienteid,
                    hp.tbhorariopersonalestado,
                    hp.tbhorariopersonalduracion,
                    hp.tbhorariopersonaltipo,
                    i.tbinstructornombre
                  FROM tbhorariopersonal hp
                  LEFT JOIN tbinstructor i ON hp.tbinstructorid = i.tbinstructorid
                  WHERE hp.tbclienteid = ?
                  ORDER BY hp.tbhorariopersonalfecha DESC, hp.tbhorariopersonalhora DESC";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $clienteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $reservas = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reserva = new HorarioPersonal(
                $row['tbhorariopersonalid'],
                $row['tbhorariopersonalfecha'],
                $row['tbhorariopersonalhora'],
                $row['tbinstructorid'],
                $row['tbclienteid'],
                $row['tbhorariopersonalestado'],
                $row['tbhorariopersonalduracion'],
                $row['tbhorariopersonaltipo']
            );
            if (isset($row['tbinstructornombre'])) {
                $reserva->setInstructorNombre($row['tbinstructornombre']);
            }
            $reservas[] = $reserva;
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $reservas;
    }

    public function getHorariosDisponiblesPorInstructor($instructorId, $fechaInicio, $fechaFin)
    {
        return $this->getHorariosPorRangoFechas($fechaInicio, $fechaFin, $instructorId);
    }

    public function getHorariosPorInstructor($instructorId, $fechaInicio, $fechaFin)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("Error de conexión en getHorariosPorInstructor");
            return [];
        }
        $conn->set_charset('utf8');

        $query = "SELECT
                    hp.tbhorariopersonalid,
                    hp.tbhorariopersonalfecha,
                    hp.tbhorariopersonalhora,
                    hp.tbinstructorid,
                    hp.tbclienteid,
                    hp.tbhorariopersonalestado,
                    hp.tbhorariopersonalduracion,
                    hp.tbhorariopersonaltipo,
                    i.tbinstructornombre,
                    c.tbclientenombre
                  FROM tbhorariopersonal hp
                  LEFT JOIN tbinstructor i ON hp.tbinstructorid = i.tbinstructorid
                  LEFT JOIN tbcliente c ON hp.tbclienteid = c.tbclienteid
                  WHERE hp.tbinstructorid = ?
                  AND hp.tbhorariopersonalfecha BETWEEN ? AND ?
                  ORDER BY hp.tbhorariopersonalfecha, hp.tbhorariopersonalhora";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $instructorId, $fechaInicio, $fechaFin);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $horarios = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $horario = new HorarioPersonal(
                $row['tbhorariopersonalid'],
                $row['tbhorariopersonalfecha'],
                $row['tbhorariopersonalhora'],
                $row['tbinstructorid'],
                $row['tbclienteid'],
                $row['tbhorariopersonalestado'],
                $row['tbhorariopersonalduracion'],
                $row['tbhorariopersonaltipo']
            );

            if (isset($row['tbinstructornombre'])) {
                $horario->setInstructorNombre($row['tbinstructornombre']);
            }
            if (isset($row['tbclientenombre'])) {
                $horario->setClienteNombre($row['tbclientenombre']);
            }
            $horarios[] = $horario;
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $horarios;
    }

    public function getHorarioPersonalPorId($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        if (!$conn) {
            error_log("Error de conexión a la base de datos");
            return null;
        }
        $conn->set_charset('utf8');

        $query = "SELECT
                    hp.tbhorariopersonalid,
                    hp.tbhorariopersonalfecha,
                    hp.tbhorariopersonalhora,
                    hp.tbinstructorid,
                    hp.tbclienteid,
                    hp.tbhorariopersonalestado,
                    hp.tbhorariopersonalduracion,
                    hp.tbhorariopersonaltipo,
                    i.tbinstructornombre,
                    c.tbclientenombre
                 FROM tbhorariopersonal hp
                 LEFT JOIN tbinstructor i ON hp.tbinstructorid = i.tbinstructorid
                 LEFT JOIN tbcliente c ON hp.tbclienteid = c.tbclienteid
                 WHERE hp.tbhorariopersonalid = ?";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $horario = new HorarioPersonal(
                $row['tbhorariopersonalid'],
                $row['tbhorariopersonalfecha'],
                $row['tbhorariopersonalhora'],
                $row['tbinstructorid'],
                $row['tbclienteid'],
                $row['tbhorariopersonalestado'],
                $row['tbhorariopersonalduracion'],
                $row['tbhorariopersonaltipo']
            );

            if (isset($row['tbinstructornombre'])) {
                $horario->setInstructorNombre($row['tbinstructornombre']);
            }
            if (isset($row['tbclientenombre'])) {
                $horario->setClienteNombre($row['tbclientenombre']);
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $horario;
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return null;
    }
}
?>