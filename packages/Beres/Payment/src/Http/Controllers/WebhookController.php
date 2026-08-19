<?php

namespace Beres\Payment\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Payment\Services\PaymentService;
use Beres\Payment\Services\MidtransService;
use App\Models\AdminOrder;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
        , protected MidtransService $midtransService
    ) {}

    /**
     * Handle Midtrans webhook notification.
     */
    public function handleMidtrans(Request $request)
    {
        $payload = $request->all();
        $headers = $request->headers->all();

        if ($this->isCustomCheckoutOrder($payload)) {
            return $this->updateCustomOrder($payload);
        }

        Log::info('Midtrans webhook received', ['payload' => $payload]);

        $result = $this->paymentService->handleWebhook($payload, $headers);

        if ($result) {
            return response()->json(['status' => 'ok'], 200);
        }

        return response()->json(['status' => 'error'], 400);
    }

    /**
     * Handle Midtrans notification (POST).
     */
    public function notification(Request $request)
    {
        $payload = $request->all();
        $headers = $request->headers->all();

        if ($this->isCustomCheckoutOrder($payload)) {
            return $this->updateCustomOrder($payload);
        }

        Log::info('Midtrans notification received', ['payload' => $payload]);

        $result = $this->paymentService->handleWebhook($payload, $headers);

        return response()->json(['status' => 'ok'], 200);
    }

    protected function isCustomCheckoutOrder(array $payload): bool
    {
        return str_starts_with((string) ($payload['order_id'] ?? ''), 'ORD-');
    }

    protected function updateCustomOrder(array $payload)
    {
        $order = AdminOrder::where('order_number', $payload['order_id'] ?? '')->first();

        if (! $order || ! $this->midtransService->verifySignatureData(
            (string) ($payload['order_id'] ?? ''),
            (string) ($payload['status_code'] ?? ''),
            (string) ($payload['gross_amount'] ?? ''),
            (string) ($payload['signature_key'] ?? '')
        )) {
            return response()->json(['status' => 'error'], 400);
        }

        $status = (string) ($payload['transaction_status'] ?? 'pending');
        $paid = in_array($status, ['settlement', 'capture'], true);
        $order->update([
            'payment_status' => $status,
            'status' => $paid ? 'processing' : (in_array($status, ['deny', 'cancel', 'expire'], true) ? 'canceled' : $order->status),
        ]);

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Get webhook logs.
     */
    public function logs(Request $request)
    {
        $logs = $this->paymentService->getRecentWebhooks(50);

        return response()->json([
            'success' => true,
            'data'    => $logs,
        ]);
    }
}
