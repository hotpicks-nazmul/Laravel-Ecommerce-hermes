<?php
// sync_simple.php - Simple data sync script
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

echo "Syncing...\n";

// Slider updates
DB::table('sliders')->where('id', 1)->update(['show_title' => 0, 'show_subtitle' => 0, 'show_button' => 0, 'show_gradient' => 0]);
DB::table('sliders')->where('id', 3)->update(['show_title' => 0, 'show_subtitle' => 0, 'show_button' => 0, 'show_gradient' => 0]);
DB::table('sliders')->where('id', 4)->update(['show_title' => 0, 'show_subtitle' => 0, 'show_button' => 0, 'show_gradient' => 0]);
echo "Sliders updated\n";

// Check existing blogs
$existing = DB::table('blogs')->pluck('slug')->toArray();
echo "Existing blogs: " . implode(', ', $existing) . "\n";

// Blog 1
if (!in_array('hamko-houseware-quality-you-can-trust', $existing)) {
    DB::table('blogs')->insert([
        'title' => 'Transform Your Home with Hamko Houseware: Quality You Can Trust',
        'slug' => 'hamko-houseware-quality-you-can-trust',
        'excerpt' => 'Discover why Hamko Houseware is Bangladeshs trusted choice for quality storage solutions, kitchen essentials, and everyday home products.',
        'content' => '<h2>Transform Your Home with Hamko Houseware</h2><p>Every home tells a story, and the houseware products within it speak volumes about the people who live there. At Hamko, we understand that houseware is not just about functionality - it is about creating an environment that reflects your style, meets your daily needs, and stands the test of time.</p><h3>Storage Solutions That Organize Your Life</h3><p>Hamko storage containers come in multiple sizes - 3L, 5L, 7L, and 10L - making them perfect for storing everything from kitchen staples to seasonal clothing. The containers feature airtight lids that keep contents fresh and protected from moisture and insects.</p><h3>Buckets: The Everyday Essential</h3><p>No Bangladeshi home is complete without reliable buckets. Hamko bucket collection includes the popular Design Bucket series, available in 13L, 16L, and 20L capacities.</p><h3>Kitchen Essentials</h3><p>The Hamko Cafe Tool is a premium houseware product. This versatile kitchen companion helps with food preparation, serving, and organization.</p><h3>Durability That Lasts Years</h3><p>What sets Hamko houseware apart is the durability. We use premium-grade materials that resist cracking, fading, and warping.</p><h3>Affordable Luxury for Every Budget</h3><p>With prices ranging from 50 Taka to 410 Taka, there is something for every budget.</p><h3>Conclusion</h3><p>Your home deserves the best, and Hamko is here to deliver. Visit our online store today.</p>',
        'featured_image' => '/storage/sliders/6a829403562ba_1786942467.jpg.webp',
        'category_id' => 16,
        'author_id' => 1,
        'status' => 'published',
        'published_at' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    echo "Blog 1 created\n";
}

// Blog 2
if (!in_array('hamko-furniture-comfort-style-bangladeshi-homes', $existing)) {
    DB::table('blogs')->insert([
        'title' => 'Hamko Furniture: Redefining Comfort and Style for Bangladeshi Homes',
        'slug' => 'hamko-furniture-comfort-style-bangladeshi-homes',
        'excerpt' => 'Explore Hamko complete furniture collection - from classic dining chairs to garden furniture, baby chairs to executive seating.',
        'content' => '<h2>Hamko Furniture: Redefining Comfort and Style</h2><p>Furniture is more than just functional - it is the foundation of your home personality. Hamko Furniture has been serving Bangladeshi families with high-quality, durable, and stylish furniture solutions.</p><h3>The Classic Collection</h3><p>Hamko Classic Flora Chair (1,300 Taka) features an elegant floral pattern. Classic Super Chair (1,380 Taka) offers enhanced comfort at 45cm height.</p><h3>Garden Furniture</h3><p>Hamko Garden Chair (570 Taka) is designed for outdoor use with weather-resistant materials.</p><h3>Dining Furniture</h3><p>Dining Heavy Chair (670 Taka) and Dining Super Chair (750 Taka) offer durability and comfort for family meals.</p><h3>Specialized Furniture</h3><p>Baby Chair (290 Taka) for little ones, Commode Chair (850 Taka) for elderly family members, and Folding Super Chair (1,190 Taka) for space-saving needs.</p><h3>The President Collection</h3><p>President Chair (990 Taka) offers executive-level comfort and style for home offices.</p><h3>Conclusion</h3><p>Your home deserves furniture that combines comfort, style, and durability. Visit Hamko today.</p>',
        'featured_image' => '/storage/sliders/6a829481afad8_1786942593.jpg.webp',
        'category_id' => 17,
        'author_id' => 1,
        'status' => 'published',
        'published_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
    ]);
    echo "Blog 2 created\n";
}

// Blog 3
if (!in_array('hamko-cookware-premium-kitchen-essentials', $existing)) {
    DB::table('blogs')->insert([
        'title' => 'Hamko Cookware: Elevate Your Cooking Experience with Premium Kitchen Essentials',
        'slug' => 'hamko-cookware-premium-kitchen-essentials',
        'excerpt' => 'Discover Hamko premium cookware collection - from pressure cookers to stainless steel pans and casserole pots.',
        'content' => '<h2>Hamko Cookware: Elevate Your Cooking Experience</h2><p>Cooking is an art, and every artist needs the right tools. Hamko Cookware brings you premium kitchen essentials designed to make every meal a masterpiece.</p><h3>Pressure Cookers</h3><p>SS Three Layer Pressure Cooker available in 3.5L (2,245 Taka), 4.5L (2,500 Taka), 5.5L (2,885 Taka), and 6.5L (3,015 Taka). Triple-layer construction ensures even heat distribution.</p><h3>Stainless Steel Cookware</h3><p>Long Handle SS Fry Pan with Lid available in 26cm (1,605 Taka) and 24cm (1,435 Taka). Professional results at home.</p><h3>Casserole Pans</h3><p>Casserole Pan with Glass Lid available in 28cm (2,320 Taka) and 26cm (2,000 Taka). For slow-cooked perfection.</p><h3>Why Stainless Steel</h3><p>Durability, health safety, even heating, easy maintenance, and professional appearance.</p><h3>Cooking Tips</h3><p>Preheat before adding oil, use the water test for pan temperature, and cook low and slow for casserole dishes.</p><h3>Conclusion</h3><p>The right cookware transforms cooking from a chore into a pleasure. Invest in Hamko today.</p>',
        'featured_image' => '/storage/sliders/6a82948aae7f3_1786942602.jpg.webp',
        'category_id' => 18,
        'author_id' => 1,
        'status' => 'published',
        'published_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
    ]);
    echo "Blog 3 created\n";
}

// Clear caches
Artisan::call('cache:clear');
Artisan::call('config:clear');
Artisan::call('view:clear');
echo "Caches cleared\n";

echo "Done!\n";
