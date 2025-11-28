<?php
require_once __DIR__ . '/../../src/Auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$token = $_GET['token'] ?? $_POST['token'] ?? null;
$msg = null; $error = null;
if (!$token) { http_response_code(400); echo 'Token missing'; exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw = $_POST['password'] ?? '';
    $pw2 = $_POST['password_confirm'] ?? '';
    if ($pw !== $pw2) { $error = 'Passwords do not match'; }
    elseif (strlen($pw) < 8) { $error = 'La contraseña debe tener al menos 8 caracteres'; }
    else {
        if (Auth::resetPassword($token, $pw)) { $msg = 'Contraseña actualizada. Puedes entrar.'; }
        else { $error = 'Token inválido o expirado'; }
    }
}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><title>Reset Password</title></head><body>
  <h2>Nueva contraseña</h2>
  <?php if ($msg) echo '<p style="color:green">'.htmlspecialchars($msg).'</p>'; ?>
  <?php if ($error) echo '<p style="color:red">'.htmlspecialchars($error).'</p>'; ?>
  <form method="post">
    <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
    <label>Nueva contraseña<br><input name="password" type="password" required></label><br>
    <label>Confirmar<br><input name="password_confirm" type="password" required></label><br>
    <button type="submit">Cambiar</button>
  </form>
</body></html>
