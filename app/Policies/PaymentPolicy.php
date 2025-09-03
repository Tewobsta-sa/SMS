<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Invoice $invoice): bool
    {
        // Allow if the user is an admin of the school the invoice belongs to.
        // Or if the user is a parent linked to the student on the invoice.
        // Add your parent logic here. For now, we'll check for admin.
        return $user->role === 'admin' && $user->school_id === $invoice->school_id;
    }

    /**
     * Determine whether the user can view the payment history for a student.
     */
    public function viewHistory(User $user, Student $student): bool
    {
        // Allow if the user is an admin of the school the student belongs to.
        return $user->role === 'admin' && $user->school_id === $student->school_id;
    }
}