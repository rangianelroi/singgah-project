<?php

namespace App\Policies;

use App\Models\CommunicationLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommunicationLogPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Izinkan peran ini untuk membuat log komunikasi
        return in_array($user->role, [
            'team_leader_avsec',
            'department_head_avsec',
            'admin'
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CommunicationLog $communicationLog): bool
    {
        return in_array($user->role, ['team_leader_avsec', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CommunicationLog $communicationLog): bool
    {
        return in_array($user->role, ['team_leader_avsec', 'admin']);
    }
}