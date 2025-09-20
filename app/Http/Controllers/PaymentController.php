<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yosinan\Chapavel\ChapaClient;
use Yosinan\Chapavel\WebhookVerifier;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Initialize payment
     */
    public function initialize(Request $req, ChapaClient $chapa)
    {
        $data = $req->validate([
            'name'   => ['required','string','max:120'],
            'email'  => ['required','email','max:190'],
            'amount' => ['required','numeric','min:1'],
        ]);

        $txRef = 'tx_' . Str::uuid()->toString();

        $payment = Payment::create([
            'tx_ref' => $txRef,
            'name'   => $data['name'],
            'email'  => $data['email'],
            'amount' => $data['amount'],
            'status' => 'pending',
        ]);

        $payload = [
            'amount'       => (string) $payment->amount,
            'currency'     => 'ETB',
            'email'        => $payment->email,
            'first_name'   => $payment->name,
            'tx_ref'       => $payment->tx_ref,
            'callback_url' => route('payment.return') . '?tx_ref=' . $payment->tx_ref,
            'return_url'   => route('payment.return') . '?tx_ref=' . $payment->tx_ref,
            'customization'=> [
                'title'       => 'Order Payment',
                'description' => 'Checkout'
            ],
        ];

        $res = $chapa->initialize($payload);

        $payment->update(['raw_init' => $res]);
        Log::info('Payment return URL: ' . route('payment.return'));

        return response()->json($res);
    }

    /**
     * Check status manually
     */
    public function status(Request $req)
    {
        $txRef = $req->query('tx_ref');
        $p = Payment::where('tx_ref', $txRef)->firstOrFail();

        return response()->json([
            'tx_ref' => $p->tx_ref,
            'status' => $p->status,
            'amount' => $p->amount,
            'email'  => $p->email,
            'name'   => $p->name,
        ]);
    }

    /**
     * Return from Chapa (API-only, no frontend)
     */
    public function return(Request $req)
    {
        $txRef = $req->query('tx_ref');
        $payment = Payment::where('tx_ref', $txRef)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json([
            'message' => 'Payment return',
            'tx_ref'  => $payment->tx_ref,
            'status'  => $payment->status,
            'amount'  => $payment->amount,
            'email'   => $payment->email,
            'name'    => $payment->name,
        ]);
    }

    /**
     * Webhook (authoritative update)
     */
    public function webhook(Request $req, ChapaClient $chapa)
    {
        $raw = $req->getContent();
        $sig1 = $req->header('Chapa-Signature');
        $sig2 = $req->header('x-chapa-signature');
        $secret = config('chapa.secret_key');

        if (!WebhookVerifier::isValid($raw, $sig1, $sig2, $secret)) {
            Log::warning('Chapa webhook signature mismatch', [
                'sig1' => $sig1,
                'sig2' => $sig2,
                'body' => $raw
            ]);
            return response()->json(['message' => 'invalid signature'], 400);
        }

        $payload = $req->json()->all();
        $txRef = $payload['tx_ref'] ?? $payload['reference'] ?? null;

        if (!$txRef) {
            return response()->json(['message' => 'tx_ref missing'], 422);
        }

        $payment = Payment::where('tx_ref', $txRef)->first();
        if (!$payment) {
            return response()->json(['message' => 'unknown tx_ref'], 404);
        }

        $status = strtolower((string) ($payload['status'] ?? ''));
        if (str_contains($status, 'success')) {
            $payment->status = 'success';
        } elseif (str_contains($status, 'failed') || str_contains($status, 'cancel')) {
            $payment->status = 'failed';
        }

        $payment->raw_verify = $payload;
        $payment->save();

        return response()->json(['message' => 'ok']);
    }
}
