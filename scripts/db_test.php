<?php
require __DIR__ . '/../src/Model/Database.php';
try {
    $pdo = Database::pdo();
    echo $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . PHP_EOL;
} catch (Exception $e) {
    echo 'ERROR: ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
