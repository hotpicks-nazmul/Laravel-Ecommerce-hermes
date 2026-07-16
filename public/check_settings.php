<?php
$host = 'localhost';
$db = 'nazmulstech_hamko_ecom';
$user = 'nazmulstech_hamko';
$pass = 'Rn)l=7K(p53Xd_Lc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get section order
    $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
    $stmt->execute(['homepage_section_order']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Section Order: " . ($row['value'] ?? 'NOT SET') . "\n\n";
    
    // Get all homepage settings
    $stmt = $pdo->prepare("SELECT `key`, `value` FROM settings WHERE `group` = ? ORDER BY `key`");
    $stmt->execute(['homepage']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Homepage Settings:\n";
    foreach ($rows as $r) {
        echo "  {$r['key']} = {$r['value']}\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
