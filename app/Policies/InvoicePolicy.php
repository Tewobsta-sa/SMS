<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        // User must be an admin of the school the invoice belongs to.
        return $user->role === 'admin' && $user->school_id === $invoice->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create invoices.
        return $user->role === 'admin';
    }
}