<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CentroMedicoController;
use App\Http\Controllers\CategoriaRepuestoController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\LlamadoController;
use App\Http\Controllers\CategoriaLlamadoController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PrivilegioController;

Route::redirect('/', '/login')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Settings
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Roles y Privilegios: solo admin, conservando nombres roles.* y privilegios.*
    Route::resource('roles', RoleController::class)->except(['show'])->middleware(['role:admin']);
    Route::resource('privilegios', PrivilegioController::class)->except(['show'])->middleware(['role:admin']);

    // USERS: SOLO ADMIN
    Route::resource('users', \App\Http\Controllers\UserController::class)
        ->middleware(['role:admin']);
    // CLIENTES y CENTROS (ajusta si también deben ser solo admin)
    Route::resource('clientes', ClienteController::class);
    Route::resource('centros', CentroMedicoController::class);

    // CATEGORÍAS DE REPUESTOS
    Route::resource('categorias', CategoriaRepuestoController::class)
        ->only(['index', 'show'])
        ->middleware(['privilege:ver_repuestos']);
    Route::resource('categorias', CategoriaRepuestoController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware(['privilege:editar_repuestos']);

    // REPUESTOS
    Route::resource('repuestos', RepuestoController::class)
        ->only(['index', 'show'])
        ->middleware(['privilege:ver_repuestos']);
    Route::resource('repuestos', RepuestoController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware(['privilege:editar_repuestos']);

    // SOLICITUDES
    Route::resource('solicitudes', SolicitudController::class)
        ->only(['index', 'show'])
        ->middleware(['privilege:ver_solicitudes']);
    Route::resource('solicitudes', SolicitudController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware(['privilege:aprobar_solicitudes']);

    // SALIDAS
    Route::resource('salidas', SalidaController::class);

    // TICKETS (sin create/store)
    Route::resource('tickets', TicketController::class)->except(['create', 'store']);

    // AJAX categorías (crear rápida)
    Route::post('/categorias/ajax-store', [CategoriaRepuestoController::class, 'ajaxStore'])
        ->name('categorias.ajax-store')
        ->middleware(['privilege:editar_repuestos']);

    // LLAMADOS y CATEGORÍAS DE LLAMADOS
    Route::resource('llamados', LlamadoController::class);
    Route::resource('categoria_llamados', CategoriaLlamadoController::class);
    Route::get('get-all-categorias', [CategoriaLlamadoController::class, 'getAllCategorias'])
        ->name('get.all.categorias');
});

require __DIR__ . '/auth.php';
