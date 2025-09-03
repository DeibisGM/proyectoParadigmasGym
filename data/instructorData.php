<?php
include_once 'data.php';
include '../domain/instructor.php';

class InstructorData extends Data
{

    public function insertarTBInstructor($instructor)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        // Generar ID de 3 dígitos si no existe
        $id = $instructor->getInstructorId();
        if (empty($id)) {
            $id = $this->getNextInstructorId();
        }
        // Asegurar formato de 3 dígitos
        $id = str_pad($id, 3, '0', STR_PAD_LEFT);
        $instructor->setInstructorId($id);

        $queryInsert = "INSERT INTO tbinstructor (tbinstructorid, tbinstructornombre, tbinstructortelefono, tbinstructordireccion, tbinstructorcorreo, tbinstructorcuenta, tbinstructorcontraseña, tbinstructoractivo, tbinstructorcertificado) VALUES (" .
            $instructor->getInstructorId() . ",'" .
            $instructor->getInstructorNombre() . "','" .
            $instructor->getInstructorTelefono() . "','" .
            $instructor->getInstructorDireccion() . "','" .
            $instructor->getInstructorCorreo() . "','" .
            $instructor->getInstructorCuenta() . "','" .
            $instructor->getInstructorContraseña() . "', 1, '');";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTBInstructor($instructor)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbinstructor SET tbinstructornombre='" . $instructor->getInstructorNombre() .
            "', tbinstructortelefono='" . $instructor->getInstructorTelefono() .
            "', tbinstructordireccion='" . $instructor->getInstructorDireccion() .
            "', tbinstructorcorreo='" . $instructor->getInstructorCorreo() .
            "', tbinstructorcuenta='" . $instructor->getInstructorCuenta() .
            "', tbinstructorcontraseña='" . $instructor->getInstructorContraseña() .
            "', tbinstructorcertificado='" . $instructor->getInstructorCertificado() .
            "' WHERE tbinstructorid=" . $instructor->getInstructorId() . ";";

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

    public function getAllTBInstructor($esAdmin = false)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $querySelect = "SELECT * FROM tbinstructor";
        if (!$esAdmin) {
            $querySelect .= " WHERE tbinstructoractivo = 1";
        }
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
                $row['tbinstructorcertificado']
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
                $row['tbinstructorcertificado']
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
                $row['tbinstructorcertificado']
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