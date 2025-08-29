<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: ../view/loginView.php");
    exit();
}

// Todos los tipos de usuario pueden ver certificados, pero con diferentes permisos
$tipoUsuario = $_SESSION['tipo_usuario'];
$puedeCrearCertificados = ($tipoUsuario === 'admin' || $tipoUsuario === 'instructor');

include_once '../business/certificadoBusiness.php';
include_once '../business/instructorBusiness.php';

$certificadoBusiness = new CertificadoBusiness();
$instructorBusiness = new InstructorBusiness();

$certificados = $certificadoBusiness->getCertificados();
$instructores = $instructorBusiness->getAllTBInstructor(true); // Obtener todos los instructores

// FILTRAR POR INSTRUCTOR SI SE ESPECIFICA
if (isset($_GET['instructor_id'])) {
    $instructorIdFiltro = $_GET['instructor_id'];
    $certificados = array_filter($certificados, function($cert) use ($instructorIdFiltro) {
        return $cert->getIdInstructor() == $instructorIdFiltro;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Certificados</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background: #eee; }
        form { margin-bottom: 20px; }
        input, select {
            padding: 8px;
            margin: 5px 0;
            width: 200px;
            box-sizing: border-box;
        }
        button { padding: 8px 15px; cursor: pointer; }
    </style>
</head>
<body>
    <header>
        <h2>Gestión de Certificados</h2>
        <?php if (isset($_GET['instructor_id'])): ?>
            <a href="instructorView.php">← Volver a Instructores</a>
        <?php else: ?>
            <a href="../index.php">← Volver al Inicio</a>
        <?php endif; ?>
    </header>
    <hr>

    <main>
        <?php if ($puedeCrearCertificados): ?>
        <h2>Agregar Certificado</h2>
        <form method="post" action="../action/certificadoAction.php">
            <input type="text" name="nombre" placeholder="Nombre" required maxlength="100"><br>
            <input type="text" name="descripcion" placeholder="Descripción" required><br>
            <input type="text" name="entidad" placeholder="Entidad" required><br>

            <label for="idInstructor">Instructor:</label><br>
            <select name="idInstructor" required>
                <option value="">Seleccione un instructor</option>
                <?php foreach ($instructores as $instructor): ?>
                    <option value="<?php echo $instructor->getInstructorId(); ?>">
                        <?php echo htmlspecialchars($instructor->getInstructorNombre() . ' (' . $instructor->getInstructorId() . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <button type="submit" name="create">Agregar</button>
        </form>
        <?php endif; ?>

        <h2>Lista de Certificados</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Entidad</th>
                <th>Instructor</th>
                <?php if ($puedeCrearCertificados): ?>
                <th>Acciones</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($certificados as $cert):
                // Obtener el nombre del instructor para este certificado
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
                    <td><?php echo htmlspecialchars($cert->getNombre()); ?></td>
                    <td><?php echo htmlspecialchars($cert->getDescripcion()); ?></td>
                    <td><?php echo htmlspecialchars($cert->getEntidad()); ?></td>
                    <td>
                        <?php
                        if ($instructorCert) {
                            echo htmlspecialchars($instructorCert->getInstructorNombre() . ' (' . str_pad($instructorCert->getInstructorId(), 3, '0', STR_PAD_LEFT) . ')');
                        } else {
                            echo "Instructor no encontrado";
                        }
                        ?>
                    </td>
                    <?php if ($puedeCrearCertificados): ?>
                    <td>
                        <button type="submit" name="delete" onclick="return confirm('¿Eliminar este certificado?');">Eliminar</button>
                    </td>
                    <?php endif; ?>
                </form>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php if (isset($_GET['success'])): ?>
            <p style="color:green;">
                <?php 
                if ($_GET['success'] == 'created') echo 'Certificado creado correctamente.';
                elseif ($_GET['success'] == 'updated') echo 'Certificado actualizado correctamente.';
                elseif ($_GET['success'] == 'deleted') echo 'Certificado eliminado correctamente.';
                ?>
            </p>
        <?php elseif (isset($_GET['error'])): ?>
            <p style="color:red;">
                <?php 
                if ($_GET['error'] == 'emptyFields') echo 'Error: Todos los campos son obligatorios.';
                elseif ($_GET['error'] == 'nameTooLong') echo 'Error: El nombre es demasiado largo (máximo 100 caracteres).';
                elseif ($_GET['error'] == 'dbError') echo 'Error: No se pudo completar la operación en la base de datos.';
                else echo 'Error: ' . htmlspecialchars($_GET['error']);
                ?>
            </p>
        <?php endif; ?>
    </main>
    <hr>
    <footer>
        <p>Fin de la página.</p>
    </footer>
</body>
</html>