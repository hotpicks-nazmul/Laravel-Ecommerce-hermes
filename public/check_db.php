<?php
// Simple database test without full Laravel bootstrap
$host = 'localhost';
$db = 'ecom';
$user = 'nazmul';
$pass = 'abc123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "DB Connected\n";
    
    // Check sliders
    $stmt = $pdo->query("SELECT id, title, show_title FROM sliders");
    $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Sliders: " . count($sliders) . "\n";
    foreach ($sliders as $s) {
        echo "  - {$s['id']}: {$s['title']} (show_title={$s['show_title']})\n";
    }
    
    // Check blogs
    $stmt = $pdo->query("SELECT id, title, slug FROM blogs ORDER BY id");
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Blogs: " . count($blogs) . "\n";
    foreach ($blogs as $b) {
        echo "  - {$b['id']}: {$b['title']}\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
