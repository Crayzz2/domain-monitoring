<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['Listar Usuário', 'Editar Usuário']);
    }

    public function view($user, User $item): bool
    {
        return $user->hasAnyPermission(['Listar Usuário', 'Editar Usuário']);
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('Editar Usuário');
    }

    public function update($user, User $item): bool
    {
        return $user->hasPermissionTo('Editar Usuário');
    }

    public function delete($user, User $item): bool
    {
        return $user->hasPermissionTo('Excluir Usuário');
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
