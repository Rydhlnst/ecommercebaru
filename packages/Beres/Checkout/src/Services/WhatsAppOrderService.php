<?php

namespace Beres\Checkout\Services;

use App\Models\AdminOrder;
use App\Support\CheckoutSettings;

class WhatsAppOrderService
{
    public function isConfigured(): bool
    {
        return CheckoutSettings::whatsappNumber() !== '';
    }

    public function urlFor(AdminOrder $order, array $shippingAddress): string
    {
        $number = CheckoutSettings::whatsappNumber();

        if ($number === '') {
            throw new \RuntimeException('Nomor WhatsApp admin belum dikonfigurasi.');
        }

        $order->loadMissing('items');

        $lines = [
            CheckoutSettings::whatsappIntro(),
            '',
            '*Pesanan Baru*',
            'Nomor pesanan: '.$order->order_number,
            '',
            '*Produk:*',
        ];

        foreach ($order->items as $item) {
            $lines[] = sprintf(
                '- %s x%s — %s (Total %s)',
                $item->product_name,
                $item->quantity,
                $this->formatRupiah((float) $item->price),
                $this->formatRupiah((float) $item->total)
            );
        }

        $lines[] = '';
        $lines[] = '*Ringkasan pembayaran:*';
        $lines[] = 'Subtotal: '.$this->formatRupiah((float) $order->subtotal);
        $lines[] = 'Ongkir: '.$this->formatRupiah((float) $order->shipping_cost);
        $lines[] = 'Total: '.$this->formatRupiah((float) $order->total);
        $lines[] = '';
        $lines[] = '*Alamat pengiriman:*';
        $lines[] = 'Nama: '.($order->customer_name ?: '-');
        $lines[] = 'Telepon: '.($order->customer_phone ?: '-');
        $lines[] = 'Email: '.($shippingAddress['email'] ?? '-');
        $lines[] = 'Alamat: '.$this->address($shippingAddress);
        $lines[] = 'Pengiriman: '.$this->shippingMethod($order);

        if (filled($order->notes)) {
            $lines[] = 'Catatan: '.$order->notes;
        }

        $footer = CheckoutSettings::whatsappFooter();

        if ($footer !== '') {
            $lines[] = '';
            $lines[] = $footer;
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode(implode("\n", $lines));
    }

    private function address(array $shippingAddress): string
    {
        return collect([
            $shippingAddress['address1'] ?? null,
            $shippingAddress['address2'] ?? null,
            $shippingAddress['city'] ?? null,
            $shippingAddress['state'] ?? null,
            $shippingAddress['postcode'] ?? null,
            $shippingAddress['country'] ?? null,
        ])->filter(fn ($part) => filled($part))->implode(', ') ?: '-';
    }

    private function shippingMethod(AdminOrder $order): string
    {
        return collect([$order->shipping_courier, $order->shipping_service])
            ->filter(fn ($part) => filled($part))
            ->implode(' — ') ?: '-';
    }

    private function formatRupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
