<?php

namespace App\Policies;

use App\Models\Receipt;
use App\Models\User;

class ReceiptPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Receipt $receipt): bool
    {
        // A user can view a receipt if they are an admin of that school.
        // You could add additional logic here for parents.
        return $user->role === 'admin' && $user->school_id === $receipt->school_id;
    }
}