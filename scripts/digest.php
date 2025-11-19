<?php
// Daily digest: new form submissions and new contacts in last 24h
chdir(dirname(__DIR__));
$env = parse_ini_file('.env', INI_SCANNER_RAW);
$pdo = new PDO("mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4", $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$since = date('Y-m-d H:i:s', time() - 86400);
$forms = $pdo->prepare('SELECT * FROM forms_submissions WHERE created_at >= ?'); $forms->execute([$since]); $forms = $forms->fetchAll();
$contacts = $pdo->prepare('SELECT * FROM contacts WHERE created_at >= ?'); $contacts->execute([$since]); $contacts = $contacts->fetchAll();
$body = "Resumen diario (últimas 24h):\nForm submissions: " . count($forms) . "\nNuevos contactos: " . count($contacts) . "\n";
$admin = $env['MAIL_ADMIN'] ?? 'admin@xlerion.com';
$headers = "From: " . ($env['MAIL_FROM'] ?? 'webmaster@xlerion.com') . "\r\nContent-Type: text/plain; charset=UTF-8";
@mail($admin, 'Digest diario Xlerion', $body, $headers);
echo "Digest sent\n";
