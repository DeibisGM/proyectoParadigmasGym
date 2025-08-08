<?php
include '../business/clienteBusiness.php';

// Insertar un nuevo cliente
if (isset($_POST['insertar'])) {
    if (isset($_POST['carnet']) && isset($_POST['nombre']) && isset($_POST['fechaNacimiento']) && 
        isset($_POST['telefono']) && isset($_POST['correo']) && isset($_POST['contrasena']) && 
        isset($_POST['direccion']) && isset($_POST['genero']) && isset($_POST['fechaInscripcion'])) {
        
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
        
        // Validaciones
        if (empty($carnet) || empty($nombre) || empty($fechaNacimiento) || empty($telefono) || 
            empty($correo) || empty($contrasena) || empty($direccion) || empty($genero) || 
            empty($fechaInscripcion)) {
            header("location: ../view/clienteView.php?error=datos_faltantes");
            exit();
        }
        
        // Verificar si el carnet ya existe
        $clienteBusiness = new ClienteBusiness();
        if ($clienteBusiness->existeClientePorCarnet($carnet)) {
            header("location: ../view/clienteView.php?error=existe");
            exit();
        }
        
        // Validar correo electrónico
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            header("location: ../view/clienteView.php?error=correo_invalido");
            exit();
        }
        
        // Crear y guardar el cliente
        $cliente = new Cliente(null, $carnet, $nombre, $fechaNacimiento, $telefono, $correo, 
                             $direccion, $genero, $fechaInscripcion, $estado, $contrasena);
        
        $result = $clienteBusiness->insertarTBCliente($cliente);
        
        if ($result == 1) {
            header("location: ../view/clienteView.php?success=insertado");
        } else {
            header("location: ../view/clienteView.php?error=insertar");
        }
    } else {
        header("location: ../view/clienteView.php?error=datos_faltantes");
    }
}
// Actualizar un cliente existente
else if (isset($_POST['actualizar'])) {
    if (isset($_POST['id']) && isset($_POST['carnet']) && isset($_POST['nombre']) && 
        isset($_POST['fechaNacimiento']) && isset($_POST['telefono']) && isset($_POST['correo']) && 
        isset($_POST['contrasena']) && isset($_POST['direccion']) && isset($_POST['genero']) && 
        isset($_POST['fechaInscripcion']) && isset($_POST['estado'])) {
        
        $id = $_POST['id'];
        $carnet = trim($_POST['carnet']);
        $nombre = trim($_POST['nombre']);
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);
        $contrasena = trim($_POST['contrasena']);
        $direccion = trim($_POST['direccion']);
        $genero = $_POST['genero'];
        $fechaInscripcion = $_POST['fechaInscripcion'];
        $estado = $_POST['estado'];
        
        // Validaciones
        if (empty($id) || empty($carnet) || empty($nombre) || empty($fechaNacimiento) || 
            empty($telefono) || empty($correo) || empty($contrasena) || empty($direccion) || 
            empty($genero) || empty($fechaInscripcion)) {
            header("location: ../view/clienteView.php?error=datos_faltantes");
            exit();
        }
        
        // Validar correo electrónico
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            header("location: ../view/clienteView.php?error=correo_invalido");
            exit();
        }
        
        // Actualizar el cliente
        $cliente = new Cliente($id, $carnet, $nombre, $fechaNacimiento, $telefono, $correo, 
                             $direccion, $genero, $fechaInscripcion, $estado, $contrasena);
        
        $clienteBusiness = new ClienteBusiness();
        $result = $clienteBusiness->actualizarTBCliente($cliente);
        
        if ($result == 1) {
            header("location: ../view/clienteView.php?success=actualizado");
        } else {
            header("location: ../view/clienteView.php?error=actualizar");
        }
    } else {
        header("location: ../view/clienteView.php?error=datos_faltantes");
    }
}
// Eliminar un cliente
else if (isset($_POST['eliminar'])) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        
        $clienteBusiness = new ClienteBusiness();
        $result = $clienteBusiness->eliminarTBCliente($id);
        
        if ($result == 1) {
            header("location: ../view/clienteView.php?success=eliminado");
        } else {
            header("location: ../view/clienteView.php?error=eliminar");
        }
    } else {
        header("location: ../view/clienteView.php?error=id_faltante");
    }
} else {
    header("location: ../view/clienteView.php?error=accion_no_valida");
}
?>