<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
$ids = $_POST['files'] ?? [];
$ids = $_POST['files'] ?? [];
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) { http_response_code(403); echo 'Forbidden'; exit; }
if (!is_array($ids) || empty($ids)) { header('Location:/admin/media_manager.php'); exit; }
$pdo = Database::pdo();
$placeholders = implode(',', array_fill(0,count($ids),'?'));
$stmt = $pdo->prepare("SELECT id,filename,url,thumb320,thumb720,mime,size,uploaded_by,created_at,deleted_at FROM media_files WHERE id IN ($placeholders)");
$stmt->execute($ids); $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="media_export_'.date('Ymd_His').'.csv"');
$out = fopen('php://output','w');
fputcsv($out, array_keys($rows[0] ?? ['id'=>'id','filename'=>'filename']));
foreach ($rows as $r) fputcsv($out, $r);
fclose($out); exit;
