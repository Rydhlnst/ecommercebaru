<?php

namespace Beres\Payment\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        $this->configure();
    }

    /**
     * Read a Midtrans setting: admin dashboard first, package config fallback.
     */
    protected function setting(string $key, $default = null)
    {
        try {
            $siteValue = SiteSetting::getValue("midtrans_$key");
            if ($siteValue !== null && $siteValue !== '') {
                return $siteValue;
            }
        } catch (\Throwable $e) {
        }

        $value = core()->getConfigData("beres_storefront.midtrans.$key");
        if ($value !== null && $value !== '') {
            return $value;
        }

        return config("midtrans.$key", $default);
    }

    /**
     * Configure Midtrans SDK.
     */
    protected function configure(): void
    {
        Config::$serverKey = (string) $this->setting('server_key', '');
        Config::$clientKey = (string) $this->setting('client_key', '');
        Config::$isProduction = $this->setting('environment', 'sandbox') === 'production';
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Check if Midtrans is enabled by admin.
     */
    public function isActive(): bool
    {
        try {
            $siteActive = SiteSetting::getValue('midtrans_is_active');
            if ($siteActive !== null && $siteActive !== '') {
                return (bool) $siteActive;
            }
        } catch (\Throwable $e) {
        }

        return (bool) core()->getConfigData('beres_storefront.midtrans.active', true);
    }

    /**
     * Get enabled payment types (comma-separated in admin, returns array).
     */
    public function getPaymentTypes(): array
    {
        $raw = (string) core()->getConfigData('beres_storefront.midtrans.payment_types');
        if ($raw === '') {
            return ['credit_card', 'bca_va', 'bni_va', 'bri_va', 'mandiri_va', 'gopay', 'shopeepay', 'qris'];
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /**
     * Create Snap token for payment.
     */
    public function createSnapToken(array $params): ?string
    {
        try {
            $paymentUrl = Snap::getSnapUrl($params);

            return $paymentUrl;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Get Snap token (redirect URL).
     */
    public function getSnapUrl(array $params): string
    {
        return Snap::getSnapUrl($params);
    }

    /**
     * Get transaction status from Midtrans.
     */
    public function getTransactionStatus(string $orderId): ?array
    {
        try {
            $status = new Transaction;
            $response = $status->status($orderId);

            return $response;
        } catch (\Exception $e) {
            Log::error('Midtrans Transaction Status Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Approve a pending transaction.
     */
    public function approveTransaction(string $orderId): ?array
    {
        try {
            $status = new Transaction;
            $response = $status->approve($orderId);

            return $response;
        } catch (\Exception $e) {
            Log::error('Midtrans Approve Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel a transaction.
     */
    public function cancelTransaction(string $orderId): ?array
    {
        try {
            $status = new Transaction;
            $response = $status->cancel($orderId);

            return $response;
        } catch (\Exception $e) {
            Log::error('Midtrans Cancel Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Expire a transaction.
     */
    public function expireTransaction(string $orderId): ?array
    {
        try {
            $status = new Transaction;
            $response = $status->expire($orderId);

            return $response;
        } catch (\Exception $e) {
            Log::error('Midtrans Expire Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Refund a transaction.
     */
    public function refundTransaction(string $orderId, float $amount, ?string $reason = null): ?array
    {
        try {
            $status = new Transaction;
            $response = $status->refund($orderId, $amount, $reason);

            return $response;
        } catch (\Exception $e) {
            Log::error('Midtrans Refund Error: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify notification from Midtrans.
     */
    public function verifyNotification(Notification $notification): bool
    {
        try {
            $orderId = $notification->order_id;
            $statusCode = $notification->status_code;
            $grossAmount = $notification->gross_amount;
            $serverKey = (string) $this->setting('server_key', '');

            // Build verification string
            $orderId.$statusCode.$grossAmount.$serverKey;

            $signatureKey = $notification->signature_key;
            $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

            return $signatureKey === $expectedSignature;
        } catch (\Exception $e) {
            Log::error('Midtrans Signature Verification Error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Get client key for frontend.
     */
    public function getClientKey(): string
    {
        return (string) $this->setting('client_key', '');
    }

    /**
     * Check if using production environment.
     */
    public function isProduction(): bool
    {
        return $this->setting('environment', 'sandbox') === 'production';
    }
}
