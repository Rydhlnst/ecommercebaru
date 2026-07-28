<?php

namespace Beres\Payment\Services;

use Beres\Payment\Contracts\PaymentTransactionRepositoryInterface;
use Beres\Payment\Contracts\WebhookLogRepositoryInterface;
use Beres\Payment\Models\PaymentTransaction;
use Beres\Payment\Models\WebhookLog;
use Beres\Order\Services\OrderService;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Payment statuses.
     */
    const STATUSES = [
        'pending'     => 'Menunggu Pembayaran',
        'settlement'  => 'Lunas',
        'capture'     => 'Tangkap',
        'deny'        => 'Ditolak',
        'cancel'      => 'Dibatalkan',
        'expire'      => 'Kadaluarsa',
        'refund'      => 'Dikembalikan',
        'partial_refund' => 'Sebagian Dikembalikan',
    ];

    /**
     * Fraud statuses.
     */
    const FRAUD_STATUSES = [
        'accept'  => 'Diterima',
        'challenge' => 'Tantangan',
        'reject'  => 'Ditolak',
    ];

    public function __construct(
        protected MidtransService $midtransService,
        protected PaymentTransactionRepositoryInterface $transactionRepository,
        protected WebhookLogRepositoryInterface $webhookLogRepository
    ) {}

    /**
     * Create payment for an order.
     */
    public function createPayment(int $orderId): ?string
    {
        $order = Order::with(['customer', 'items'])->find($orderId);

        if (!$order) {
            return null;
        }

        // Build Midtrans params
        $params = [
            'transaction_details' => [
                'order_id'     => $order->increment_id ?? $order->id,
                'gross_amount' => (int) $order->grand_total,
            ],
            'customer_details' => [
                'first_name' => $order->customer?->first_name ?? 'Guest',
                'last_name'  => $order->customer?->last_name ?? '',
                'email'      => $order->customer?->email ?? '',
                'phone'      => $order->customer?->phone ?? '',
            ],
            'callbacks' => [
                'finish'    => config('midtrans.finish_redirect_url'),
                'unfinish'  => config('midtrans.unfinish_redirect_url'),
                'error'     => config('midtrans.error_redirect_url'),
            ],
        ];

        // Add item details
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id'    => $item->sku ?? $item->product_id,
                'name'  => $item->name,
                'price' => (int) $item->price,
                'qty'   => (int) $item->qty_ordered,
            ];
        }
        $params['item_details'] = $itemDetails;

        try {
            // Create payment transaction record
            $transaction = $this->transactionRepository->create([
                'order_id'          => $order->id,
                'payment_method'    => 'midtrans',
                'gross_amount'      => $order->grand_total,
                'status'            => 'pending',
                'order_id_midtrans' => $order->increment_id ?? $order->id,
            ]);

            // Get Snap URL
            $snapUrl = $this->midtransService->createSnapToken($params);

            Log::info("Payment created for order #{$order->id}: {$snapUrl}");

            return $snapUrl;
        } catch (\Exception $e) {
            Log::error("Payment creation failed for order #{$order->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle webhook notification from Midtrans.
     */
    public function handleWebhook(array $payload, array $headers = []): bool
    {
        // Log the webhook
        $webhookLog = $this->webhookLogRepository->create([
            'source'  => 'midtrans',
            'payload' => $payload,
            'headers' => $headers,
        ]);

        try {
            // Extract order ID from payload
            $orderId = $payload['order_id'] ?? null;

            if (!$orderId) {
                $this->webhookLogRepository->markFailed($webhookLog->id, 'Missing order_id');
                return false;
            }

            // Check idempotency - prevent duplicate processing
            if ($this->webhookLogRepository->alreadyProcessed('midtrans', $orderId)) {
                Log::info("Webhook already processed for order: {$orderId}");
                $this->webhookLogRepository->markProcessed($webhookLog->id);
                return true;
            }

            // Verify signature
            $statusCode = $payload['status_code'] ?? '';
            $grossAmount = $payload['gross_amount'] ?? '';
            $serverKey = config('midtrans.server_key');
            $signatureKey = $payload['signature_key'] ?? '';

            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signatureKey !== $expectedSignature) {
                $this->webhookLogRepository->markFailed($webhookLog->id, 'Invalid signature');
                Log::warning("Invalid webhook signature for order: {$orderId}");
                return false;
            }

            // Find and update transaction
            $transaction = $this->transactionRepository->getByMidtransOrderId($orderId);

            if (!$transaction) {
                $this->webhookLogRepository->markFailed($webhookLog->id, 'Transaction not found');
                return false;
            }

            // Update transaction status
            $status = $payload['transaction_status'] ?? 'pending';
            $fraudStatus = $payload['fraud_status'] ?? null;

            $this->transactionRepository->updateStatus(
                $transaction->id,
                $status,
                $fraudStatus,
                $payload
            );

            // Update order status based on payment status
            $this->updateOrderStatus($transaction->order_id, $status);

            // Mark webhook as processed
            $this->webhookLogRepository->markProcessed($webhookLog->id);

            Log::info("Webhook processed successfully for order: {$orderId}, status: {$status}");

            return true;
        } catch (\Exception $e) {
            $this->webhookLogRepository->markFailed($webhookLog->id, $e->getMessage());
            Log::error("Webhook processing failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update order status based on payment status.
     */
    protected function updateOrderStatus(int $orderId, string $paymentStatus): void
    {
        $order = Order::find($orderId);

        if (!$order) {
            return;
        }

        $newOrderStatus = match ($paymentStatus) {
            'settlement', 'capture' => 'processing',
            'deny', 'cancel'        => 'canceled',
            'expire'                => 'canceled',
            default                 => $order->status,
        };

        if ($newOrderStatus !== $order->status) {
            $order->update(['status' => $newOrderStatus]);
            Log::info("Order #{$orderId} status updated to: {$newOrderStatus}");
        }
    }

    /**
     * Get payment transaction for an order.
     */
    public function getTransaction(int $orderId): ?object
    {
        return $this->transactionRepository->getByOrderId($orderId);
    }

    /**
     * Get recent transactions.
     */
    public function getRecentTransactions(int $limit = 20): array
    {
        return $this->transactionRepository->getRecent($limit);
    }

    /**
     * Get recent webhook logs.
     */
    public function getRecentWebhooks(int $limit = 20): array
    {
        return $this->webhookLogRepository->getRecent($limit);
    }

    /**
     * Get payment status label.
     */
    public function getStatusLabel(string $status): string
    {
        return self::STATUSES[$status] ?? $status;
    }

    /**
     * Get fraud status label.
     */
    public function getFraudStatusLabel(string $status): string
    {
        return self::FRAUD_STATUSES[$status] ?? $status;
    }
}
