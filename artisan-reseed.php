<?php
// One-shot reseed helper — delete after use
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0');
DB::table('category_translations')->truncate();
DB::table('category_filterable_attributes')->truncate();
DB::table('product_categories')->truncate();
DB::table('categories')->truncate();
DB::table('products')->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "Cleared OK\n";
