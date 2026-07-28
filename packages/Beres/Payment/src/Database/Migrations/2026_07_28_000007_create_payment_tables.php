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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('payment_method', 50);
            $table->string('payment_type', 50)->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('order_id_midtrans')->nullable();
            $table->decimal('gross_amount', 15, 2);
            $table->string('status', 50);
            $table->string('fraud_status', 50)->nullable();
            $table->json('payment_response')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('status');
        });

        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50);
            $table->json('payload');
            $table->json('headers')->nullable();
            $table->boolean('processed')->default(false);
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index('source');
            $table->index('processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('payment_transactions');
    }
};
