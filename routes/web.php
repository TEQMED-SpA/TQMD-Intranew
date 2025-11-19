<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Security;

use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\PasskeyLoginController;
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
use App\Http\Controllers\PasskeyController;


// ---------------------------------------------------------
// Redirección inicial
// ---------------------------------------------------------
Route::redirect('/', '/login')->name('home');

// ---------------------------------------------------------
// Dashboard
// ---------------------------------------------------------
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
Route::middleware(['auth', 'twofactor'])->group(function () {

    // Settings
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/security', Security::class)->name('settings.security');

    // Passkeys
    Route::post('/passkeys/options', [PasskeyController::class, 'options'])->name('passkeys.options');
    Route::post('/passkeys', [PasskeyController::class, 'store'])->name('passkeys.store');
    Route::get('/passkeys', [PasskeyController::class, 'index'])->name('passkeys.index');
    Route::delete('/passkeys/{passkey}', [PasskeyController::class, 'destroy'])->name('passkeys.destroy');

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
        ->middleware(['role:admin']);

    Route::resource('privilegios', PrivilegioController::class);

    // -----------------------------------------------------
    // Usuarios (solo admin)
    // -----------------------------------------------------
    Route::resource('users', UserController::class)
        ->middleware(['role:admin']);

    // -----------------------------------------------------
    // Clientes (separado por privilegio de edición)
    // -----------------------------------------------------
    // Acciones de escritura (primero)
    Route::resource('clientes', ClienteController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware(['privilege:editar_clientes']);

    // Lectura (después)
    Route::resource('clientes', ClienteController::class)
        ->only(['index', 'show']); // si tienes 'ver_clientes' puedes agregarlo aquí

    // -----------------------------------------------------
    // Categorías de repuestos
    // -----------------------------------------------------
    Route::resource('categorias', CategoriaRepuestoController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware(['privilege:editar_repuestos']);

    Route::resource('categorias', CategoriaRepuestoController::class)
        ->only(['index', 'show'])
        ->middleware(['privilege:ver_repuestos']);

    // AJAX categorías (crear rápida)
    Route::post('/categorias/ajax-store', [CategoriaRepuestoController::class, 'ajaxStore'])
        ->name('categorias.ajax-store')
        ->middleware(['privilege:editar_repuestos']);

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
    Route::resource('salidas', SalidaController::class);

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
    Route::resource('tickets', TicketController::class)->except(['create', 'store']);

    // -----------------------------------------------------
    // Llamados y categorías de llamados
    // -----------------------------------------------------
    Route::resource('llamados', LlamadoController::class);
    Route::resource('categoria_llamados', CategoriaLlamadoController::class);
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

    Route::get('/informes', [\App\Http\Controllers\InformesController::class, 'index'])->name('informes.index');

    Route::get('/informes/create', 'App\Http\Controllers\InformesController@create')
        ->name('informes.create');

    // Correctivo
    Route::post('/informes/correctivo', 'App\Http\Controllers\InformesController@storeCorrectivo')
        ->name('informes.correctivo.store');

    Route::get('/informes/correctivo/{id}', 'App\Http\Controllers\InformesController@showCorrectivo')
        ->name('informes.correctivo.show');

    // Preventivo
    Route::post('/informes/preventivo', 'App\Http\Controllers\InformesController@storePreventivo')
        ->name('informes.preventivo.store');

    Route::get('/informes/preventivo/{id}', 'App\Http\Controllers\InformesController@showPreventivo')
        ->name('informes.preventivo.show');

    // ---------- RUTAS UNIFICADAS PARA PDF ----------
    // tipo = 'correctivo' o 'preventivo'
    Route::get('/informes/{tipo}/{id}/download', 'App\Http\Controllers\InformesController@downloadPdf')
        ->name('informes.download');

    Route::get('/informes/{tipo}/{id}/print', 'App\Http\Controllers\InformesController@printPdf')
        ->name('informes.print');
    Route::get('/clientes/{cliente}/centros', [CentroMedicoController::class, 'porCliente']);
    Route::get('/centros-medicos/{centro}/equipos', [EquipoController::class, 'porCentro'])
        ->name('centros-medicos.equipos');
    Route::get('/equipos/{equipo}/horas-uso', [EquipoController::class, 'horasUso'])
        ->name('equipos.horas-uso');
    Route::get('/clientes/{cliente}/centros', [CentroMedicoController::class, 'porCliente'])
        ->name('clientes.centros');
    Route::get('/centros/{centro}/equipos', [EquipoController::class, 'porCentro'])
        ->name('centros.equipos');

    // Estas sí las vi (registro/listado/borrado), dentro de auth + twofactor
    Route::post('/passkeys/options', [PasskeyController::class, 'options'])->name('passkeys.options');
    Route::post('/passkeys', [PasskeyController::class, 'store'])->name('passkeys.store');
    Route::get('/passkeys', [PasskeyController::class, 'index'])->name('passkeys.index');
    Route::delete('/passkeys/{passkey}', [PasskeyController::class, 'destroy'])->name('passkeys.destroy');
});


Route::post('/passkeys/login/options', [PasskeyLoginController::class, 'options'])
    ->name('passkeys.login.options');

Route::post('/passkeys/login', [PasskeyLoginController::class, 'login'])
    ->name('passkeys.login');






// ---------------------------------------------------------
// Auth scaffolding
// ---------------------------------------------------------
require __DIR__ . '/auth.php';
