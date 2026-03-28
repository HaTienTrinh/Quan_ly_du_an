<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prod = App\Models\Product::latest()->first();
if (!$prod) {
    echo "NO_PRODUCT\n";
    exit(0);
}
echo "THUMBNAIL: {$prod->thumbnail}\n";
$path = storage_path('app/public/' . $prod->thumbnail);
echo "FILE PATH: {$path}\n";
echo file_exists($path) ? "FILE_EXISTS\n" : "FILE_MISSING\n";
