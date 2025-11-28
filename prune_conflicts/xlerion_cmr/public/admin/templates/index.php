<?php
// Public admin route: /admin/templates
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
// NOTE: authentication guard removed so this section is directly accessible for local work.
// Re-enable a proper auth check here in production (Auth::requireAdmin() or redirect to /admin/login.php).

$title = 'Admin - Plantillas';
$banner_title_class = 'section-title-lg';
ob_start();
include __DIR__ . '/../../../../xlerion_cmr/views/admin/templates_list.php';
$slot = ob_get_clean();
// include the shared layout which will load global CSS/JS and render the $slot inside the main container
include __DIR__ . '/../../../views/layout.php';
