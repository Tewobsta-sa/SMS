<?php

namespace App\Policies;

use App\Models\FeeStructure;
use App\Models\User;

class FeeStructurePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Any admin can view the list (the controller will scope it).
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FeeStructure $feeStructure): bool
    {
        // Allow if the user is an admin AND the fee belongs to their school.
        return $user->role === 'admin' && $user->school_id === $feeStructure->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create fee structures.
        return $user->role === 'admin';
    }
}