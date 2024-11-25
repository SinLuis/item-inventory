<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Bbinadj;
use App\Models\User;

class BbinadjPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return True;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bbinadj $bbinadj): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bbinadj $bbinadj): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bbinadj $bbinadj): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bbinadj $bbinadj): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bbinadj $bbinadj): bool
    {
        //
    }
}
