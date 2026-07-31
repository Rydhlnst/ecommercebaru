<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$seeder = new Webkul\Installer\Database\Seeders\ProductTableSeeder();
$seeder->run(['default_locale' => 'en', 'allowed_locales' => ['en']]);
echo "Product seeding complete\n";
