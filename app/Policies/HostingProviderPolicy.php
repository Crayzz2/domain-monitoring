<?php

namespace App\Policies;

use App\Models\HostingProviders;
use App\Models\User;

class HostingProviderPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Provedor de Hospedagem', 'Editar Provedor de Hospedagem']);
    }

    public function view($user, HostingProviders $item): bool
    {
        return $user->hasAnyPermission(['Listar Provedor de Hospedagem', 'Editar Provedor de Hospedagem']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Provedor de Hospedagem');
    }

    public function update($user, HostingProviders $item): bool
    {
        return $user->hasPermissionTo('Editar Provedor de Hospedagem');
    }

    public function delete($user, HostingProviders $item): bool
    {
        return $user->hasPermissionTo('Excluir Provedor de Hospedagem');
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
