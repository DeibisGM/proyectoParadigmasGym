<?php
include_once 'data.php';
include_once '../domain/horarioPersonal.php';

class HorarioPersonalData extends Data
{
    public function insertarHorarioPersonal($horarioPersonal)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "INSERT INTO tbhorariopersonal (tbhorariopersonalfecha, tbhorariopersonalhora, tbinstructorid, tbclienteid, tbhorariopersonalestado, tbhorariopersonalduracion, tbhorariopersonaltipo)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $query);

        // Valores por defecto manuales
        $clienteId = $horarioPersonal->getClienteId();
        $estado = $horarioPersonal->getEstado() ?: 'disponible';
        $duracion = $horarioPersonal->getDuracion() ?: 60;
        $tipo = $horarioPersonal->getTipo() ?: 'personal';

        mysqli_stmt_bind_param($stmt, "ssiisis",
            $horarioPersonal->getFecha(),
            $horarioPersonal->getHora(),
            $horarioPersonal->getInstructorId(),
            $clienteId,
            $estado,
            $duracion,
            $tipo
        );

        $result = mysqli_stmt_execute($stmt);
        $lastId = mysqli_insert_id($conn);
        mysqli_close($conn);
        return $result ? $lastId : false;
    }

    public function getHorariosPorRangoFechas($fechaInicio, $fechaFin, $instructorId = null)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT hp.*, i.tbinstructornombre, c.tbclientenombre
                  FROM tbhorariopersonal hp
                  LEFT JOIN tbinstructor i ON hp.tbinstructorid = i.tbinstructorid
                  LEFT JOIN tbcliente c ON hp.tbclienteid = c.tbclienteid
                  WHERE hp.tbhorariopersonalfecha BETWEEN ? AND ?";

        if ($instructorId) {
            $query .= " AND hp.tbinstructorid = ?";
        }

        $query .= " ORDER BY hp.tbhorariopersonalfecha, hp.tbhorariopersonalhora";

        $stmt = mysqli_prepare($conn, $query);

        if ($instructorId) {
            mysqli_stmt_bind_param($stmt, "ssi", $fechaInicio, $fechaFin, $instructorId);
        } else {
            mysqli_stmt_bind_param($stmt, "ss", $fechaInicio, $fechaFin);
        }

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
            // Agregar nombres si existen
            if (isset($row['tbinstructornombre'])) {
                $horario->setInstructorNombre($row['tbinstructornombre']);
            }
            if (isset($row['tbclientenombre'])) {
                $horario->setClienteNombre($row['tbclientenombre']);
            }
            $horarios[] = $horario;
        }
        mysqli_close($conn);
        return $horarios;
    }

    public function reservarHorarioPersonal($horarioId, $clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "UPDATE tbhorariopersonal SET tbclienteid = ?, tbhorariopersonalestado = 'reservado'
                  WHERE tbhorariopersonalid = ? AND (tbhorariopersonalestado = 'disponible' OR tbhorariopersonalestado IS NULL)";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $clienteId, $horarioId);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function cancelarReservaPersonal($horarioId, $clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "UPDATE tbhorariopersonal SET tbclienteid = NULL, tbhorariopersonalestado = 'disponible'
                  WHERE tbhorariopersonalid = ? AND tbclienteid = ?";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $horarioId, $clienteId);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getReservasPorCliente($clienteId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT hp.*, i.tbinstructornombre
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
        mysqli_close($conn);
        return $reservas;
    }

    public function getHorariosDisponiblesPorInstructor($instructorId, $fechaInicio, $fechaFin)
    {
        return $this->getHorariosPorRangoFechas($fechaInicio, $fechaFin, $instructorId);
    }
}
?>