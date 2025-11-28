<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
// NOTE: auth redirect removed for convenience during local development.
// Re-enable proper admin checks in production.

$title = 'Admin - Editar plantilla';
$banner_title_class = 'section-title-lg';
ob_start();
include __DIR__ . '/../../../../xlerion_cmr/views/admin/templates_edit.php';
$slot = ob_get_clean();
include __DIR__ . '/../../../views/layout.php';
