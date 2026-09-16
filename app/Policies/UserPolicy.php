<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Voir la liste des utilisateurs.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Voir un utilisateur.
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin'
            || $user->id === $model->id;
    }

    /**
     * Créer un utilisateur.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Modifier un utilisateur.
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin'
            || $user->id === $model->id;
    }

    /**
     * Supprimer un utilisateur.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->role === 'admin'
            && $user->id !== $model->id;
    }

    /**
     * Restaurer un utilisateur.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Supprimer définitivement un utilisateur.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
}
