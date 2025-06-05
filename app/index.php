<?php
session_start();

require_once __DIR__ . '/config/database.php';


spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    } else {
        echo "Fichier introuvable : $file<br>";
    }
});


function handleRequest($routes)
{
    $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $basePath = '/projet_java/app'; 
    $url = str_replace($basePath, '', $urlPath);

    foreach ($routes as $pattern => $handler) {
        $regex = preg_replace('#\{(\w+):([^}]+)\}#', '(?P<$1>$2)', $pattern);
        $regex = "#^" . $regex . "$#";

        if (preg_match($regex, $url, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return call_user_func_array($handler, array_values($params));

        }
    }

    http_response_code(404);
    echo "404 Not Found - Route non trouvée: $url";
}


$routes = require_once __DIR__ . '/config/routes.php';


handleRequest($routes);
