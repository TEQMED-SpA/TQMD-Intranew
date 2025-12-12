<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;

use App\Models\Equipo;
use App\Models\Cliente;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CentroMedicoController;
use App\Http\Controllers\CategoriaRepuestoController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\LlamadoController;
use App\Http\Controllers\CategoriaLlamadoController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PrivilegioController;
use App\Http\Controllers\InventarioTecnicoController;
use App\Http\Controllers\InformesController;
use App\Http\Controllers\InformePreventivoController;


// ---------------------------------------------------------
// Redirección inicial
// ---------------------------------------------------------
Route::redirect('/', '/login')->name('home');

// ---------------------------------------------------------
// Dashboard
// ---------------------------------------------------------
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'privilege:ver_dashboard'])
    ->name('dashboard');

Route::get('dashboard/charts/equipos-estado', function (Request $request) {
    $clienteId = $request->integer('cliente_id');

    $estadosValidos = ['Operativo', 'En observacion', 'Fuera de servicio', 'Baja'];
    $estadoMeta = [
        'Operativo' => 'En funcionamiento',
        'En observacion' => 'En revisión',
        'Fuera de servicio' => 'Fuera de servicio',
        'Baja' => 'Dados de baja',
    ];
    $estadoColorHex = [
        'Operativo' => '#10b981',
        'En observacion' => '#f59e0b',
        'Fuera de servicio' => '#ef4444',
        'Baja' => '#64748b',
    ];

    $totales = Equipo::select('estado', DB::raw('COUNT(*) as total'))
        ->whereIn('estado', $estadosValidos)
        ->when(
            $clienteId,
            fn($q) => $q->whereHas('centro', fn($w) => $w->where('cliente_id', $clienteId)),
        )
        ->groupBy('estado')
        ->pluck('total', 'estado');

    $payload = [
        'keys' => $estadosValidos,
        'labels' => collect($estadosValidos)->map(fn($estado) => $estadoMeta[$estado] ?? $estado)->toArray(),
        'values' => collect($estadosValidos)->map(fn($estado) => $totales[$estado] ?? 0)->toArray(),
        'colors' => collect($estadosValidos)->map(fn($estado) => $estadoColorHex[$estado] ?? '#94a3b8')->toArray(),
    ];
    $payload['total'] = array_sum($payload['values']);
    $payload['cliente'] = $clienteId ? optional(Cliente::find($clienteId))->nombre : null;

    return response()->json($payload);
})->middleware(['auth', 'verified', 'privilege:ver_dashboard'])->name('dashboard.charts.equipos-estado');

// ---------------------------------------------------------
// PATRONES GLOBALES (evitan choques tipo /recurso/create vs /recurso/{id})
// ---------------------------------------------------------
Route::pattern('user', '[0-9]+');
Route::pattern('cliente', '[0-9]+');
Route::pattern('centros_medico', '[0-9]+'); // nombre del parámetro singular de resource('centros_medicos', ...)
Route::pattern('equipo', '[0-9]+');
Route::pattern('repuesto', '[0-9]+');
Route::pattern('categoria', '[0-9]+');
Route::pattern('ticket', '[0-9]+');
Route::pattern('salida', '[0-9]+');
Route::pattern('llamado', '[0-9]+');
Route::pattern('categoria_llamado', '[0-9]+');
Route::pattern('solicitud', '[0-9]+');

