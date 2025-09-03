<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only allow users with the 'admin' role to create categories.
        return $user->role === 'admin';
    }

    // ... other policy methods like view, update, delete, etc.
}