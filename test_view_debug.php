<?php
// Script para probar la vista edit sin conexión a BD
require_once __DIR__ . '/vendor/autoload.php';

// Inicializar aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Prueba de Vista Edit (sin BD) ===\n\n";

try {
    // 1. Crear un objeto simulado de CentroMedico
    echo "1. Creando objeto simulado de CentroMedico...\n";

    $centroMedico = new stdClass();
    $centroMedico->id = 1;
    $centroMedico->nombre = 'Centro de Prueba';
    $centroMedico->centro_dialisis = 'Centro Dialisis Prueba';
    $centroMedico->direccion = 'Dirección de prueba';
    $centroMedico->telefono = '123456789';
    $centroMedico->ciudad = 'Ciudad de prueba';
    $centroMedico->region = 'Región de prueba';
    $centroMedico->cod_cliente = 'CLIENTE001';
    $centroMedico->cod_centro_medico = 'CENTRO001';
    $centroMedico->activo = true;
    $centroMedico->cliente_id = 1;

    echo "✅ Objeto simulado creado\n";

    // 2. Crear array de clientes simulados
    echo "\n2. Creando clientes simulados...\n";

    $clientes = [
        (object)['id' => 1, 'nombre' => 'Cliente 1'],
        (object)['id' => 2, 'nombre' => 'Cliente 2'],
    ];

    echo "✅ Clientes simulados creados\n";

    // 3. Probar compilar la vista con datos simulados
    echo "\n3. Probando compilación de la vista...\n";

    try {
        $viewContent = view('centros_medicos.edit', [
            'centroMedico' => $centroMedico,
            'clientes' => $clientes
        ])->render();

        echo "✅ Vista compilada exitosamente\n";

        // 4. Buscar la ruta generada en el contenido
        echo "\n4. Analizando contenido de la vista...\n";

        if (strpos($viewContent, 'route(\'centros_medicos.show\'') !== false) {
            echo "✅ Se encontró la función route() en la vista\n";

            // Extraer la línea que contiene la ruta
            $lines = explode("\n", $viewContent);
            foreach ($lines as $lineNum => $line) {
                if (strpos($line, 'route(\'centros_medicos.show\'') !== false) {
                    echo "   Línea " . ($lineNum + 1) . ": " . trim($line) . "\n";
                }
            }
        } else {
            echo "❌ No se encontró la función route() en la vista\n";
        }

        // 5. Verificar si hay errores de sintaxis
        echo "\n5. Verificando sintaxis de Blade...\n";

        // Buscar patrones problemáticos
        $patterns = [
            'route(\'centros_medicos.show\', $centroMedico)' => 'Objeto completo',
            'route(\'centros_medicos.show\', $centroMedico->id)' => 'Solo ID',
            'route(\'centros_medicos.show\', [\'centros_medico\' => $centroMedico->id])' => 'Array asociativo',
        ];

        foreach ($patterns as $pattern => $description) {
            if (strpos($viewContent, $pattern) !== false) {
                echo "✅ Encontrado: " . $description . "\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error compilando vista: " . $e->getMessage() . "\n";
        echo "   Error en línea: " . $e->getLine() . "\n";
        echo "   Archivo: " . $e->getFile() . "\n";
    }

    echo "\n=== Prueba completada ===\n";
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
