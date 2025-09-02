<?php
include_once '../data/instructorData.php';
include_once '../utility/ImageManager.php';
include_once '../data/certificadoData.php'; // New include

class InstructorBusiness
{

    private $instructorData;
    private $imageManager;
    private $certificadoData; // New property

    public function __construct()
    {
        $this->instructorData = new InstructorData();
        $this->imageManager = new ImageManager();
        $this->certificadoData = new CertificadoData(); // Initialize new property
    }

    public function insertarTBInstructor($instructor)
    {
        $validationResult = $this->validateInstructorData($instructor, false); // false for create
        if ($validationResult !== true) {
            return $validationResult; // Return error message
        }
        return $this->instructorData->insertarTBInstructor($instructor);
    }

    public function actualizarTBInstructor($instructor)
    {
        $validationResult = $this->validateInstructorData($instructor, true); // true for update
        if ($validationResult !== true) {
            return $validationResult; // Return error message
        }
        return $this->instructorData->actualizarTBInstructor($instructor);
    }

    public function eliminarTBInstructor($idInstructor)
    {
        $instructor = $this->getInstructorPorId($idInstructor);
        // Si el instructor existe y tiene una imagen, se elimina
        if ($instructor && $instructor->getTbinstructorImagenId() != '' && $instructor->getTbinstructorImagenId() != '0') {
            $this->imageManager->deleteImage($instructor->getTbinstructorImagenId());
            // Se actualiza el instructor para que no tenga referencia a la imagen eliminada
            $instructor->setTbinstructorImagenId('');
            $this->actualizarTBInstructor($instructor);
        }
        return $this->instructorData->eliminarTBInstructor($idInstructor);
    }

    public function activarTBInstructor($idInstructor)
    {
        return $this->instructorData->activarTBInstructor($idInstructor);
    }

    public function getAllTBInstructor($esAdmin = false)
    {
        $instructors = $this->instructorData->getAllTBInstructor($esAdmin);
        foreach ($instructors as $instructor) {
            $instructorId = $instructor->getInstructorId();
            $certificados = $this->certificadoData->getCertificadosPorInstructor($instructorId);
            $instructor->setInstructorCertificado($certificados);
        }
        return $instructors;
    }

    public function autenticarInstructor($correo, $contrasena)
    {
        $instructor = $this->instructorData->autenticarInstructor($correo, $contrasena);
        if ($instructor) {
            $instructorId = $instructor->getInstructorId();
            $certificados = $this->certificadoData->getCertificadosPorInstructor($instructorId);
            $instructor->setInstructorCertificado($certificados);
        }
        return $instructor;
    }

    public function getInstructorPorId($id)
    {
        $instructor = $this->instructorData->getInstructorPorId($id);
        if ($instructor) {
            $instructorId = $instructor->getInstructorId();
            $certificados = $this->certificadoData->getCertificadosPorInstructor($instructorId);
            $instructor->setInstructorCertificado($certificados);
        }
        return $instructor;
    }

    public function existeInstructorPorCorreo($correo)
    {
        // Esta es la línea que causaba el error. Ahora funcionará.
        return $this->instructorData->existeInstructorPorCorreo($correo);
    }

    private function validateInstructorData($instructor, $isUpdate = false)
    {
        // Validate ID (only for create, assuming it's not changeable on update)
        if (!$isUpdate && empty($instructor->getInstructorId())) {
            return "La cédula del instructor es obligatoria.";
        }

        // Validate Nombre
        if (empty($instructor->getInstructorNombre())) {
            return "El nombre del instructor es obligatorio.";
        }

        // Validate Correo
        if (empty($instructor->getInstructorCorreo())) {
            return "El correo del instructor es obligatorio.";
        }
        if (!filter_var($instructor->getInstructorCorreo(), FILTER_VALIDATE_EMAIL)) {
            return "El formato del correo electrónico es inválido.";
        }

        // Validate Contraseña length (max 8 characters)
        $contrasena = $instructor->getInstructorContraseña();
        if (empty($contrasena)) {
            return "La contraseña es obligatoria.";
        }
        if (strlen($contrasena) > 8) {
            return "La contraseña no debe tener más de 8 caracteres.";
        }

        // Add more validations as needed (e.g., telefono format, cuenta format, uniqueness)

        return true; // All validations passed
    }

    public function actualizarCertificadosInstructor($idInstructor, $certificadosStr)
    {
        // Este método no estaba definido, lo agrego por si se necesita en el futuro
        $instructor = $this->getInstructorPorId($idInstructor);
        if ($instructor) {
            $instructor->setInstructorCertificado($certificadosStr);
            return $this->actualizarTBInstructor($instructor);
        }
        return false;
    }
}

?>