<?php
include_once 'data.php';
include_once '../domain/evento.php';

class EventoData extends Data
{
    public function insertarEvento($evento)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // CAMBIO: Columna 'tbeventofecha' en lugar de 'tbeventodiasemana'
        $query = "INSERT INTO tbevento (tbeventonombre, tbeventodescripcion, tbeventofecha, tbeventohorainicio, tbeventohorafin, tbeventoaforo, tbinstructorid, tbeventoestado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        // CAMBIO: El tipo de dato para la fecha es 's' (string), no 'i' (integer)
        mysqli_stmt_bind_param($stmt, "sssssiii",
            $evento->getNombre(), $evento->getDescripcion(), $evento->getFecha(), $evento->getHoraInicio(),
            $evento->getHoraFin(), $evento->getAforo(), $evento->getInstructorId(), $evento->getEstado()
        );

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarEvento($evento)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // CAMBIO: Columna 'tbeventofecha'
        $query = "UPDATE tbevento SET tbeventonombre=?, tbeventodescripcion=?, tbeventofecha=?, tbeventohorainicio=?, tbeventohorafin=?, tbeventoaforo=?, tbinstructorid=?, tbeventoestado=? WHERE tbeventoid=?";
        $stmt = mysqli_prepare($conn, $query);
        // CAMBIO: Tipo de dato 's' para la fecha
        mysqli_stmt_bind_param($stmt, "sssssiiii",
            $evento->getNombre(), $evento->getDescripcion(), $evento->getFecha(), $evento->getHoraInicio(),
            $evento->getHoraFin(), $evento->getAforo(), $evento->getInstructorId(), $evento->getEstado(), $evento->getId()
        );

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarEvento($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "DELETE FROM tbevento WHERE tbeventoid = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function getAllEventos()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // CAMBIO: Ordenar por fecha, no por día de la semana
        $query = "SELECT e.*, i.tbinstructornombre FROM tbevento e LEFT JOIN tbinstructor i ON e.tbinstructorid = i.tbinstructorid ORDER BY e.tbeventofecha, e.tbeventohorainicio";
        $result = mysqli_query($conn, $query);

        $eventos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // CAMBIO: Usar 'tbeventofecha' en el constructor
            $evento = new Evento(
                $row['tbeventoid'], $row['tbeventonombre'], $row['tbeventodescripcion'], $row['tbeventofecha'],
                $row['tbeventohorainicio'], $row['tbeventohorafin'], $row['tbeventoaforo'],
                $row['tbinstructorid'], $row['tbeventoestado']
            );
            $evento->setInstructorNombre($row['tbinstructornombre'] ?? 'No asignado');
            $eventos[] = $evento;
        }
        mysqli_close($conn);
        return $eventos;
    }
}

?>