<?php

namespace App\Policies;

use App\Models\Hosting;
use App\Models\User;

class HostingPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Hospedagem', 'Editar Hospedagem']) || $user->hasAnyRole(['Listar', 'Editar']);
    }

    public function view($user, Hosting $item): bool
    {
        return $user->hasAnyPermission(['Listar Hospedagem', 'Editar Hospedagem']) || $user->hasAnyRole(['Listar', 'Editar']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Hospedagem') || $user->hasRole('Editar');
    }

    public function update($user, Hosting $item): bool
    {
        return $user->hasPermissionTo('Editar Hospedagem') || $user->hasRole('Editar');
    }

    public function delete($user, Hosting $item): bool
    {
        return $user->hasPermissionTo('Excluir Hospedagem') || $user->hasRole('Excluir');
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
