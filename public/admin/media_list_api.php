<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();

header('Content-Type: application/json; charset=utf-8');
$pdo = Database::pdo();
$q = trim($_GET['q'] ?? '');
$page = max(1,intval($_GET['page'] ?? 1)); $per = 24; $offset = ($page-1)*$per;
$showTrashed = intval($_GET['show_trashed'] ?? 0) === 1;
$where = [];$params = [];
if ($q !== '') { $where[] = '(filename LIKE ? OR url LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if (!$showTrashed) { $where[] = 'deleted_at IS NULL'; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$total = $pdo->prepare("SELECT COUNT(*) FROM media_files $whereSql"); $total->execute($params); $totalCount = $total->fetchColumn();
$stmt = $pdo->prepare("SELECT id,filename,url,thumb320,thumb720,mime,size,uploaded_by,created_at,deleted_at FROM media_files $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?");
array_push($params, $per, $offset); $stmt->execute($params); $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['ok'=>true,'total'=>intval($totalCount),'page'=>$page,'per'=>$per,'items'=>$rows]);
