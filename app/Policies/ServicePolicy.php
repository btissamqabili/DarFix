<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'prestataire';
    }

    public function view(User $user, Service $service): bool
    {
        return $service->prestataire_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'prestataire';
    }

    public function update(User $user, Service $service): bool
    {
        return $service->prestataire_id === $user->id;
    }

    public function delete(User $user, Service $service): bool
    {
        return $service->prestataire_id === $user->id;
    }
}
