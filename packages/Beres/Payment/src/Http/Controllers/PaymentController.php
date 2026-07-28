<?php

namespace Beres\Payment\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Payment\Services\PaymentService;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\Response;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Display payment listing.
     */
    public function index(Request $request)
    {
        $transactions = $this->paymentService->getRecentTransactions(50);
        $statuses = PaymentService::STATUSES;

        return view('beres-payment::payment.index', [
            'transactions' => $transactions,
            'statuses'      => $statuses,
        ]);
    }

    /**
     * Initiate payment for an order.
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        try {
            $snapUrl = $this->paymentService->createPayment($request->input('order_id'));

            if (!$snapUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment.',
                ], 400);
            }

            return response()->json([
                'success'  => true,
                'snap_url' => $snapUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get payment transaction for an order.
     */
    public function transaction($orderId)
    {
        $transaction = $this->paymentService->getTransaction($orderId);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $transaction,
        ]);
    }

    /**
     * Export transactions to CSV.
     */
    public function export(Request $request)
    {
        $transactions = $this->paymentService->getRecentTransactions(1000);

        $tempFile = tempnam(sys_get_temp_dir(), 'payment_export_');
        $handle = fopen($tempFile, 'w');

        // Headers
        fputcsv($handle, ['ID', 'Order ID', 'Method', 'Amount', 'Status', 'Fraud Status', 'Created At']);

        foreach ($transactions as $transaction) {
            fputcsv($handle, [
                $transaction['id'],
                $transaction['order_id'],
                $transaction['payment_method'],
                $transaction['gross_amount'],
                $transaction['status'],
                $transaction['fraud_status'] ?? '',
                $transaction['created_at'],
            ]);
        }

        fclose($handle);

        return response()->download($tempFile, 'payments_export_' . date('Y-m-d_His') . '.csv', [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}
