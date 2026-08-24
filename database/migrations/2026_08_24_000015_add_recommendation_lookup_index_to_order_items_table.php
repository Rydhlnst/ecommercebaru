<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasIndex('order_items', 'order_items_product_parent_order_index')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->index(
                ['product_id', 'parent_id', 'order_id'],
                'order_items_product_parent_order_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasIndex('order_items', 'order_items_product_parent_order_index')) {
            return;
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_product_parent_order_index');
        });
    }
};
