<?php
// Auto-detect base path when project is served from a subfolder (example: /xlerion_cmr/public)
// and buffer the template output so we can rewrite internal absolute attributes (src/href/action)
// to point to the correct public folder without changing every link in the template.
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$base = '';
// Robust base detection for hosts where DocumentRoot is public_html.
// Common deployments: /xlerion_cmr/public, /xlerion_cmr/public/index.php, or served from root.
// We'll prefer '/xlerion_cmr/public' when we detect the project folder in SCRIPT_NAME or REQUEST_URI
// and ensure the target public folder exists on disk before using it.
$candidateBases = [];
if (strpos($scriptName, '/xlerion_cmr/public') !== false) {
  $candidateBases[] = substr($scriptName, 0, strpos($scriptName, '/xlerion_cmr/public')) . '/xlerion_cmr/public';
}
// If script name includes /public (generic), use that as a candidate
if (strpos($scriptName, '/public') !== false) {
  $candidateBases[] = substr($scriptName, 0, strpos($scriptName, '/public')) . '/public';
}
// Also check REQUEST_URI for the project name (useful when index.php is front controller in root)
$req = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($req, '/xlerion_cmr/') === 0 || strpos($req, '/xlerion_cmr') === 0) {
  $candidateBases[] = '/xlerion_cmr/public';
}
// Normalize and validate candidates: prefer the first one whose directory exists relative to project
foreach ($candidateBases as $c) {
  $c = rtrim($c, '/');
  // map url path to filesystem path assuming views are in views/ and public is ../public
  $fs = __DIR__ . '/..' . $c;
  if (is_dir($fs)) { $base = $c; break; }
}
// If nothing matched, leave base empty (site served from root)
// start output buffering so we can rewrite internal asset links when needed
ob_start();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="index,follow">
  <title><?= htmlspecialchars($title ?? 'Xlerion') ?></title>
  <!-- Bootstrap CSS (CDN) with SRI -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <!-- Material Symbols (Google) -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <?php
