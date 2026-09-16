<?php

namespace App\Policies;

use App\Models\Mission;
use App\Models\User;

class MissionPolicy
{
    /**
     * Determine whether the user can update the mission.
     */
    public function update(User $user, Mission $mission): bool
    {
        return $mission->client_id === $user->id;
    }

    /**
     * Determine whether the user can delete the mission.
     */
    public function delete(User $user, Mission $mission): bool
    {
        return $mission->client_id === $user->id;
    }
}
