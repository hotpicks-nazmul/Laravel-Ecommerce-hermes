<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Store;

echo "STORES:\n";
$stores = Store::all(['id', 'name', 'is_default', 'is_active', 'latitude', 'longitude', 'address']);
foreach ($stores as $s) {
    echo $s->id . " | " . $s->name . " | default=" . ($s->is_default ? 'Y' : 'N') . " | active=" . ($s->is_active ? 'Y' : 'N') . " | lat=" . $s->latitude . " | lng=" . $s->longitude . "\n";
}
$d = Store::getDefault();
echo "getDefault: ";
if ($d) { echo $d->id . " | lat=" . $d->latitude . " | lng=" . $d->longitude . "\n"; } else { echo "NONE\n"; }
