<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user if not exists
        if (!User::where('email', 'admin@admin.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '+8801712345678',
            ]);
        }

        // Create categories for halal food
        $categories = [
            [
                'name' => 'Fresh Meat',
                'slug' => 'fresh-meat',
                'description' => 'Premium quality halal fresh meat - beef, lamb, goat',
                'icon' => 'bi bi-heart-pulse',
                'image' => '/uploads/categories/fresh-meat.svg',
            ],
            [
                'name' => 'Poultry',
                'slug' => 'poultry',
                'description' => 'Farm fresh halal chicken and duck',
                'icon' => 'bi bi-egg-fried',
                'image' => '/uploads/categories/poultry.svg',
            ],
            [
                'name' => 'Seafood',
                'slug' => 'seafood',
                'description' => 'Fresh fish and seafood from local waters',
                'icon' => 'bi bi-fish',
                'image' => '/uploads/categories/seafood.svg',
            ],
            [
                'name' => 'Fruits & Vegetables',
                'slug' => 'fruits-vegetables',
                'description' => 'Organic fresh fruits and vegetables',
                'icon' => 'bi bi-tree',
                'image' => '/uploads/categories/fruits-vegetables.svg',
            ],
            [
                'name' => 'Dairy & Eggs',
                'slug' => 'dairy-eggs',
                'description' => 'Fresh milk, cheese, butter and farm eggs',
                'icon' => 'bi bi-cup-straw',
                'image' => '/uploads/categories/dairy-eggs.svg',
            ],
            [
                'name' => 'Grocery',
                'slug' => 'grocery',
                'description' => 'Rice, flour, spices and cooking essentials',
                'icon' => 'bi bi-basket',
                'image' => '/uploads/categories/grocery.svg',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'icon' => $category['icon'],
                    'image' => $category['image'],
                    'status' => 'active',
                ]
            );
        }

        // Create products with Unsplash images
        $products = [
            // Fresh Meat
            [
                'name' => 'Premium Beef Boneless',
                'slug' => 'premium-beef-boneless',
                'sku' => 'MT001',
                'short_description' => 'Tender boneless beef from grass-fed cattle',
                'long_description' => 'Premium quality boneless beef meat sourced from grass-fed cattle. 100% halal certified. Perfect for curry, steak, or kebabs. Fresh and tender meat delivered to your doorstep.',
                'price' => 850.00,
                'sale_price' => 780.00,
                'quantity' => 50,
                'category_id' => 1,
                'featured_image' => '/uploads/products/beef.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Lamb/Mutton',
                'slug' => 'fresh-lamb-mutton',
                'sku' => 'MT002',
                'short_description' => 'Tender lamb meat perfect for biryani',
                'long_description' => 'Fresh and tender lamb/mutton meat. Perfect for making delicious biryani, tehari, or curry. 100% halal certified and quality assured.',
                'price' => 950.00,
                'sale_price' => null,
                'quantity' => 35,
                'category_id' => 1,
                'featured_image' => '/uploads/products/lamb.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Goat Meat (Khashi)',
                'slug' => 'goat-meat-khashi',
                'sku' => 'MT003',
                'short_description' => 'Premium quality khashi meat',
                'long_description' => 'Premium quality goat meat (khashi) known for its tenderness and flavor. Perfect for special occasions and festivals. 100% halal certified.',
                'price' => 1100.00,
                'sale_price' => 990.00,
                'quantity' => 25,
                'category_id' => 1,
                'featured_image' => '/uploads/products/goat.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            // Poultry
            [
                'name' => 'Farm Fresh Chicken (Whole)',
                'slug' => 'farm-fresh-chicken-whole',
                'sku' => 'PT001',
                'short_description' => 'Fresh whole chicken from local farms',
                'long_description' => 'Farm fresh whole chicken. Healthy and nutritious. Perfect for roasting, grilling, or curry. 100% halal certified and processed in hygienic conditions.',
                'price' => 280.00,
                'sale_price' => 250.00,
                'quantity' => 100,
                'category_id' => 2,
                'featured_image' => '/uploads/products/chicken.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Chicken Breast (Boneless)',
                'slug' => 'chicken-breast-boneless',
                'sku' => 'PT002',
                'short_description' => 'Premium boneless chicken breast',
                'long_description' => 'Premium quality boneless chicken breast. Lean and healthy meat perfect for grilling, frying, or salads. 100% halal certified.',
                'price' => 380.00,
                'sale_price' => null,
                'quantity' => 75,
                'category_id' => 2,
                'featured_image' => '/uploads/products/breast.svg',
                'is_featured' => false,
                'is_active' => true,
            ],
            // Seafood
            [
                'name' => 'Rui Fish (Rohu)',
                'slug' => 'rui-fish-rohu',
                'sku' => 'SF001',
                'short_description' => 'Fresh water Rui fish from local rivers',
                'long_description' => 'Fresh Rui fish (Rohu) sourced from local rivers. Known for its taste and nutritional value. Perfect for curry, fry, or steaming.',
                'price' => 350.00,
                'sale_price' => 320.00,
                'quantity' => 40,
                'category_id' => 3,
                'featured_image' => '/uploads/products/rui.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Hilsa Fish (Ilish)',
                'slug' => 'hilsa-fish-ilish',
                'sku' => 'SF002',
                'short_description' => 'Premium Hilsa fish - King of Bengali cuisine',
                'long_description' => 'Premium quality Hilsa fish, the king of Bengali cuisine. Known for its unique taste and aroma. Perfect for Shorshe Ilish or Bhapa Ilish.',
                'price' => 1500.00,
                'sale_price' => null,
                'quantity' => 20,
                'category_id' => 3,
                'featured_image' => '/uploads/products/hilsa.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Prawn/Shrimp (Large)',
                'slug' => 'prawn-shrimp-large',
                'sku' => 'SF003',
                'short_description' => 'Jumbo size fresh prawns',
                'long_description' => 'Large size fresh prawns perfect for malai curry or grilling. Sourced from coastal waters. High in protein and taste.',
                'price' => 850.00,
                'sale_price' => 750.00,
                'quantity' => 30,
                'category_id' => 3,
                'featured_image' => '/uploads/products/prawn.svg',
                'is_featured' => false,
                'is_active' => true,
            ],
            // Fruits & Vegetables
            [
                'name' => 'Organic Vegetables Pack',
                'slug' => 'organic-vegetables-pack',
                'sku' => 'FV001',
                'short_description' => 'Fresh organic vegetable assortment',
                'long_description' => 'A pack of fresh organic vegetables including tomatoes, potatoes, onions, carrots, and leafy greens. Pesticide-free and naturally grown.',
                'price' => 250.00,
                'sale_price' => 220.00,
                'quantity' => 60,
                'category_id' => 4,
                'featured_image' => '/uploads/products/vegetables.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Seasonal Fruits Basket',
                'slug' => 'seasonal-fruits-basket',
                'sku' => 'FV002',
                'short_description' => 'Fresh seasonal fruits assortment',
                'long_description' => 'A basket of fresh seasonal fruits including mangoes, bananas, apples, oranges, and grapes. Fresh and nutritious.',
                'price' => 450.00,
                'sale_price' => null,
                'quantity' => 45,
                'category_id' => 4,
                'featured_image' => '/uploads/products/fruits.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            // Dairy & Eggs
            [
                'name' => 'Farm Fresh Eggs (12 pcs)',
                'slug' => 'farm-fresh-eggs-12-pcs',
                'sku' => 'DE001',
                'short_description' => 'Nutritious farm fresh eggs',
                'long_description' => 'Farm fresh eggs from healthy hens. Rich in protein and nutrients. Perfect for breakfast, baking, or cooking.',
                'price' => 180.00,
                'sale_price' => 160.00,
                'quantity' => 100,
                'category_id' => 5,
                'featured_image' => '/uploads/products/eggs.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Milk (1 Liter)',
                'slug' => 'fresh-milk-1-liter',
                'sku' => 'DE002',
                'short_description' => 'Pure fresh cow milk',
                'long_description' => 'Pure fresh cow milk delivered daily. No preservatives or additives. Rich in calcium and vitamins.',
                'price' => 90.00,
                'sale_price' => null,
                'quantity' => 80,
                'category_id' => 5,
                'featured_image' => '/uploads/products/milk.svg',
                'is_featured' => false,
                'is_active' => true,
            ],
            // Grocery
            [
                'name' => 'Premium Basmati Rice (5kg)',
                'slug' => 'premium-basmati-rice-5kg',
                'sku' => 'GR001',
                'short_description' => 'Aged premium basmati rice',
                'long_description' => 'Premium quality aged basmati rice. Long grain and aromatic. Perfect for biryani, pulao, or plain rice.',
                'price' => 650.00,
                'sale_price' => 580.00,
                'quantity' => 50,
                'category_id' => 6,
                'featured_image' => '/uploads/products/rice.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Pure Mustard Oil (1 Liter)',
                'slug' => 'pure-mustard-oil-1-liter',
                'sku' => 'GR002',
                'short_description' => 'Cold pressed pure mustard oil',
                'long_description' => 'Cold pressed pure mustard oil. Traditional extraction method. Perfect for cooking Bengali dishes.',
                'price' => 220.00,
                'sale_price' => null,
                'quantity' => 70,
                'category_id' => 6,
                'featured_image' => '/uploads/products/oil.svg',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Spice Box (Mixed Spices)',
                'slug' => 'spice-box-mixed-spices',
                'sku' => 'GR003',
                'short_description' => 'Essential Bengali spices collection',
                'long_description' => 'A collection of essential Bengali spices including turmeric, cumin, coriander, red chili, and garam masala. Premium quality.',
                'price' => 350.00,
                'sale_price' => 299.00,
                'quantity' => 40,
                'category_id' => 6,
                'featured_image' => '/uploads/products/spices.svg',
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }

        // Create default settings
        $settings = [
            ['key' => 'site_name', 'value' => 'Halal Food Store'],
            ['key' => 'site_tagline', 'value' => 'Premium Quality Halal Food'],
            ['key' => 'site_email', 'value' => 'info@halalfood.com'],
            ['key' => 'site_phone', 'value' => '+8801712345678'],
            ['key' => 'site_address', 'value' => '123 Food Street, Dhaka, Bangladesh'],
            ['key' => 'currency', 'value' => 'BDT'],
            ['key' => 'active_theme', 'value' => 'general'],
            ['key' => 'meta_title', 'value' => 'Halal Food Store - Premium Quality Halal Food in Bangladesh'],
            ['key' => 'meta_description', 'value' => 'Buy premium quality halal meat, poultry, seafood, and groceries online. Fresh delivery across Bangladesh.'],
            ['key' => 'meta_keywords', 'value' => 'halal food, halal meat, online grocery, fresh meat, halal chicken, Bangladesh'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
