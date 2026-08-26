<?php

namespace App\Support;

use App\Models\SiteSetting;

class CheckoutSettings
{
    public const MIDTRANS = 'midtrans';

    public const WHATSAPP = 'whatsapp';

    public static function paymentMode(): string
    {
        try {
            $mode = SiteSetting::getValue('checkout_payment_mode', self::WHATSAPP);
        } catch (\Throwable $e) {
            $mode = self::WHATSAPP;
        }

        return in_array($mode, [self::MIDTRANS, self::WHATSAPP], true)
            ? $mode
            : self::WHATSAPP;
    }

    public static function whatsappNumber(): string
    {
        try {
            $number = SiteSetting::getValue('store_whatsapp');
        } catch (\Throwable $e) {
            $number = null;
        }

        if (! $number) {
            try {
                $number = core()->getConfigData('beres_storefront.contact.whatsapp_number');
            } catch (\Throwable $e) {
                $number = null;
            }
        }

        $digits = preg_replace('/\D+/', '', (string) $number) ?: '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    public static function whatsappIntro(): string
    {
        return self::settingOrDefault(
            'whatsapp_order_intro',
            'Halo Admin, saya ingin melakukan pemesanan berikut:'
        );
    }

    public static function whatsappFooter(): string
    {
        return self::settingOrDefault(
            'whatsapp_order_footer',
            'Mohon konfirmasi ketersediaan dan total pembayaran. Terima kasih.'
        );
    }

    private static function settingOrDefault(string $key, string $default): string
    {
        try {
            $value = trim((string) SiteSetting::getValue($key, ''));
        } catch (\Throwable $e) {
            $value = '';
        }

        return $value !== '' ? $value : $default;
    }
}
