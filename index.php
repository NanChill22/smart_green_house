<?php

session_start();

// Config and Helpers
require_once __DIR__ . '/config/config.php';

// Models
require_once __DIR__ . '/app/models/User.php';
require_once __DIR__ . '/app/models/Petani.php';
require_once __DIR__ . '/app/models/Sensor.php';
require_once __DIR__ . '/app/models/KontrolLog.php';
require_once __DIR__ . '/app/models/LaporanHarian.php';

// Controllers
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/DashboardController.php';
require_once __DIR__ . '/app/controllers/PetaniController.php';
require_once __DIR__ . '/app/controllers/SensorController.php';
require_once __DIR__ . '/app/controllers/UserController.php';

// Simple router based on REQUEST_URI
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Normalize the base path and request URI to handle running in a subdirectory
$basePath = str_replace('\\', '/', $basePath);
if ($basePath === '/') {
    $basePath = '';
}
$path = trim(str_replace($basePath, '', $requestUri), '/');
$path = $path ?: 'dashboard/index'; // Default route

$segments = explode('/', $path);
$controllerName = ucfirst(strtolower($segments[0] ?? 'dashboard')) . 'Controller';
$methodName = strtolower($segments[1] ?? 'index');
$param = $segments[2] ?? null;

// Instantiate controller and call method
if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $methodName)) {
        // Panggil metode dengan atau tanpa parameter
        if ($param !== null) {
            $controller->$methodName($param);
        } else {
            $controller->$methodName();
        }
    } else {
        // Handle method not found
        http_response_code(404);
        echo "404 Not Found: Method {$methodName} not found in controller {$controllerName}.";
    }
} else {
    // Handle controller not found
    http_response_code(404);
    echo "404 Not Found: Controller {$controllerName} not found.";
}
