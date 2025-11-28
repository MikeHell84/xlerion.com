<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$fix = [
  'footer_social_indiegogo' => 'https://www.indiegogo.com/es/profile/miguel_rodriguez-martinez_edb9',
  'footer_social_kickstarter' => 'https://www.kickstarter.com/profile/xlerionstudios',
  'footer_social_patreon' => 'https://www.patreon.com/xlerionstudios'
];
$up = $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)');
foreach ($fix as $k => $v) { $up->execute([$k, $v]); }
// Rebuild snapshot from current footer_* settings
$stmt = $pdo->query("SELECT k,v FROM settings WHERE k LIKE 'footer_%'");
$out = [];
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) { $out[$r['k']] = $r['v']; }
$json = json_encode($out, JSON_UNESCAPED_UNICODE);
// Update variant id 1 if exists
$fv = $pdo->prepare('UPDATE footer_variants SET data = ? WHERE id = ?');
$fv->execute([$json, 1]);
echo "OK\n";
