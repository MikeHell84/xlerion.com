<?php
$root = __DIR__ . '/..';
if (!is_dir($root . '/storage')) mkdir($root . '/storage', 0755, true);
$dbFile = $root . '/storage/database.sqlite';
if (!file_exists($dbFile)) file_put_contents($dbFile, '');
$sqlFile = $root . '/sql/sqlite_migrations.sql';
if (!file_exists($sqlFile)) { echo "sqlite migrations file missing\n"; exit(1); }
$sql = file_get_contents($sqlFile);
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // execute statements individually and ignore 'table exists' errors
    $stmts = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
    foreach ($stmts as $s) {
        try {
            if ($s !== '') $db->exec($s);
        } catch (PDOException $pe) {
            // ignore duplicate table errors, rethrow others
            if (stripos($pe->getMessage(), 'table') !== false && stripos($pe->getMessage(), 'exists') !== false) {
                // skip
            } else {
                throw $pe;
            }
        }
    }
    echo "sqlite migrations executed\n";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n"; exit(1);
}
