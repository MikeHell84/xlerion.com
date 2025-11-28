<?php
// Execute all .sql files under database/migrations in alphabetical order
$base = dirname(__DIR__);
require_once $base . '/src/Model/Database.php';
$pdo = Database::pdo();
$migDir = $base . '/database/migrations';
$files = glob($migDir . '/*.sql');
sort($files);
foreach ($files as $f) {
    echo "Applying: $f\n";
    $sql = file_get_contents($f);
    try {
        $pdo->exec($sql);
        echo "OK\n";
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
