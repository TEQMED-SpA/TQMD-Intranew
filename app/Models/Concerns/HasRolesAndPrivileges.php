<?php

namespace App\Models\Concerns;

use App\Models\Role;

trait HasRolesAndPrivileges
{
    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function hasRole(string|array $roles): bool
    {
        if (!$this->role) return false;

        $current = $this->role->nombre;

        if (is_array($roles)) {
            return in_array($current, $roles, true);
        }
        return $current === $roles;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function hasPrivilege(string $nombre): bool
    {
        if ($this->isAdmin()) return true;
        if (!$this->role) return false;

        return $this->role->privilegios()->where('nombre', $nombre)->exists();
    }

    public function hasAnyPrivilege(array $nombres): bool
    {
        if ($this->isAdmin()) return true;
        if (!$this->role) return false;

        return $this->role->privilegios()->whereIn('nombre', $nombres)->exists();
    }

    public function hasAllPrivileges(array $nombres): bool
    {
        if ($this->isAdmin()) return true;
        if (!$this->role) return false;

        $count = $this->role->privilegios()->whereIn('nombre', $nombres)->count();
        return $count === count($nombres);
    }
}
