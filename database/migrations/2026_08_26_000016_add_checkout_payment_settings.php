<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('site_settings')->insertOrIgnore([
            [
                'key' => 'checkout_payment_mode',
                'value' => 'whatsapp',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'whatsapp_order_intro',
                'value' => 'Halo Admin, saya ingin melakukan pemesanan berikut:',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'whatsapp_order_footer',
                'value' => 'Mohon konfirmasi ketersediaan dan total pembayaran. Terima kasih.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->whereIn('key', [
                'checkout_payment_mode',
                'whatsapp_order_intro',
                'whatsapp_order_footer',
            ])
            ->delete();
    }
};
