<?php
// Script para probar endpoint de centros médicos
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\CentroMedicoController;
use App\Models\CentroMedico;

// Inicializar aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Prueba de Endpoint de Centros Médicos ===\n\n";

try {
    // 1. Verificar que el modelo existe
    echo "1. Verificando modelo CentroMedico...\n";
    $centro = CentroMedico::find(1);
    if (!$centro) {
        echo "❌ No se encontró el centro médico con ID 1\n";
        exit(1);
    }
    echo "✅ Centro médico encontrado: " . $centro->nombre . "\n";

    // 2. Probar generar la ruta
    echo "\n2. Probando generación de ruta...\n";
    try {
        $route = route('centros_medicos.show', ['centros_medico' => $centro->id]);
        echo "✅ Ruta generada: " . $route . "\n";
    } catch (Exception $e) {
        echo "❌ Error generando ruta: " . $e->getMessage() . "\n";
    }

    // 3. Probar con el objeto completo
    echo "\n3. Probando con objeto completo...\n";
    try {
        $route2 = route('centros_medicos.show', $centro);
        echo "✅ Ruta con objeto: " . $route2 . "\n";
    } catch (Exception $e) {
        echo "❌ Error con objeto: " . $e->getMessage() . "\n";
    }

    // 4. Probar con solo ID
    echo "\n4. Probando con solo ID...\n";
    try {
        $route3 = route('centros_medicos.show', $centro->id);
        echo "✅ Ruta con ID: " . $route3 . "\n";
    } catch (Exception $e) {
        echo "❌ Error con ID: " . $e->getMessage() . "\n";
    }

    // 5. Listar todas las rutas disponibles
    echo "\n5. Rutas de centros médicos disponibles:\n";
    $routes = app('router')->getRoutes();
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'centros_medicos') !== false) {
            echo "   - " . $route->uri() . " [" . $route->getName() . "]\n";
        }
    }

    echo "\n=== Prueba completada ===\n";
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
