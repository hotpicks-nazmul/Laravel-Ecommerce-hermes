<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$applied = DB::table('migrations')->pluck('migration')->map(fn($m) => (string) $m);
$files = collect(glob(database_path('migrations/*.php')))->map(function ($p) {
    return str_replace('.php', '', basename($p));
})->sort();

$pending = $files->diff($applied)->values();
echo "Applied: " . $applied->count() . "\n";
echo "Files: " . $files->count() . "\n";
echo "PENDING: " . $pending->count() . "\n";
foreach ($pending as $p) { echo "  - " . $p . "\n"; }

// Check store row
$store = DB::table('stores')->first();
echo "Store: " . ($store ? $store->name . " | lat=" . $store->latitude . " | lng=" . $store->longitude . " | default=" . $store->is_default : "NONE") . "\n";

// Check homepage visibility setting keys
$keys = DB::table('settings')->whereIn('key', ['homepage_show_shop_now_button','homepage_show_add_to_cart','homepage_show_wishlist','homepage_show_quick_view','homepage_show_product_code','homepage_show_rating'])->pluck('key');
echo "homepage_show_* keys present: " . $keys->count() . "\n";
echo "DONE\n";
