<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$pdo = Database::pdo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
  header('Location: /admin/contacts.php'); exit;
}

$id = intval($_POST['id'] ?? 0);
if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')){
  $_SESSION['flash_error'] = 'CSRF inválido';
  header('Location: /admin/contacts.php'); exit;
}

try{
  $d = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
  $d->execute([$id]);
  $_SESSION['flash'] = 'Contacto eliminado';
} catch (Exception $e){
  $_SESSION['flash_error'] = 'Error al eliminar';
}
header('Location: /admin/contacts.php'); exit;
