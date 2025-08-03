<?php
include '../business/clienteBusiness.php';

$clienteBusiness = new ClienteBusiness();

if (isset($_POST['create'])) {
    $carnet = $_POST['carnet'];
    $nombre = $_POST['nombre'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $genero = $_POST['genero'];
    $inscripcion = $_POST['inscripcion'];

    // Validar campos vacíos (correo no es requerido)
    if (
        empty($carnet) || empty($nombre) || empty($fechaNacimiento) ||
        empty($telefono) || empty($direccion) || empty($genero) || empty($inscripcion)
    ) {
        header("Location: ../view/clienteView.php?error=emptyField");
        exit();
    }

    // Validar si ya existe por carnet
    if ($clienteBusiness->verificarCarnetExistente($carnet)) {
        header("Location: ../view/clienteView.php?error=existe");
        exit();
    }

    $nuevoCliente = new Cliente(
        0, $carnet, $nombre, $fechaNacimiento,
        $telefono, $correo, $direccion, $genero, $inscripcion, 1
    );

    if ($clienteBusiness->insertarTBCliente($nuevoCliente)) {
        header("Location: ../view/clienteView.php?success=inserted");
    } else {
        header("Location: ../view/clienteView.php?error=dbError");
    }

} else if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $genero = $_POST['genero'];
    $inscripcion = $_POST['inscripcion'];

    if (
        empty($nombre) || empty($fechaNacimiento) ||
        empty($telefono) || empty($direccion) || empty($genero) || empty($inscripcion)
    ) {
        header("Location: ../view/clienteView.php?error=emptyField");
        exit();
    }

    $cliente = new Cliente(
        $id, "", $nombre, $fechaNacimiento,
        $telefono, $correo, $direccion, $genero, $inscripcion, 1
    );

    if ($clienteBusiness->actualizarTBCliente($cliente)) {
        header("Location: ../view/clienteView.php?success=updated");
    } else {
        header("Location: ../view/clienteView.php?error=dbError");
    }

} else if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    if ($clienteBusiness->eliminarTBCliente($id)) {
        header("Location: ../view/clienteView.php?success=deleted");
    } else {
        header("Location: ../view/clienteView.php?error=dbError");
    }
} else {
    header("Location: ../view/clienteView.php?error=error");
}
?>
