<?php
if (session_status()!==PHP_SESSION_ACTIVE) session_start();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin/dashboard.php', PHP_URL_PATH);
$links = [
  ['/admin/dashboard.php', 'Resumen'],
  ['/admin/pages.php', 'Páginas'],
  ['/admin/contacts.php', 'Contactos'],
  ['/admin/media.php', 'Medios'],
  ['/admin/import.php', 'Importar'],
  ['/admin/export.php?table=contacts', 'Exportar'],
  ['/admin/settings.php', 'Ajustes'],
  ['/admin/logout.php', 'Salir']
];
?>
<nav aria-label="Admin sidebar nav">
  <?php foreach ($links as $ln):
    $href = $ln[0]; $label = $ln[1];
    $hrefPath = parse_url($href, PHP_URL_PATH);
    $isActive = ($currentPath === $hrefPath) || (strpos($currentPath, $hrefPath . '/') === 0) || (strpos($currentPath, $hrefPath) === 0);
    $class = 'nav-link' . ($isActive ? ' active' : '');
    $aria = $isActive ? ' aria-current="page"' : '';
  ?>
    <a class="<?= $class ?>" href="<?= htmlspecialchars($href) ?>"<?= $aria ?>><?= htmlspecialchars($label) ?></a>
  <?php endforeach; ?>
</nav>
