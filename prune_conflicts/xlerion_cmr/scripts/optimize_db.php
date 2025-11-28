<?php
// Simple optimize: run ANALYZE and OPTIMIZE on key tables
chdir(dirname(__DIR__));
$env = parse_ini_file('.env', INI_SCANNER_RAW);
$pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4", $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$tables = ['contacts','interactions','opportunities','blog_posts','cms_pages'];
foreach ($tables as $t) {
  try { $pdo->exec("ANALYZE TABLE `{$t}`"); $pdo->exec("OPTIMIZE TABLE `{$t}`"); } catch (Exception $e) { }
}
echo "Optimize done\n";
