<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Cliente', 'Editar Cliente']);
    }

    public function view($user, Client $item): bool
    {
        return $user->hasAnyPermission(['Listar Cliente', 'Editar Cliente']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Cliente');
    }

    public function update($user, Client $item): bool
    {
        return $user->hasPermissionTo('Editar Cliente');
    }

    public function delete($user, Client $item): bool
    {
        return $user->hasPermissionTo('Excluir Cliente');
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
