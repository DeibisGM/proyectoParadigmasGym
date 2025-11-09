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
include_once '../utility/ImageManager.php';

$certificadoBusiness = new CertificadoBusiness();
$instructorBusiness = new InstructorBusiness();
$imageManager = new ImageManager();

$certificados = $certificadoBusiness->getCertificados();
$instructores = $instructorBusiness->getAllTBInstructor(true);

$instructor_nombre = "Todos los instructores";
$instructorIdFiltro = null;

if (isset($_GET['instructor_id']) && !empty($_GET['instructor_id'])) {
    $instructorIdFiltro = $_GET['instructor_id'];
    $certificados = array_filter($certificados, function ($cert) use ($instructorIdFiltro) {
        return $cert->getIdInstructor() == $instructorIdFiltro;
    });

    foreach ($instructores as $instructor) {
        if ($instructor->getInstructorId() == $instructorIdFiltro) {
            $instructor_nombre = $instructor->getInstructorNombre();
            break;
        }
    }

    if ($instructor_nombre === "Todos los instructores") {
        $instructor_nombre = "Instructor ID: " . htmlspecialchars($instructorIdFiltro);
    }
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
            <a href="instructorView.php" class="back-button"><i class="ph ph-arrow-left"></i></a>
            <h2>Gestión de Certificados: <strong>
                    <?php echo htmlspecialchars($instructor_nombre); ?>
                </strong></h2>
        </header>

        <main>
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message">
                    <?php
                    if ($_GET['success'] == 'created')
                        echo 'Certificado creado correctamente.';
                    elseif ($_GET['success'] == 'updated')
                        echo 'Certificado actualizado correctamente.';
                    elseif ($_GET['success'] == 'deleted')
                        echo 'Certificado eliminado correctamente.';
                    elseif ($_GET['success'] == 'image_deleted')
                        echo 'Imagen eliminada correctamente.';
                    ?>
                </p>
            <?php elseif (isset($_GET['error'])): ?>
                <p class="error-message">
                    <?php
                    if ($_GET['error'] == 'emptyFields')
                        echo 'Error: Todos los campos son obligatorios.';
                    elseif ($_GET['error'] == 'nameTooLong')
                        echo 'Error: El nombre es demasiado largo (máximo 100 caracteres).';
                    elseif ($_GET['error'] == 'dbError')
                        echo 'Error: No se pudo completar la operación en la base de datos.';
                    elseif ($_GET['error'] == 'instructorNotFound')
                        echo 'Error: Instructor no encontrado.';
                    elseif ($_GET['error'] == 'notFound')
                        echo 'Error: Certificado no encontrado.';
                    elseif ($_GET['error'] == 'image_deleted')
                        echo 'Error: No se pudo eliminar la imagen.';
                    else
                        echo 'Error: ' . htmlspecialchars($_GET['error']);
                    ?>
                </p>
            <?php endif; ?>

            <?php if ($puedeCrearCertificados): ?>
                <section>
                    <h3><i class="ph ph-plus"></i> Agregar Certificado</h3>
                    <form method="post" action="../action/certificadoAction.php" enctype="multipart/form-data">
                        <div class="form-grid-container">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" id="nombre" name="nombre" placeholder="Nombre del certificado"
                                    required maxlength="100">
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <input type="text" id="descripcion" name="descripcion" placeholder="Descripción"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="entidad">Entidad:</label>
                                <input type="text" id="entidad" name="entidad" placeholder="Entidad emisora" required>
                            </div>
                            <div class="form-group">
                                <label for="idInstructor">Instructor:</label>
                                <select name="idInstructor" id="idInstructor" required>
                                    <option value="">Seleccione un instructor</option>
                                    <?php foreach ($instructores as $instructor): ?>
                                        <option value="<?php echo $instructor->getInstructorId(); ?>" <?php echo ($instructorIdFiltro && $instructor->getInstructorId() == $instructorIdFiltro) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($instructor->getInstructorNombre() . ' (' . str_pad($instructor->getInstructorId(), 3, '0', STR_PAD_LEFT) . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group form-group-horizontal">
                                <label for="tbcertificadoimagenid">Imagen:</label>
                                <input type="file" name="tbcertificadoimagenid[]" id="tbcertificadoimagenid"
                                    accept="image/png, image/jpeg, image/webp">
                            </div>
                        </div>
                        <button type="submit" name="create"><i class="ph ph-plus"></i> Agregar</button>
                    </form>
                </section>
            <?php endif; ?>

            <section>
                <h3><i class="ph ph-list-bullets"></i> Lista de Certificados</h3>
                <?php if (empty($certificados)): ?>
                    <p>No hay certificados registrados
                        <?php echo $instructorIdFiltro ? ' para este instructor' : ''; ?>.
                    </p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table-clients">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Entidad</th>
                                    <th>Instructor</th>
                                    <th>Imagen</th>
                                    <?php if ($puedeCrearCertificados): ?>
                                        <th>Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($certificados as $cert): ?>
                                    <tr>
                                        <form id="form-cert-<?php echo $cert->getId(); ?>" method="post"
                                            action="../action/certificadoAction.php" enctype="multipart/form-data"></form>
                                        <input type="hidden" name="id" value="<?php echo $cert->getId(); ?>"
                                            form="form-cert-<?php echo $cert->getId(); ?>">
                                        <td data-label="ID">
                                            <?php echo str_pad($cert->getId(), 3, '0', STR_PAD_LEFT); ?>
                                        </td>
                                        <td data-label="Nombre">
                                            <input type="text" name="nombre"
                                                value="<?php echo htmlspecialchars($cert->getNombre()); ?>" <?php echo $puedeCrearCertificados ? '' : 'readonly'; ?> required
                                                form="form-cert-<?php echo $cert->getId(); ?>">
                                        </td>
                                        <td data-label="Descripción">
                                            <input type="text" name="descripcion"
                                                value="<?php echo htmlspecialchars($cert->getDescripcion()); ?>" <?php echo $puedeCrearCertificados ? '' : 'readonly'; ?> required
                                                form="form-cert-<?php echo $cert->getId(); ?>">
                                        </td>
                                        <td data-label="Entidad">
                                            <input type="text" name="entidad"
                                                value="<?php echo htmlspecialchars($cert->getEntidad()); ?>" <?php echo $puedeCrearCertificados ? '' : 'readonly'; ?> required
                                                form="form-cert-<?php echo $cert->getId(); ?>">
                                        </td>
                                        <td data-label="Instructor">
                                            <?php if ($puedeCrearCertificados): ?>
                                                <select name="idInstructor" required
                                                    form="form-cert-<?php echo $cert->getId(); ?>">
                                                    <?php foreach ($instructores as $instructor): ?>
                                                        <option value="<?php echo $instructor->getInstructorId(); ?>" <?php echo $instructor->getInstructorId() == $cert->getIdInstructor() ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($instructor->getInstructorNombre()); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <?php
                                                $instructorCert = null;
                                                foreach ($instructores as $instructor) {
                                                    if ($instructor->getInstructorId() == $cert->getIdInstructor()) {
                                                        $instructorCert = $instructor;
                                                        break;
                                                    }
                                                }
                                                echo $instructorCert ? htmlspecialchars($instructorCert->getInstructorNombre()) : 'N/A';
                                                ?>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Imagen">
                                            <?php
                                            $imageId = $cert->getTbcertificadoImagenId();
                                            if (!empty($imageId)) {
                                                $imagen = $imageManager->getImagesByIds($imageId);
                                                if (!empty($imagen) && !empty($imagen[0]['tbimagenruta'])) {
                                                    $imagePath = '..' . htmlspecialchars($imagen[0]['tbimagenruta']);
                                                    $finalSrc = $imagePath . '?t=' . time();
                                                    echo '<div class="image-container"><img src="' . $finalSrc . '" alt="Imagen" onerror="this.onerror=null; this.src=\'noimage.svg\';">';
                                                    if ($puedeCrearCertificados) {
                                                        echo '<button type="submit" name="delete_image" value="' . $imageId . '" onclick="return confirm(\'¿Eliminar esta imagen?\');" form="form-cert-' . $cert->getId() . '"><i class="ph ph-x"></i></button>';
                                                    }
                                                    echo '</div>';
                                                } else {
                                                    if ($puedeCrearCertificados) {
                                                        echo '<input type="file" name="tbcertificadoimagenid[]" accept="image/png, image/jpeg, image/webp" form="form-cert-' . $cert->getId() . '">';
                                                    } else {
                                                        echo 'Sin imagen';
                                                    }
                                                }
                                            } else {
                                                if ($puedeCrearCertificados) {
                                                    echo '<input type="file" name="tbcertificadoimagenid[]" accept="image/png, image/jpeg, image/webp" form="form-cert-' . $cert->getId() . '">';
                                                } else {
                                                    echo 'Sin imagen';
                                                }
                                            }
                                            ?>
                                        </td>
                                        <?php if ($puedeCrearCertificados): ?>
                                            <td data-label="Acciones">
                                                <div class="actions">
                                                    <button type="submit" name="update"
                                                        form="form-cert-<?php echo $cert->getId(); ?>" class="btn-row"><i
                                                            class="ph ph-pencil-simple"></i></button>
                                                    <button type="submit" name="delete"
                                                        onclick="return confirm('¿Eliminar este certificado?');"
                                                        form="form-cert-<?php echo $cert->getId(); ?>"
                                                        class="btn-row btn-danger"><i class="ph ph-trash"></i></button>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>

    </div>
</body>

</html>