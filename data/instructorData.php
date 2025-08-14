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
                $instructor->getInstructorCuenta() . "','" .
                $instructor->getInstructorContraseña() . "');";

        $result = mysqli_query($conn, $queryInsert);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTBInstructor($instructor) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbinstructor SET tbinstructornombre='" . $instructor->getInstructorNombre() .
                "', tbinstructortelefono='" . $instructor->getInstructorTelefono() .
                "', tbinstructordireccion='" . $instructor->getInstructorDireccion() .
                "', tbinstructorcorreo='" . $instructor->getInstructorCorreo() .
                "', tbinstructorcuenta='" . $instructor->getInstructorCuenta() .
                "', tbinstructorcontraseña='" . $instructor->getInstructorContraseña() .

                "' WHERE tbinstructorid=" . $instructor->getInstructorId() . ";";

        $result = mysqli_query($conn, $queryUpdate);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBInstructor($instructorId) {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryDelete = "DELETE from tbinstructor
    where tbinstructorid=" . $instructorId . ";";
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
               $row['tbinstructorid'],
               $row['tbinstructornombre'],
               $row['tbinstructortelefono'],
               $row['tbinstructordireccion'],
               $row['tbinstructorcorreo'],
               $row['tbinstructorcuenta'],
               $row['tbinstructorcontraseña']
           );
       }
       return $instructors;
   }
}
?>