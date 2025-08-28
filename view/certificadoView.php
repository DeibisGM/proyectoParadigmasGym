
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
$instructorId = isset($_GET['instructor_id']) ? intval($_GET['instructor_id']) : null;

// Control de acceso:
// - Admin: siempre puede acceder
// - Instructor/Cliente: solo puede acceder si viene con parámetro instructor_id
if ($tipoUsuario !== 'admin' && !$instructorId) {
    header("Location: ../index.php?error=acceso_denegado");
    exit();
}

include_once '../business/certificadoBusiness.php';
include_once '../business/instructorBusiness.php'; 

$certificadoBusiness = new CertificadoBusiness();
$instructorBusiness = new InstructorBusiness();

// Obtener todos los instructores para el formulario (solo para admin)
$instructores = $instructorBusiness->getAllTBInstructor();

// Obtener certificados según el contexto
if ($instructorId) {
    // Si se especificó un instructor, obtener solo sus certificados
    $certificados = $certificadoBusiness->getCertificadosPorInstructor($instructorId);
    $instructorEspecifico = $instructorBusiness->getInstructorPorId($instructorId);
} else {
    // Si es admin sin parámetro, obtener todos los certificados
    $certificados = $certificadoBusiness->getCertificados();
    $instructorEspecifico = null;
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
        .back-button { margin-bottom: 20px; }
        .admin-only { display: <?php echo ($tipoUsuario === 'admin') ? 'block' : 'none'; ?>; }
    </style>
</head>
<body>
    <header>
        <h2>Gestión de Certificados</h2>
        <a href="../index.php">Volver al Inicio</a>
        
        <?php if ($instructorEspecifico): ?>
        <div class="back-button">
            <a href="../view/instructorView.php">← Volver a Instructores</a>
        </div>
        <h3>Certificados de: <?php echo htmlspecialchars($instructorEspecifico->getInstructorNombre()); ?></h3>
        <?php endif; ?>
    </header>
    <hr>

    <main>
        <?php if ($tipoUsuario === 'admin' && !$instructorId): ?>
        <div class="admin-only">
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
        </div>
        <?php endif; ?>

        <h2>Lista de Certificados</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Entidad</th>
                <th>Instructor</th>
                <?php if ($tipoUsuario === 'admin' && !$instructorId): ?>
                <th>Acciones</th>
                <?php endif; ?>
            </tr>
            <?php if (empty($certificados)): ?>
            <tr>
                <td colspan="<?php echo ($tipoUsuario === 'admin' && !$instructorId) ? 6 : 5; ?>">No hay certificados registrados</td>
            </tr>
            <?php else: ?>
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
                <?php if ($tipoUsuario === 'admin' && !$instructorId): ?>
                <form method="post" action="../action/certificadoAction.php">
                <?php endif; ?>
                    <td>
                        <?php if ($tipoUsuario === 'admin' && !$instructorId): ?>
                        <input type="hidden" name="id" value="<?php echo $cert->getId(); ?>">
                        <?php endif; ?>
                        <?php echo $cert->getId(); ?>
                    </td>
                    <td><?php echo htmlspecialchars($cert->getNombre()); ?></td>
                    <td><?php echo htmlspecialchars($cert->getDescripcion()); ?></td>
                    <td><?php echo htmlspecialchars($cert->getEntidad()); ?></td>
                    <td>
                        <?php 
                        if ($instructorCert) {
                            echo htmlspecialchars($instructorCert->getInstructorNombre() . ' (' . $instructorCert->getInstructorId() . ')');
                        } else {
                            echo "Instructor no encontrado";
                        }
                        ?>
                    </td>
                    <?php if ($tipoUsuario === 'admin' && !$instructorId): ?>
                    <td>
                        <button type="submit" name="delete" onclick="return confirm('¿Eliminar este certificado?');">Eliminar</button>
                    </td>
                    <?php endif; ?>
                <?php if ($tipoUsuario === 'admin' && !$instructorId): ?>
                </form>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
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
                elseif ($_GET['error'] == 'instructorNotFound') echo 'Error: Instructor no encontrado.';
                elseif ($_GET['error'] == 'acceso_denegado') echo 'Error: No tiene permisos para acceder a esta página.';
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
