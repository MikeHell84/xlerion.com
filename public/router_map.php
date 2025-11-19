<?php
// Extend front controller routing map
require_once dirname(__DIR__) . '/src/Controller/BlogController.php';
$router->get('/blog', [\BlogController::class, 'index']);
$router->get('/blog/:slug', [\BlogController::class, 'show']);
// Note: public/index.php router does not support param routes yet; BlogController expects slug via GET
