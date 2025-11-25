<?php

namespace App\Models;

use App\Models\Concerns\HasRolesAndPrivileges;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

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

        \Log::info('TienePrivilegio', [
            'user_id' => $this->id,
            'user_name' => $this->name,
            'user_role' => $this->role ? $this->role->nombre : null,
            'user_privs' => $userPrivs,
            'checking' => $privilegios,
            'intersect' => $intersect,
            'result' => count($intersect) > 0,
        ]);

        return count($intersect) > 0;
    }

    public function esAdmin()
    {
        return $this->isAdmin();
    }

    public function esTecnico()
    {
        return $this->role && $this->role->nombre === 'tecnico';
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
