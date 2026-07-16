<?php
$host = 'localhost';
$db = 'nazmulstech_hamko_ecom';
$user = 'nazmulstech_hamko';
$pass = 'Rn)l=7K(p53Xd_Lc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check featured products
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_featured = 1 AND is_active = 1");
    $featuredCount = $stmt->fetchColumn();
    echo "Featured products (is_featured=1): $featuredCount\n";
    
    // Check active products
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1");
    $activeCount = $stmt->fetchColumn();
    echo "Active products: $activeCount\n";
    
    // Check products with stock
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1 AND quantity > 0");
    $stockCount = $stmt->fetchColumn();
    echo "Products in stock: $stockCount\n";
    
    // Check homepage section order
    $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
    $stmt->execute(['homepage_section_order']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nSection Order: " . ($row['value'] ?? 'NOT SET') . "\n";
    
    // Check show settings
    $showSettings = ['homepage_show_featured_section', 'homepage_show_new_arrivals_section', 'homepage_show_sale_section', 'homepage_show_category_products_section'];
    foreach ($showSettings as $key) {
        $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "$key = " . ($row['value'] ?? 'NOT SET') . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
