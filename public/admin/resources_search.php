<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
header('Content-Type: application/json; charset=utf-8');
$q = trim($_GET['q'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1)); $per = 20; $offset = ($page-1)*$per;
try{
  $pdo = Database::pdo();
  if ($q === ''){
    $st = $pdo->prepare('SELECT id,slug,title,description,file_path,url FROM resources ORDER BY title ASC LIMIT ? OFFSET ?');
    $st->execute([$per, $offset]);
    $items = $st->fetchAll(PDO::FETCH_ASSOC);
    $total = $pdo->query('SELECT COUNT(*) FROM resources')->fetchColumn();
  } else {
    $like = '%' . str_replace('%','\%',$q) . '%';
    $st = $pdo->prepare('SELECT id,slug,title,description,file_path,url FROM resources WHERE title LIKE ? OR slug LIKE ? ORDER BY title ASC LIMIT ? OFFSET ?');
    $st->execute([$like,$like,$per,$offset]);
    $items = $st->fetchAll(PDO::FETCH_ASSOC);
    $totst = $pdo->prepare('SELECT COUNT(*) FROM resources WHERE title LIKE ? OR slug LIKE ?'); $totst->execute([$like,$like]); $total = $totst->fetchColumn();
  }
  echo json_encode(['ok'=>true,'items'=>$items,'total'=>intval($total),'per'=>$per,'page'=>$page]);
} catch(Exception $e){ http_response_code(500); echo json_encode(['ok'=>false,'error'=>$e->getMessage()]); }
