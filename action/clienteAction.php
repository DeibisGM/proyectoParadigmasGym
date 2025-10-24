<?php
session_start();
include '../business/clienteBusiness.php';
include '../business/instructorBusiness.php';
include_once '../utility/ImageManager.php';
include_once '../utility/Validation.php';

Validation::start();

$instructorBusiness = new InstructorBusiness();
$clienteBusiness = new ClienteBusiness();
$imageManager = new ImageManager();
$redirect_path = '../view/clienteView.php';

if (isset($_POST['delete_image'])) {
    if (isset($_POST['id'])) {
        $clienteId = $_POST['id'];
        $cliente = $clienteBusiness->getClientePorId($clienteId);
        if ($cliente) {
            $imageManager->deleteImage($cliente->getTbclienteImagenId());
            $cliente->setTbclienteImagenId('');
            $clienteBusiness->actualizarTBCliente($cliente);
            header("location: " . $redirect_path . "?success=image_deleted");
        } else {
            header("location: " . $redirect_path . "?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=error");
    }
} else if (isset($_POST['insertar'])) {
    if (isset($_POST['carnet']) && isset($_POST['nombre']) && isset($_POST['fechaNacimiento']) &&
        isset($_POST['telefono']) && isset($_POST['correo']) && isset($_POST['contrasena']) &&
        isset($_POST['direccion']) && isset($_POST['genero']) && isset($_POST['fechaInscripcion'])) {

        Validation::setOldInput($_POST);

        $carnet = trim($_POST['carnet']);
        $nombre = trim($_POST['nombre']);
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);
        $contrasena = trim($_POST['contrasena']);
        $direccion = trim($_POST['direccion']);
        $genero = $_POST['genero'];
        $fechaInscripcion = $_POST['fechaInscripcion'];
        $estado = 1; // Por defecto activo

        if (empty($carnet)){
            Validation::setError('carnet', 'La Identificacion es obligatoria.');
        }elseif ($clienteBusiness->existeClientePorCarnet($carnet)){
            Validation::setError('carnet', 'Identificacion ya registrada.');
        }

        if (empty($nombre)){
            Validation::setError('nombre', 'El nombre es obligatorio.');
        }elseif (preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre', 'El nombre no puede contener números.');
        }

        if (empty($fechaNacimiento)){
            Validation::setError('fechaNacimiento', 'La fecha de nacimiento es obligatoria.');
        }elseif ($fechaNacimiento > date('Y-m-d')) {
            Validation::setError('fechaNacimiento', 'La fecha no puede ser mayor al dia actual.');
        }

        if (empty($telefono)) {
            Validation::setError('telefono', 'El teléfono es obligatorio.');
        } elseif (!preg_match('/^[428657][0-9]+$/', $telefono)) {
            Validation::setError('telefono', 'El teléfono debe iniciar con 4, 2, 8, 6, 5 o 7.');
        }elseif (!preg_match('/^\d{8}$/', $telefono)) {
            Validation::setError('telefono', 'El numero de telefono tiene que tener 8 digitos.');
        }

        if (empty($correo)){
            Validation::setError('correo', 'El correo es obligatorio.');
        }elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            Validation::setError('correo', 'El formato del correo no es valido.');
        }elseif ($clienteBusiness->existeclientePorCorreo($correo) || $instructorBusiness->existeInstructorPorCorreo($correo)) {
            Validation::setError('correo', 'El correo ya se encuentra registrado a un usuario.');
        }

        if (empty($contrasena)){
            Validation::setError('contrasena', 'La contraseña es obligatoria.');
        }

        if (empty($direccion)){
            Validation::setError('direccion', 'La direccion en obligatoria.');
        }

        if (empty($genero)){
            Validation::setError('genero', 'El genero es obligatorio.');
        }

        if (empty($fechaInscripcion)){
            Validation::setError('fechaInscripcion', 'La fecha de inscripcion es obligatoria es obligatorio.');
        }elseif ($fechaInscripcion > date('Y-m-d')) {
            Validation::setError('fechaInscripcion', 'La fecha no puede ser mayor al dia actual.');
        }

        if (Validation::hasErrors()) {
            header("Location: " . $redirect_path);
            exit();
        }

        $cliente = new Cliente(0, $carnet, $nombre, $fechaNacimiento, $telefono, $correo,
            $direccion, $genero, $fechaInscripcion, $estado, $contrasena, '');

        $nuevoId = $clienteBusiness->insertarTBCliente($cliente);

        if ($nuevoId > 0) {
            if (isset($_FILES['tbclienteimagenid']) && !empty($_FILES['tbclienteimagenid']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['tbclienteimagenid'], $nuevoId, 'cli');
                if (!empty($newImageIds)) {
                    $clienteCreado = $clienteBusiness->getClientePorId($nuevoId);
                    $clienteCreado->setTbclienteImagenId($newImageIds[0]);
                    $clienteBusiness->actualizarTBCliente($clienteCreado);
                }
            }

            header("location: " . $redirect_path . "?success=inserted");
            Validation::clear();
        } else {
            header("location: " . $redirect_path . "?error=insertar");
        }
    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
} else if(isset($_POST['actualizar'])){
    if (isset($_POST['id'], $_POST['nombre'], $_POST['fechaNacimiento'], $_POST['telefono'], $_POST['correo'], $_POST['contrasena'], $_POST['direccion'], $_POST['genero'], $_POST['fechaInscripcion'], $_POST['estado'])) {

        $id = $_POST['id'];
        $carnet = $_POST['carnet'];
        $nombre = $_POST['nombre'];
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];
        $contrasena = $_POST['contrasena'];
        $direccion = $_POST['direccion'];
        $genero = $_POST['genero'];
        $fechaInscripcion = $_POST['fechaInscripcion'];
        $estado = $_POST['estado'];

        Validation::setOldInput('id_'.$id, $id);
        Validation::setOldInput('nombre_'.$id, $nombre);
        Validation::setOldInput('fechaNacimiento_'.$id, $fechaNacimiento);
        Validation::setOldInput('telefono_'.$id, $telefono);
        Validation::setOldInput('correo_'.$id, $correo);
        Validation::setOldInput('contrasena_'.$id, $contrasena);
        Validation::setOldInput('direccion_'.$id, $direccion);
        Validation::setOldInput('genero_'.$id, $genero);
        Validation::setOldInput('fechaInscripcion_'.$id, $fechaInscripcion);
        Validation::setOldInput('estado_'.$id, $estado);

        // Validaciones
        if(empty($nombre)){
            Validation::setError('nombre_'.$id, 'El nombre es obligatorio.');
        } elseif(preg_match('/[0-9]/', $nombre)) {
            Validation::setError('nombre_'.$id, 'No se permiten números.');
        }

        if(empty($telefono)){
            Validation::setError('telefono_'.$id, 'El teléfono es obligatorio.');
        } elseif(!preg_match('/^\d{8}$/', $telefono)){
            Validation::setError('telefono_'.$id, 'El teléfono debe tener 8 dígitos.');
        }

        if(empty($correo)){
            Validation::setError('correo_'.$id, 'El correo es obligatorio.');
        } elseif(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
            Validation::setError('correo_'.$id, 'Correo no válido.');
        }

        if(empty($contrasena)){
            Validation::setError('contrasena_'.$id, 'La contraseña es obligatoria.');
        }

        if(empty($direccion)){
            Validation::setError('direccion_'.$id, 'La dirección es obligatoria.');
        }

        if(empty($genero)){
            Validation::setError('genero_'.$id, 'Debe seleccionar un género.');
        }

        if(empty($fechaNacimiento)){
            Validation::setError('fechaNacimiento_'.$id, 'Debe seleccionar fecha de nacimiento.');
        } elseif ($fechaNacimiento > date('Y-m-d')) {
            Validation::setError('fechaNacimiento_'.$id, 'La fecha no puede ser mayor al día actual.');
        }

        if(empty($fechaInscripcion)){
            Validation::setError('fechaInscripcion_'.$id, 'Debe seleccionar fecha de inscripción.');
        } elseif ($fechaInscripcion > date('Y-m-d')) {
            Validation::setError('fechaInscripcion_'.$id, 'La fecha no puede ser mayor al día actual.');
        }

        if(Validation::hasErrors()){
            header("Location: ../view/clienteView.php?error=actualizar");
            exit;
        }

        // Si pasa validación, actualizar cliente
        $clienteActual = $clienteBusiness->getClientePorId($id);
        if($clienteActual) {
            $clienteActual->setNombre($nombre);
            $clienteActual->setFechaNacimiento($fechaNacimiento);
            $clienteActual->setTelefono($telefono);
            $clienteActual->setCorreo($correo);
            $clienteActual->setContrasena($contrasena);
            $clienteActual->setDireccion($direccion);
            $clienteActual->setGenero($genero);
            $clienteActual->setInscripcion($fechaInscripcion);
            $clienteActual->setActivo($estado);

            if (isset($_FILES['tbclienteimagenid']) && !empty($_FILES['tbclienteimagenid']['name'][0])) {
                $newImageIds = $imageManager->addImages($_FILES['tbclienteimagenid'], $id, 'cli');
                $currentIdString = $clienteActual->getTbclienteImagenId();
                $newIdString = ImageManager::addIdsToString($newImageIds, $currentIdString);
                $clienteActual->setTbclienteImagenId($newIdString);
            }

            $resultado = $clienteBusiness->actualizarTBCliente($clienteActual);

            if($resultado){
                Validation::clear();
                header("Location: ../view/clienteView.php?success=updated");
            } else {
                header("Location: ../view/clienteView.php?error=dbError");
            }
        } else {
            header("Location: ../view/clienteView.php?error=notFound");
        }
    } else {
        header("location: " . $redirect_path . "?error=datos_faltantes");
    }
} else if (isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $result = $clienteBusiness->eliminarTBCliente($id);

        if ($result == 1) {
            header("location: " . $redirect_path . "?success=eliminado");
        } else {
            header("location: " . $redirect_path . "?error=eliminar");
        }
    } else {
        header("location: " . $redirect_path . "?error=id_faltante");
    }
} else {
    header("location: " . $redirect_path . "?error=accion_no_valida");
}
?>