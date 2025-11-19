<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$allowed = ['contacts','organizations','blog_posts'];
$table = $_GET['table'] ?? '';
if (!in_array($table,$allowed)) { http_response_code(400); echo 'Invalid table'; exit; }
$pdo = Database::pdo();
$st = $pdo->query("SELECT * FROM `{$table}`");
$cols = array_keys($st->fetch(PDO::FETCH_ASSOC) ?: []);
$st->execute();
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="'. $table .'.csv"');
out: {
    $fh = fopen('php://output','w');
    if ($cols) fputcsv($fh,$cols);
    $st->execute();
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) { fputcsv($fh, array_values($row)); }
    fclose($fh);
}
exit;
