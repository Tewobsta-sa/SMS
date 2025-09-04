<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Admin triggered invoice generation for one-time fees.
     */
    public function generateAdminInvoice(Request $request)
    {
        // 1. Authorize that the user is an admin.
        $this->authorize('create', Invoice::class);
        $user = $request->user();

        $validated = $request->validate([
            // 2. Scoped Validation: Ensure student and fee belong to the admin's school.
            'student_id' => ['required', Rule::exists('students', 'id')->where('school_id', $user->school_id)],
            'fee_id' => ['required', Rule::exists('fee_structures', 'id')->where('school_id', $user->school_id)],
            'total_amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after_or_equal:today',
        ]);
        

        try {
            $invoice = Invoice::create([
                'school_id' => $user->school_id, // Securely use the admin's school ID
                'student_id' => $validated['student_id'],
                'fee_id' => $validated['fee_id'],
                'invoice_number' => 'INV-' . \Illuminate\Support\Str::uuid(),  // Consider a better generator,
                'total_amount' => $validated['total_amount'],
                'balance_remaining' => $validated['total_amount'],
                'due_date' => $validated['due_date'],
                'issued_date' => now(),
                'status' => 'Unpaid',
            ]);

            return response()->json(['message' => 'Invoice created successfully', 'invoice' => $invoice], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Download the specified invoice as a PDF.
     */
    public function download(Invoice $invoice) // 3. Using Route-Model Binding
    {
        // 4. Authorize that the user can view this specific invoice.
        $this->authorize('view', $invoice);

        // Eager load relationships for the PDF view
        $invoice->load(['student', 'school', 'fee']);

        $pdf = Pdf::loadView('invoices.show', compact('invoice'));

        return $pdf->download('invoice_' . $invoice->invoice_number . '.pdf');
    }
}