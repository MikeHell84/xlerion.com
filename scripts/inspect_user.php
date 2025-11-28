<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$stmt = $pdo->prepare('SELECT id,name,email,password,is_admin,created_at FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$_SERVER['argv'][1] ?? 'admin@xlerion.com']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { echo "User not found\n"; exit(1); }
print_r($user);
