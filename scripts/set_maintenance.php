<?php
// Quick script to set maintenance_mode = '0' in local sqlite settings
$path = __DIR__ . '/../storage/database.sqlite';
if (!file_exists($path)){
    echo "ERROR: DB file not found: $path\n";
    exit(2);
}
try{
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (k TEXT PRIMARY KEY, v TEXT)");
    $stmt = $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)');
    $stmt->execute(['maintenance_mode','0']);
    echo "OK: maintenance_mode set to 0\n";
} catch (Exception $e){
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
