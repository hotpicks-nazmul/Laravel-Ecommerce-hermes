<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Test OK\n";
echo "Sliders: " . DB::table('sliders')->count() . "\n";
echo "Blogs: " . DB::table('blogs')->count() . "\n";
