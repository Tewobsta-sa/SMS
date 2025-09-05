<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Services\PaymentService; // Import the service
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentController extends Controller

{
    protected $paymentService;
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_method' => ['required', Rule::in(['Cash', 'Bank', 'Online'])],
            'transaction_ref' => 'nullable|string|max:255',
        ]);

        $invoice = Invoice::with('student')->findOrFail($validated['invoice_id']);

        // 1. AUTHORIZE the action
        $this->authorize('create', [Payment::class, $invoice]);
        
        // 2. Further validation
        if ($validated['paid_amount'] > $invoice->balance_remaining) {
            return response()->json([
                'message' => 'Payment amount cannot exceed the remaining balance.',
                'errors' => ['paid_amount' => ['Amount exceeds balance of ' . $invoice->balance_remaining]]
            ], 422);
        }

        try {
            // 3. Use the service to handle the logic
            $result = $this->paymentService->recordManualPayment($invoice, $validated, $request->user());

            return response()->json([
                'message' => 'Payment recorded successfully and receipt issued',
                'payment' => $result['payment'],
                'receipt' => $result['receipt']
            ], 201);
        } catch (\Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while processing the payment.'], 500);
        }
    }

    public function history($studentId)
    {
        $student = Student::findOrFail($studentId);
        $this->authorize('viewHistory', [Payment::class, $student]);

        $payments = Payment::where('student_id', $studentId)
            ->with(['feeStructure.category'])
            ->orderBy('payment_date', 'desc')
            ->get();
        return response()->json($payments);
    }


    public function payOnline(Request $request)
    {
        $request->validate(['invoice_id' => 'required|exists:invoices,id']);
        $invoice = Invoice::findOrFail($request->invoice_id);

        // 1. AUTHORIZE: Ensure the logged-in parent can pay for this invoice.
        // You'll need to create this policy method.
        $this->authorize('pay', $invoice);

        // 2. VALIDATE: Check if there's a balance to pay.
        if ($invoice->balance_remaining <= 0) {
            return response()->json(['message' => 'This invoice has already been fully paid.'], 422);
        }

        $tx_ref = 'TX-' . now()->timestamp . '-' . Str::upper(Str::random(6));

        // 3. Call the gateway
        $response = Http::withToken(config('services.chapa.secret_key')) // ✅ Use config
            ->post('https://api.chapa.co/v1/transaction/initialize', [
                'amount' => $invoice->balance_remaining, // ✅ Pay the remaining balance
                'currency' => 'ETB',
                'email' => $request->user()->email, // Use authenticated user's email
                'tx_ref' => $tx_ref,
                'callback_url' => route('chapa.callback'),
                'return_url' => route('parent.payments.success'), // Use named parent route
                'customization' => [
                    'title' => 'School Fee Payment',
                    'description' => 'Payment for Invoice #' . $invoice->invoice_number,
                ],
            ])
            ->json();

        if (!isset($response['status']) || $response['status'] !== 'success') {
            Log::error('Chapa Initialization Failed', $response);
            return response()->json(['error' => 'Failed to initialize payment gateway.'], 500);
        }

        // 4. Create a PENDING payment record using the service
        $this->paymentService->createPendingOnlinePayment($invoice, $tx_ref);

        return redirect($response['data']['checkout_url']);
    }

    public function chapaCallback(Request $request)
    {
        // Add webhook signature verification here if Chapa supports it.

        $tx_ref = $request->input('tx_ref');

        // Verify the transaction with Chapa
        $response = Http::withToken(config('services.chapa.secret_key')) // ✅ Use config
            ->get("https://api.chapa.co/v1/transaction/verify/{$tx_ref}")
            ->json();

        if (isset($response['status']) && $response['status'] === 'success') {
            try {
                // 5. Use the service to finalize the payment in a transaction
                $this->paymentService->finalizeOnlinePayment($tx_ref, $response);
                return redirect()->route('parent.payments.success');
            } catch (\Exception $e) {
                Log::critical('Payment finalization failed after successful verification!', [
                    'tx_ref' => $tx_ref,
                    'error' => $e->getMessage()
                ]);
                // Redirect to a failed page, but log as critical because money was charged.
                return redirect()->route('parent.payments.failed');
            }
        }

        return redirect()->route('parent.payments.failed');
    }

    public function success()
    {
        return view('payments.success'); // Or a JSON response for an API
    }

    }