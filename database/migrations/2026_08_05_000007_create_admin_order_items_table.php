<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('admin_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('admin_products')->nullOnDelete();
            $table->string('product_name');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_order_items');
    }
};
