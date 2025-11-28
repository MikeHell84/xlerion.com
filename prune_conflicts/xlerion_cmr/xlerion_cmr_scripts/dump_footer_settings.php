<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
// Fetch footer settings
$stmt = $pdo->query("SELECT k,v FROM settings WHERE k LIKE 'footer_%' ORDER BY k");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$active = $pdo->query("SELECT v FROM settings WHERE k='active_footer_id'")->fetchColumn();
$variants = [];
try{
  $vstmt = $pdo->query("SELECT id, name, substr(data,1,200) as sample, created_at FROM footer_variants ORDER BY id DESC");
  $variants = $vstmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) { $variants = ['error' => $e->getMessage()]; }
$out = ['settings' => $rows, 'active_footer_id' => $active, 'footer_variants' => $variants];
header('Content-Type: application/json');
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
