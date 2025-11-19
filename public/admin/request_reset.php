<?php
require_once __DIR__ . '/../../src/Auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pdo = Database::pdo();
    $st = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1'); $st->execute([$email]);
    if ($st->fetch()) {
        $token = Auth::generateResetToken($email);
        $link = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'https') . '://' . ($_SERVER['HTTP_HOST'] ?? 'xlerion.com') . '/admin/reset_password.php?token=' . $token;
        $body = "Solicitaste reiniciar tu contraseña. Usa el siguiente enlace (válido 1 hora): $link";
        @mail(getenv('MAIL_ADMIN') ?: 'admin@xlerion.com','Reset de contraseña',$body,"From: " . (getenv('MAIL_FROM') ?? 'webmaster@xlerion.com'));
    }
    $msg = 'Si el correo existe, hemos enviado instrucciones.';
}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><title>Reset</title></head><body>
  <h2>Restablecer contraseña</h2>
  <?php if ($msg) echo '<p>'.htmlspecialchars($msg).'</p>'; ?>
  <form method="post">
    <label>Email<br><input name="email" type="email" required></label><br>
    <button type="submit">Enviar instrucciones</button>
  </form>
</body></html>
