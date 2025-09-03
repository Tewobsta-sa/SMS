<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Record a manual payment and update related records atomically.
     */
    public function recordManualPayment(Invoice $invoice, array $data, User $recorder): array
    {
        return DB::transaction(function () use ($invoice, $data, $recorder) {
            // 1. Create the Payment
            $payment = Payment::create([
                'school_id' => $invoice->school_id,
                'student_id' => $invoice->student_id,
                'invoice_id' => $invoice->id,
                'fee_id' => $invoice->fee_id,
                'expected_amount' => $invoice->balance_remaining,
                'paid_amount' => $data['paid_amount'],
                'payment_date' => now(),
                'payment_method' => $data['payment_method'],
                'transaction_ref' => $data['transaction_ref'] ?? null,
                'status' => 'Completed',
                'receipt_no' => 'RCT-' . now()->timestamp . '-' . Str::upper(Str::random(4)),
            ]);

            // 2. Update the Invoice
            $invoice->balance_remaining = max(0, $invoice->balance_remaining - $payment->paid_amount);
            if ($invoice->balance_remaining == 0) {
                $invoice->status = 'Paid';
            } else {
                $invoice->status = 'Partial';
            }
            $invoice->save();

            // 3. Create the Receipt
            $receipt = Receipt::create([
                'school_id' => $payment->school_id,
                'payment_id' => $payment->id,
                'receipt_number' => $payment->receipt_no,
                'issued_date' => now(),
                'issued_by' => $recorder->id,
            ]);

            return ['payment' => $payment, 'receipt' => $receipt];
        });
    }

    public function createPendingOnlinePayment(Invoice $invoice, string $tx_ref): Payment
    {
        return Payment::create([
            'school_id' => $invoice->school_id,
            'student_id' => $invoice->student_id,
            'fee_id' => $invoice->fee_id,
            'invoice_id' => $invoice->id,
            'expected_amount' => $invoice->balance_remaining, // Pay remaining balance
            'paid_amount' => 0,
            'payment_method' => 'Online',
            'transaction_ref' => $tx_ref,
            'gateway' => 'Chapa',
            'status' => 'Pending',
        ]);
    }

    /**
     * Finalize a successful online payment from a webhook callback.
     */
    public function finalizeOnlinePayment(string $tx_ref, array $gatewayResponse): bool
    {
        return DB::transaction(function () use ($tx_ref, $gatewayResponse) {
            // Find the pending payment record, locking it to prevent race conditions.
            $payment = Payment::where('transaction_ref', $tx_ref)->lockForUpdate()->firstOrFail();

            // Idempotency check: If already completed, do nothing and return success.
            if ($payment->status === 'Completed') {
                return true;
            }

            $paidAmount = $gatewayResponse['data']['amount'];

            // Update Payment
            $payment->status = 'Completed';
            $payment->paid_amount = $paidAmount;
            $payment->gateway_response = $gatewayResponse; // Optional: Store the full response
            $payment->save();

            // Update Invoice
            $invoice = $payment->invoice; // Assumes you have the relationship set up
            $invoice->balance_remaining = max(0, $invoice->balance_remaining - $paidAmount);
            $invoice->status = ($invoice->balance_remaining == 0) ? 'Paid' : 'Partial';
            $invoice->save();
            
            // Create a receipt
            Receipt::create([
                'school_id' => $payment->school_id,
                'payment_id' => $payment->id,
                'receipt_number' => 'RCT-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10)),
                'issued_date' => now(),
                'issued_by' => null, // Online payments might not have a specific user issuer
            ]);
            return true;
        });
    }
}