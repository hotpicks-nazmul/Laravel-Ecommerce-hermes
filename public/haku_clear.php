<?php
// Temp cache clear helper — DELETE AFTER USE
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$artisan = Illuminate\Support\Facades\Artisan::class;
foreach (['view:clear', 'cache:clear', 'config:clear', 'route:clear'] as $cmd) {
    try {
        Illuminate\Support\Facades\Artisan::call($cmd);
        echo $cmd . " OK\n";
    } catch (\Throwable $e) {
        echo $cmd . " ERR: " . $e->getMessage() . "\n";
    }
}
if (function_exists('opcache_reset')) { opcache_reset(); echo "opcache reset\n"; }
echo "DONE\n";
