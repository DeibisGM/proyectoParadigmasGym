<?php

include_once 'data.php';
include '../domain/instructor.php';

class InstructorData extends Data {

    public function insertarTBInstructor($instructor) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryGetLastId = "SELECT MAX(tbinstructorid) AS tbinstructorid FROM tbinstructor";
        $resultId = mysqli_query($conn, $queryGetLastId);
        $nextId = 1;

        if ($row = mysqli_fetch_row($resultId)) {
            if ($row[0] !== null) {
                $nextId = (int)$row[0] + 1;
            }
        }

        $queryInsert = "INSERT INTO tbinstructor VALUES (" . $nextId . ",'" .
                $instructor->getInstructorNombre() . "','" .
                $instructor->getInstructorTelefono() . "','" .
                $instructor->getInstructorDireccion() . "','" .
                $instructor->getInstructorCorreo() . "','" .
                $instructor->getInstructorCuenta() . "');";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTBInstructor($instructor) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbinstructor SET tbinstructorNombre='" . $instructor->getInstructorNombre() .
                "', tbinstructorTelefono='" . $instructor->getInstructorTelefono() .
                "', tbinstructorDireccion='" . $instructor->getInstructorDireccion() .
                "', tbinstructorCorreo='" . $instructor->getInstructorCorreo() .
                "', tbinstructorCuenta='" . $instructor->getInstructorCuenta() .
                "' WHERE tbinstructorId=" . $instructor->getInstructorId() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBInstructor($instructorId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryDelete = "DELETE from tbinstructor
    where tbinstructorId=" . $instructorId . ";";
        $result = mysqli_query($conn, $queryDelete);
        mysqli_close($conn);
        return $result;
    }

    public function getAllTBInstructor() {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $querySelect = "SELECT * FROM tbinstructor;";
        $result = mysqli_query($conn, $querySelect);
        mysqli_close($conn);

        $instructors = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $instructors[] = new Instructor(
                $row['tbinstructorId'],
                $row['tbinstructorNombre'],
                $row['tbinstructorTelefono'],
                $row['tbinstructorDireccion'],
                $row['tbinstructorCorreo'],
                $row['tbinstructorCuenta']
            );
        }
        return $instructors;
    }
    
    public function autenticarInstructor($correo, $cuenta) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        
        // Escape strings to prevent SQL injection
        $correo = mysqli_real_escape_string($conn, $correo);
        $cuenta = mysqli_real_escape_string($conn, $cuenta);
        
        $query = "SELECT * FROM tbinstructor WHERE tbinstructorCorreo='" . $correo . "' AND tbinstructorContraseña='" . $cuenta . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        
        $instructor = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $instructor = new Instructor(
                $row['tbinstructorId'],
                $row['tbinstructorNombre'],
                $row['tbinstructorTelefono'],
                $row['tbinstructorDireccion'],
                $row['tbinstructorCorreo'],
                $row['tbinstructorContraseña']
            );
        }
        
        mysqli_close($conn);
        return $instructor;
    }
    
    public function getInstructorPorId($id) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        
        $id = mysqli_real_escape_string($conn, $id);
        $query = "SELECT * FROM tbinstructor WHERE tbinstructorId='" . $id . "' LIMIT 1;";
        $result = mysqli_query($conn, $query);
        
        $instructor = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $instructor = new Instructor(
                $row['tbinstructorId'],
                $row['tbinstructorNombre'],
                $row['tbinstructorTelefono'],
                $row['tbinstructorDireccion'],
                $row['tbinstructorCorreo'],
                $row['tbinstructorContraseña']
            );
        }
        
        mysqli_close($conn);
        return $instructor;
    }
}
?>