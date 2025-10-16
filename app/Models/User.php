<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Concerns\HasRolesAndPrivileges;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPrivileges;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
        'avatar',
        'activo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean'
        ];
    }

    // Relación alternativa usada en vistas/controladores existentes
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    // Proxies compatibles en español (usan el trait)
    public function tienePrivilegio($privilegio)
    {
        return $this->hasPrivilege($privilegio);
    }

    public function esAdmin()
    {
        return $this->hasRole('admin');
    }

    public function esTecnico()
    {
        return $this->hasRole('tecnico');
    }

    // Métodos existentes
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
}
