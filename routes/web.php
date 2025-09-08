<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CentroMedicoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PrivilegioController;

Route::redirect('/', '/login')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // RUTAS RESOURCE DE MÓDULOS PRINCIPALES
    Route::resource('users', UserController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('centros', CentroMedicoController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('repuestos', RepuestoController::class);
    Route::resource('solicitudes', SolicitudController::class);
    Route::resource('salidas', SalidaController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('privilegios', PrivilegioController::class);
});

Route::post('/categorias/ajax-store', [CategoriaController::class, 'ajaxStore'])->name('categorias.ajax-store');

require __DIR__ . '/auth.php';
