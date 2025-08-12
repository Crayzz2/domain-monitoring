<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;

class StatusPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Status', 'Editar Status']);
    }

    public function view($user, Status $item): bool
    {
        return $user->hasAnyPermission(['Listar Status', 'Editar Status']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Status');
    }

    public function update($user, Status $item): bool
    {
        return $user->hasPermissionTo('Editar Status');
    }

    public function delete($user, Status $item): bool
    {
        return $user->hasPermissionTo('Excluir Status');
    }

    public function restore($user, Status $item): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function forceDelete($user, Status $item): bool
    {
        return $user->hasRole('Super Admin');
    }
}
