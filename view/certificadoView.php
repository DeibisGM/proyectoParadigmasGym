<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

$tipoUsuario = $_SESSION['tipo_usuario'];
$puedeCrearCertificados = ($tipoUsuario === 'admin' || $tipoUsuario === 'instructor');

include_once '../business/certificadoBusiness.php';
include_once '../business/instructorBusiness.php';

$certificadoBusiness = new CertificadoBusiness();
$instructorBusiness = new InstructorBusiness();

$certificados = $certificadoBusiness->getCertificados();
$instructores = $instructorBusiness->getAllTBInstructor(true);

if (isset($_GET['instructor_id'])) {
    $instructorIdFiltro = $_GET['instructor_id'];
    $certificados = array_filter($certificados, function ($cert) use ($instructorIdFiltro) {
        return $cert->getIdInstructor() == $instructorIdFiltro;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Certificados</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header>
        <?php if (isset($_GET['instructor_id'])): ?>
            <a href="instructorView.php"><i class="ph ph-arrow-left"></i>Volver a Instructores</a><br><br>
        <?php else: ?>
            <a href="../index.php"><i class="ph ph-arrow-left"></i>Volver al Inicio</a><br><br>
        <?php endif; ?>
        <h2><i class="ph ph-certificate"></i>Gestión de Certificados</h2>

    </header>

    <main>
        <?php if ($puedeCrearCertificados): ?>
            <section>
                <h3><i class="ph ph-plus-circle"></i>Agregar Certificado</h3>
                <form method="post" action="../action/certificadoAction.php">
                    <input type="text" name="nombre" placeholder="Nombre del certificado" required maxlength="100">
                    <input type="text" name="descripcion" placeholder="Descripción" required>
                    <input type="text" name="entidad" placeholder="Entidad emisora" required>

                    <label for="idInstructor">Asignar a Instructor:</label>
                    <select name="idInstructor" id="idInstructor" required>
                        <option value="">Seleccione un instructor</option>
                        <?php foreach ($instructores as $instructor): ?>
                            <option value="<?php echo $instructor->getInstructorId(); ?>">
                                <?php echo htmlspecialchars($instructor->getInstructorNombre() . ' (' . $instructor->getInstructorId() . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="create"><i class="ph ph-plus"></i>Agregar Certificado</button>
                </form>
            </section>
        <?php endif; ?>

        <section>
            <h3><i class="ph ph-list-bullets"></i>Lista de Certificados</h3>
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message">
                    <?php
                    if ($_GET['success'] == 'created') echo 'Certificado creado correctamente.';
                    elseif ($_GET['success'] == 'updated') echo 'Certificado actualizado correctamente.';
                    elseif ($_GET['success'] == 'deleted') echo 'Certificado eliminado correctamente.';
                    ?>
                </p>
            <?php elseif (isset($_GET['error'])): ?>
                <p class="error-message">
                    <?php
                    if ($_GET['error'] == 'emptyFields') echo 'Error: Todos los campos son obligatorios.';
                    elseif ($_GET['error'] == 'nameTooLong') echo 'Error: El nombre es demasiado largo (máximo 100 caracteres).';
                    elseif ($_GET['error'] == 'dbError') echo 'Error: No se pudo completar la operación en la base de datos.';
                    else echo 'Error: ' . htmlspecialchars($_GET['error']);
                    ?>
                </p>
            <?php endif; ?>
            <div style="overflow-x:auto; width: 100%;">
                <table style="table-layout: fixed; width: 100%;">
                    <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 20%;">Nombre</th>
                        <th style="width: 30%;">Descripción</th>
                        <th style="width: 20%;">Entidad</th>
                        <th style="width: 20%;">Instructor</th>
                        <?php if ($puedeCrearCertificados): ?>
                            <th style="width: 15%;">Acciones</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($certificados as $cert):
                        $instructorCert = null;
                        foreach ($instructores as $instructor) {
                            if ($instructor->getInstructorId() == $cert->getIdInstructor()) {
                                $instructorCert = $instructor;
                                break;
                            }
                        }
                        ?>
                        <tr>
                            <form method="post" action="../action/certificadoAction.php">
                                <td>
                                    <input type="hidden" name="id" value="<?php echo $cert->getId(); ?>">
                                    <?php echo str_pad($cert->getId(), 3, '0', STR_PAD_LEFT); ?>
                                </td>
                                <td><input type="text" name="nombre"
                                           value="<?php echo htmlspecialchars($cert->getNombre()); ?>" required
                                           maxlength="100"></td>
                                <td><input type="text" name="descripcion"
                                           value="<?php echo htmlspecialchars($cert->getDescripcion()); ?>" required>
                                </td>
                                <td><input type="text" name="entidad"
                                           value="<?php echo htmlspecialchars($cert->getEntidad()); ?>" required></td>
                                <td>
                                    <select name="idInstructor" required>
                                        <option value=""
                                                disabled <?php echo ($instructorCert === null) ? 'selected' : ''; ?>>
                                            Instructor no existe
                                        </option>
                                        <?php foreach ($instructores as $inst): ?>
                                            <option value="<?php echo $inst->getInstructorId(); ?>"
                                                    <?php echo ($instructorCert && $inst->getInstructorId() == $cert->getIdInstructor()) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($inst->getInstructorNombre() . ' (' . str_pad($inst->getInstructorId(), 3, '0', STR_PAD_LEFT) . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <?php if ($puedeCrearCertificados): ?>
                                    <td>
                                        <button type="submit" name="update" title="Actualizar" class="button-icon-only">
                                            <i class="ph ph-floppy-disk"></i></button>
                                        <button type="submit" name="delete"
                                                onclick="return confirm('¿Eliminar este certificado?');"
                                                class="button-icon-only"><i
                                                    class="ph ph-trash"></i></button>
                                    </td>
                                <?php endif; ?>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Gimnasio. Todos los derechos reservados.</p>
    </footer>
</div>
</body>
</html>