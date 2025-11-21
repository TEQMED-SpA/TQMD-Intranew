<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Laragear\WebAuthn\Contracts\WebAuthnAuthenticatable;
use Laragear\WebAuthn\WebAuthnAuthentication;

class User extends Authenticatable implements WebAuthnAuthenticatable
{
    use HasFactory, Notifiable, WebAuthnAuthentication;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
        'avatar',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        // puedes ocultar también secretos 2FA si quieres:
        // 'two_factor_secret',
        // 'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'two_factor_confirmed_at' => 'datetime', // importante para 2FA
        ];
    }

    // ==========================
    // 2FA helpers
    // ==========================

    /**
     * Indica si el usuario tiene 2FA activado.
     * Usa la columna two_factor_confirmed_at de la tabla users.
     */

    public function hasTwoFactorEnabled(): bool
    {
        return ! is_null($this->two_factor_secret)
            && ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Marca el 2FA como verificado (cuando el código TOTP fue correcto).
     */
    public function markTwoFactorAsVerified(): void
    {
        $this->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();
    }

    // (Opcional) Por si alguna vez quieres saber si está pendiente de confirmar
    public function isTwoFactorPending(): bool
    {
        return ! is_null($this->two_factor_secret)
            && is_null($this->two_factor_confirmed_at);
    }

    // ==========================
    // Roles / privilegios
    // ==========================

    // Relación principal con Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    // Métodos para verificar privilegios
    public function tienePrivilegio($privilegios)
    {
        $privilegios = is_array($privilegios) ? $privilegios : [$privilegios];
        $userPrivs = $this->role ? $this->role->privilegios->pluck('nombre')->toArray() : [];
        $intersect = array_intersect($userPrivs, $privilegios);

        Log::info('TienePrivilegio', [
            'user_id'    => $this->id,
            'user_name'  => $this->name,
            'user_role'  => $this->role ? $this->role->nombre : null,
            'user_privs' => $userPrivs,
            'checking'   => $privilegios,
            'intersect'  => $intersect,
            'result'     => count($intersect) > 0,
        ]);

        return count($intersect) > 0;
    }

    public function esAdmin()
    {
        return $this->role && $this->role->nombre === 'admin';
    }

    public function esTecnico()
    {
        return $this->role && $this->role->nombre === 'tecnico';
    }

    // ==========================
    // Otros helpers
    // ==========================

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function passkeys()
    {
        return $this->hasMany(\App\Models\Passkey::class);
    }
}
