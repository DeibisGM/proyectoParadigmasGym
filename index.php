<?php

// 1. OBTENER LA RUTA SOLICITADA
// Esto toma la URL que el usuario escribió, por ejemplo: "/zonas-cuerpo"
$request_uri = $_SERVER['REQUEST_URI'];
$ruta = parse_url($request_uri, PHP_URL_PATH);

// 2. DEFINIR LAS RUTAS Y MOSTRAR LA VISTA CORRESPONDIENTE
// Este switch es como el directorio del hotel. Compara la ruta solicitada
// con las rutas que conocemos.

switch ($ruta) {
    // Caso 1: La página de inicio (cuando la URL es solo "/" o está vacía)
    case '/':
    case '':
        // Mostramos un menú de navegación con los enlaces
        echo "<h1>Bienvenido al Gimnasio</h1>";
        echo "<p>Por favor, elige una opción:</p>";
        echo '<ul>';
        // Este enlace funcionará gracias al .htaccess
        echo '<li><a href="/zonas-cuerpo">Ver Zonas del Cuerpo</a></li>';
        echo '</ul>';
        break;

    // Caso 2: La ruta para ver las zonas del cuerpo
    case '/zonas-cuerpo':
        // Cargamos el archivo de la vista correspondiente
        require_once 'view/zonaCuerpoView.php';
        break;

    // Caso por defecto: Si la ruta no coincide con ninguna conocida
    default:
        // Enviamos una cabecera de error 404 "No Encontrado"
        http_response_code(404);
        echo "<h1>Error 404: Página no encontrada</h1>";
        echo '<p>La página que buscas no existe.</p>';
        echo '<a href="/">Volver al inicio</a>';
        break;
}

?>
