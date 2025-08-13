<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Usuário', 'Editar Usuário']) || $user->hasAnyRole(['Listar', 'Editar']);
    }

    public function view($user, User $item): bool
    {
        return $user->hasAnyPermission(['Listar Usuário', 'Editar Usuário']) || $user->hasAnyRole(['Listar', 'Editar']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Usuário') || $user->hasRole('Editar');
    }

    public function update($user, User $item): bool
    {
        return $user->hasPermissionTo('Editar Usuário') || $user->hasRole('Editar');
    }

    public function delete($user, User $item): bool
    {
        return $user->hasPermissionTo('Excluir Usuário') || $user->hasRole('Excluir');
    }

    public function restore($user, User $item): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function forceDelete($user, User $item): bool
    {
        return $user->hasRole('Super Admin');
    }
}
