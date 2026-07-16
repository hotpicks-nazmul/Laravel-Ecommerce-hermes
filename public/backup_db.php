<?php
$host = 'localhost';
$db = 'nazmulstech_hamko_ecom';
$user = 'nazmulstech_hamko';
$pass = 'Rn)l=7K(p53Xd_Lc';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables found: " . count($tables) . "\n";
    
    // Export each table
    $backup = "-- Hamko Bazar Live Database Backup\n";
    $backup .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
    $backup .= "-- Database: $db\n\n";
    $backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    foreach ($tables as $table) {
        // Get CREATE TABLE
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $backup .= "DROP TABLE IF EXISTS `$table`;\n";
        $backup .= $row['Create Table'] . ";\n\n";
        
        // Get data
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows) > 0) {
            $backup .= "INSERT INTO `$table` VALUES\n";
            $values = [];
            foreach ($rows as $row) {
                $vals = [];
                foreach ($row as $v) {
                    if ($v === null) {
                        $vals[] = 'NULL';
                    } else {
                        $vals[] = "'" . addslashes($v) . "'";
                    }
                }
                $values[] = '(' . implode(', ', $vals) . ')';
            }
            $backup .= implode(",\n", $values) . ";\n\n";
        }
    }
    
    $backup .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    // Save to file
    $filename = '/tmp/hamko_live_backup_' . date('Y%m%d_%H%M%S') . '.sql';
    file_put_contents($filename, $backup);
    
    echo "Backup saved to: $filename\n";
    echo "Size: " . number_format(filesize($filename)) . " bytes\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
