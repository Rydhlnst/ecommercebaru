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
                'key' => 'rajaongkir_is_active',
                'value' => '0',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'rajaongkir_api_type',
                'value' => 'starter',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'rajaongkir_origin_city',
                'value' => (string) config('rajaongkir.origin_city', '152'),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'rajaongkir_couriers',
                'value' => 'jne,jnt,sicepat,anteraja',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('site_settings')
            ->whereIn('key', [
                'rajaongkir_is_active',
                'rajaongkir_api_type',
                'rajaongkir_origin_city',
                'rajaongkir_couriers',
            ])
            ->delete();
    }
};
