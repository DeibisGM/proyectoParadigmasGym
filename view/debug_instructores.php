<?php
session_start();
include_once '../business/instructorBusiness.php';

$instructorBusiness = new InstructorBusiness();
$instructores = $instructorBusiness->getAllTBInstructor(true);

echo "<h1>Debug Instructores</h1>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Activo</th></tr>";
foreach($instructores as $inst) {
    echo "<tr>";
    echo "<td>" . $inst->getInstructorId() . "</td>";
    echo "<td>" . $inst->getInstructorNombre() . "</td>";
    echo "<td>" . $inst->getInstructorTelefono() . "</td>";
    echo "<td>" . $inst->getInstructorActivo() . "</td>";
    echo "</tr>";
}
echo "</table>";
?>