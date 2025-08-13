<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Cliente', 'Editar Cliente']) || $user->hasAnyRole(['Listar', 'Editar']);
    }

    public function view($user, Client $item): bool
    {
        return $user->hasAnyPermission(['Listar Cliente', 'Editar Cliente']) || $user->hasAnyRole(['Listar', 'Editar']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Cliente') || $user->hasRole('Editar');
    }

    public function update($user, Client $item): bool
    {
        return $user->hasPermissionTo('Editar Cliente') || $user->hasRole('Editar');
    }

    public function delete($user, Client $item): bool
    {
        return $user->hasPermissionTo('Excluir Cliente') || $user->hasRole('Excluir');
    }

    public function restore($user, Client $item): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function forceDelete($user, Client $item): bool
    {
        return $user->hasRole('Super Admin');
    }
}