// Forzar rutas absolutes para assets (evita resoluciones relativas tipo /Planos.png/styles.css)
$assetBase = '/';
if (substr($assetBase, 0, 1) !== '/') $assetBase = '/' . $assetBase;
if (substr($assetBase, -1) !== '/') $assetBase .= '/';
?>
<head>
  <base href="<?php echo htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="<?php echo $assetBase; ?>styles.css">
  <script src="<?php echo $assetBase; ?>app.js" defer></script>
  <!-- Pre-render theme script: apply saved or system theme before paint to avoid FOUC -->
  <script>!function(){try{var s=localStorage.getItem('xlerion_theme');var t=s?s:(window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');if(t==='dark'){document.documentElement.classList.add('theme-dark');document.documentElement.classList.remove('theme-light')}else{document.documentElement.classList.remove('theme-dark');document.documentElement.classList.add('theme-light')}}catch(e){/*ignore*/}}();</script>
  <!-- Tailwind CDN for rapid prototyping (no build required) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // Configure Tailwind theme colors to match Xlerion palette
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            xlerion: {
              accent: '#00EEFF',
              cyan: '#43FFFF',
              deep: '#004080',
              coal: '#121212',
              muted: '#2C2C2C',
              white: '#FFFFFF'
            }
          }
        }
      }
    }
  </script>
  <style>
    :root{
      --accent:#00EEFF; /* bright cyan */
      --accent-2:#43FFFF; /* cyan accent */
      --deep:#004080; /* deep blue */
      --bg:#FFFFFF; /* white background */
      --fg:#121212; /* primary text */
      --muted:#2C2C2C; /* muted dark */
    }
    html,body{height:100%;}
  /* IE-friendly font stack */
  body{font-family:"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin:0;background:var(--bg);color:var(--fg);-webkit-font-smoothing:antialiased}
    header{background:linear-gradient(90deg,var(--deep),#002a66);color:var(--bg);padding:1rem}
    nav a{color:var(--bg);margin-right:1rem;text-decoration:none;font-weight:600}
    .container{max-width:980px;margin:1.25rem auto;padding:0 1rem}
    footer{border-top:1px solid #eee;padding:1rem;color:#666;background:transparent}
    .btn{background:var(--accent);color:#000;padding:.5rem 1rem;border-radius:6px;text-decoration:none;display:inline-block}
    .hero{padding:2rem;border-radius:8px;background:linear-gradient(180deg, rgba(0,238,255,0.06), rgba(67,255,255,0.03));margin-bottom:1rem}
    h1,h2,h3{color:var(--fg)}
    a { color: var(--deep);} 
    @media (prefers-color-scheme: dark) {
      :root{ --bg:#121212; --fg:#FFFFFF; }
      header{color:var(--fg)}
    }
  </style>
</head>
<body>
  <?php
    // Maintenance mode check: if enabled in settings and the current user is not an admin, show maintenance page and exit.
    try{
      require_once __DIR__ . '/../src/Model/Database.php';
      require_once __DIR__ . '/../src/Auth.php';
      $pdo = Database::pdo();
      $row = $pdo->query("SELECT v FROM settings WHERE k='maintenance_mode'")->fetchColumn();
      if ($row === '1'){
        // allow admins to bypass
        $isAdmin = false;
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        try{ $isAdmin = isset($_SESSION['user_is_admin']) && $_SESSION['user_is_admin']; } catch(Exception $e) { $isAdmin = false; }
        if (!$isAdmin){
          $msg = $pdo->query("SELECT v FROM settings WHERE k='maintenance_message'")->fetchColumn() ?: 'El sitio está en mantenimiento. Vuelve pronto.';
          http_response_code(503);
          echo "<div style=\"min-height:100vh;display:flex;align-items:center;justify-content:center;background:#000;color:#fff;padding:2rem;\">";
          echo "<div style=\"max-width:720px;text-align:center\">";
          echo "<h1 style=\"font-size:2rem;margin-bottom:0.5rem\">Sitio en mantenimiento</h1>";
          echo "<p style=\"opacity:0.92\">".htmlspecialchars($msg)."</p>";
          echo "</div></div>";
          exit;
        }
      }
    } catch(Exception $e){ /* ignore maintenance check errors */ }
  ?>
  <header class="site-header">
      <?php
        // Build grouped menu HTML for desktop (dropdowns) and a grouped list for offcanvas mobile
        $menuHtml = '';
        $offcanvasHtml = '';
        try {
          require_once __DIR__ . '/../src/Model/Database.php';
          $pdo = Database::pdo();
          // current slug for active highlighting
          $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
          $currentSlug = trim(parse_url($currentUri, PHP_URL_PATH), '/');
          if ($currentSlug === '') $currentSlug = 'inicio';

          $rows = $pdo->query("SELECT id,slug,title,meta FROM cms_pages WHERE is_published = 1")->fetchAll(PDO::FETCH_ASSOC);

          // map slugs to Material Symbols icons
          $iconNameMap = [
            'inicio' => 'home',
            'servicios' => 'build_circle',
            'soluciones' => 'build',
            'proyectos' => 'work',
            'documentacion' => 'description',
            'contact' => 'mail',
            'contacto' => 'mail',
            'blog' => 'rss_feed',
            'filosofia' => 'emoji_objects',
            'acerca-del-creador' => 'person',
            'convocatorias-alianzas' => 'campaign',
            'legal' => 'gavel'
          ];

          // Define logical groups (related sections grouped together)
          $groupsDef = [
            'Información' => ['slugs' => ['inicio','filosofia','acerca-del-creador','legal'], 'icon' => 'info'],
            'Servicios' => ['slugs' => ['servicios','soluciones','proyectos'], 'icon' => 'build'],
            'Recursos' => ['slugs' => ['documentacion','blog','convocatorias-alianzas'], 'icon' => 'menu_book'],
            'Contacto' => ['slugs' => ['contact','contacto'], 'icon' => 'mail']
          ];
          // Build menu using cms_pages.meta if available. We'll create groups and parent-child relations.
          $pages = [];
          $byId = [];
          foreach ($rows as $r) {
            $meta = [];
            if (!empty($r['meta'])) { $tmp = json_decode($r['meta'], true); if (is_array($tmp)) $meta = $tmp; }
            $id = intval($r['id']);
            $byId[$id] = [
              'id' => $id,
              'slug' => $r['slug'],
              'title' => $r['title'],
              'meta' => $meta,
              'children' => []
            ];
            $pages[] = &$byId[$id];
          }

          // Determine group for each page (meta.menu_group preferred, otherwise fallback to groupsDef by slug)
          foreach ($byId as $id => &$node) {
            $meta = $node['meta'];
            $node['menu_group'] = isset($meta['menu_group']) ? $meta['menu_group'] : null;
            $node['menu_parent'] = isset($meta['menu_parent']) ? (int)$meta['menu_parent'] : null;
            $node['menu_order'] = isset($meta['menu_order']) ? (int)$meta['menu_order'] : null;
            // fallback group detection
            if (!$node['menu_group']) {
              $assigned = null;
              foreach ($groupsDef as $gTitle => $gDef) {
                if (in_array($node['slug'], $gDef['slugs'], true)) { $assigned = $gTitle; break; }
              }
              $node['menu_group'] = $assigned ?: 'Otros';
            }
          }
          unset($node);

          // Attach children to parents when parent exists
          foreach ($byId as $id => &$node) {
            $parent = $node['menu_parent'];
            if ($parent && isset($byId[$parent])) {
              $byId[$parent]['children'][] = &$node;
            }
          }
          unset($node);

          // Build groups with only top-level nodes (parent null or parent not found)
          $grouped = [];
          foreach ($byId as $id => $node) {
            $parent = $node['menu_parent'];
            if ($parent && isset($byId[$parent])) continue; // skip children here
            $g = $node['menu_group'] ?: 'Otros';
            $grouped[$g][] = $node;
          }

          // sorting helper by menu_order then title
          $cmp = function($a, $b){
            $oa = isset($a['menu_order']) ? $a['menu_order'] : 999999;
            $ob = isset($b['menu_order']) ? $b['menu_order'] : 999999;
            if ($oa !== $ob) return $oa <=> $ob;
            return strcasecmp($a['title'] ?? '', $b['title'] ?? '');
          };

          // sort items and their children
          foreach ($grouped as $gTitle => &$items) {
            usort($items, $cmp);
            foreach ($items as &$it) {
              if (!empty($it['children'])) usort($it['children'], $cmp);
            }
          }
          unset($items, $it);

          // Build desktop menu with dropdowns when a top-level item has children
          foreach ($grouped as $gTitle => $items) {
            $gIcon = isset($groupsDef[$gTitle]['icon']) ? $groupsDef[$gTitle]['icon'] : 'folder';
            // if group has multiple top-level items we show a group header as dropdown container
            if (count($items) > 1) {
              // create a group dropdown that contains top-level items as menu entries
              $parentActive = false;
              foreach ($items as $it) {
                if ($it['slug'] === $currentSlug) { $parentActive = true; break; }
                foreach ($it['children'] as $c) { if ($c['slug'] === $currentSlug) { $parentActive = true; break 2; } }
              }
              $parentActiveClass = $parentActive ? ' active' : '';
              $ariaExpanded = $parentActive ? 'true' : 'false';
              $menuHtml .= "<li class=\"nav-item dropdown\">";
              $menuHtml .= "<a class=\"nav-link dropdown-toggle text-white{$parentActiveClass}\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"{$ariaExpanded}\">";
              $menuHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$gIcon}</span> {$gTitle}</a>";
              $menuHtml .= "<ul class=\"dropdown-menu dropdown-menu-dark\" role=\"menu\">";
              foreach ($items as $it) {
                $slug = htmlspecialchars($it['slug']);
                $full = htmlspecialchars($it['title']);
                $icon = isset($iconNameMap[$it['slug']]) ? $iconNameMap[$it['slug']] : 'chevron_right';
                $isActive = ($currentSlug === $it['slug']) ? ' aria-current="page"' : '';
                if (!empty($it['children'])) {
                  // top-level with children -> render header and children inside submenu
                  $menuHtml .= "<li class=\"dropdown-header text-muted small px-3\">".htmlspecialchars($it['title'])."</li>";
                  foreach ($it['children'] as $c) {
                    $cslug = htmlspecialchars($c['slug']);
                    $cfull = htmlspecialchars($c['title']);
                    $cicon = isset($iconNameMap[$c['slug']]) ? $iconNameMap[$c['slug']] : 'chevron_right';
                    $cisActive = ($currentSlug === $c['slug']) ? ' aria-current="page"' : '';
                    $menuHtml .= "<li role=\"none\"><a class=\"dropdown-item\" role=\"menuitem\" tabindex=\"-1\" href=\"/{$cslug}\" title=\"{$cfull}\" aria-label=\"{$cfull}\"{$cisActive}>";
                    $menuHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$cicon}</span> {$cfull}</a></li>";
                  }
                } else {
                  $menuHtml .= "<li role=\"none\"><a class=\"dropdown-item\" role=\"menuitem\" tabindex=\"-1\" href=\"/{$slug}\" title=\"{$full}\" aria-label=\"{$full}\"{$isActive}>";
                  $menuHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$icon}</span> {$full}</a></li>";
                }
              }
              $menuHtml .= "</ul></li>";
            } else {
              // single top-level item in this group -> render as normal nav link (may have children)
              $it = $items[0];
              if (!empty($it['children'])) {
                $slug = htmlspecialchars($it['slug']);
                $full = htmlspecialchars($it['title']);
                $icon = isset($iconNameMap[$it['slug']]) ? $iconNameMap[$it['slug']] : 'chevron_right';
                $parentActive = ($currentSlug === $it['slug']);
                foreach ($it['children'] as $c) if ($c['slug'] === $currentSlug) $parentActive = true;
                $parentActiveClass = $parentActive ? ' active' : '';
                $ariaExpanded = $parentActive ? 'true' : 'false';
                $menuHtml .= "<li class=\"nav-item dropdown\">";
                $menuHtml .= "<a class=\"nav-link dropdown-toggle text-white{$parentActiveClass}\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"{$ariaExpanded}\">";
                $menuHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$icon}</span> {$full}</a>";
                $menuHtml .= "<ul class=\"dropdown-menu dropdown-menu-dark\" role=\"menu\">";
                foreach ($it['children'] as $c) {
                  $cslug = htmlspecialchars($c['slug']);
                  $cfull = htmlspecialchars($c['title']);
                  $cicon = isset($iconNameMap[$c['slug']]) ? $iconNameMap[$c['slug']] : 'chevron_right';
                  $cisActive = ($currentSlug === $c['slug']) ? ' aria-current="page"' : '';
                  $menuHtml .= "<li role=\"none\"><a class=\"dropdown-item\" role=\"menuitem\" tabindex=\"-1\" href=\"/{$cslug}\" title=\"{$cfull}\" aria-label=\"{$cfull}\"{$cisActive}>";
                  $menuHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$cicon}</span> {$cfull}</a></li>";
                }
                $menuHtml .= "</ul></li>";
              } else {
                $slug = htmlspecialchars($it['slug']);
                $full = htmlspecialchars($it['title']);
                $icon = isset($iconNameMap[$it['slug']]) ? $iconNameMap[$it['slug']] : 'chevron_right';
                $isActive = ($currentSlug === $it['slug']) ? ' aria-current="page"' : '';
                $activeClass = ($currentSlug === $it['slug']) ? ' active' : '';
                $menuHtml .= "<li class=\"nav-item\"><a class=\"nav-link text-white{$activeClass}\" href=\"/{$slug}\" title=\"{$full}\" aria-label=\"{$full}\"{$isActive}>";
                $menuHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$icon}</span> {$full}</a></li>";
              }
            }
          }

          // Build offcanvas list: grouped but linear for accessibility on mobile
          foreach ($grouped as $gTitle => $items) {
            if (count($items) > 0) {
              $offcanvasHtml .= "<li class=\"nav-item mt-2\"><div class=\"nav-link text-muted small fw-semibold\">".htmlspecialchars($gTitle)."</div></li>";
              foreach ($items as $it) {
                $slug = htmlspecialchars($it['slug']);
                $full = htmlspecialchars($it['title']);
                $icon = isset($iconNameMap[$it['slug']]) ? $iconNameMap[$it['slug']] : 'chevron_right';
                $isActive = ($currentSlug === $it['slug']) ? ' aria-current="page"' : '';
                $activeClass = ($currentSlug === $it['slug']) ? ' active' : '';
                $offcanvasHtml .= "<li class=\"nav-item\"><a class=\"nav-link text-white{$activeClass}\" href=\"/{$slug}\" title=\"{$full}\" aria-label=\"{$full}\"{$isActive}>";
                $offcanvasHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$icon}</span> {$full}</a></li>";
                // children flattened under parent for mobile
                if (!empty($it['children'])) {
                  foreach ($it['children'] as $c) {
                    $cslug = htmlspecialchars($c['slug']);
                    $cfull = htmlspecialchars($c['title']);
                    $co = isset($iconNameMap[$c['slug']]) ? $iconNameMap[$c['slug']] : 'chevron_right';
                    $cactive = ($currentSlug === $c['slug']) ? ' aria-current="page"' : '';
                    $offcanvasHtml .= "<li class=\"nav-item ps-3\"><a class=\"nav-link text-white small\" href=\"/{$cslug}\" title=\"{$cfull}\" aria-label=\"{$cfull}\"{$cactive}>";
                    $offcanvasHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$co}</span> {$cfull}</a></li>";
                  }
                }
              }
            }
          }
        } catch (Exception $e) { 
          // fall back to empty menus on error
          $menuHtml = '';
          $offcanvasHtml = '';
        }
      ?>

      <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
          <a class="navbar-brand text-white" href="/">
            <?php
              // prefer logo from settings if available
              try{
                $logo = '/media/LogoX.svg';
                $val = $pdo->query("SELECT v FROM settings WHERE k='logo_url'")->fetchColumn();
                if ($val) $logo = $val;
              } catch(Exception $e){ $logo = '/media/LogoX.svg'; }
            ?>
            <img src="<?= htmlspecialchars($logo) ?>" alt="Xlerion" style="height:36px;max-height:40px;display:inline-block;vertical-align:middle">
          </a>
          <!-- Toggler opens offcanvas on mobile -->
          <button id="ctrl-menu-toggle" class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
          </button>

          <!-- Desktop menu (visible on large screens) -->
          <div class="navbar-collapse d-none d-lg-flex" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <?= $menuHtml ?>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
            </ul>
          </div>
        </div>
      </nav>

      <!-- Floating controls: outside the main menu, bottom-right of header (desktop only) -->
      <div class="menu-controls-floating d-none d-lg-flex" aria-label="Controles flotantes del menú">
        <div class="mc-stack">
          <button id="ctrl-search" class="mc-btn" data-action="search" title="Buscar" aria-label="Buscar">
            <span class="material-symbols-outlined">search</span>
          </button>
          <button id="ctrl-theme" class="mc-btn" data-action="theme" title="Alternar tema" aria-label="Alternar tema">
            <span class="material-symbols-outlined">brightness_2</span>
          </button>
          <a id="ctrl-admin" class="mc-btn" href="/admin/login.php" title="Admin" aria-label="Admin">
            <span class="material-symbols-outlined">settings</span>
          </a>
        </div>
      </div>

      <!-- Offcanvas mobile menu -->
      <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="mobileMenuLabel">Menú</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav">
            <?= $offcanvasHtml ?>
            <li class="nav-item mt-2"><a class="nav-link text-white" href="/admin/login.php">Admin</a></li>
          </ul>
        </div>
      </div>
    </header>
  <?php
    // Parallax banner: video on home, image on other pages.
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $slug = trim(parse_url($uri, PHP_URL_PATH), '/');
    if ($slug === '') $slug = 'inicio';

    // mapping from slug to media file (image) — fallback to a generic image
    $mediaMap = [
      'inicio' => ['/media/intro.mp4', 'video'],
      'blog' => ['/media/images/parallax/blog-bitacora-parallax.jpg', 'image'],
      'contact' => ['/media/images/parallax/contacto-parallax.jpg', 'image'],
      'documentacion' => ['/media/images/parallax/documentacion-parallax.jpg', 'image'],
      'proyectos' => ['/media/images/parallax/proyectos-parallax.jpg', 'image'],
      'servicios' => ['/media/images/parallax/servicios-productos-parallax.jpg', 'image'],
    ];

    $bannerType = 'image';
    $bannerSrc = '/media/Planos.png';
    // subtitles per slug
    $subtitles = [
      'inicio' => 'Creamos soluciones digitales que impulsan tu negocio',
      'blog' => 'Artículos, guías y novedades del equipo',
      'contact' => 'Hablemos: planifica tu proyecto con nosotros',
      'documentacion' => 'Guías, API y recursos para integrarte rápido',
      'proyectos' => 'Proyectos reales, resultados medibles',
      'servicios' => 'Soluciones a medida: desde diseño hasta despliegue',
    ];
    if (isset($mediaMap[$slug])){
      [$src, $type] = $mediaMap[$slug];
      $bannerSrc = $src;
      $bannerType = $type;
    } else if ($slug === 'inicio' && file_exists(__DIR__ . '/../media/intro.mp4')){
      $bannerSrc = '/media/intro.mp4'; $bannerType = 'video';
    }
  ?>

  <?php $bannerClass = ($slug === 'inicio') ? 'parallax-banner home-banner' : 'parallax-banner'; ?>
  <section class="<?= $bannerClass ?>" role="region" aria-label="Banner principal">
    <?php if ($bannerType === 'video'): ?>
      <video class="parallax-media" autoplay muted loop playsinline preload="auto" poster="/media/intro.jpg">
        <source src="<?= htmlspecialchars($bannerSrc) ?>" type="video/mp4">
        <!-- fallback image if video not supported -->
      </video>
    <?php else: ?>
      <?php
        // prepare thumb/webp paths
        $thumbDir = '/media/images/parallax/thumbs/';
        $base = basename($bannerSrc, '.jpg');
        $webp400 = $thumbDir . $base . '-400.webp';
        $webp800 = $thumbDir . $base . '-800.webp';
        $jpg400 = $thumbDir . $base . '-400.jpg';
        $jpg800 = $thumbDir . $base . '-800.jpg';
        $hasWebp = file_exists(__DIR__ . '/..' . $webp800) || file_exists(__DIR__ . '/..' . $webp400);
        $fallbackJpg = file_exists(__DIR__ . '/..' . $jpg800) ? $jpg800 : $bannerSrc;
      ?>
      <picture>
        <?php if ($hasWebp): ?>
          <source type="image/webp" srcset="<?= htmlspecialchars(file_exists(__DIR__ . '/..' . $webp400) ? $webp400 : $webp800) ?> 400w, <?= htmlspecialchars(file_exists(__DIR__ . '/..' . $webp800) ? $webp800 : $webp400) ?> 800w" sizes="(max-width:800px) 400px, 800px">
        <?php endif; ?>
        <?php if (file_exists(__DIR__ . '/..' . $jpg400) && file_exists(__DIR__ . '/..' . $jpg800)): ?>
          <img class="parallax-media" src="<?= htmlspecialchars($fallbackJpg) ?>" srcset="<?= htmlspecialchars($jpg400) ?> 400w, <?= htmlspecialchars($jpg800) ?> 800w" sizes="(max-width:800px) 400px, 800px" alt="<?= htmlspecialchars($subtitles[$slug] ?? 'Imagen de cabecera') ?>">
        <?php else: ?>
          <img class="parallax-media" src="<?= htmlspecialchars($fallbackJpg) ?>" alt="<?= htmlspecialchars($subtitles[$slug] ?? 'Imagen de cabecera') ?>">
        <?php endif; ?>
      </picture>
    <?php endif; ?>
    <div class="parallax-overlay">
      <div class="container hero">
        <?php
          // allow views to override banner title class or hide subtitle
          $bannerClassAttr = isset($banner_title_class) ? ' class="' . htmlspecialchars($banner_title_class) . '"' : '';
        ?>
        <h1 id="hero-title"<?= $bannerClassAttr ?>><?php echo htmlspecialchars($title ?? 'Xlerion'); ?></h1>
        <?php if (empty($hide_banner_subtitle)): ?>
          <p id="hero-sub"><?php echo htmlspecialchars($subtitles[$slug] ?? 'Bienvenido a Xlerion — soluciones y servicios.'); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <!-- Desktop controls moved into navbar (compact mobile controls remain in offcanvas) -->

  <!-- Search modal (simple, accessible) -->
  <div class="modal fade" id="siteSearchModal" tabindex="-1" aria-labelledby="siteSearchLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="siteSearchLabel">Buscar en Xlerion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form action="/search" method="get" role="search">
            <label for="site-search-input" class="visually-hidden">Buscar</label>
            <div class="input-group">
              <input id="site-search-input" name="q" type="search" class="form-control" placeholder="Buscar..." aria-label="Buscar en el sitio">
              <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <main class="container">
    <?php if (!empty($_SESSION['flash']) || !empty($_SESSION['flash_error'])): ?>
      <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
      <?php endif; ?>
      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
      <?php endif; ?>
    <?php endif; ?>
    <?= $slot ?? '' ?>
  </main>
  <footer class="site-footer" role="contentinfo" aria-label="Pie de página de Xlerion">
    <div class="container py-3">
      <div class="row">
        <div class="col-12 col-md-4 mb-3">
          <h5>Contacto</h5>
          <p>Desde Nocaima, Cundinamarca</p>
          <ul class="list-unstyled">
            <li>contactus@xlerion.com</li>
            <li>totaldarkness@xlerion.com</li>
            <li>support@xlerion.com</li>
            <li>sales@xlerion.com</li>
            <li>admin@xlerion.com</li>
          </ul>
          <p>WhatsApp: +57 320 860 5600</p>
          <p class="mb-0">Redes: 
            <a href="https://www.linkedin.com/company/xlerion" target="_blank" rel="noopener noreferrer">LinkedIn</a> · 
            <a href="https://www.indiegogo.com/es/profile/miguel_rodriguez-martinez_edb9?redirect_reason#/overview" target="_blank" rel="noopener noreferrer">Indiegogo</a> · 
            <a href="https://www.kickstarter.com/profile/xlerionstudios" target="_blank" rel="noopener noreferrer">Kickstarter</a> · 
            <a href="https://www.patreon.com/xlerionstudios" target="_blank" rel="noopener noreferrer">Patreon</a> · 
            <a href="https://www.instagram.com/ultimatexlerion/" target="_blank" rel="noopener noreferrer">Instagram</a> · 
            <a href="https://www.facebook.com/xlerionultimate" target="_blank" rel="noopener noreferrer">Facebook</a> · 
            <a href="https://www.behance.net/xlerionultimate" target="_blank" rel="noopener noreferrer">Behance</a>
          </p>
        </div>
        <div class="col-6 col-md-2 mb-3">
          <h5>Enlaces</h5>
          <ul class="list-unstyled">
            <li><a href="/">Inicio</a></li>
            <li><a href="/proyectos">Proyectos</a></li>
            <li><a href="/soluciones">Soluciones</a></li>
            <li><a href="/documentacion">Documentación</a></li>
            <li><a href="/contact">Contacto</a></li>
          </ul>
        </div>
        <div class="col-6 col-md-3 mb-3">
          <h5>Redes</h5>
          <div class="social-icons" role="navigation" aria-label="Redes sociales">
            <a class="social-link" href="https://www.linkedin.com/company/xlerion" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
              <!-- LinkedIn SVG -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.761 0 5-2.239 5-5v-14c0-2.761-2.239-5-5-5zm-11 19h-3v-9h3v9zm-1.5-10.268c-.966 0-1.75-.785-1.75-1.75s.784-1.75 1.75-1.75 1.75.785 1.75 1.75-.784 1.75-1.75 1.75zm13.5 10.268h-3v-4.5c0-1.074-.021-2.454-1.496-2.454-1.498 0-1.727 1.171-1.727 2.381v4.573h-3v-9h2.879v1.233h.041c.401-.76 1.379-1.556 2.839-1.556 3.037 0 3.599 2.    273 3.599 5.23v4.093z"/></svg>
            </a>
            <a class="social-link" href="https://www.instagram.com/ultimatexlerion/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
              <!-- Instagram SVG -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.34 3.608 1.314.975.975 1.252 2.242 1.314 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.339 2.633-1.314 3.608-.975.975-2.242 1.252-3.608 1.314-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.339-3.608-1.314-.975-.975-1.252-2.242-1.314-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.339-2.633 1.314-3.608.975-.975 2.242-1.252 3.608-1.314 1.266-.058 1.646-.07 4.85-.07m0-2.163c-3.259 0-3.667.012-4.947.072-1.558.071-2.944.34-3.995 1.392-1.052 1.05-1.321 2.436-1.392 3.995-.06 1.28-.072 1.688-.072 4.947s.012 3.667.072 4.947c.071 1.558.34 2.944 1.392 3.995 1.05 1.052 2.436 1.321 3.995 1.392 1.28.06 1.688.072 4.947.072s3.667-.012 4.947-.072c1.558-.071 2.944-.34 3.995-1.392 1.052-1.05 1.321-2.436 1.392-3.995.06-1.28.072-1.688.072-4.947s-.012-3.667-.072-4.947c-.071-1.558-.34-2.944-1.392-3.995-1.05-1.052-2.436-1.321-3.995-1.392-1.28-.06-1.688-.072-4.947-.072z"/><path d="M12 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998z"/></svg>
            </a>
            <a class="social-link" href="https://www.facebook.com/xlerionultimate" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
              <!-- Facebook SVG -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M22 12c0-5.523-4.477-10-10-10s-10 4.477-10 10c0 4.991 3.657 9.128 8.438 9.878v-6.99h-2.54v-2.888h2.54v-2.2c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.772-1.63 1.562v1.873h2.773l-.443 2.888h-2.33v6.99c4.781-.75 8.438-4.887 8.438-9.878z"/></svg>
            </a>
            <a class="social-link" href="https://www.behance.net/xlerionultimate" target="_blank" rel="noopener noreferrer" aria-label="Behance">
              <!-- Behance SVG (simple b icon) -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M8.5 10.5c0-1.657-1.343-3-3-3h-3v9h3c1.657 0 3-1.343 3-3v-3zm11.5 7.5h-7v-2h7v2zm0-4h-4.5c.276-.46.5-1 .5-1.6 0-1.933-1.567-3.5-3.5-3.5h-4v9h4c2.485 0 4.5-2.015 4.5-4.5 0-.29-.03-.57-.06-.85.52.18 1.06.35 1.56.35 1.93 0 3.5-1.57 3.5-3.5 0-1.93-1.57-3.5-3.5-3.5h-1v2h1c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5c-.46 0-.86-.24-1.1-.6-.14.29-.25.61-.36.92.46.15 1 .23 1.46.23 1.93 0 3.5 1.57 3.5 3.5 0 1.93-1.57 3.5-3.5 3.5z"/></svg>
            </a>
            <a class="social-link" href="https://www.indiegogo.com/es/profile/miguel_rodriguez-martinez_edb9?redirect_reason#/overview" target="_blank" rel="noopener noreferrer" aria-label="Indiegogo">
              <!-- Indiegogo (text mark) -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.2" />
                <text x="12" y="16" font-family="Arial, Helvetica, sans-serif" font-size="10" text-anchor="middle" fill="currentColor">IG</text>
              </svg>
            </a>
            <a class="social-link" href="https://www.kickstarter.com/profile/xlerionstudios" target="_blank" rel="noopener noreferrer" aria-label="Kickstarter">
              <!-- Kickstarter (K badge) -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.2" />
                <text x="12" y="16" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="700" text-anchor="middle" fill="currentColor">K</text>
              </svg>
            </a>
            <a class="social-link" href="https://www.patreon.com/xlerionstudios" target="_blank" rel="noopener noreferrer" aria-label="Patreon">
              <!-- Patreon (P badge) -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                <circle cx="12" cy="12" r="10" fill="currentColor" />
                <text x="12" y="16" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="700" text-anchor="middle" fill="#fff">P</text>
              </svg>
            </a>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <h5>Suscríbete</h5>
          <p>Recibe novedades y convocatorias por correo.</p>
          <form action="/subscribe" method="post" class="d-flex" aria-label="Formulario de suscripción">
            <input class="form-control me-2" type="email" name="email" placeholder="tu@correo.com" aria-label="email">
            <button class="btn btn-primary" type="submit">Suscribir</button>
          </form>
          <p class="mt-2">También nos encuentras en: <a href="https://www.indiegogo.com/es/profile/miguel_rodriguez-martinez_edb9?redirect_reason#/overview" target="_blank" rel="noopener noreferrer">Indiegogo</a>, <a href="https://www.kickstarter.com/profile/xlerionstudios" target="_blank" rel="noopener noreferrer">Kickstarter</a> y <a href="https://www.patreon.com/xlerionstudios" target="_blank" rel="noopener noreferrer">Patreon</a>.</p>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-12 text-center small copyright">© Xlerion - Todos los derechos reservados</div>
      </div>
    </div>
  </footer>
  <!-- Bootstrap JS (CDN) with SRI -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous" defer></script>
  <?php
    // Analytics injection: simple Google Analytics 4 gtag.js if analytics_id is set
    try{
      $aid = $pdo->query("SELECT v FROM settings WHERE k='analytics_id'")->fetchColumn();
      if ($aid){
        // Minimal GA4 snippet
        echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id=".htmlspecialchars($aid)."\"></script>";
        echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','".htmlspecialchars($aid)."');</script>";
      }
    } catch(Exception $e){ /* ignore */ }
  ?>
  <script>
    // small runtime for site controls (no external deps)
    document.addEventListener('DOMContentLoaded', function(){
      // Menu toggle: trigger Bootstrap offcanvas used for mobile menu
      var menuBtn = document.getElementById('ctrl-menu-toggle');
      if(menuBtn){
        menuBtn.addEventListener('click', function(){
          var off = document.getElementById('mobileMenu');
          if(off){ var oc = new bootstrap.Offcanvas(off); oc.toggle(); }
        });
      }

  // Search modal (button and Ctrl+K)
  var searchBtn = document.getElementById('ctrl-search');
  var showSearch = function(){ var modalEl = document.getElementById('siteSearchModal'); if(modalEl){ var m = new bootstrap.Modal(modalEl); m.show(); document.getElementById('site-search-input')?.focus(); } };
  if(searchBtn){ searchBtn.addEventListener('click', showSearch); }
  document.addEventListener('keydown', function(e){ if((e.ctrlKey||e.metaKey) && e.key.toLowerCase() === 'k'){ e.preventDefault(); showSearch(); } });

      // Theme toggle: toggle dark body class and persist + set aria-pressed
      var themeBtn = document.getElementById('ctrl-theme');
      if (themeBtn) {
        // helper to update DOM based on theme
        var updateThemeUI = function(t){
          // ensure both theme classes are managed explicitly
          if(t === 'dark'){
            document.documentElement.classList.add('theme-dark');
            document.documentElement.classList.remove('theme-light');
          } else {
            document.documentElement.classList.remove('theme-dark');
            document.documentElement.classList.add('theme-light');
          }
          // update aria-pressed for accessibility (pressed = dark)
          themeBtn.setAttribute('aria-pressed', (t === 'dark') ? 'true' : 'false');
          // update icon inside button (Material Symbols) — show sun for light, moon for dark
          var ic = themeBtn.querySelector('span');
          if(ic){ ic.textContent = (t === 'dark') ? 'dark_mode' : 'light_mode'; }
          // small transition helper: briefly apply class to animate color changes
          document.documentElement.classList.add('theme-transition');
          window.setTimeout(function(){ document.documentElement.classList.remove('theme-transition'); }, 300);
        };

        // determine initial preference: stored value or system preference
        var stored = localStorage.getItem('xlerion_theme');
  var initial = stored ? stored : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        updateThemeUI(initial);

        themeBtn.addEventListener('click', function(){
          var current = (localStorage.getItem('xlerion_theme') || (document.documentElement.classList.contains('theme-dark') ? 'dark' : 'light'));
          var next = (current === 'dark') ? 'light' : 'dark';
          localStorage.setItem('xlerion_theme', next);
          updateThemeUI(next);
        });
      }

      // Ensure offcanvas aria-expanded is kept in sync with the navbar toggler
      var mobileOffcanvas = document.getElementById('mobileMenu');
      var menuToggler = document.getElementById('ctrl-menu-toggle');
      if(menuToggler && mobileOffcanvas){
        // initialize
        menuToggler.setAttribute('aria-expanded', 'false');
        mobileOffcanvas.addEventListener('show.bs.offcanvas', function(){ menuToggler.setAttribute('aria-expanded', 'true'); });
        mobileOffcanvas.addEventListener('hide.bs.offcanvas', function(){ menuToggler.setAttribute('aria-expanded', 'false'); });
      }
    });
  </script>
  <?php
    // Capture output, rewrite internal absolute attributes to include base when needed, then echo
  $html = ob_get_clean();
  if (!empty($base)) {
    // Only rewrite asset paths — do NOT rewrite navigation links (href on anchors) or form actions.
    // 1) exact assets
    $replacements = [
      '"/styles.css"' => '"' . $base . '/styles.css"',
      "'/styles.css'" => "'" . $base . "/styles.css'",
      'src="/app.js"' => 'src="' . $base . '/app.js"',
      "src='/app.js'" => "src='" . $base . "/app.js'",
      'href="/favicon.ico"' => 'href="' . $base . '/favicon.ico"',
      "href='/favicon.ico'" => "href='" . $base . "/favicon.ico'",
    ];
    $html = str_replace(array_keys($replacements), array_values($replacements), $html);

    // 2) any src/href that begins with /media/ -> prefix with base
    $html = preg_replace('#(src|href)=("|\')/media/#i', '$1=$2' . $base . '/media/', $html);
  }
  echo $html;
  ?>
</body>
</html>
