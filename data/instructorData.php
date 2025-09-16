
<?php
include_once 'data.php';
include '../domain/instructor.php';

class InstructorData extends Data
{

// En instructorData.php - método insertarTBInstructor
public function insertarTBInstructor($instructor)
{
    $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
    $conn->set_charset('utf8');

    $id = $instructor->getInstructorId();
    $imagenId = $instructor->getTbinstructorImagenId();

    // Si no hay imagen, establecer como NULL o cadena vacía
    $imagenValue = ($imagenId === '' || $imagenId === null) ? "NULL" : "'" . mysqli_real_escape_string($conn, $imagenId) . "'";

    $queryInsert = "INSERT INTO tbinstructor (tbinstructorid, tbinstructornombre, tbinstructortelefono, tbinstructordireccion, tbinstructorcorreo, tbinstructorcuenta, tbinstructorcontraseña, tbinstructoractivo, tbinstructorcertificado, tbinstructorimagenid) VALUES ('" .
        $id . "','" .
        mysqli_real_escape_string($conn, $instructor->getInstructorNombre()) . "','" .
        mysqli_real_escape_string($conn, $instructor->getInstructorTelefono()) . "','" .
        mysqli_real_escape_string($conn, $instructor->getInstructorDireccion()) . "','" .
        mysqli_real_escape_string($conn, $instructor->getInstructorCorreo()) . "','" .
        mysqli_real_escape_string($conn, $instructor->getInstructorCuenta()) . "','" .
        mysqli_real_escape_string($conn, $instructor->getInstructorContraseña()) . "', 1, '', " .
        $imagenValue . ");";

    $result = mysqli_query($conn, $queryInsert);
    mysqli_close($conn);

    return $result;
}

public function actualizarTBInstructor($instructor)
{
    $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
    $conn->set_charset('utf8');

    $queryUpdate = "UPDATE tbinstructor SET
        tbinstructornombre='" . mysqli_real_escape_string($conn, $instructor->getInstructorNombre()) .
        "', tbinstructortelefono='" . mysqli_real_escape_string($conn, $instructor->getInstructorTelefono()) .
        "', tbinstructordireccion='" . mysqli_real_escape_string($conn, $instructor->getInstructorDireccion()) .
        "', tbinstructorcorreo='" . mysqli_real_escape_string($conn, $instructor->getInstructorCorreo()) .
        "', tbinstructorcuenta='" . mysqli_real_escape_string($conn, $instructor->getInstructorCuenta()) .
        "', tbinstructorcontraseña='" . mysqli_real_escape_string($conn, $instructor->getInstructorContraseña()) .
        "', tbinstructorcertificado='" . mysqli_real_escape_string($conn, $instructor->getInstructorCertificado()) .
        "', tbinstructorimagenid='" . mysqli_real_escape_string($conn, $instructor->getTbinstructorImagenId()) .
        "' WHERE tbinstructorid='" . mysqli_real_escape_string($conn, $instructor->getInstructorId()) . "';";

    $result = mysqli_query($conn, $queryUpdate);
    mysqli_close($conn);
    return $result;
}

    public function eliminarTBInstructor($instructorId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryDelete = "UPDATE tbinstructor SET tbinstructoractivo = 0 WHERE tbinstructorid=" . $instructorId . ";";
        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    public function activarTBInstructor($instructorId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryUpdate = "UPDATE tbinstructor SET tbinstructoractivo = 1 WHERE tbinstructorid=" . $instructorId . ";";
        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    // En el método getAllTBInstructor
    public function getAllTBInstructor($esAdmin = false)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $querySelect = "SELECT * FROM tbinstructor";
        if (!$esAdmin) {
            $querySelect .= " WHERE tbinstructoractivo = 1";
        }
        $querySelect .= " ORDER BY LPAD(tbinstructorid, 3, '0') ASC";

        $result = mysqli_query($conn, $querySelect);
        mysqli_close($conn);

        $instructors = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $instructors[] = new Instructor(
                $row['tbinstructorid'],
                $row['tbinstructornombre'],
                $row['tbinstructortelefono'],
                $row['tbinstructordireccion'],
                $row['tbinstructorcorreo'],
                $row['tbinstructorcuenta'],
                $row['tbinstructorcontraseña'],
                $row['tbinstructoractivo'],
                $row['tbinstructorcertificado'],
                isset($row['tbinstructorimagenid']) ? $row['tbinstructorimagenid'] : ''
            );
        }
        return $instructors;
    }

    public function autenticarInstructor($correo, $contraseña)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $correo = mysqli_real_escape_string($conn, $correo);
        $contraseña = mysqli_real_escape_string($conn, $contraseña);

        $query = "SELECT * FROM tbinstructor WHERE tbinstructorcorreo='" . $correo . "' AND tbinstructorcontraseña='" . $contraseña . "' AND tbinstructoractivo = 1 LIMIT 1;";
        $result = mysqli_query($conn, $query);

        $instructor = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $instructor = new Instructor(
                $row['tbinstructorid'],
                $row['tbinstructornombre'],
                $row['tbinstructortelefono'],
                $row['tbinstructordireccion'],
                $row['tbinstructorcorreo'],
                $row['tbinstructorcuenta'],
                $row['tbinstructorcontraseña'],
                $row['tbinstructoractivo'],
                $row['tbinstructorcertificado'],
                isset($row['tbinstructorimagenid']) ? $row['tbinstructorimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $instructor;
    }

    public function getInstructorPorId($id)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $id = mysqli_real_escape_string($conn, $id);
        $query = "SELECT * FROM tbinstructor WHERE tbinstructorid='" . $id . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);

        $instructor = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $instructor = new Instructor(
                $row['tbinstructorid'],
                $row['tbinstructornombre'],
                $row['tbinstructortelefono'],
                $row['tbinstructordireccion'],
                $row['tbinstructorcorreo'],
                $row['tbinstructorcuenta'],
                $row['tbinstructorcontraseña'],
                $row['tbinstructoractivo'],
                $row['tbinstructorcertificado'],
                isset($row['tbinstructorimagenid']) ? $row['tbinstructorimagenid'] : ''
            );
        }

        mysqli_close($conn);
        return $instructor;
    }

    public function existeInstructorPorCorreo($correo)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $correo = mysqli_real_escape_string($conn, $correo);
        $query = "SELECT tbinstructorid FROM tbinstructor WHERE tbinstructorcorreo='" . $correo . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_close($conn);
        return $existe;
    }

    public function getNextInstructorId()
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT MAX(tbinstructorid) as max_id FROM tbinstructor";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);

        $nextId = ($row['max_id'] ? intval($row['max_id']) + 1 : 1);
        return $nextId;
    }
}
?>