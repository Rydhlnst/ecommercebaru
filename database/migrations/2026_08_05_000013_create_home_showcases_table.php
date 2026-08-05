<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_showcases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('admin_products')->nullOnDelete();
            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->json('items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_showcases');
    }
};
