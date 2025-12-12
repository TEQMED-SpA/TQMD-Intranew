<?php
// Script para depurar la vista show de centros médicos
require_once __DIR__ . '/vendor/autoload.php';

// Inicializar aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Depuración Vista Show Centros Médicos ===\n\n";

try {
    // 1. Verificar que la vista existe
    echo "1. Verificando existencia de la vista...\n";
    $viewPath = resource_path('views/centros_medicos/show.blade.php');
    if (file_exists($viewPath)) {
        echo "✅ Vista encontrada en: " . $viewPath . "\n";
    } else {
        echo "❌ Vista NO encontrada\n";
        exit(1);
    }

    // 2. Leer el contenido de la vista para analizar variables
    echo "\n2. Analizando variables usadas en la vista...\n";
    $viewContent = file_get_contents($viewPath);

    // Buscar variables Blade
    preg_match_all('/\{\{\s*\$(\w+)\s*(?:->|\.|\||\s)/', $viewContent, $matches);
    $variables = array_unique($matches[1]);

    echo "Variables encontradas:\n";
    foreach ($variables as $var) {
        echo "   - $" . $var . "\n";
    }

    // 3. Verificar el controlador
    echo "\n3. Verificando controlador...\n";
    $controllerPath = app_path('Http/Controllers/CentroMedicoController.php');
    if (file_exists($controllerPath)) {
        echo "✅ Controlador encontrado\n";

        // Extraer el método show
        $controllerContent = file_get_contents($controllerPath);
        if (strpos($controllerContent, 'public function show') !== false) {
            echo "✅ Método show() encontrado\n";

            // Analizar qué variables pasa el método show
            preg_match('/public function show\([^{]*\)\s*{([^}]*)return view/s', $controllerContent, $methodMatch);
            if (isset($methodMatch[1])) {
                echo "Variables que probablemente pasa el controlador:\n";
                if (strpos($methodMatch[1], 'centroMedico') !== false) {
                    echo "   - \$centroMedico\n";
                }
                if (strpos($methodMatch[1], 'equipos') !== false) {
                    echo "   - \$equipos\n";
                }
                if (strpos($methodMatch[1], 'equiposTotal') !== false) {
                    echo "   - \$equiposTotal\n";
                }
                if (strpos($methodMatch[1], 'solicitudesTotal') !== false) {
                    echo "   - \$solicitudesTotal\n";
                }
                if (strpos($methodMatch[1], 'solicitudesPendientes') !== false) {
                    echo "   - \$solicitudesPendientes\n";
                }
            }
        } else {
            echo "❌ Método show() NO encontrado\n";
        }
    } else {
        echo "❌ Controlador NO encontrado\n";
    }

    // 4. Crear datos simulados para probar la vista
    echo "\n4. Creando datos simulados...\n";

    $centroMedico = new stdClass();
    $centroMedico->id = 1;
    $centroMedico->nombre = 'Centro Médico de Prueba';
    $centroMedico->centro_dialisis = 'Centro Diálisis Prueba';
    $centroMedico->direccion = 'Dirección de prueba 123';
    $centroMedico->telefono = '+56 9 1234 5678';
    $centroMedico->ciudad = 'Santiago';
    $centroMedico->region = 'Metropolitana';
    $centroMedico->cod_cliente = 'CLIENTE001';
    $centroMedico->cod_centro_medico = 'CENTRO001';
    $centroMedico->activo = true;
    $centroMedico->cliente_id = 1;
    $centroMedico->cliente = (object)[
        'nombre' => 'Cliente de Prueba'
    ];
    $centroMedico->updated_at = now();

    // Crear paginador simulado
    $equipos = new Illuminate\Pagination\LengthAwarePaginator(
        [
            (object)[
                'id' => 1,
                'nombre' => 'Equipo 1',
                'codigo' => 'EQ001',
                'modelo' => 'Modelo X',
                'marca' => 'Marca A',
                'numero_serie' => 'SN123456',
                'tipo' => (object)['nombre' => 'Tipo 1'],
                'estado' => 'Operativo',
                'proxima_mantencion' => now()->addDays(60)
            ],
            (object)[
                'id' => 2,
                'nombre' => 'Equipo 2',
                'codigo' => 'EQ002',
                'modelo' => 'Modelo Y',
                'marca' => 'Marca B',
                'numero_serie' => 'SN789012',
                'tipo' => (object)['nombre' => 'Tipo 2'],
                'estado' => 'En observacion',
                'proxima_mantencion' => now()->subDays(10)
            ]
        ],
        2, // total
        1, // per page
        1, // current page
        ['path' => url('/centros_medicos/1')]
    );

    $tipos_equipo = [
        (object)['id' => 1, 'nombre' => 'Tipo 1'],
        (object)['id' => 2, 'nombre' => 'Tipo 2']
    ];

    $estadoOpciones = ['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'];
    $equiposTotal = 2;
    $solicitudesTotal = 5;
    $solicitudesPendientes = 1;

    echo "✅ Datos simulados creados\n";

    // 5. Probar renderizar la vista
    echo "\n5. Probando renderizar la vista...\n";

    try {
        $renderedView = view('centros_medicos.show', [
            'centroMedico' => $centroMedico,
            'equipos' => $equipos,
            'tipos_equipo' => $tipos_equipo,
            'estadoOpciones' => $estadoOpciones,
            'equiposTotal' => $equiposTotal,
            'solicitudesTotal' => $solicitudesTotal,
            'solicitudesPendientes' => $solicitudesPendientes,
        ])->render();

        echo "✅ Vista renderizada exitosamente\n";

        // 6. Analizar contenido renderizado
        echo "\n6. Analizando contenido renderizado...\n";

        $checks = [
            'Centro Médico de Prueba' => 'Nombre del centro',
            'Dirección de prueba 123' => 'Dirección',
            'Santiago' => 'Ciudad',
            'Metropolitana' => 'Región',
            'Equipo 1' => 'Primer equipo',
            'EQ001' => 'Código de equipo',
            'Operativo' => 'Estado de equipo',
            'Cliente de Prueba' => 'Nombre del cliente'
        ];

        foreach ($checks as $text => $description) {
            if (strpos($renderedView, $text) !== false) {
                echo "✅ $description: Encontrado\n";
            } else {
                echo "❌ $description: NO encontrado\n";
            }
        }

        // Guardar el HTML para inspección manual
        file_put_contents(__DIR__ . '/debug_show_output.html', $renderedView);
        echo "\n📄 HTML guardado en: debug_show_output.html\n";
    } catch (Exception $e) {
        echo "❌ Error renderizando vista: " . $e->getMessage() . "\n";
        echo "   Línea: " . $e->getLine() . "\n";
        echo "   Archivo: " . $e->getFile() . "\n";
    }

    echo "\n=== Depuración completada ===\n";
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
