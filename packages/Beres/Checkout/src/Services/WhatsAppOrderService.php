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
            '*PESANAN BARU*',
            'Nomor pesanan: *#'.$this->cleanLine($order->order_number).'*',
            'Tanggal: '.$this->orderDate($order),
            '',
            '*DATA PELANGGAN*',
            'Nama: '.$this->cleanLine($order->customer_name),
            'Telepon: '.$this->cleanLine($order->customer_phone),
            'Email: '.$this->cleanLine($shippingAddress['email'] ?? null),
            '',
            '*DAFTAR PRODUK*',
        ];

        if ($order->items->isEmpty()) {
            $lines[] = '- Tidak ada produk';
        } else {
            foreach ($order->items as $index => $item) {
                $lines[] = sprintf(
                    '%d. %s',
                    $index + 1,
                    $this->cleanLine($item->product_name)
                );
                $lines[] = sprintf(
                    '   %s x %s = %s',
                    $item->quantity,
                    $this->formatRupiah((float) $item->price),
                    $this->formatRupiah((float) $item->total)
                );
            }
        }

        $lines[] = '';
        $lines[] = '*RINGKASAN PEMBAYARAN*';
        $lines[] = 'Subtotal: '.$this->formatRupiah((float) $order->subtotal);
        $lines[] = 'Ongkir: '.$this->formatRupiah((float) $order->shipping_cost);
        $lines[] = '*TOTAL: '.$this->formatRupiah((float) $order->total).'*';
        $lines[] = 'Pembayaran: WhatsApp (konfirmasi manual)';
        $lines[] = '';
        $lines[] = '*PENGIRIMAN*';
        $lines[] = 'Kurir/layanan: '.$this->shippingMethod($order);
        $lines[] = 'Alamat:';
        $lines = array_merge($lines, $this->addressLines($shippingAddress));

        if (filled($order->notes)) {
            $lines[] = '';
            $lines[] = '*CATATAN*';
            $lines[] = $this->cleanLine($order->notes);
        }

        $footer = CheckoutSettings::whatsappFooter();

        if ($footer !== '') {
            $lines[] = '';
            $lines[] = $footer;
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode(implode("\n", $lines));
    }

    private function addressLines(array $shippingAddress): array
    {
        $lines = [];

        foreach (['address1', 'address2'] as $field) {
            if (filled($shippingAddress[$field] ?? null)) {
                $lines[] = '  '.$this->cleanLine($shippingAddress[$field]);
            }
        }

        $locality = collect([
            $shippingAddress['city'] ?? null,
            $shippingAddress['state'] ?? null,
        ])->filter(fn ($part) => filled($part))
            ->map(fn ($part) => $this->cleanLine($part))
            ->implode(', ');

        if ($locality !== '') {
            $lines[] = '  '.$locality;
        }

        $postalCountry = collect([
            $shippingAddress['postcode'] ?? null,
            $shippingAddress['country'] ?? null,
        ])->filter(fn ($part) => filled($part))
            ->map(fn ($part) => $this->cleanLine($part))
            ->implode(' ');

        if ($postalCountry !== '') {
            $lines[] = '  '.$postalCountry;
        }

        return $lines ?: ['  -'];
    }

    private function shippingMethod(AdminOrder $order): string
    {
        return collect([$order->shipping_courier, $order->shipping_service])
            ->filter(fn ($part) => filled($part))
            ->map(fn ($part) => $this->cleanLine($part))
            ->implode(' — ') ?: '-';
    }

    private function orderDate(AdminOrder $order): string
    {
        return $order->created_at
            ? $order->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i')
            : '-';
    }

    private function cleanLine(mixed $value): string
    {
        $value = preg_replace('/[\r\n]+/', ' ', trim((string) $value));

        return $value !== '' ? $value : '-';
    }

    private function formatRupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
