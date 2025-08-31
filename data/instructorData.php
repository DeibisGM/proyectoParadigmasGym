<?php
include_once 'data.php';
include_once '../domain/instructor.php';

class InstructorData extends Data
{

    public function insertarTBInstructor($instructor)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryInsert = "INSERT INTO tbinstructor (tbinstructorid, tbinstructornombre, tbinstructortelefono, tbinstructordireccion, tbinstructorcorreo, tbinstructorcuenta, tbinstructorcontraseña, tbinstructoractivo, tbinstructorcertificado, tbinstructorimagenid) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?)";

        $stmt = mysqli_prepare($conn, $queryInsert);
        $id = $instructor->getInstructorId();
        $nombre = $instructor->getInstructorNombre();
        $telefono = $instructor->getInstructorTelefono();
        $direccion = $instructor->getInstructorDireccion();
        $correo = $instructor->getInstructorCorreo();
        $cuenta = $instructor->getInstructorCuenta();
        $contrasena = $instructor->getInstructorContraseña();
        $certificado = $instructor->getInstructorCertificado();
        $imagenid = $instructor->getTbinstructorImagenId();

        mysqli_stmt_bind_param($stmt, 'sssssssss', $id, $nombre, $telefono, $direccion, $correo, $cuenta, $contrasena, $certificado, $imagenid);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function actualizarTBInstructor($instructor)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $queryUpdate = "UPDATE tbinstructor SET tbinstructornombre=?, tbinstructortelefono=?, tbinstructordireccion=?, tbinstructorcorreo=?, tbinstructorcuenta=?, tbinstructorcontraseña=?, tbinstructorcertificado=?, tbinstructorimagenid=? WHERE tbinstructorid=?";

        $stmt = mysqli_prepare($conn, $queryUpdate);
        $nombre = $instructor->getInstructorNombre();
        $telefono = $instructor->getInstructorTelefono();
        $direccion = $instructor->getInstructorDireccion();
        $correo = $instructor->getInstructorCorreo();
        $cuenta = $instructor->getInstructorCuenta();
        $contrasena = $instructor->getInstructorContraseña();
        $certificado = $instructor->getInstructorCertificado();
        $imagenid = $instructor->getTbinstructorImagenId();
        $id = $instructor->getInstructorId();

        mysqli_stmt_bind_param($stmt, 'sssssssss', $nombre, $telefono, $direccion, $correo, $cuenta, $contrasena, $certificado, $imagenid, $id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function eliminarTBInstructor($instructorId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryDelete = "UPDATE tbinstructor SET tbinstructoractivo = 0 WHERE tbinstructorid=?;";
        $stmt = mysqli_prepare($conn, $queryDelete);
        mysqli_stmt_bind_param($stmt, 's', $instructorId);
        $result = mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        return $result;
    }

    public function activarTBInstructor($instructorId)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $queryUpdate = "UPDATE tbinstructor SET tbinstructoractivo = 1 WHERE tbinstructorid=?;";
        $stmt = mysqli_prepare($conn, $queryUpdate);
        mysqli_stmt_bind_param($stmt, 's', $instructorId);
        $result = mysqli_stmt_execute($stmt);
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
                isset($row['tbinstructorcertificado']) ? $row['tbinstructorcertificado'] : '',
                isset($row['tbinstructorimagenid']) ? $row['tbinstructorimagenid'] : ''
            );
        }
        return $instructors;
    }

    public function autenticarInstructor($correo, $contraseña)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');
        $query = "SELECT * FROM tbinstructor WHERE tbinstructorcorreo=? AND tbinstructorcontraseña=? AND tbinstructoractivo = 1 LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $correo, $contraseña);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $instructor = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $instructor = new Instructor(
                $row['tbinstructorid'], $row['tbinstructornombre'], $row['tbinstructortelefono'],
                $row['tbinstructordireccion'], $row['tbinstructorcorreo'], $row['tbinstructorcuenta'],
                $row['tbinstructorcontraseña'], $row['tbinstructoractivo'],
                isset($row['tbinstructorcertificado']) ? $row['tbinstructorcertificado'] : '',
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
        $query = "SELECT * FROM tbinstructor WHERE tbinstructorid=? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $instructor = null;
        if ($row = mysqli_fetch_assoc($result)) {
            $instructor = new Instructor(
                $row['tbinstructorid'], $row['tbinstructornombre'], $row['tbinstructortelefono'],
                $row['tbinstructordireccion'], $row['tbinstructorcorreo'], $row['tbinstructorcuenta'],
                $row['tbinstructorcontraseña'], $row['tbinstructoractivo'],
                isset($row['tbinstructorcertificado']) ? $row['tbinstructorcertificado'] : '',
                isset($row['tbinstructorimagenid']) ? $row['tbinstructorimagenid'] : ''
            );
        }
        mysqli_close($conn);
        return $instructor;
    }

    // MÉTODO AÑADIDO PARA SOLUCIONAR EL ERROR
    public function existeInstructorPorCorreo($correo)
    {
        $conn = mysqli_connect($this->server, $this->user, $this->password, $this->db, $this->port);
        $conn->set_charset('utf8');

        $query = "SELECT tbinstructorid FROM tbinstructor WHERE tbinstructorcorreo = ? LIMIT 1;";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $correo);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $existe = mysqli_num_rows($result) > 0;

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $existe;
    }
}

?>