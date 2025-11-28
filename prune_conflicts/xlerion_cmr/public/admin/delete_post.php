<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) { header('Location: /admin/posts.php'); exit; }
$pdo = Database::pdo();
$id = intval($_POST['id']);
try{ $st = $pdo->prepare('DELETE FROM blog_posts WHERE id = ?'); $st->execute([$id]); } catch(Exception $e){ error_log('delete_post: '.$e->getMessage()); }
// Prefer explicit return_to (from form), then safe HTTP_REFERER, else posts list
$return = $_POST['return_to'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
if ($return) {
	$u = parse_url($return);
	if (isset($u['path']) && strpos($u['path'], '/admin') === 0) { header('Location: ' . $return); exit; }
}
header('Location: /admin/posts.php'); exit;
