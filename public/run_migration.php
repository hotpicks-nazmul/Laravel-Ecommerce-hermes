<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "Running migrations...\n";
Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();

echo "\nClearing caches...\n";
Artisan::call('cache:clear');
Artisan::call('config:clear');
Artisan::call('view:clear');
echo "Caches cleared!\n";

echo "\nDone!\n";
