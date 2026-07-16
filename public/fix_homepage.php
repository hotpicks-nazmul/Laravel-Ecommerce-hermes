<?php
$host = 'localhost';
$db = 'nazmulstech_hamko_ecom';
$user = 'nazmulstech_hamko';
$pass = 'Rn)l=7K(p53Xd_Lc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Fixing Homepage Sections ===\n\n";
    
    // 1. Get category IDs
    $stmt = $pdo->query("SELECT id, name, slug FROM categories WHERE slug IN ('houseware', 'furniture', 'cookware')");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "1. Found categories:\n";
    $catIds = [];
    foreach ($categories as $cat) {
        echo "   - {$cat['name']} (ID: {$cat['id']}, Slug: {$cat['slug']})\n";
        $catIds[] = $cat['id'];
    }
    
    // 2. Update category_products_order setting
    echo "\n2. Updating homepage_category_products_order...\n";
    $catOrderJson = json_encode($catIds);
    $stmt = $pdo->prepare("UPDATE settings SET `value` = ? WHERE `key` = 'homepage_category_products_order'");
    $stmt->execute([$catOrderJson]);
    echo "   Set to: $catOrderJson\n";
    
    // 3. Update homepage_selected_categories setting (fallback)
    echo "\n3. Updating homepage_selected_categories...\n";
    $stmt = $pdo->prepare("UPDATE settings SET `value` = ? WHERE `key` = 'homepage_selected_categories'");
    $stmt->execute([$catOrderJson]);
    echo "   Set to: $catOrderJson\n";
    
    // 4. Ensure category_products is in section_order
    echo "\n4. Checking section_order...\n";
    $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = 'homepage_section_order'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $sectionOrder = json_decode($row['value'], true);
    
    if (!in_array('category_products', $sectionOrder)) {
        // Insert after new_arrivals
        $idx = array_search('new_arrivals', $sectionOrder);
        if ($idx !== false) {
            array_splice($sectionOrder, $idx + 1, 0, ['category_products']);
        } else {
            $sectionOrder[] = 'category_products';
        }
        $stmt = $pdo->prepare("UPDATE settings SET `value` = ? WHERE `key` = 'homepage_section_order'");
        $stmt->execute([json_encode($sectionOrder)]);
        echo "   Added category_products to section_order\n";
    } else {
        echo "   category_products already in section_order\n";
    }
    echo "   Order: " . json_encode($sectionOrder) . "\n";
    
    // 5. Verify show settings
    echo "\n5. Verifying show settings...\n";
    $showKeys = [
        'homepage_show_categories_section',
        'homepage_show_featured_section', 
        'homepage_show_category_products_section',
        'homepage_show_new_arrivals_section',
        'homepage_show_sale_section',
    ];
    foreach ($showKeys as $key) {
        $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $val = $row['value'] ?? '1';
        echo "   $key = $val\n";
    }
    
    // 6. Verify products exist in categories
    echo "\n6. Products per category:\n";
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ? AND is_active = 1 AND quantity > 0");
        $stmt->execute([$cat['id']]);
        $count = $stmt->fetchColumn();
        echo "   {$cat['name']}: $count products\n";
        
        // Also check subcategories
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id IN (SELECT id FROM categories WHERE parent_id = ?) AND is_active = 1 AND quantity > 0");
        $stmt->execute([$cat['id']]);
        $subCount = $stmt->fetchColumn();
        if ($subCount > 0) {
            echo "   {$cat['name']} (subcategories): $subCount products\n";
        }
    }
    
    echo "\n=== Fix Complete! ===\n";
    echo "Clear browser cache (Ctrl+Shift+R) to see changes.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
