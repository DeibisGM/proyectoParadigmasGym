<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructores - Vista Cliente</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .instructor-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<header>
    <h2>Gimnasio - Instructores Disponibles</h2>
</header>

<hr>

<main>
    <h3>Nuestros Instructores</h3>

    <table border="1">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Direccion</th>
            <th>Correo</th>

        </tr>
        </thead>
        <tbody>
        <?php
        require_once '../business/instructorBusiness.php';
        $business = new InstructorBusiness();
        $instructores = $business->getAllTBInstructor();

        if (empty($instructores)) {
            echo "<tr><td colspan='4'>No hay instructores disponibles</td></tr>";
        } else {
            foreach ($instructores as $instructor) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($instructor->getInstructorNombre()) . '</td>';
                echo '<td>' . htmlspecialchars($instructor->getInstructorTelefono()) . '</td>';
                echo '<td>' . htmlspecialchars($instructor->getInstructorDireccion()) . '</td>';
                echo '<td>' . htmlspecialchars($instructor->getInstructorCorreo()) . '</td>';
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <h3>Conoce a nuestro equipo</h3>
        <?php
        if (!empty($instructores)) {
            foreach ($instructores as $instructor) {
                echo '<div class="instructor-card">';
                echo '<h4>' . htmlspecialchars($instructor->getInstructorNombre()) . '</h4>';
                echo '<p><strong>Contacto:</strong> ' . htmlspecialchars($instructor->getInstructorCorreo()) . '</p>';
                echo '<p><strong>Teléfono:</strong> ' . htmlspecialchars($instructor->getInstructorTelefono()) . '</p>';
                echo '<p><strong>Direccion:</strong> ' . htmlspecialchars($instructor->getInstructorDireccion()) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No hay instructores registrados en este momento.</p>';
        }
        ?>
    </div>
</main>

<hr>


</body>
</html>