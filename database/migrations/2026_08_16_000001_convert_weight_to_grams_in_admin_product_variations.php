<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert existing kg values to grams (multiply by 1000)
        DB::table('admin_product_variations')
            ->where('weight', '>', 0)
            ->update([
                'weight' => DB::raw('ROUND(weight * 1000)'),
            ]);

        // Change column type from decimal(10,2) to integer
        Schema::table('admin_product_variations', function (Blueprint $table) {
            $table->integer('weight')->default(0)->change();
        });
    }

    public function down(): void
    {
        // Convert grams back to kg (divide by 1000)
        DB::table('admin_product_variations')
            ->where('weight', '>', 0)
            ->update([
                'weight' => DB::raw('ROUND(weight / 1000, 2)'),
            ]);

        Schema::table('admin_product_variations', function (Blueprint $table) {
            $table->decimal('weight', 10, 2)->default(0)->change();
        });
    }
};
