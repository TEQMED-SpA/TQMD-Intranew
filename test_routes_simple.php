<?php
// Script simple para probar rutas sin conexión a BD
require_once __DIR__ . '/vendor/autoload.php';

// Inicializar aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Prueba de Rutas de Centros Médicos (sin BD) ===\n\n";

try {
    // 1. Listar todas las rutas de centros médicos
    echo "1. Rutas de centros médicos disponibles:\n";
    $routes = app('router')->getRoutes();
    $centrosRoutes = [];

    foreach ($routes as $route) {
        if (strpos($route->uri(), 'centros_medicos') !== false) {
            $centrosRoutes[] = $route;
            echo "   - " . $route->uri() . " [" . $route->getName() . "]\n";
            echo "     Métodos: " . implode(', ', $route->methods()) . "\n";
            echo "     Parámetros: " . json_encode($route->parameterNames()) . "\n\n";
        }
    }

    // 2. Probar generar rutas con diferentes formatos
    echo "2. Probando generación de rutas:\n";

    // Crear un objeto simulado
    $mockCentro = new stdClass();
    $mockCentro->id = 1;

    try {
        $route1 = route('centros_medicos.show', ['centros_medico' => 1]);
        echo "✅ Formato array asociativo: " . $route1 . "\n";
    } catch (Exception $e) {
        echo "❌ Error array asociativo: " . $e->getMessage() . "\n";
    }

    try {
        $route2 = route('centros_medicos.show', [1]);
        echo "✅ Formato array posicional: " . $route2 . "\n";
    } catch (Exception $e) {
        echo "❌ Error array posicional: " . $e->getMessage() . "\n";
    }

    try {
        $route3 = route('centros_medicos.show', 1);
        echo "✅ Formato parámetro simple: " . $route3 . "\n";
    } catch (Exception $e) {
        echo "❌ Error parámetro simple: " . $e->getMessage() . "\n";
    }

    // 3. Verificar configuración de rutas
    echo "\n3. Verificando configuración de rutas:\n";
    $routeCollection = app('router')->getRoutes();
    $showRoute = $routeCollection->getByName('centros_medicos.show');

    if ($showRoute) {
        echo "✅ Ruta 'centros_medicos.show' encontrada\n";
        echo "   URI: " . $showRoute->uri() . "\n";
        echo "   Parámetros esperados: " . json_encode($showRoute->parameterNames()) . "\n";
    } else {
        echo "❌ Ruta 'centros_medicos.show' NO encontrada\n";
    }

    echo "\n=== Prueba completada ===\n";
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
