<?php
// Script minimal para probar solo la vista show sin layouts
require_once __DIR__ . '/vendor/autoload.php';

// Inicializar aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Prueba Minimal Vista Show ===\n\n";

try {
    // Extraer solo el contenido principal de la vista show
    $viewPath = resource_path('views/centros_medicos/show.blade.php');
    $viewContent = file_get_contents($viewPath);

    // Eliminar el layout y dejar solo el contenido
    $contentStart = strpos($viewContent, '<x-layouts.app');
    if ($contentStart !== false) {
        $layoutEnd = strpos($viewContent, '>', $contentStart);
        $contentAfterLayout = substr($viewContent, $layoutEnd + 1);

        // Encontrar el cierre del layout
        $layoutClose = strrpos($contentAfterLayout, '</x-layouts.app>');
        if ($layoutClose !== false) {
            $mainContent = substr($contentAfterLayout, 0, $layoutClose);
        } else {
            $mainContent = $contentAfterLayout;
        }
    } else {
        $mainContent = $viewContent;
    }

    // Crear datos simulados
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

    // Datos para la paginación
    $equiposData = [
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
        ]
    ];

    $equipos = new Illuminate\Pagination\LengthAwarePaginator(
        $equiposData,
        1, // total
        10, // per page
        1, // current page
        ['path' => url('/centros_medicos/1')]
    );

    $tipos_equipo = [(object)['id' => 1, 'nombre' => 'Tipo 1']];
    $estadoOpciones = ['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'];
    $equiposTotal = 1;
    $solicitudesTotal = 5;
    $solicitudesPendientes = 1;

    // Compilar Blade manualmente
    $compiled = Illuminate\Support\Facades\Blade::compileString($mainContent);

    // Reemplazar variables PHP
    $compiled = str_replace([
        '<?php echo e($nombreCentro); ?>',
        '<?php echo e($centroMedico->nombre ?? \'—\'); ?>',
        '<?php echo e($centroMedico->telefono ?? \'—\'); ?>',
        '<?php echo e($centroMedico->centro_dialisis ?? \'—\'); ?>',
        '<?php echo e($centroMedico->direccion ?? \'—\'); ?>',
        '<?php echo e($centroMedico->ciudad ?? \'—\'); ?>',
        '<?php echo e($centroMedico->region ?? \'—\'); ?>',
        '<?php echo e($centroMedico->cod_cliente ?? \'—\'); ?>',
        '<?php echo e($codigoCentro ?? \'—\'); ?>',
        '<?php echo e($centroMedico->cliente->nombre ?? \'Sin cliente\'); ?>',
        '<?php echo e(number_format($equiposTotal ?? 0)); ?>',
        '<?php echo e(number_format($solicitudesTotal ?? 0)); ?>',
        '<?php echo e(number_format($solicitudesPendientes ?? 0)); ?>',
    ], [
        $centroMedico->nombre ?? ($centroMedico->centro_dialisis ?? 'Centro Médico'),
        $centroMedico->nombre ?? '—',
        $centroMedico->telefono ?? '—',
        $centroMedico->centro_dialisis ?? '—',
        $centroMedico->direccion ?? '—',
        $centroMedico->ciudad ?? '—',
        $centroMedico->region ?? '—',
        $centroMedico->cod_cliente ?? '—',
        $centroMedico->cod_centro_medico ?? '—',
        $centroMedico->cliente->nombre ?? 'Sin cliente',
        number_format($equiposTotal ?? 0),
        number_format($solicitudesTotal ?? 0),
        number_format($solicitudesPendientes ?? 0),
    ], $compiled);

    // Evaluar el código PHP compilado
    ob_start();
    eval('?>' . $compiled);
    $output = ob_get_clean();

    echo "✅ Vista procesada exitosamente\n\n";

    // Verificar contenido clave
    $checks = [
        'Centro Médico de Prueba' => 'Nombre del centro',
        'Dirección de prueba 123' => 'Dirección',
        'Santiago' => 'Ciudad',
        'Metropolitana' => 'Región',
        'Cliente de Prueba' => 'Nombre del cliente',
        'EQ001' => 'Código de equipo',
        'Equipo 1' => 'Nombre del equipo'
    ];

    echo "Verificación de contenido:\n";
    foreach ($checks as $text => $description) {
        if (strpos($output, $text) !== false) {
            echo "✅ $description: Encontrado\n";
        } else {
            echo "❌ $description: NO encontrado\n";
        }
    }

    // Guardar resultado
    file_put_contents(__DIR__ . '/test_show_minimal_output.html', $output);
    echo "\n📄 HTML guardado en: test_show_minimal_output.html\n";

    echo "\n=== Prueba completada ===\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
}
