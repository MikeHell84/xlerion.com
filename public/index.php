<?php
// Minimal front controller for Vanilla MVC CMR
session_start();

// If the user hasn't seen the intro yet, and is requesting the root/home,
// redirect them to the static intro page. The intro page will set a cookie
// named `seen_intro` so this redirect only happens once per user.
$requestUriFull = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestUriFull, PHP_URL_PATH) ?: '/';
$isRootRequest = in_array($requestPath, ['/', '/index.php'], true);
$suppressIntro = isset($_GET['no_intro']) && $_GET['no_intro'] === '1';
// Serve intro on root unless explicitly suppressed via ?no_intro=1
// Determine project root early so includes work when serving intro
$projectRoot = __DIR__;
if (!is_dir($projectRoot . '/src')) {
    $projectRoot = dirname(__DIR__);
}
if ($isRootRequest && !$suppressIntro) {
    // Serve the intro directly on root so http://localhost:8000 starts with the video.
    $introFile = $projectRoot . '/public/intro.html';
    if (file_exists($introFile)) { include $introFile; exit; }
    echo "<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>Intro</title></head><body style='margin:0;background:#000'>";
    echo "<video id='v' playsinline webkit-playsinline muted autoplay style='width:100vw;height:100vh;object-fit:cover'><source src='/media/xlerionIntro.mp4' type='video/mp4'></video>";
    echo "<script>var v=document.getElementById('v');function done(){location.replace('/?no_intro=1');}v&&v.addEventListener('ended',done);</script>";
    echo "</body></html>";
    exit;
}

// Determine project root: support two deployment layouts:
// 1) Normal repo: public/index.php -> project root is dirname(__DIR__)
// 2) All files copied into webroot: index.php is in project root -> project root is __DIR__
$projectRoot = __DIR__;
if (!is_dir($projectRoot . '/src')) {
    $projectRoot = dirname(__DIR__);
}

// Load .env simple loader using detected project root
function env($key, $default = null) {
    static $vars = null;
    if ($vars === null) {
        $vars = [];
        global $projectRoot;
        $p = $projectRoot . '/.env';
        if (file_exists($p)) {
            foreach (file($p, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                [$k,$v] = array_map('trim', explode('=', $line, 2) + [1 => null]);
                if ($k) $vars[$k] = trim($v, " \"'\n\r");
            }
        }
    }
    return $vars[$key] ?? $default;
}

require_once $projectRoot . '/src/Router.php';
require_once $projectRoot . '/src/Model/Database.php';
require_once $projectRoot . '/src/Controller/ContactController.php';
require_once $projectRoot . '/src/RateLimiter.php';

$router = new Router();
$router->get('/', function() use ($projectRoot) { include $projectRoot . '/views/home.php'; });
$router->get('/intro-reset', function() {
    // Clear the intro cookie and redirect to root to show the intro again
    setcookie('seen_intro', '', time() - 3600, '/', '', false, true);
    header('Location: /?intro=1', true, 302);
    exit;
});
$router->get('/search', function() use ($projectRoot) { include $projectRoot . '/views/search_results.php'; });
$router->get('/contact', [\ContactController::class, 'show']);
$router->post('/contact', [\ContactController::class, 'submit']);
// Alias Spanish localized path to same handlers
$router->get('/contacto', [\ContactController::class, 'show']);
$router->post('/contacto', [\ContactController::class, 'submit']);
// Filosofía page
$router->get('/filosofia', function() use ($projectRoot) { include $projectRoot . '/views/filosofia.php'; });
// Acerca del creador (vista empresarial)
$router->get('/acerca-del-creador', function() use ($projectRoot) { include $projectRoot . '/views/acerca-del-creador.php'; });
$router->get('/legal', function() use ($projectRoot) { include $projectRoot . '/views/legal.php'; });
$router->get('/proyectos', function() use ($projectRoot) { include $projectRoot . '/views/proyectos.php'; });
// Serve intro video from workspace media folder (outside public) so intro.html can load it
$router->get('/intro.mp4', function() use ($projectRoot) {
    // Resolve media path robustly. Common layout: workspace/media/xlerionIntro.mp4
    $candidates = [
        realpath($projectRoot . '/../media/xlerionIntro.mp4'),
        realpath($projectRoot . '/../../media/xlerionIntro.mp4'),
        realpath(__DIR__ . '/../media/xlerionIntro.mp4'),
        realpath(__DIR__ . '/../../media/xlerionIntro.mp4'),
    ];
    $videoPath = null;
    foreach ($candidates as $c) {
        if ($c && file_exists($c)) { $videoPath = $c; break; }
    }

    if (!$videoPath) {
        http_response_code(404);
        echo 'Intro video not found';
        return;
    }

    // Minimal file serving; not implementing byte-range here (seek may be limited)
    header('Content-Type: video/mp4');
    header('Content-Length: ' . filesize($videoPath));
    header('Accept-Ranges: bytes');
    readfile($videoPath);
    exit;
});
// Resources public listing and single resource page
require_once __DIR__ . '/../src/Controller/ResourceController.php';
$router->get('/recursos', [\ResourceController::class, 'index']);
$router->get('/recursos/:slug', [\ResourceController::class, 'show']);

// Minimal admin CRUD for resources
$router->get('/admin/resources', [\ResourceController::class, 'adminIndex']);
$router->get('/admin/resources/create', [\ResourceController::class, 'adminCreate']);
$router->post('/admin/resources/store', [\ResourceController::class, 'adminStore']);
$router->get('/admin/resources/edit', [\ResourceController::class, 'adminEdit']);
$router->post('/admin/resources/update', [\ResourceController::class, 'adminUpdate']);
$router->post('/admin/resources/delete', [\ResourceController::class, 'adminDelete']);
// Load additional route map (plugins, controllers)
if (file_exists($projectRoot . '/public/router_map.php')) {
    require_once $projectRoot . '/public/router_map.php';
}
$router->run();
