<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
// Build data from existing footer_ settings
$stmt = $pdo->query("SELECT k,v FROM settings WHERE k LIKE 'footer_%'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$data = [];
foreach($rows as $r) { $data[$r['k']] = $r['v']; }
// If footer_variants table doesn't exist, create it
try{
  $pdo->exec("CREATE TABLE IF NOT EXISTS footer_variants (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, data TEXT, created_at DATETIME)");
} catch(Exception $e){ /* ignore */ }
$name = 'Import desde settings ' . date('Y-m-d H:i:s');
$insert = $pdo->prepare('INSERT INTO footer_variants (name, data, created_at) VALUES (?, ?, ?)');
$ok = $insert->execute([$name, json_encode($data, JSON_UNESCAPED_UNICODE), date('Y-m-d H:i:s')]);
if (!$ok) { echo "ERROR inserting variant\n"; exit(1); }
$id = $pdo->lastInsertId();
// write active_footer_id to settings (insert or update)
try{
  $exists = $pdo->prepare("SELECT COUNT(1) FROM settings WHERE k = ?"); $exists->execute(['active_footer_id']);
  if ($exists->fetchColumn() > 0) {
    $upd = $pdo->prepare("UPDATE settings SET v = ? WHERE k = ?"); $upd->execute([$id, 'active_footer_id']);
  } else {
    $ins = $pdo->prepare("INSERT INTO settings (k,v) VALUES (?,?)"); $ins->execute(['active_footer_id', $id]);
  }
} catch(Exception $e){ echo 'ERROR writing active_footer_id: ' . $e->getMessage() . "\n"; exit(1); }
echo json_encode(['created_variant_id' => $id, 'name' => $name], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
