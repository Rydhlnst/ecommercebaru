<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_product_images', function (Blueprint $table) {
            $table->unsignedInteger('width')->nullable()->after('image_path');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->string('fit_mode')->default('cover')->after('height');
            $table->unsignedTinyInteger('focal_x')->default(50)->after('fit_mode');
            $table->unsignedTinyInteger('focal_y')->default(50)->after('focal_x');
            $table->string('alt_text')->nullable()->after('focal_y');
            $table->string('image_480_path')->nullable()->after('alt_text');
            $table->string('image_800_path')->nullable()->after('image_480_path');
            $table->string('image_1600_path')->nullable()->after('image_800_path');
        });
    }

    public function down(): void
    {
        Schema::table('admin_product_images', function (Blueprint $table) {
            $table->dropColumn([
                'width', 'height', 'fit_mode', 'focal_x', 'focal_y', 'alt_text',
                'image_480_path', 'image_800_path', 'image_1600_path',
            ]);
        });
    }
};
