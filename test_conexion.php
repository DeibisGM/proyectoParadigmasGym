<?php
require_once 'Data.php';

$data = new Data();
echo "<h2>🔍 Probando conexión a base de datos local</h2>";

try {
    // Conectar a la base de datos
    $connection = $data->connect();
    echo "✅ <strong>¡Conexión exitosa!</strong><br><br>";

    // Ver información de conexión
    echo "<strong>Configuración usada:</strong><br>";
    echo "- Servidor: " . $data->server . "<br>";
    echo "- Base de datos: " . $data->db . "<br>";
    echo "- Usuario: " . $data->user . "<br>";
    echo "- Puerto: " . $data->port . "<br><br>";

    // Contar clientes
    $result = $connection->query("SELECT COUNT(*) as total FROM tbcliente");
    $row = $result->fetch_assoc();
    echo "👥 <strong>Total de clientes:</strong> " . $row['total'] . "<br>";

    // Contar instructores
    $result = $connection->query("SELECT COUNT(*) as total FROM tbinstructor");
    $row = $result->fetch_assoc();
    echo "🏋️ <strong>Total de instructores:</strong> " . $row['total'] . "<br><br>";

    // Mostrar todas las tablas
    $result = $connection->query("SHOW TABLES");
    echo "<strong>📊 Tablas en la base de datos:</strong><br>";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }

    echo "<br><br>🎉 <strong>¡Todo funciona correctamente!</strong>";

    $connection->close();

} catch (Exception $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage();
}
?>