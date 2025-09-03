<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * Download the specified receipt as a PDF.
     *
     * This is the primary and most secure way to present a receipt.
     * The flawed `show` method has been removed.
     */
    public function download(Receipt $receipt) // <-- Using Route-Model Binding
    {
        try {
            // 1. AUTHORIZE: Ensure the user has permission to view this receipt.
            // This will automatically throw a 403 Forbidden error if the policy check fails.
            $this->authorize('view', $receipt);

            // 2. EAGER LOAD: Load the relationships needed for the PDF view.
            $receipt->load(['payment.student', 'payment.feeStructure.category', 'school']);

            $pdf = Pdf::loadView('receipts.show', compact('receipt'));

            return $pdf->download('receipt_' . $receipt->receipt_number . '.pdf');

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            // \Log::error("Receipt download failed for receipt #{$receipt->id}: " . $e->getMessage());

            // Return a user-friendly error response
            // In a real app, you'd redirect back with an error message.
            return response()->json(['message' => 'Could not generate PDF.'], 500);
        }
    }
}