// ---------------------------------------------------------
// ZONA PRIVADA
// ---------------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // Settings
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Endpoints JSON (selects dependientes)
    Route::get('/api/clientes/{cliente}/centros', [\App\Http\Controllers\Api\LookupController::class, 'centrosPorCliente'])
        ->whereNumber('cliente')
        ->name('api.clientes.centros');

    Route::get('/api/centros/{centro}/equipos', [\App\Http\Controllers\Api\LookupController::class, 'equiposPorCentro'])
        ->whereNumber('centro')
        ->name('api.centros.equipos');

    // -----------------------------------------------------
    // Roles y Privilegios
    // -----------------------------------------------------
    Route::resource('roles', RoleController::class)
        ->only(['index', 'show'])
        ->middleware('privilege:ver_roles');

    Route::resource('roles', RoleController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('privilege:editar_roles');

    Route::resource('privilegios', PrivilegioController::class)
        ->only(['index', 'show'])
        ->middleware('privilege:ver_privilegios');

    Route::resource('privilegios', PrivilegioController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('privilege:editar_privilegios');

    // -----------------------------------------------------
    // Usuarios (solo admin)
    // -----------------------------------------------------
    Route::resource('users', UserController::class)
        ->only(['index', 'show'])
        ->middleware('privilege:ver_usuarios');

    Route::resource('users', UserController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('privilege:editar_usuarios');

    // -----------------------------------------------------
    // Clientes (separado por privilegio de edición)
    // -----------------------------------------------------
    // Acciones de escritura (primero)
    Route::resource('clientes', ClienteController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware(['privilege:editar_clientes']);

    // Lectura (después)
    Route::resource('clientes', ClienteController::class)
        ->only(['index', 'show'])
        ->middleware(['privilege:ver_clientes']);

    // -----------------------------------------------------
    // Categorías de repuestos
    // -----------------------------------------------------
    Route::resource('categorias', CategoriaRepuestoController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware(['privilege:editar_categorias_repuestos']);

    Route::resource('categorias', CategoriaRepuestoController::class)
        ->only(['index', 'show'])
        ->middleware(['privilege:ver_categorias_repuestos']);

    // AJAX categorías (crear rápida)
    Route::post('/categorias/ajax-store', [CategoriaRepuestoController::class, 'ajaxStore'])
        ->name('categorias.ajax-store')
        ->middleware(['privilege:editar_categorias_repuestos']);

    // -----------------------------------------------------
    // Repuestos
    // -----------------------------------------------------
    Route::resource('repuestos', RepuestoController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware(['privilege:editar_repuestos']);

    Route::resource('repuestos', RepuestoController::class)
        ->only(['index', 'show'])
        ->middleware(['privilege:ver_repuestos']);

    Route::get('repuestos-baja', [RepuestoController::class, 'baja'])
        ->name('repuestos.baja')
        ->middleware(['privilege:ver_repuestos']);

    // -----------------------------------------------------
    // Solicitudes
    // -----------------------------------------------------
    Route::prefix('solicitudes')->name('solicitudes.')->group(function () {
        // Crear
        Route::get('/create', [SolicitudController::class, 'create'])
            ->name('create')
            ->middleware('privilege:crear_solicitudes');

        Route::post('/', [SolicitudController::class, 'store'])
            ->name('store')
            ->middleware('privilege:crear_solicitudes');

        // Listado / Detalle
        Route::get('/', [SolicitudController::class, 'index'])
            ->name('index')
            ->middleware('privilege:ver_solicitudes');

        Route::get('/{solicitud}', [SolicitudController::class, 'show'])
            ->whereNumber('solicitud')
            ->name('show')
            ->middleware('privilege:ver_solicitudes');

        // Aprobaciones
        Route::put('/{solicitud}/aprobar', [SolicitudController::class, 'aprobar'])
            ->whereNumber('solicitud')
            ->name('aprobar')
            ->middleware('privilege:aprobar_solicitudes');

        Route::put('/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])
            ->whereNumber('solicitud')
            ->name('rechazar')
            ->middleware('privilege:aprobar_solicitudes');

        // Entregar ítem de solicitud
        Route::post('/{solicitud}/repuestos/{repuesto}/entregar', [SalidaController::class, 'entregarItemDeSolicitud'])
            ->whereNumber('solicitud')
            ->whereNumber('repuesto')
            ->name('repuestos.entregar')
            ->middleware('privilege:aprobar_solicitudes');
    });

    // -----------------------------------------------------
    // Salidas
    // -----------------------------------------------------
    Route::resource('salidas', SalidaController::class)
        ->only(['index'])
        ->middleware('privilege:ver_repuestos');

    // -----------------------------------------------------
    // Inventario Técnico
    // -----------------------------------------------------
    Route::prefix('inventario-tecnico')->name('invtecnico.')->group(function () {
        Route::get('/', [InventarioTecnicoController::class, 'index'])
            ->name('index')
            ->middleware('privilege:ver_inventario_tecnico');

        Route::post('/{item}/devolver', [InventarioTecnicoController::class, 'devolver'])
            ->whereNumber('item')
            ->name('devolver')
            ->middleware('privilege:gestionar_inventario_tecnico');
    });

    // -----------------------------------------------------
    // Equipos (máquinas)
    // -----------------------------------------------------
    // Escritura (primero) para evitar choque con {equipo}
    Route::middleware(['role:admin|auditor|tecnico'])->group(function () {
        Route::get('/equipos/create', [EquipoController::class, 'create'])->name('equipos.create');
        Route::post('/equipos',       [EquipoController::class, 'store'])->name('equipos.store');
    });

    // Lectura
    Route::get('/equipos', [EquipoController::class, 'index'])->name('equipos.index');

    Route::get('/equipos/{equipo}', [EquipoController::class, 'show'])
        ->whereNumber('equipo')
        ->name('equipos.show');

    // Edición/Eliminación
    Route::middleware(['role:admin|auditor'])->group(function () {
        Route::get('/equipos/{equipo}/edit',  [EquipoController::class, 'edit'])->whereNumber('equipo')->name('equipos.edit');
        Route::put('/equipos/{equipo}',       [EquipoController::class, 'update'])->whereNumber('equipo')->name('equipos.update');
        Route::delete('/equipos/{equipo}',    [EquipoController::class, 'destroy'])->whereNumber('equipo')->name('equipos.destroy');
    });

    // -----------------------------------------------------
    // Tickets (sin create/store)
    // -----------------------------------------------------
    Route::resource('tickets', TicketController::class)
        ->only(['index', 'show'])
        ->middleware('privilege:ver_tickets');

    Route::resource('tickets', TicketController::class)
        ->only(['edit', 'update', 'destroy'])
        ->middleware('privilege:editar_tickets');

    // -----------------------------------------------------
    // Llamados y categorías de llamados
    // -----------------------------------------------------
    Route::resource('llamados', LlamadoController::class)
        ->only(['index', 'show'])
        ->middleware('privilege:ver_llamados');

    Route::resource('llamados', LlamadoController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('privilege:editar_llamados');

    Route::resource('categoria_llamados', CategoriaLlamadoController::class)
        ->only(['index', 'show'])
        ->middleware('privilege:ver_llamados');

    Route::resource('categoria_llamados', CategoriaLlamadoController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('privilege:editar_llamados');
    Route::get('get-all-categorias', [CategoriaLlamadoController::class, 'getAllCategorias'])
        ->name('get.all.categorias');

    // -----------------------------------------------------
    // Centros Médicos (separado por privilegios) - OJO al orden
    // -----------------------------------------------------
    Route::resource('centros_medicos', CentroMedicoController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware(['privilege:editar_centros_medicos']);

    Route::resource('centros_medicos', CentroMedicoController::class)
        ->only(['index', 'show'])
        ->middleware(['privilege:ver_centros_medicos']);

    // -----------------------------------------------------
    // Informes
    // -----------------------------------------------------

    Route::get('/informes', [InformesController::class, 'index'])
        ->name('informes.index')
        ->middleware('privilege:ver_reportes');

    Route::get('/informes/create', [InformesController::class, 'create'])
        ->name('informes.create')
        ->middleware('privilege:ver_reportes');

    // Correctivo
    Route::post('/informes/correctivo', [InformesController::class, 'storeCorrectivo'])
        ->name('informes.correctivo.store')
        ->middleware('privilege:ver_reportes');

    Route::get('/informes/correctivo/{id}', [InformesController::class, 'showCorrectivo'])
        ->name('informes.correctivo.show')
        ->middleware('privilege:ver_reportes');

    // Preventivo (nuevo flujo)
    Route::prefix('informes/preventivos')->name('informes.preventivos.')->middleware('privilege:ver_reportes')->group(function () {
        Route::get('/', function () {
            return redirect()->route('informes.preventivos.select-tipo');
        })->name('index');

        Route::get('/nuevo', [InformePreventivoController::class, 'selectTipo'])
            ->name('select-tipo');

        Route::get('/crear/{tipoInformePreventivo}', [InformePreventivoController::class, 'create'])
            ->whereNumber('tipoInformePreventivo')
            ->name('create');

        Route::post('/', [InformePreventivoController::class, 'store'])
            ->name('store');
    });

    Route::post('/informes/preventivo', [InformesController::class, 'storePreventivo'])
        ->name('informes.preventivo.store')
        ->middleware('privilege:ver_reportes');

    Route::get('/informes/preventivo/{id}', [InformesController::class, 'showPreventivo'])
        ->name('informes.preventivo.show')
        ->middleware('privilege:ver_reportes');

    // ---------- RUTAS UNIFICADAS PARA PDF ----------
    // tipo = 'correctivo' o 'preventivo'
    Route::get('/informes/{tipo}/{id}/download', [InformesController::class, 'downloadPdf'])
        ->name('informes.download')
        ->middleware('privilege:ver_reportes');

    Route::get('/informes/{tipo}/{id}/print', [InformesController::class, 'printPdf'])
        ->name('informes.print')
        ->middleware('privilege:ver_reportes');

    Route::get('/clientes/{cliente}/centros', [CentroMedicoController::class, 'porCliente'])
        ->whereNumber('cliente')
        ->name('clientes.centros');
    Route::get('/centros-medicos/{centro}/equipos', [EquipoController::class, 'porCentro'])
        ->name('centros-medicos.equipos');
    Route::get('/equipos/{equipo}/horas-uso', [EquipoController::class, 'horasUso'])
        ->name('equipos.horas-uso');
    Route::get('/clientes/{cliente}/centros', [CentroMedicoController::class, 'porCliente'])
        ->name('clientes.centros');
    Route::get('/centros/{centro}/equipos', [EquipoController::class, 'porCentro'])
        ->name('centros.equipos');
});



// ---------------------------------------------------------
// Auth scaffolding
// ---------------------------------------------------------
require __DIR__ . '/auth.php';
