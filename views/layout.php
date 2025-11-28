<?php
// Auto-detect base path when project is served from a subfolder (example: /xlerion_cmr/public)
// and buffer the template output so we can rewrite internal absolute attributes (src/href/action)
// to point to the correct public folder without changing every link in the template.
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
// $base = '';
$detectedBase = '';
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
  if (is_dir($fs)) { $detectedBase = $c; break; }
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
  // Forzar rutas desde la raíz para evitar resoluciones relativas (ej. /Planos.png/styles.css)
  $assetBase = '/';
  if (substr($assetBase,0,1) !== '/') $assetBase = '/' . $assetBase;
  if (substr($assetBase,-1) !== '/') $assetBase .= '/';
  ?>
  <base href="<?php echo htmlspecialchars($assetBase, ENT_QUOTES, 'UTF-8'); ?>">
  <?php
    // add cache-busting based on file modification time so browser picks latest styles during development
    $localStylesPath = __DIR__ . '/../public/styles.css';
    $styleVer = (file_exists($localStylesPath)) ? filemtime($localStylesPath) : time();
  ?>
  <link rel="stylesheet" href="<?php echo $assetBase; ?>styles.css?v=<?php echo $styleVer; ?>">
  <script src="<?php echo $assetBase; ?>app.js" defer></script>
  <!-- Inline critical overrides: force light-mode dropdown and Contact CTA visuals while debugging (higher priority than external CSS) -->
  <style>
    html.theme-light header.site-header .navbar .dropdown-menu,
    html.theme-light header.site-header .navbar .dropdown,
    html.theme-light .navbar .dropdown-menu {
      background: #ffffff !important;
      color: #0f1724 !important;
      border: 1px solid rgba(16,24,32,0.06) !important;
      box-shadow: 0 12px 30px rgba(2,12,32,0.08) !important;
    }
    html.theme-light header.site-header .navbar .dropdown-menu::after,
    html.theme-light .navbar .dropdown-menu::after {
      content: '' !important;
      position: absolute !important;
      left: 6px !important; right: 6px !important; top: 6px !important; height: 1px !important;
      background: rgba(16,24,32,0.12) !important; pointer-events: none !important;
    }
    /* Contact CTA exact appearance in light mode */
    html.theme-light header.site-header .navbar .nav-item.d-none.d-lg-flex.align-items-center > a.contact-cta,
    html.theme-light a.contact-cta {
      background: #ffffff !important; color: #6b6f75 !important;
      border: 1px solid rgba(16,24,32,0.06) !important; box-shadow: none !important;
      height: 40px !important; padding: 0 .9rem !important; display:inline-flex !important; align-items:center !important; gap:.5rem !important; border-radius:999px !important;
    }
    html.theme-light a.contact-cta:hover, html.theme-light header.site-header .navbar a.contact-cta:hover {
      background: #374151 !important; color: #ffffff !important;
    }
  </style>
  <!-- Pre-render theme script: apply saved or system theme before paint to avoid FOUC -->
  <script>!function(){try{var s=localStorage.getItem('xlerion_theme');var t=s?s:(window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');if(t==='dark'){document.documentElement.classList.add('theme-dark');document.documentElement.classList.remove('theme-light')}else{document.documentElement.classList.remove('theme-dark');document.documentElement.classList.add('theme-light')}}catch(e){/*ignore*/}}();</script>
  <!-- debug CSS removed (was temporarily injected for live_debug verification) -->
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
  <!-- Image lightbox modal (global) -->
  <div class="modal fade" id="xlerionImageModal" tabindex="-1" aria-labelledby="xlerionImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="xlerionImageModalLabel">Imagen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body d-flex flex-column flex-lg-row gap-3">
          <div style="flex:1 1 60%;">
            <img id="xlerionModalImg" src="" alt="" class="pm-main-image" />
          </div>
          <div style="flex:1 1 40%;">
            <h4 id="xlerionModalTitle" class="mb-1">Título</h4>
            <p id="xlerionModalSubtitle" class="text-muted small mb-3">Subtítulo</p>
            <div id="xlerionModalDesc">Descripción de la imagen.</div>
            <div class="mt-3">
              <a id="xlerionModalOpenPost" href="#" class="btn btn-primary d-none" target="_self">Abrir entrada</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function(){
      function openImageModal(data){
        var img = document.getElementById('xlerionModalImg');
        var title = document.getElementById('xlerionModalTitle');
        var subtitle = document.getElementById('xlerionModalSubtitle');
        var desc = document.getElementById('xlerionModalDesc');
        var openPost = document.getElementById('xlerionModalOpenPost');
        if(!data) return;
        if(img && data.src) img.setAttribute('src', data.src);
        if(img && data.alt) img.setAttribute('alt', data.alt);
        if(title) title.textContent = data.title || '';
        if(subtitle) subtitle.textContent = data.subtitle || '';
        if(desc) desc.textContent = data.desc || '';
        if(openPost){
          if(data.postHref){ openPost.classList.remove('d-none'); openPost.setAttribute('href', data.postHref); }
          else { openPost.classList.add('d-none'); }
        }
        // show modal programmatically if bootstrap is loaded
        if(window.bootstrap){
          var modalEl = document.getElementById('xlerionImageModal');
          if(modalEl){
            try { new bootstrap.Modal(modalEl).show(); } catch(e) { /* ignore */ }
          }
        }
      }
      document.addEventListener('keydown', function(e){
        var key = e.key || e.code || '';
        if (key === 'Enter' || key === ' ' || key === 'Spacebar'){
          var af = document.activeElement;
          if(!af) return;
          // check focused element is inside the gallery/sidebar or is a gallery item
          var isGalleryItem = (af.matches && af.matches('.section-cards figure, .section-cards img, .section-cards .card-img-top')) || (af.closest && af.closest('.section-cards'));
          if (!isGalleryItem) return;
          e.preventDefault();
          // Prefer the <img> element if available
          var img = (af.matches && (af.matches('img') || af.matches('.card-img-top'))) ? af : (af.querySelector ? af.querySelector('img') : null);
          if (img){
            var data = {
              src: img.getAttribute('src') || img.getAttribute('data-src') || '',
              alt: img.getAttribute('alt') || '',
              title: img.getAttribute('data-title') || img.getAttribute('title') || img.getAttribute('alt') || '',
              subtitle: img.getAttribute('data-subtitle') || '',
              desc: img.getAttribute('data-desc') || ''
            };
            var cardWrap = img.closest && img.closest('.blog-card-wrap');
            var link = cardWrap ? cardWrap.querySelector('.stretched-link-wrapper') : null;
            if (link && link.getAttribute('href')) data.postHref = link.getAttribute('href');
            openImageModal(data);
            return;
          }
          // Fallback: element itself may carry data-* attributes
          if (af.getAttribute && (af.getAttribute('data-src') || af.getAttribute('data-title'))){
            var data2 = {
              src: af.getAttribute('data-src') || '',
              alt: af.getAttribute('data-alt') || '',
              title: af.getAttribute('data-title') || af.getAttribute('data-alt') || '',
              subtitle: af.getAttribute('data-subtitle') || '',
              desc: af.getAttribute('data-desc') || ''
            };
            var wrap = af.closest && af.closest('.blog-card-wrap');
            var link2 = wrap ? wrap.querySelector('.stretched-link-wrapper') : null;
            if (link2 && link2.getAttribute('href')) data2.postHref = link2.getAttribute('href');
            openImageModal(data2);
          }
        }
      }, false);
    })();
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
  /* Header: remove blue gradient and make fully transparent */
  header{background:transparent !important;color:var(--fg);padding:1rem}
  /* Desktop navbar padding refinement (avoid large top gap) */
    @media (min-width:992px){
    header.site-header{padding:.35rem .85rem !important;}
    header.site-header .navbar{min-height:0 !important; padding:0 !important; margin:0 !important;}
    header.site-header .navbar > .container-fluid{padding-top:0 !important; padding-bottom:0 !important; min-height:0 !important;}
    header.site-header .navbar-brand img{height:40px !important;}
    header.site-header .navbar .container-fluid{padding-top:0 !important; padding-bottom:0 !important;}
    header.site-header .navbar-nav > li > a.nav-link{padding-top:.4rem; padding-bottom:.4rem;}
    header.site-header .navbar-brand{padding-top:.25rem; padding-bottom:.25rem;}
    body > header.site-header + nav.sticky-top {margin-top:0 !important;}
  }
  /* Make header inner container match site header width on large screens
     Keep full-width on small screens so bootstrap's responsive behavior remains intact */
  @media (min-width:1200px) {
    header.site-header .container-fluid,
    header.site-header .navbar > .container-fluid,
    header.site-header .navbar .container-fluid {
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
      padding-left: 0.85rem; /* align with header padding */
      padding-right: 0.85rem;
    }
  }
  /* Place the visible header background behind the inner container so the container-fluid
     no longer displays as a separate full-width bar. This uses a pseudo-element on the
     header to render the background while keeping the inner container content on top. */
  header.site-header {
    position: relative; /* ensure pseudo-element is positioned relative to header */
    z-index: 1000; /* keep header on top of page content */
  }
  /* Remove pseudo-element background (no colored layer) */
  header.site-header::before { content: none !important; }
  /* Ensure the inner container does not draw its own background, shadow or color */
  header.site-header .container-fluid,
  header.site-header .navbar > .container-fluid,
  header.site-header .navbar .container-fluid {
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
  }
  /* Explicit theme overrides: keep header transparent in both themes */
  html.theme-dark header.site-header, html.theme-light header.site-header { background: transparent !important; }
  @media (min-width:1200px){
    header.site-header{padding:.4rem 1.4rem !important;}
  }
  /* Make the main header/nav sticky at the top */
  header.site-header, nav.navbar, .navbar { position: sticky; top: 0; z-index: 9999; }
  nav a{color:var(--deep);margin-right:1rem;text-decoration:none;font-weight:600}
  /* container sizing is controlled via public/styles.css to allow responsive adjustments */
    footer{border-top:1px solid #eee;padding:1rem;color:#666;background:transparent}
    .btn{background:var(--accent);color:#000;padding:.5rem 1rem;border-radius:6px;text-decoration:none;display:inline-block}
    .hero{padding:2rem;border-radius:8px;background:linear-gradient(180deg, rgba(0,238,255,0.06), rgba(67,255,255,0.03));margin-bottom:1rem}
    h1,h2,h3{color:var(--fg)}
    a { color: var(--deep);} 
    @media (prefers-color-scheme: dark) {
      :root{ --bg:#121212; --fg:#FFFFFF; }
      header{color:var(--fg)}
    }

    /* --- Contact improvements --- */
    .contact-card{background:rgba(0,0,0,0.03);padding:1rem;border-radius:8px;display:flex;flex-direction:column;gap:.6rem}
    .contact-card h5{margin:0 0 .25rem 0;font-size:1.05rem}
    .contact-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.45rem}
    .contact-item{display:flex;align-items:center;gap:.5rem;color:var(--muted);font-size:0.95rem}
    .contact-item a{color:var(--deep);text-decoration:none}
    .contact-item .material-symbols-outlined{font-size:18px;opacity:.9}
    .contact-cta{background:var(--deep);color:var(--bg);padding:.5rem .75rem;border-radius:6px;display:inline-block;text-decoration:none}
    .contact-meta{font-size:.9rem;color:#666;margin-top:.5rem}
  /* Social icons: fixed order and compact spacing */
  .social-icons{display:flex;gap:.5rem;align-items:center;flex-wrap:wrap}
  .social-icons .social-link{display:inline-flex;align-items:center;gap:.4rem;padding:.15rem .35rem;color:inherit;text-decoration:none;font-size:0.95rem;line-height:1}
  .social-icons .social-link svg{width:20px;height:20px;display:block;fill:currentColor}
  /* make text links visually consistent with icons */
  .social-icons .social-link:not(:has(svg)) { font-weight:600; padding:.15rem .45rem; }
  /* hover/active states */
  .social-icons .social-link:hover, .social-icons .social-link:focus { color: var(--accent) !important; text-decoration:none }
  /* Ensure visibility on dark theme */
  html.theme-dark .site-footer .social-icons .social-link { color: rgba(255,255,255,0.95); }
  /* Ensure visibility on light theme */
  html.theme-light .site-footer .social-icons .social-link { color: var(--deep); }
  /* Subscribe compact adjustments */
  .footer-subscribe{padding-top:.1rem;padding-bottom:.1rem}
  /* Title: force white for strong contrast in footer */
  .footer-subscribe h5{margin-bottom:.35rem;color:#ffffff}
  /* Body text for subscribe block */
  .footer-subscribe .footer-subscribe-text{margin-bottom:.4rem;margin-top:0;color:#ffffff}
  /* Make subscribe input and button compact and responsive */
  .footer-subscribe .form-control { height: 36px; padding: .45rem .6rem; background: rgba(255,255,255,0.03); color: inherit; border:1px solid rgba(255,255,255,0.04); }
  .footer-subscribe .form-control::placeholder { color: rgba(255,255,255,0.45); opacity:1 }
  .footer-subscribe .btn { height: 36px; padding: .35rem .7rem; white-space: nowrap; font-size: .92rem; max-width: 180px; overflow: hidden; text-overflow: ellipsis; }
  .footer-subscribe form.d-flex { gap: .5rem; align-items: center; }
  /* Stack inputs earlier for better small-screen layout */
  @media (max-width: 767px) {
    .footer-subscribe form.d-flex { flex-direction: column !important; align-items: stretch !important; }
    .footer-subscribe .form-control, .footer-subscribe .btn { width: 100%; }
    .footer-subscribe .btn { margin-top: .25rem; }
  }
  /* Ensure subscribe colors look good in both themes */
  html.theme-dark .site-footer .footer-subscribe h5 { color: #ffffff !important }
  html.theme-light .site-footer .footer-subscribe h5 { color: #ffffff !important }
  /* Force subscribe subtitle/text to pure white in all themes */
  .site-footer .footer-subscribe .footer-subscribe-text,
  .site-footer .footer-subscribe p,
  .site-footer .footer-subscribe p.mt-2 {
    color: #ffffff !important;
  }

  /* Stronger overrides: theme-specific selectors with higher specificity to beat other !important rules */
  html.theme-dark .site-footer .footer-subscribe .footer-subscribe-text,
  html.theme-dark .site-footer .footer-subscribe p,
  html.theme-dark .site-footer .footer-subscribe p.mt-2 {
    color: #ffffff !important;
  }
  html.theme-light .site-footer .footer-subscribe .footer-subscribe-text,
  html.theme-light .site-footer .footer-subscribe p,
  html.theme-light .site-footer .footer-subscribe p.mt-2 {
    color: #ffffff !important;
  }

  /* Footer links title placed under social icons */
  .footer-links-title { padding-top: .75rem; margin-top:.4rem; color: #ffffff; font-weight:700; font-size:0.95rem }
  .site-footer .col-6.col-md-3 .list-unstyled { margin-top: .35rem }
    @media (max-width:767px){
      .contact-card{padding:.75rem}
    }
    /* Modal theme support: make modal content follow root theme classes */
    html.theme-dark .modal-content {
      background: #121212;
      color: #FFFFFF;
      border-color: #2C2C2C;
    }
    html.theme-dark .modal-header,
    html.theme-dark .modal-footer {
      border-color: rgba(255,255,255,0.06);
    }
    html.theme-dark .modal-body { color: #e6e6e6; }
    html.theme-dark .btn-outline-primary { color: var(--accent); border-color: rgba(0,238,255,0.12); }
    html.theme-dark .btn-outline-primary:hover { background: rgba(0,238,255,0.04); }
    html.theme-dark .modal-backdrop.show { background-color: rgba(0,0,0,0.6); }
    /* Inputs inside modals should remain readable */
    html.theme-dark .modal .form-control { background: #1a1a1a; color: #fff; border-color: #2c2c2c; }
  /* Modal image sizing and gallery overlay */
  .pm-main-image { width:100%; height:380px; object-fit:cover; display:block; border-radius:6px; }
  @media (min-width:992px) { .pm-main-image { height:420px; } }
  /* Project thumbnails: ensure list thumbnails and card images are uniform and responsive */
    /* Ensure subscribe block remains readable in dark theme: force white for title and subtitle
       (previous rule used var(--accent) which conflicted with our intent to keep subtitle white) */
    html.theme-dark .site-footer .footer-subscribe h5,
    html.theme-dark .site-footer .footer-subscribe p,
    html.theme-dark .site-footer .footer-subscribe .footer-subscribe-text,
    html.theme-dark .site-footer .footer-subscribe .form-control,
    html.theme-dark .site-footer .footer-subscribe .mt-2 {
      color: #ffffff !important;
    }
  /* Force Suscríbete title to white in all themes (override other rules) */
  .site-footer .footer-subscribe h5 { color: #ffffff !important; }
  .project-thumb { width:100%; height:180px; object-fit:cover; display:block; border-radius:6px; }
  .project-card-img { width:100%; height:200px; object-fit:cover; display:block; }
  @media (min-width:768px) { .project-thumb { height:200px; } .project-card-img { height:220px; } }
  @media (min-width:992px) { .project-thumb { height:220px; } .project-card-img { height:260px; } }
  /* Fullscreen gallery overlay */
  #pm-gallery-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.95); display:none; align-items:center; justify-content:center; z-index:2000; }
  #pm-gallery-overlay.show { display:flex; }
  #pm-gallery-overlay img { max-width:95%; max-height:95%; object-fit:contain; }
  #pm-gallery-overlay .g-btn { position:absolute; top:16px; right:16px; z-index:2010 }
  #pm-gallery-overlay .g-prev, #pm-gallery-overlay .g-next { position:absolute; top:50%; transform:translateY(-50%); background:rgba(0,0,0,0.4); border:none;color:#fff;padding:.6rem .8rem;border-radius:6px }
  #pm-gallery-overlay .g-prev { left:12px } #pm-gallery-overlay .g-next { right:12px }
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
        // allow admins to bypass and also allow any request to the /admin area to proceed
        $isAdmin = false;
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        try{ $isAdmin = isset($_SESSION['user_is_admin']) && $_SESSION['user_is_admin']; } catch(Exception $e) { $isAdmin = false; }

        // Determine current request path and allow admin area to bypass maintenance
        $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($currentUri, PHP_URL_PATH) ?: '/';
        // Also check common server variables in case the app is hosted in a subfolder or rewrites are used
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $phpSelf = $_SERVER['PHP_SELF'] ?? '';
        $isAdminPath = (
          preg_match('#(^|/)admin($|/)#', $path) === 1 ||
          preg_match('#(/|^)admin(/|$)#', $scriptName) === 1 ||
          preg_match('#(/|^)admin(/|$)#', $phpSelf) === 1
        );

        if (!$isAdmin && !$isAdminPath){
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
  <header class="site-header" data-theme-bg="site-dedede">
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
          // Exclude contact pages from the generated menu — we'll keep the dedicated Contact CTA on the right
          $rows = array_values(array_filter($rows, function($r){
            return !in_array($r['slug'], ['contact','contacto'], true);
          }));

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
            'Recursos' => ['slugs' => ['blog','convocatorias-alianzas'], 'icon' => 'menu_book'],
            // New primary group for tools and technical resources
            'Herramientas' => ['slugs' => ['documentacion','aplicaciones','toolkits-modulares'], 'icon' => 'extension'],
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

          // Remove clearly noisy / autogenerated / duplicate slugs from the public menu
          // (these pages are useful as content but shouldn't clutter the 'Otros' nav)
          $otrosBlacklist = [
            'aplicaciones-destacadas','aplicaciones-pr-cticas','aplicaciones','beneficios',
            'capacidades-t-cnicas','caracter-sticas-clave','componentes-clave',
            'documentaci-n','documentaci-n-estructurada-para-mantenimiento-y-transferencia-de-conocimiento',
            'documentaci-n-incluida','ejemplo-destacado','funcionalidades-principales',
            'integraci-n-avanzada-con-motores-gr-ficos','m-dulos-disponibles','principios-de-dise-o',
            'sistemas-de-diagn-stico','toolkits-modulares','legal-y-privacidad','convocatorias-y-alianzas',
            'redes','el-origen-de-total-darkness'
          ];

          // Apply blacklist across all groups: remove matching top-level items and children
          foreach ($grouped as $gTitle => $items) {
            $clean = [];
            foreach ($items as $it) {
              if (in_array($it['slug'], $otrosBlacklist, true)) continue;
              // filter children
              if (!empty($it['children'])) {
                $children = [];
                foreach ($it['children'] as $c) {
                  if (!in_array($c['slug'], $otrosBlacklist, true)) $children[] = $c;
                }
                $it['children'] = $children;
              }
              $clean[] = $it;
            }
            $grouped[$gTitle] = $clean;
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
          // Render groups in a predictable, designer-defined order (groupsDef keys first),
          // then render any remaining groups (like 'Otros'). This prevents 'Otros' from
          // appearing before principal sections.
          $orderedGroupTitles = array_keys($groupsDef);
          // append any extra groups that exist in $grouped but not in groupsDef
          foreach (array_keys($grouped) as $g) { if (!in_array($g, $orderedGroupTitles, true)) $orderedGroupTitles[] = $g; }

          // Remove the 'Otros' group entirely from the menu
          // This prevents autogenerated or miscellaneous pages from appearing in navigation
          if (isset($grouped['Otros'])) {
            unset($grouped['Otros']);
          }
          // Also ensure orderedGroupTitles does not contain 'Otros'
          $orderedGroupTitles = array_values(array_filter($orderedGroupTitles, function($t){ return $t !== 'Otros'; }));
          foreach ($orderedGroupTitles as $gTitle) {
            if (!isset($grouped[$gTitle]) || count($grouped[$gTitle]) === 0) continue;
            $items = $grouped[$gTitle];
            $gIcon = isset($groupsDef[$gTitle]['icon']) ? $groupsDef[$gTitle]['icon'] : 'folder';
            // css hook: mark groups that are part of the primary groupsDef
            $isMainGroup = isset($groupsDef[$gTitle]);
            // sanitized class for group (fallback to generic nav-group)
            $gClass = 'nav-group' . ($isMainGroup ? ' nav-group-main' : '');
            $gData = ' data-group="' . htmlspecialchars($gTitle, ENT_QUOTES, 'UTF-8') . '"';
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
              $menuHtml .= "<li class=\"nav-item dropdown {$gClass}\"{$gData}>";
              $menuHtml .= "<a class=\"nav-link dropdown-toggle{$parentActiveClass}\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"{$ariaExpanded}\">";
              $menuHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$gIcon}</span> {$gTitle}</a>";
              $menuHtml .= "<ul class=\"dropdown-menu\" role=\"menu\">";
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
                $menuHtml .= "<li class=\"nav-item dropdown {$gClass}\"{$gData}>";
                $menuHtml .= "<a class=\"nav-link dropdown-toggle{$parentActiveClass}\" href=\"#\" role=\"button\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"{$ariaExpanded}\">";
                $menuHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$icon}</span> {$full}</a>";
                $menuHtml .= "<ul class=\"dropdown-menu\" role=\"menu\">";
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
                $menuHtml .= "<li class=\"nav-item {$gClass}\"{$gData}><a class=\"nav-link{$activeClass}\" href=\"/{$slug}\" title=\"{$full}\" aria-label=\"{$full}\"{$isActive}>";
                $menuHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$icon}</span> {$full}</a></li>";
              }
            }
          }

          // Build offcanvas list: grouped but linear for accessibility on mobile
          // Use the same ordered group titles as desktop so mobile menu mirrors desktop order
          foreach ($orderedGroupTitles as $gTitle) {
            if (!isset($grouped[$gTitle]) || count($grouped[$gTitle]) === 0) continue;
            $items = $grouped[$gTitle];
            $offcanvasHtml .= "<li class=\"nav-item mt-2\"><div class=\"nav-link text-muted small fw-semibold\">".htmlspecialchars($gTitle)."</div></li>";
            foreach ($items as $it) {
              $slug = htmlspecialchars($it['slug']);
              $full = htmlspecialchars($it['title']);
              $icon = isset($iconNameMap[$it['slug']]) ? $iconNameMap[$it['slug']] : 'chevron_right';
              $isActive = ($currentSlug === $it['slug']) ? ' aria-current="page"' : '';
              $activeClass = ($currentSlug === $it['slug']) ? ' active' : '';
              $offcanvasHtml .= "<li class=\"nav-item\"><a class=\"nav-link{$activeClass}\" href=\"/{$slug}\" title=\"{$full}\" aria-label=\"{$full}\"{$isActive}>";
              $offcanvasHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$icon}</span> {$full}</a></li>";
              // children flattened under parent for mobile
              if (!empty($it['children'])) {
                foreach ($it['children'] as $c) {
                  $cslug = htmlspecialchars($c['slug']);
                  $cfull = htmlspecialchars($c['title']);
                  $co = isset($iconNameMap[$c['slug']]) ? $iconNameMap[$c['slug']] : 'chevron_right';
                  $cactive = ($currentSlug === $c['slug']) ? ' aria-current="page"' : '';
                  $offcanvasHtml .= "<li class=\"nav-item ps-3\"><a class=\"nav-link small\" href=\"/{$cslug}\" title=\"{$cfull}\" aria-label=\"{$cfull}\"{$cactive}>";
                  $offcanvasHtml .= "<span class=\"material-symbols-outlined nav-icon\" aria-hidden=\"true\">{$co}</span> {$cfull}</a></li>";
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

      <?php
        // Ensure the 'Inicio' link appears first in both desktop and mobile menus.
        try {
          $inicioActive = (isset($currentSlug) && $currentSlug === 'inicio') ? ' active' : '';
          $inicioAria = (isset($currentSlug) && $currentSlug === 'inicio') ? ' aria-current="page"' : '';
          $inicioLi = '<li class="nav-item"><a class="nav-link' . $inicioActive . '" href="/" title="Inicio" aria-label="Inicio"' . $inicioAria . '>';
          $inicioLi .= '<span class="material-symbols-outlined nav-icon" aria-hidden="true">home</span> Inicio</a></li>';

          // If the generated menu already contains an <a ... aria-label="Inicio">, move its enclosing <li> to the front.
          $pattern = '/(<li\b[^>]*>\s*<a[^>]*aria-label="Inicio"[^>]*>.*?<\/a>\s*<\/li>)/is';
          if (preg_match($pattern, $menuHtml, $m)) {
            $found = $m[1];
            // remove first occurrence and prepend
            $menuHtml = preg_replace($pattern, '', $menuHtml, 1);
            $menuHtml = $found . $menuHtml;
          } else {
            // inject synthetic Inicio if no existing item found
            if (empty($menuHtml) || (strpos($menuHtml, 'href="/"') === false && strpos($menuHtml, '> Inicio') === false && strpos($menuHtml, '>Inicio') === false)) {
              $menuHtml = $inicioLi . $menuHtml;
            }
          }

          // Repeat same logic for offcanvas mobile HTML
          if (preg_match($pattern, $offcanvasHtml, $m2)) {
            $found2 = $m2[1];
            $offcanvasHtml = preg_replace($pattern, '', $offcanvasHtml, 1);
            $offcanvasHtml = $found2 . $offcanvasHtml;
          } else {
            if (empty($offcanvasHtml) || (strpos($offcanvasHtml, 'href="/"') === false && strpos($offcanvasHtml, '> Inicio') === false && strpos($offcanvasHtml, '>Inicio') === false)) {
              $offcanvasHtml = $inicioLi . $offcanvasHtml;
            }
          }
        } catch (Exception $e) { /* non-fatal */ }
      ?>

      <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
          <a class="navbar-brand" href="/">
            <?php
              // prefer logo from settings if available
              try{
                // Prefer the bundled SVG logo if the file exists on disk. If it does not exist,
                // fall back to a configured value from settings (if present).
                $defaultLogo = '/media/LogoX.svg';
                $logo = $defaultLogo;
                $val = $pdo->query("SELECT v FROM settings WHERE k='logo_url'")->fetchColumn();
                if ($val && !file_exists(__DIR__ . '/../public' . $defaultLogo)) {
                  $logo = $val;
                }
              } catch(Exception $e){
                // On any error, ensure we still reference the bundled logo
                $logo = '/media/LogoX.svg';
              }
            ?>
            <img id="siteLogoImg" src="<?= htmlspecialchars($logo) ?>" alt="Xlerion" class="site-logo" data-logo-src="<?= htmlspecialchars($logo) ?>" />
          </a>
          <!-- Toggler (manual JS controls offcanvas) -->
          <button id="ctrl-menu-toggle" class="navbar-toggler" type="button" aria-controls="mobileMenu" aria-label="Abrir menú" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
          </button>

          <!-- Desktop menu (visible on large screens) -->
          <div class="navbar-collapse d-none d-lg-flex" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <?= $menuHtml ?>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
              <!-- Right-aligned contact CTA (desktop) -->
              <li class="nav-item d-none d-lg-flex align-items-center">
                <a class="nav-link d-flex align-items-center gap-2 contact-cta" href="/contacto" title="Contacto" aria-label="Contacto">
                  <span class="material-symbols-outlined" aria-hidden="true">mail</span>
                  <span class="d-none d-sm-inline">Contacto</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <?php
        // Render assigned header template for this page if available
        try{
          require_once __DIR__ . '/../src/Helpers/TemplateRenderer.php';
          $currentPageId = isset($GLOBALS['page']) && is_array($GLOBALS['page']) && isset($GLOBALS['page']['id']) ? intval($GLOBALS['page']['id']) : null;
          if (!$currentPageId){
            // try to map current slug to page id
            try{ $st = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1'); $st->execute([trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/')]); $r = $st->fetch(PDO::FETCH_ASSOC); if ($r) $currentPageId = intval($r['id']); }catch(Exception $ee){}
          }
          if ($currentPageId){ echo TemplateRenderer::render_section_for_page($currentPageId, 'header'); }
        }catch(Exception $e){ /* ignore render errors */ }
      ?>

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

      <script>
        (function(){
          // Initialize all mc-stack instances (desktop + mobile) so controls behave the same
          const stacks = Array.from(document.querySelectorAll('.mc-stack'));
          if (!stacks.length) return;

          // Helper: toggle theme and sync visual pressed state across all theme buttons
          function setTheme(isDark){
            if (isDark) {
              document.documentElement.classList.remove('theme-light');
              document.documentElement.classList.add('theme-dark');
              localStorage.setItem('x-theme','dark');
            } else {
              document.documentElement.classList.remove('theme-dark');
              document.documentElement.classList.add('theme-light');
              localStorage.setItem('x-theme','light');
            }
            // sync theme button visual state
            Array.from(document.querySelectorAll('[data-action="theme"]')).forEach(btn => {
              btn.classList.toggle('is-active', isDark);
              btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            });
          }

          // Wire each stack independently to limit cross-effect between stacks when non-theme buttons are toggled
          stacks.forEach(stack => {
            const btns = Array.from(stack.querySelectorAll('.mc-btn'));
            if (!btns.length) return;

            btns.forEach(b => {
              b.setAttribute('aria-pressed', b.classList.contains('is-active') ? 'true' : 'false');
              b.addEventListener('click', (e) => {
                const action = b.getAttribute('data-action');
                if (action === 'theme') {
                  const pressed = b.getAttribute('aria-pressed') === 'true';
                  setTheme(!pressed);
                  return;
                }

                if (action === 'search') {
                  // attempt to reuse desktop search control behavior: trigger existing click if present
                  const desktopSearch = document.getElementById('ctrl-search');
                  if (desktopSearch && desktopSearch !== b) desktopSearch.click();
                }

                // Non-theme: in each stack make selected button visually active (tab-like)
                btns.forEach(x => { x.classList.remove('is-active'); x.setAttribute('aria-pressed','false'); });
                b.classList.add('is-active');
                b.setAttribute('aria-pressed','true');
              });
            });
          });

          // Restore theme on load and sync buttons
          try {
            const saved = localStorage.getItem('x-theme');
            if (saved === 'dark') setTheme(true);
            else if (saved === 'light') setTheme(false);
          } catch (e) { /* ignore storage errors */ }
        })();
      </script>

      <!-- Offcanvas mobile menu -->
      <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="mobileMenuLabel">Menú</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav">
            <?= $offcanvasHtml ?>
            <li class="nav-item mt-2"><a class="nav-link" href="/admin/login.php">Admin</a></li>
            <!-- Mobile-only menu controls integrated into offcanvas -->
            <li class="nav-item mt-3 d-lg-none">
              <div class="nav-link text-muted small fw-semibold">Controles</div>
            </li>
            <li class="nav-item d-lg-none">
              <div class="mc-stack d-flex" role="toolbar" aria-label="Controles móviles">
                <button id="ctrl-search-mobile" class="mc-btn" data-action="search" title="Buscar" aria-label="Buscar">
                  <span class="material-symbols-outlined">search</span>
                </button>
                <button id="ctrl-theme-mobile" class="mc-btn" data-action="theme" title="Alternar tema" aria-label="Alternar tema">
                  <span class="material-symbols-outlined">brightness_2</span>
                </button>
                <a id="ctrl-admin-mobile" class="mc-btn" href="/admin/login.php" title="Admin" aria-label="Admin">
                  <span class="material-symbols-outlined">settings</span>
                </a>
              </div>
            </li>
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
        // $base = basename($bannerSrc, '.jpg');
        $bannerBase = basename($bannerSrc, '.jpg');
        $webp400 = $thumbDir . $bannerBase . '-400.webp';
        $webp800 = $thumbDir . $bannerBase . '-800.webp';
        $jpg400 = $thumbDir . $bannerBase . '-400.jpg';
        $jpg800 = $thumbDir . $bannerBase . '-800.jpg';
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
          $bannerClassAttr = isset($banner_title_class) ? ' class="' . htmlspecialchars($banner_title_class) . '"' : ' class="hero-content text-center"';
          // allow views to supply a custom hero id (avoid duplicate ids when nested templates also render one)
          if (!isset($hero_id)) { $hero_id = 'hero-title'; }
        ?>
        <h1 id="<?php echo htmlspecialchars($hero_id, ENT_QUOTES, 'UTF-8'); ?>"<?= $bannerClassAttr ?>><?php echo htmlspecialchars($title ?? 'Xlerion'); ?></h1>
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
  <?php
    // Allow a per-page assigned footer template to replace or augment the default footer.
    try{
      require_once __DIR__ . '/../src/Helpers/TemplateRenderer.php';
      $currentPageId = isset($GLOBALS['page']) && is_array($GLOBALS['page']) && isset($GLOBALS['page']['id']) ? intval($GLOBALS['page']['id']) : null;
      if (!$currentPageId){
        try{ $st = $pdo->prepare('SELECT id FROM cms_pages WHERE slug = ? LIMIT 1'); $st->execute([trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/')]); $r = $st->fetch(PDO::FETCH_ASSOC); if ($r) $currentPageId = intval($r['id']); }catch(Exception $ee){}
      }
      if ($currentPageId){
        $rendered = TemplateRenderer::render_section_for_page($currentPageId, 'footer');
        if ($rendered && trim($rendered) !== ''){
          echo $rendered;
        } else {
          // fallback to existing footer markup
          ?>
          <footer class="site-footer" role="contentinfo" aria-label="Pie de página de Xlerion">
            <div class="container py-3">
              <div class="row">
                <?php
                // ...existing footer markup preserved when no assigned footer present...
                ?>
          <?php
        }
      } else {
        // no page id: render default footer
        ?>
        <footer class="site-footer" role="contentinfo" aria-label="Pie de página de Xlerion">
          <div class="container py-3">
            <div class="row">
        <?php
      }
    } catch(Exception $e){
      // on error, render default footer
      ?>
      <footer class="site-footer" role="contentinfo" aria-label="Pie de página de Xlerion">
        <div class="container py-3">
          <div class="row">
      <?php
    }
  ?>
            <?php // Now render text links for crowd/patron platforms in the chosen order ?>
            <?php if (!empty($social['indiegogo'])): ?>
              <a class="social-link" href="<?= htmlspecialchars($social['indiegogo']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Indiegogo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="currentColor"/><path d="M3 12h18M12 3v18" stroke="rgba(0,0,0,0.12)" stroke-width="1" stroke-linecap="round"/></svg>
                <span class="visually-hidden">Indiegogo</span>
              </a>
            <?php endif; ?>
            <?php if (!empty($social['kickstarter'])): ?>
              <a class="social-link" href="<?= htmlspecialchars($social['kickstarter']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Kickstarter">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M12 2l3 6-3 4-3-4 3-6z" fill="currentColor"/><path d="M5 20c0-3 7-7 7-7s7 4 7 7v2H5v-2z" fill="currentColor"/></svg>
                <span class="visually-hidden">Kickstarter</span>
              </a>
            <?php endif; ?>
            <?php if (!empty($social['patreon'])): ?>
              <a class="social-link" href="<?= htmlspecialchars($social['patreon']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Patreon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M12 21s-6-4.5-8-8c-1.5-2.7.5-5.5 3-5.5 1.5 0 2.5 1 5 4 2.5-3 3.5-4 5-4 2.5 0 4.5 2.8 3 5.5-2 3.5-8 8-8 8z" fill="currentColor"/></svg>
                <span class="visually-hidden">Patreon</span>
              </a>
            <?php endif; ?>
          </div>
          <?php
            // Normalize footer links: prefer $footerVars['footer_links'] (newline list "Label|/path"),
            // otherwise fall back to an existing $links if already provided, else use defaults.
            $parsedLinks = [];
            if (!empty($footerVars['footer_links'])) {
              $raw = str_replace(["\r\n", "\r"], "\n", $footerVars['footer_links']);
              $lines = array_filter(array_map('trim', explode("\n", $raw)));
              foreach ($lines as $ln) {
                // Expect format: Label|/path — if missing pipe, treat whole line as label and use '#'
                if (strpos($ln, '|') !== false) {
                  list($lab, $url) = array_map('trim', explode('|', $ln, 2));
                } else {
                  $lab = $ln; $url = '#';
                }
                if ($lab === '') continue;
                $parsedLinks[] = ['label' => $lab, 'url' => $url ?: '#'];
              }
            } elseif (!empty($links) && is_array($links)) {
              // use existing $links provided by other templates
              foreach ($links as $item) {
                if (!is_array($item)) continue;
                $lbl = $item['label'] ?? null;
                $url = $item['url'] ?? ($item['href'] ?? null);
                if (!$lbl) continue;
                $parsedLinks[] = ['label' => $lbl, 'url' => $url ?: '#'];
              }
            }

            if (empty($parsedLinks)) {
              $parsedLinks = [
                ['label' => 'Inicio', 'url' => '/'],
                ['label' => 'Proyectos', 'url' => '/proyectos'],
                ['label' => 'Soluciones', 'url' => '/soluciones'],
                ['label' => 'Documentación', 'url' => '/documentacion'],
                ['label' => 'Contacto', 'url' => '/contact'],
              ];
            }
          ?>
          <div class="footer-links-block">
            <div class="footer-links-title">Enlaces rápidos</div>
            <ul class="list-unstyled">
            <?php foreach ($parsedLinks as $l): ?>
              <li><a href="<?= htmlspecialchars($l['url']) ?>"><?= htmlspecialchars($l['label']) ?></a></li>
            <?php endforeach; ?>
            </ul>
          </div>
        </div>
  <!-- Removed duplicated 'Redes' column (was duplicated earlier). Social icons are rendered in the previous column. -->
        <div class="col-12 col-md-3 footer-subscribe">
          <h5 style="color:#ffffff">Suscríbete</h5>
          <p class="footer-subscribe-text" style="color:#ffffff"><?php echo strip_tags($footerVars['footer_text'] ?? 'Recibe novedades y convocatorias por correo.', '<a><strong><em><br><p><ul><li>'); ?></p>
          <?php if (!isset($footerVars['footer_show_newsletter']) || $footerVars['footer_show_newsletter']): ?>
            <form action="<?= htmlspecialchars($footerVars['footer_subscribe_action'] ?? '/subscribe') ?>" method="post" class="d-flex align-items-center" aria-label="Formulario de suscripción" style="gap:.5rem">
              <input class="form-control me-2" type="email" name="email" placeholder="tu@correo.com" aria-label="email" style="height:36px;padding:.45rem .6rem">
              <button class="btn btn-primary" type="submit" style="height:36px;padding:.35rem .7rem"><?= htmlspecialchars($footerVars['footer_subscribe_label'] ?? 'Suscribir') ?></button>
            </form>
          <?php endif; ?>
          <?php
            // optional extra links text
            $extra = $footerVars['footer_extra_links_text'] ?? null;
          ?>
          <?php if ($extra): ?>
            <p class="mt-2" style="color:#ffffff"><?= $extra ?></p>
          <?php else: ?>
            <p class="mt-2" style="color:#ffffff">También nos encuentras en: <?php
              $pieces = [];
              if (!empty($social['indiegogo'])) $pieces[] = '<a href="'.htmlspecialchars($social['indiegogo']).'" target="_blank" rel="noopener noreferrer">Indiegogo</a>';
              if (!empty($social['kickstarter'])) $pieces[] = '<a href="'.htmlspecialchars($social['kickstarter']).'" target="_blank" rel="noopener noreferrer">Kickstarter</a>';
              if (!empty($social['patreon'])) $pieces[] = '<a href="'.htmlspecialchars($social['patreon']).'" target="_blank" rel="noopener noreferrer">Patreon</a>';
              echo implode(', ', $pieces);
            ?>.</p>
          <?php endif; ?>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-12 text-center small copyright"><?= htmlspecialchars($footerVars['footer_copyright'] ?? '© Xlerion - Todos los derechos reservados') ?></div>
      </div>
    </div>
  </footer>
  <!-- Bootstrap JS (CDN) with SRI -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous" defer></script>
  <style>
    /* When we inline the SVG we add .site-logo-inline to the svg element to
       protect it from global <img> filter rules that may dim images. */
    svg.site-logo-inline { height: 40px; max-height:44px; display:inline-block; vertical-align:middle; }
  </style>
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
      // Menu toggle: manual control to allow closing with same button & cleanup stray backdrops
      var menuBtn = document.getElementById('ctrl-menu-toggle');
      var mobileOffcanvas = document.getElementById('mobileMenu');
      var offInstance = null;
      if(menuBtn && mobileOffcanvas){
        offInstance = new bootstrap.Offcanvas(mobileOffcanvas);
        menuBtn.addEventListener('click', function(e){
          e.preventDefault();
          var expanded = menuBtn.getAttribute('aria-expanded') === 'true';
          if(expanded){ offInstance.hide(); } else { offInstance.show(); }
        });
        // sync aria-expanded
        mobileOffcanvas.addEventListener('show.bs.offcanvas', function(){ menuBtn.setAttribute('aria-expanded','true'); menuBtn.setAttribute('aria-label','Cerrar menú'); });
        mobileOffcanvas.addEventListener('hidden.bs.offcanvas', function(){ menuBtn.setAttribute('aria-expanded','false'); menuBtn.setAttribute('aria-label','Abrir menú');
          // remove any lingering backdrops (defensive)
          setTimeout(function(){
            document.querySelectorAll('.offcanvas-backdrop.show').forEach(function(b){ b.parentNode && b.parentNode.removeChild(b); });
          },150);
        });
        // Escape key closes when open (extra reliability)
        document.addEventListener('keydown', function(ev){ if(ev.key==='Escape' && menuBtn.getAttribute('aria-expanded')==='true'){ offInstance.hide(); } });
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
  // (legacy sync code replaced by manual control above)
    });
  </script>
  <script>
    // Robust logo swap: inline SVG when dark theme is active, restore <img> in light theme.
    (function(){
      var logoImg = document.getElementById('siteLogoImg');
      if (!logoImg) return;
      var originalSrc = logoImg.getAttribute('data-logo-src') || logoImg.getAttribute('src');
      var inlined = null;
      var debounce = false;

      function findExistingInline(parent){
        try { return parent.querySelector('svg.site-logo-inline'); } catch(e){ return null; }
      }

      function fetchAndInlineSvg(url){
        return fetch(url, {credentials: 'same-origin'}).then(function(res){
          if (!res.ok) throw new Error('failed to fetch logo');
          return res.text();
        }).then(function(text){
          var div = document.createElement('div');
          div.innerHTML = text.trim();
          var svg = div.querySelector('svg');
          if (!svg) throw new Error('no svg found');
          svg.classList.add('site-logo-inline');
          svg.removeAttribute('width'); svg.removeAttribute('height');
          return svg;
        });
      }

      function inlineLogo(){
        if (inlined || !originalSrc) return;
        if (!/\.svg(\?|$)/i.test(originalSrc)) return;
        // if an inline svg already exists in the DOM, reuse it instead of creating another
        var existing = findExistingInline(logoImg.parentNode);
        if (existing){ inlined = existing; logoImg.style.display = 'none'; return; }

        fetchAndInlineSvg(originalSrc).then(function(svg){
          try{
            var dupe = findExistingInline(logoImg.parentNode);
            if (dupe && dupe !== svg) { dupe.parentNode.removeChild(dupe); }
            svg.setAttribute('role','img');
            var alt = logoImg.getAttribute('alt') || '';
            if (alt) svg.setAttribute('aria-label', alt);
            // replace the <img> node with the inline svg so we don't keep hidden duplicates
            var placeholder = document.createComment('site-logo-placeholder');
            var parent = logoImg.parentNode;
            parent.replaceChild(placeholder, logoImg);
            parent.insertBefore(svg, placeholder.nextSibling);
            inlined = { svg: svg, placeholder: placeholder, originalImg: logoImg };
          }catch(e){ /* safe fallback */ }
        }).catch(function(){ /* silent fallback to <img> */ });
      }

      function restoreImg(){
        try{
          if (inlined && inlined.svg && inlined.placeholder && inlined.originalImg){
            var p = inlined.placeholder;
            var parent = p.parentNode;
            if (inlined.svg.parentNode) parent.removeChild(inlined.svg);
            parent.replaceChild(inlined.originalImg, p);
          } else {
            // fallback: remove any inline svg we can find and ensure an <img> exists
            var existing = findExistingInline(logoImg.parentNode);
            if (existing && existing.parentNode) existing.parentNode.removeChild(existing);
            if (!document.getElementById('siteLogoImg')){
              // restore original node if we have a reference
              if (inlined && inlined.originalImg && inlined.originalImg.parentNode == null){
                logoImg.parentNode.appendChild(inlined.originalImg);
              }
            }
          }
        }catch(e){ /* ignore */ }
        inlined = null;
      }

      var observer = new MutationObserver(function(){
        if (debounce) return; debounce = true;
        setTimeout(function(){ debounce = false; var isDark = document.documentElement.classList.contains('theme-dark'); if (isDark) inlineLogo(); else restoreImg(); }, 40);
      });
      observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

      // initial run
      if (document.documentElement.classList.contains('theme-dark')) inlineLogo();
    })();
  </script>
  <?php
    // Capture output, rewrite internal absolute attributes to include base when needed, then echo
  $html = ob_get_clean();
  if (!empty($detectedBase)) {
    // Only rewrite asset paths — do NOT rewrite navigation links (href on anchors) or form actions.
    $replacements = [
      '"/styles.css"' => '"' . $detectedBase . '/styles.css"',
      "'/styles.css'" => "'" . $detectedBase . "/styles.css'",
      'src="/app.js"' => 'src="' . $detectedBase . '/app.js"',
      "src='/app.js'" => "src='" . $detectedBase . "/app.js'",
      'href="/favicon.ico"' => 'href="' . $detectedBase . '/favicon.ico"',
      "href='/favicon.ico'" => "href='" . $detectedBase . "/favicon.ico'",
    ];
    $html = str_replace(array_keys($replacements), array_values($replacements), $html);

    // 2) any src/href that begins with /media/ -> prefix with detected base
    $html = preg_replace('#(src|href)=("|\')/media/#i', '$1=$2' . $detectedBase . '/media/', $html);
  }
  echo $html;
  ?>
</body>
<script src="/js/templates-integrator.js" defer></script>
<script src="/js/templates-editor.js" defer></script>
<script src="/nav-selection.js" defer></script>
</html>
