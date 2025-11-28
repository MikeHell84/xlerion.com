<?php
// Extend front controller routing map
require_once dirname(__DIR__) . '/src/Controller/BlogController.php';
require_once dirname(__DIR__) . '/src/Controller/TemplateController.php';
$router->get('/blog', [\BlogController::class, 'index']);
$router->get('/blog/:slug', [\BlogController::class, 'show']);
// Note: public/index.php router does not support param routes yet; BlogController expects slug via GET

// Load additional route files placed in `routes/` (admin/API routes)
if (file_exists(dirname(__DIR__) . '/routes/templates.php')) {
	require_once dirname(__DIR__) . '/routes/templates.php';
}
