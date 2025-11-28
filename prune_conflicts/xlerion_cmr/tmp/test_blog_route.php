<?php
// Quick local test for Router parameter routing
chdir(dirname(__DIR__));
require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Model/Database.php';
require_once __DIR__ . '/../src/Controller/BlogController.php';
// emulate server
$_SERVER['REQUEST_URI'] = '/blog/el-origen-de-total-darkness';
$_SERVER['REQUEST_METHOD'] = 'GET';
// capture output
ob_start();
$r = new Router();
$r->get('/blog', function(){ echo "INDEX"; });
$r->get('/blog/:slug', [\BlogController::class, 'show']);
$r->run();
$out = ob_get_clean();
echo "OUTPUT:\n" . $out . "\n";
