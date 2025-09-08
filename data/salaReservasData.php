<?php
include_once 'data.php';

class SalaReservasData extends Data
{
    public function getAllReservasDeSalas()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT rs.tbreservafecha, rs.tbreservahorainicio, rs.tbreservahorafin, rs.tbsalaid, e.tbeventonombre
                  FROM tbreservasala rs
                  JOIN tbevento e ON rs.tbeventoid = e.tbeventoid
                  ORDER BY rs.tbreservafecha, rs.tbreservahorainicio";

        $result = mysqli_query($conn, $query);
        $reservas = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_close($conn);
        return $reservas;
    }
}
?>