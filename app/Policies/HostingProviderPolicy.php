<?php

namespace App\Policies;

use App\Models\HostingProviders;
use App\Models\User;

class HostingProviderPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Provedor de Hospedagem', 'Editar Provedor de Hospedagem']) || $user->hasAnyRole(['Listar', 'Editar']);
    }

    public function view($user, HostingProviders $item): bool
    {
        return $user->hasAnyPermission(['Listar Provedor de Hospedagem', 'Editar Provedor de Hospedagem']) || $user->hasAnyRole(['Listar', 'Editar']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Provedor de Hospedagem') || $user->hasRole('Editar');
    }

    public function update($user, HostingProviders $item): bool
    {
        return $user->hasPermissionTo('Editar Provedor de Hospedagem') || $user->hasRole('Editar');
    }

    public function delete($user, HostingProviders $item): bool
    {
        return $user->hasPermissionTo('Excluir Provedor de Hospedagem') || $user->hasRole('Excluir');
    }

    public function restore($user, HostingProviders $item): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function forceDelete($user, HostingProviders $item): bool
    {
        return $user->hasRole('Super Admin');
    }
}
