<?php

namespace App\Policies;

use App\Models\Hosting;
use App\Models\User;

class HostingPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Hospedagem', 'Editar Hospedagem']);
    }

    public function view($user, Hosting $item): bool
    {
        return $user->hasAnyPermission(['Listar Hospedagem', 'Editar Hospedagem']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Hospedagem');
    }

    public function update($user, Hosting $item): bool
    {
        return $user->hasPermissionTo('Editar Hospedagem');
    }

    public function delete($user, Hosting $item): bool
    {
        return $user->hasPermissionTo('Excluir Hospedagem');
    }

    public function restore($user, Hosting $item): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function forceDelete($user, Hosting $item): bool
    {
        return $user->hasRole('Super Admin');
    }
}
