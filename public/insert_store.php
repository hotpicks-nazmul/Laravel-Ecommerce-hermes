<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Store;

$store = Store::updateOrCreate(
    ['slug' => 'hamko-industries-jj-tower'],
    [
        'name' => 'HAMKO Industries Ltd (J & J Tower)',
        'latitude' => 22.81920141159502,
        'longitude' => 89.55057870666296,
        'address' => 'Address- 1/3, Chorer Hat Main Road, Alom Nagar, Khalishpur, Khulna.',
        'city' => 'Khulna',
        'country' => 'Bangladesh',
        'email' => 'hamkobazar@gmail.com',
        'phone' => '+880 1766-664488',
        'is_active' => true,
        'is_default' => true,
        'is_physical' => true,
        'sort_order' => 0,
    ]
);
// Ensure exactly one default store
Store::where('id', '!=', $store->id)->update(['is_default' => false]);

echo "Store ID: " . $store->id . " | lat=" . $store->latitude . " | lng=" . $store->longitude . " | default=" . ($store->is_default ? 'Y' : 'N') . "\n";
echo "DONE\n";
