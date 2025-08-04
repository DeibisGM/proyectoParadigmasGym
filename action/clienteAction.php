<?php

include '../business/clienteBusiness.php';

if (isset($_POST['insertar'])) {

    if (
        isset($_POST['carnet']) && isset($_POST['nombre']) && isset($_POST['fechaNacimiento']) &&
        isset($_POST['telefono']) && isset($_POST['correo']) && isset($_POST['direccion']) &&
        isset($_POST['genero']) && isset($_POST['fechaInscripcion'])
    ) {
        $carnet = $_POST['carnet'];
        $nombre = $_POST['nombre'];
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];
        $direccion = $_POST['direccion'];
        $genero = $_POST['genero'];
        $fechaInscripcion = $_POST['fechaInscripcion'];

        // El estado se asigna automáticamente al crear
        $cliente = new Cliente(null, $carnet, $nombre, $fechaNacimiento, $telefono, $correo, $direccion, $genero, $fechaInscripcion, 1);
        $clienteBusiness = new ClienteBusiness();

         if ($clienteBusiness->existeClientePorCarnet($carnet)) {
            header("Location: ../view/clienteView.php?error=existe");
            exit();
         }

        $resultado = $clienteBusiness->insertarTBCliente($cliente);

        if ($resultado == 1) {
            header("Location: ../view/clienteView.php?success=insertado");
        } else {
            header("Location: ../view/clienteView.php?error=insertar");
        }
        exit();
    } else {
        header("Location: ../view/clienteView.php?error=datos_faltantes");
        exit();
    }

} else if (isset($_POST['actualizar'])) {

    if (
        isset($_POST['id']) && isset($_POST['carnet']) && isset($_POST['nombre']) && isset($_POST['fechaNacimiento']) &&
        isset($_POST['telefono']) && isset($_POST['correo']) && isset($_POST['direccion']) &&
        isset($_POST['genero']) && isset($_POST['fechaInscripcion']) && isset($_POST['estado'])
    ) {
        $id = $_POST['id'];
        $carnet = $_POST['carnet'];
        $nombre = $_POST['nombre'];
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];
        $direccion = $_POST['direccion'];
        $genero = $_POST['genero'];
        $fechaInscripcion = $_POST['fechaInscripcion'];
        $estado = $_POST['estado'];

        $cliente = new Cliente($id, $carnet, $nombre, $fechaNacimiento, $telefono, $correo, $direccion, $genero, $fechaInscripcion, $estado);
        $clienteBusiness = new ClienteBusiness();

        $resultado = $clienteBusiness->actualizarTBCliente($cliente);

        if ($resultado == 1) {
            header("Location: ../view/clienteView.php?success=actualizado");
        } else {
            header("Location: ../view/clienteView.php?error=actualizar");
        }
        exit();
    } else {
        header("Location: ../view/clienteView.php?error=datos_faltantes");
        exit();
    }

} else if (isset($_POST['eliminar'])) {

    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $clienteBusiness = new ClienteBusiness();
        $resultado = $clienteBusiness->eliminarTBCliente($id);

        if ($resultado == 1) {
            header("Location: ../view/clienteView.php?success=eliminado");
        } else {
            header("Location: ../view/clienteView.php?error=eliminar");
        }
        exit();
    } else {
        header("Location: ../view/clienteView.php?error=id_faltante");
        exit();
    }
} else {
    header("Location: ../view/clienteView.php?error=accion_no_valida");
    exit();
}
?>
