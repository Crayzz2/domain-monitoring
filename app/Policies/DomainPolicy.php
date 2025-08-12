<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;

class DomainPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Domínio', 'Editar Domínio']);
    }

    public function view($user, Domain $item): bool
    {
        return $user->hasAnyPermission(['Listar Domínio', 'Editar Domínio']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Domínio');
    }

    public function update($user, Domain $item): bool
    {
        return $user->hasPermissionTo('Editar Domínio');
    }

    public function delete($user, Domain $item): bool
    {
        return $user->hasPermissionTo('Excluir Domínio');
    }

    public function restore($user, Domain $item): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function forceDelete($user, Domain $item): bool
    {
        return $user->hasRole('Super Admin');
    }
}
