<?php
// Lightweight backup: export selected tables to JSON files in storage/backups
chdir(dirname(__DIR__));
$env = parse_ini_file('.env', INI_SCANNER_RAW);
$pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4", $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$tables = ['contacts','organizations','cms_pages','cms_blocks','blog_posts','newsletter_subscribers'];
$backupDir = __DIR__ . '/../storage/backups'; if (!is_dir($backupDir)) mkdir($backupDir,0755, true);
foreach ($tables as $t) {
  $rows = $pdo->query("SELECT * FROM `{$t}`")->fetchAll(PDO::FETCH_ASSOC);
  file_put_contents("{$backupDir}/{$t}_".date('Ymd_His').".json", json_encode($rows, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}
// rotate: keep last 7 files
$files = glob("{$backupDir}/*.json");
if (count($files)>7) {
  usort($files, function($a,$b){return filemtime($a)-filemtime($b);});
  while (count($files)>7) { unlink(array_shift($files)); }
}
echo "Backup done\n";
