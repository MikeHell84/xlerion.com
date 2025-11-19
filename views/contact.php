<?php
$title = 'Contacto - Xlerion';
ob_start(); ?>
<h2>Contacto</h2>
<form method="post" action="/contact">
  <label>Nombre<br><input name="name" required></label><br>
  <label>Email<br><input name="email" type="email" required></label><br>
  <label>Mensaje<br><textarea name="message" required></textarea></label><br>
  <!-- honeypot field for bots: keep hidden, non-focusable, but include accessibility attributes -->
  <input type="text" name="website" title="Deje este campo en blanco" placeholder="No llenar" aria-hidden="true" class="honeypot-hidden" tabindex="-1" autocomplete="off">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
  <button type="submit" class="btn">Enviar</button>
</form>
<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
