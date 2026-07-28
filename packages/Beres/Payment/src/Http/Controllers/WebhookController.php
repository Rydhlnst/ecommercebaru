<?php

namespace Beres\Payment\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Payment\Services\PaymentService;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Handle Midtrans webhook notification.
     */
    public function handleMidtrans(Request $request)
    {
        $payload = $request->all();
        $headers = $request->headers->all();

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

        Log::info('Midtrans notification received', ['payload' => $payload]);

        $result = $this->paymentService->handleWebhook($payload, $headers);

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
