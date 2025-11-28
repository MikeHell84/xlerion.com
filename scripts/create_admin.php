<?php
// Usage: php create_admin.php "Admin Name" admin@example.com "plaintextpassword"
if ($argc < 4) { echo "Usage: php create_admin.php \"Name\" email password\n"; exit(1); }
$name = $argv[1]; $email = $argv[2]; $password = $argv[3];
require_once __DIR__ . '/../src/Model/Database.php';
// load .env if exists
$env = __DIR__ . '/../.env'; if (file_exists($env)) foreach (file($env, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $l) { if (strpos(trim($l),'#')===0) continue; [$k,$v]=array_map('trim',explode('=', $l,2)+[1=>null]); if ($k) putenv("$k=$v"); }
$pdo = Database::pdo();
$hash = password_hash($password, PASSWORD_DEFAULT);
$st = $pdo->prepare('INSERT INTO users (name,email,password,is_admin,created_at,updated_at) VALUES (?,?,?,?,?,?)');
$now = date('Y-m-d H:i:s');
try { $st->execute([$name,$email,$hash,1,$now,$now]); echo "Admin created: $email\n"; } catch (Exception $e) { echo "Error: " . $e->getMessage() . "\n"; }
