<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('admin_products')->cascadeOnDelete();
            $table->decimal('weight', 10, 2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_product_variations');
    }
};
