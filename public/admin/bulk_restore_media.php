<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
$ids = $_POST['files'] ?? []; if (!is_array($ids)||empty($ids)) { header('Location:/admin/media_manager.php'); exit; }
$pdo = Database::pdo();
try {
  $pdo->beginTransaction();
  $placeholders = implode(',', array_fill(0,count($ids),'?'));
  $stmt = $pdo->prepare("UPDATE media_files SET deleted_at = NULL, deleted_by = NULL WHERE id IN ($placeholders)");
  $stmt->execute($ids);
  $pdo->commit(); $_SESSION['flash'] = "Restaurados: " . count($ids);
} catch (Exception $e) { $pdo->rollBack(); $_SESSION['flash'] = 'Error: '.$e->getMessage(); }
header('Location: /admin/media_manager.php'); exit;
