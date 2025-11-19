<?php
// Simple seeder: parse contenido.txt into sections and insert into cms_pages
$envPath = dirname(__DIR__) . '/.env';
if (!file_exists($envPath)) { echo ".env not found. Create from .env.example\n"; exit(1); }
// load .env into environment for CLI
foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
  if (strpos(trim($line),'#')===0) continue;
  [$k,$v] = array_map('trim', explode('=', $line, 2) + [1 => null]);
  if ($k) putenv("{$k}={$v}");
}
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$txtPath = dirname(__DIR__,2) . '/contenido.txt';
if (!file_exists($txtPath)) $txtPath = dirname(__DIR__) . '/contenido.txt';
if (!file_exists($txtPath)) { echo "contenido.txt not found at {$txtPath}\n"; exit(1); }
$txt = file_get_contents($txtPath);
// Define headings and slug map in order
$sections = [
  '🏠 Inicio' => 'inicio',
  '🧬 Filosofía' => 'filosofia',
  '🛠️ Soluciones' => 'soluciones',
  '🎮 Proyectos' => 'proyectos',
  '📚 Documentación' => 'documentacion',
  '🧠 Acerca del Creador' => 'acerca-del-creador',
  '🤝 Convocatorias y Alianzas' => 'convocatorias-alianzas',
  '📩 Contacto' => 'contacto',
  '🧩 Blog / Bitácora' => 'blog',
  '🛡️ Legal y Privacidad' => 'legal'
];
// Split by headings
$parts = [];
$lines = preg_split("/\r?\n/", $txt);
$current = null; foreach ($lines as $line) {
  $t = trim($line);
  if ($t==='') continue;
  if (isset($sections[$t])) { $current = $t; $parts[$current] = ''; continue; }
  if ($current) $parts[$current] .= $line . "\n";
}
// Insert into cms_pages
// Use PHP timestamps for portability (SQLite)
$now = date('Y-m-d H:i:s');
foreach ($sections as $heading => $slug) {
  $title = preg_replace('/^[^\p{L}\p{N}]*/u','',$heading);
  $content = isset($parts[$heading]) ? $parts[$heading] : $title;
  $excerpt = substr(strip_tags($content),0,200);
  // Use INSERT OR REPLACE for idempotent seeding; quote values to avoid param issues with sqlite
  $s_slug = $pdo->quote($slug);
  $s_title = $pdo->quote($title);
  $s_excerpt = $pdo->quote($excerpt);
  $s_content = $pdo->quote($content);
  $s_now = $pdo->quote($now);
  $sql = "INSERT OR REPLACE INTO cms_pages (slug,title,excerpt,content,is_published,created_at,updated_at) VALUES ($s_slug, $s_title, $s_excerpt, $s_content, 1, $s_now, $s_now)";
  try {
    $pdo->exec($sql);
    echo "Inserted/Updated $slug\n";
  } catch (Exception $e) {
    echo "Skip $slug: " . $e->getMessage() . "\n";
  }
}
echo "Seed finished\n";
