<?php
header('Content-Type: text/html; charset=utf-8');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

echo "<pre>";
echo "==================================================<br>";
echo "   PRUEBA DE DIAGNÓSTICO DE CONEXIÓN A MYSQL<br>";
echo "==================================================<br><br>";

$usuario_mysql = "root";
$contrasena_mysql = "";
$basedatos_mysql = "dbgym";

function probar_conexion($descripcion, $servidor, $usuario, $contrasena, $db, $puerto = 'default')
{
    echo "--------------------------------------------------<br>";
    echo "<b>Prueba: $descripcion</b><br>";
    $puerto_str = $puerto;
    if ($puerto === null) $puerto_str = 'null';
    if ($puerto === 'default') $puerto_str = '(omitido)';
    echo "Parámetros: [Servidor: $servidor], [Puerto: $puerto_str]<br><br>";

    try {
        $conn = false;
        if ($puerto === 'default') {
            $conn = mysqli_connect($servidor, $usuario, $contrasena, $db);
        } else {
            $conn = mysqli_connect($servidor, $usuario, $contrasena, $db, $puerto);
        }

        echo "<span style='color:green; font-weight:bold;'>--> RESULTADO: ÉXITO</span><br>";
        mysqli_close($conn);

    } catch (mysqli_sql_exception $e) {
        echo "<span style='color:red; font-weight:bold;'>--> RESULTADO: FALLÓ</span><br>";
        echo "<b>Error:</b> " . $e->getCode() . " - " . $e->getMessage() . "<br>";
    }
    echo "--------------------------------------------------<br><br>";
}

probar_conexion("Método Original (localhost, puerto nulo)", "localhost", $usuario_mysql, $contrasena_mysql, $basedatos_mysql, null);
probar_conexion("Método Recomendado (127.0.0.1, puerto 3306)", "127.0.0.1", $usuario_mysql, $contrasena_mysql, $basedatos_mysql, 3306);
probar_conexion("Alternativa 1 (localhost con puerto 3306)", "localhost", $usuario_mysql, $contrasena_mysql, $basedatos_mysql, 3306);
probar_conexion("Alternativa 2 (127.0.0.1, sin puerto)", "127.0.0.1", $usuario_mysql, $contrasena_mysql, $basedatos_mysql);

echo "Diagnóstico finalizado.";
echo "</pre>";
?>
