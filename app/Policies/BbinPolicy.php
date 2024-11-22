<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Bbin;
use App\Models\User;

class BbinPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bbin $bbin): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
    
        return false; 
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bbin $bbin): bool
    {
        return false; 
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bbin $bbin): bool
    {
        return false; 
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bbin $bbin): bool
    {
        return false; 
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bbin $bbin): bool
    {
        return false; 
    }
}
