<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
        'avatar',
        'estado'
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
            'estado' => 'boolean'
        ];
    }

    // Relaciones
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function repuestos()
    {
        return $this->hasMany(Producto::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'tecnico_id');
    }

    public function salidasPedidas()
    {
        return $this->hasMany(Salida::class, 'usuario_pedido_id');
    }

    public function salidasRequeridas()
    {
        return $this->hasMany(Salida::class, 'usuario_requiere_id');
    }

    public function historial()
    {
        return $this->hasMany(Historial::class);
    }

    public function historialSesiones()
    {
        #return $this->hasMany(HistorialSesion::class, 'id_usuario');
    }

    public function historialUsuarios()
    {
        #return $this->hasMany(HistorialUsuario::class);
    }

    // Métodos de verificación de roles
    public function tienePrivilegio($privilegio)
    {
        return $this->rol && $this->rol->tienePrivilegio($privilegio);
    }

    public function esAdmin()
    {
        return $this->rol && $this->rol->nombre === 'Administrador';
    }

    public function esTecnico()
    {
        return $this->rol && $this->rol->nombre === 'Técnico';
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
