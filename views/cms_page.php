<?php
// CMS page renderer expects $page array with keys: title, content, excerpt, images (optional array)
 $title = $page['title'] ?? 'Página';

// determine page slug early so we can influence the global banner behavior in layout.php
$page_slug = $page['slug'] ?? null;
if (!$page_slug && !empty($page['title'])) {
  $page_slug = strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', trim($page['title'])));
  $page_slug = trim($page_slug, '-');
}

// For the 'soluciones' CMS page: make the banner title use the same classes as the home
// and hide the default subtitle text under the title.
if ($page_slug === 'soluciones' || (isset($page['title']) && strtolower(trim($page['title'])) === 'soluciones')) {
  $banner_title_class = 'hero-content text-center';
  $hide_banner_subtitle = true;
}


ob_start();

// If this is the 'soluciones' page, render a redesigned modern layout.
if ($page_slug === 'soluciones' || (isset($page['title']) && strtolower(trim($page['title'])) === 'soluciones')):
  // Extract a short intro from content (first paragraph) for hero subtitle
  $intro = '';
  if (!empty($page['content'])) {
    if (preg_match('/<p[^>]*>(.*?)<\/p>/is', $page['content'], $m)) { $intro = trim(strip_tags($m[1])); }
    else { $intro = trim(strip_tags($page['content'])); }
    $intro = preg_replace('/\s+/u',' ', $intro);
    if (strlen($intro) > 220) $intro = substr($intro,0,217) . '...';
  }
  ?>

  <section class="container my-5">
    <div class="soluciones-hero">
      <h1 class="hero-content text-center"><?= htmlspecialchars($page['title'] ?? 'Soluciones') ?></h1>
      <?php if ($intro): ?><p class="lead"><?= htmlspecialchars($intro) ?></p><?php endif; ?>
      <div class="hero-cta">
        <a href="/contacto" class="btn btn-primary">Solicitar diagnóstico</a>
        <a href="/proyectos" class="btn btn-outline-light ms-2">Ver proyectos</a>
      </div>
    </div>

    <div class="soluciones-container mt-4">
      <div>
        <div class="soluciones-features">
          <!-- Feature cards: try to extract headings from content or use fallbacks -->
          <?php
            // Heuristic: split content into blocks by double newlines or <h3> tags
            $features = [];
            $contentText = strip_tags($page['content'],'<h3><h4><p><ul><li>');
            // collect <h3> blocks
            if (preg_match_all('/<h3[^>]*>(.*?)<\/h3>/is', $page['content'], $h3s)){
              foreach ($h3s[1] as $h) $features[] = ['title'=>trim(strip_tags($h)), 'body'=>''];
            }
            // fallback: use first 4 paragraphs as features
            if (empty($features)){
              if (preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $page['content'], $ps)){
                $count = 0;
                foreach ($ps[1] as $p){ if (trim(strip_tags($p))==='' ) continue; $features[]=['title'=>trim(substr(strip_tags($p),0,60)).'','body'=>trim(strip_tags($p))]; $count++; if ($count>=4) break; }
              }
            }
            // ensure at least 4 placeholder features
            while(count($features)<4) $features[]=['title'=>'Servicio X','body'=>'Descripción breve del servicio, beneficios y aplicación.'];
            $i=0; foreach($features as $f): $i++;
          ?>
            <article class="feature-card" role="article" aria-label="feature-<?= $i ?>">
              <div class="feature-icon"><?= $i ?></div>
              <div>
                <h4><?= htmlspecialchars($f['title'] ?: "Servicio $i") ?></h4>
                <p><?= htmlspecialchars($f['body'] ?: 'Descripción breve del servicio.') ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>

      <aside>
        <div class="soluciones-cta">
          <h5>¿Listo para una evaluación técnica?</h5>
          <p class="small text-muted">Programamos una reunión inicial sin costo para entender tus necesidades.</p>
          <a class="btn btn-primary" href="/contacto">Programar llamada</a>
          <a class="btn btn-outline-light mt-2" href="/documentacion">Ver documentación técnica</a>
        </div>

        <div class="card card-preview mt-3">
          <div class="card-body">
            <h6 class="card-title">Recursos</h6>
            <p class="card-text">Guías, integraciones y casos de estudio disponibles para descarga.</p>
            <a href="/documentacion" class="btn btn-link">Ir a documentación</a>
          </div>
        </div>
      </aside>
    </div>
  </section>

<?php else: ?>
  <!-- Default CMS rendering for non-soluciones pages -->
  <?php if ($page_slug === 'documentacion' || (isset($page['title']) && strtolower(trim($page['title'])) === 'documentacion')): ?>
    <!-- Enhanced documentation layout: pick some provisional images and structured placeholders -->
    <section class="section-grid container my-5" aria-labelledby="section-title">
      <!-- force a stable header id for styling -->
      <header id="page-header-documentacion" class="mb-3 page-header-visual">
        <!-- Business phrase: replace verbose content with a concise sentence -->
        <p id="hero-sub" class="lead">Documentación técnica y operativa esencial para la adopción y el mantenimiento de soluciones Xlerion.</p>
      </header>

      <div class="row">
        <div class="col-12 col-lg-7 section-text">
          <div class="prose-left">
            <?php
              // Build structured documentation sections from incoming HTML content.
              // Strategy:
              // 1. Use a safe whitelist of tags and strip others.
              // 2. Split the sanitized HTML into sections using H2/H3 headings.
              // 3. Render each section with a title, body and a small 'Normas' block.
              $raw = $page['content'] ?? '';
              // If empty, populate with artificial demo content (same as before)
              if (trim(strip_tags($raw)) === '') {
                $raw = "<h2>Introducción</h2><p>Esta es una versión de prueba de la documentación. Aquí irá el contenido final.</p><h3>Requisitos</h3><p>Lista de requisitos, dependencias y notas importantes.</p><h3>Instalación</h3><p>Instrucciones de instalación y configuración inicial.</p>";
              }

              // Allow a narrow set of HTML useful for docs
              $allowed = '<h2><h3><p><ul><ol><li><pre><code><a><strong><em><blockquote>';
              $sanitized = strip_tags($raw, $allowed);

              // Split into chunks at each h2/h3 heading (keep the heading with the chunk)
              $parts = preg_split('/(?=<h[23][^>]*>)/i', $sanitized, -1, PREG_SPLIT_NO_EMPTY);
              $sections = [];
              if ($parts && count($parts) > 0) {
                foreach ($parts as $part) {
                  // Extract the first heading in the part
                  if (preg_match('/^<h([23])[^>]*>(.*?)<\/h\1>/is', trim($part), $m)) {
                    $title = trim(strip_tags($m[0]));
                    // body is the remainder after the heading
                    $body = preg_replace('/^<h[23][^>]*>.*?<\/h[23]>/is', '', $part, 1);
                    $sections[] = ['title' => $title ?: 'Sección', 'body' => trim($body)];
                  } else {
                    // no heading found, treat as general body
                    $sections[] = ['title' => 'General', 'body' => trim($part)];
                  }
                }
              } else {
                // if split failed, treat the whole sanitized content as one section
                $sections[] = ['title' => 'General', 'body' => $sanitized];
              }

              // Render sections with visual separation and a small 'Normas' list
              foreach ($sections as $sec) {
                echo '<section class="doc-section mb-4">';
                echo '<h3 class="doc-section-title">' . htmlspecialchars($sec['title']) . '</h3>';
                echo '<div class="doc-section-body">' . $sec['body'] . '</div>';
                // Simple documentation rules to guide contributors/readers
                echo '<div class="doc-section-normas small text-muted mt-2">';
                echo '<strong>Normas:</strong> <ul>';
                echo '<li>Usar ejemplos claros y concisos.</li>';
                echo '<li>Indicar versiones y requisitos (SO, dependencias).</li>';
                echo '<li>Proveer comandos y salidas de ejemplo cuando aplique.</li>';
                echo '</ul></div>';
                echo '</section>';
              }
            ?>

            <!-- Artificial review section to be filled later -->
            <section class="callout note mt-4">
              <h4>Sección de Revisión (Borrador)</h4>
              <p>Área reservada para revisión y anotaciones internas. Después se sustituirá por la documentación técnica oficial.</p>
              <ul>
                <li>Estado: Borrador</li>
                <li>Responsable: Equipo de documentación</li>
                <li>Notas: Añadir diagramas y ejemplos de API</li>
              </ul>
            </section>
          </div>
        </div>

        <aside class="col-12 col-lg-5 section-cards" aria-label="Imágenes y tarjetas relacionadas">
          <?php
            // Prefer explicit resource associations from page meta when available
            $associated = [];
            if (!empty($page['meta'])) { $tm = json_decode($page['meta'], true); if (is_array($tm) && !empty($tm['resources']) && is_array($tm['resources'])) $associated = $tm['resources']; }
            $resourceRecords = [];
            if (!empty($associated)) {
              try {
                require_once __DIR__ . '/../src/Model/Database.php';
                $pdb = Database::pdo();
                // prepare IN clause safely
                $placeholders = implode(',', array_fill(0, count($associated), '?'));
                $st = $pdb->prepare('SELECT id,slug,title,description,file_path,url FROM resources WHERE slug IN (' . $placeholders . ') ORDER BY title ASC');
                $st->execute($associated);
                $resourceRecords = $st->fetchAll(PDO::FETCH_ASSOC);
              } catch (Exception $e) { $resourceRecords = []; }
            }

            if (!empty($resourceRecords)):
              foreach ($resourceRecords as $rr):
                $rSlug = htmlspecialchars($rr['slug']);
                $rTitle = htmlspecialchars($rr['title'] ?: $rr['slug']);
                $rDesc = htmlspecialchars($rr['description'] ?? '');
                // choose thumbnail: prefer file_path then url
                $thumb = htmlspecialchars($rr['file_path'] ?? $rr['url'] ?? '/media/placeholder.png');
          ?>
            <div class="card card-preview mb-3">
              <img src="<?= $thumb ?>" alt="<?= $rTitle ?>" class="card-img-top" style="height:160px;object-fit:cover"/>
              <div class="card-body">
                <h6 class="card-title"><?= $rTitle ?></h6>
                <p class="card-text small text-muted"><?= $rDesc ?: 'Recurso relacionado' ?></p>
                <a class="btn btn-link doc-resource-btn" href="/recursos/<?= $rSlug ?>" aria-label="Ver recurso <?= $rTitle ?>">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-inline me-1" role="img">
                    <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </a>
              </div>
            </div>
          <?php endforeach; else:
            // fallback: prefer recent admin-managed resources (so links point to real /recursos/:slug pages)
            $recentResources = [];
            try {
              require_once __DIR__ . '/../src/Model/Database.php';
              $tmpPdo = Database::pdo();
              $stR = $tmpPdo->query('SELECT id,slug,title,description,file_path,url FROM resources ORDER BY id DESC LIMIT 4');
              $recentResources = $stR->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) { $recentResources = []; }
            if (!empty($recentResources)):
              foreach ($recentResources as $rr2):
                $rSlug = htmlspecialchars($rr2['slug']);
                $rTitle = htmlspecialchars($rr2['title'] ?: $rr2['slug']);
                $rDesc = htmlspecialchars($rr2['description'] ?? 'Recurso relacionado');
                $thumb = htmlspecialchars($rr2['file_path'] ?? $rr2['url'] ?? '/media/placeholder.png');
          ?>
            <div class="card card-preview mb-3">
              <img src="<?= $thumb ?>" alt="<?= $rTitle ?>" class="card-img-top" style="height:160px;object-fit:cover"/>
              <div class="card-body">
                <h6 class="card-title"><?= $rTitle ?></h6>
                <p class="card-text small text-muted"><?= $rDesc ?></p>
                <a class="btn btn-link doc-resource-btn" href="/recursos/<?= $rSlug ?>" aria-label="Ver recurso <?= $rTitle ?>">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-inline me-1" role="img">
                    <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </a>
              </div>
            </div>
          <?php endforeach; else:
            // fallback: Pick some provisional images from media/images/parallax if available
            $provisionals = [];
            $parallaxDir = __DIR__ . '/../media/images/parallax';
            $fallbacks = ['/media/images/parallax/documentacion-parallax.jpg','/media/images/parallax/blog-bitacora-parallax.jpg','/media/images/parallax/servicios-productos-parallax.jpg'];
            try { if (is_dir($parallaxDir)){
                $files = array_values(array_filter(scandir($parallaxDir), function($n){ return preg_match('/\.jpe?g$|\.png$|\.webp$/i', $n); }));
                foreach ($files as $f) $provisionals[] = '/media/images/parallax/' . $f;
              } } catch (Exception $e) { /* ignore */ }
            if (empty($provisionals)) $provisionals = $fallbacks;
            for ($i=0;$i<min(4,count($provisionals));$i++): $src = htmlspecialchars($provisionals[$i]);
          ?>
            <div class="card card-preview mb-3">
              <img src="<?= $src ?>" alt="Recurso <?= $i+1 ?>" class="card-img-top" style="height:160px;object-fit:cover"/>
              <div class="card-body">
                <?php
                  $fname = basename($provisionals[$i]);
                  $titleGuess = preg_replace('/[-_]+/',' ', pathinfo($fname, PATHINFO_FILENAME));
                  $titleGuess = ucwords(trim($titleGuess));
                  $resTitle = $titleGuess ?: ('Recurso ' . ($i+1));
                  $resSlug = strtolower(preg_replace('/[^a-z0-9]+/i','-', $resTitle)); $resSlug = trim($resSlug, '-');
                ?>
                <h6 class="card-title"><?= htmlspecialchars($resTitle) ?></h6>
                <p class="card-text small text-muted">Descripción provisional del recurso. Reemplazar con contenido real.</p>
                <a class="btn btn-link doc-resource-btn" href="<?= htmlspecialchars('/recursos/' . $resSlug) ?>" aria-label="Ver recurso <?= $i+1 ?>">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-inline me-1" role="img">
                    <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </a>
              </div>
            </div>
          <?php endfor; endif; endif; ?>
        </aside>
      </div>
    </section>
  <?php else: ?>
  <?php
    // determine if this page requests subtitles in uppercase
    $pageMeta = [];
    if (!empty($page['meta'])) { $tmpx = json_decode($page['meta'], true); if (is_array($tmpx)) $pageMeta = $tmpx; }
    $subtitleUpperClass = (!empty($pageMeta['subtitle_uppercase']) && $pageMeta['subtitle_uppercase']) ? ' subtitle-upper' : '';
  ?>
  <section class="section-grid container my-5<?= htmlspecialchars($subtitleUpperClass) ?>" aria-labelledby="section-title">
  <?php
    // provide a stable identifier for the header so it's easy to target from CSS/JS/tests
    $slug = $page['slug'] ?? null;
    if (!$slug && !empty($page['title'])) {
      // derive a simple slug from the title (lower, remove non-alnum, replace spaces with '-')
      $slug = strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', trim($page['title'])));
      $slug = trim($slug, '-');
    }
    $headerId = 'page-header' . ($slug ? '-' . $slug : '');
  ?>
  <header id="<?= htmlspecialchars($headerId) ?>" data-section="<?= htmlspecialchars($slug ?? 'page') ?>" class="mb-3 page-header-visual">
  <?php
      // Replace the visible title with a short phrase derived from the content.
      // Strategy: extract first <p> text from content, strip tags and truncate to ~120 chars.
      $phrase = '';
      if (!empty($page['content'])) {
        // try to extract first paragraph-like text
        if (preg_match('/<p[^>]*>(.*?)<\/p>/is', $page['content'], $pm)) {
          $text = trim(strip_tags($pm[1]));
        } else {
          // fallback: strip all tags and take first 200 chars
          $text = trim(strip_tags($page['content']));
        }
        // normalize whitespace
        $text = preg_replace('/\s+/u', ' ', $text);
        if (strlen($text) > 140) $phrase = substr($text,0,137) . '...'; else $phrase = $text;
      }
    ?>
    <?php if ($phrase): ?>
      <div class="visual-phrase"><?= htmlspecialchars($phrase) ?></div>
    <?php else: ?>
      <div class="visual-phrase"><?= htmlspecialchars($page['title'] ?? 'Sección') ?></div>
    <?php endif; ?>
    <?php
      // Render media in header if configured
      $meta = [];
      if (!empty($page['meta'])) { $tmp = json_decode($page['meta'], true); if (is_array($tmp)) $meta = $tmp; }
      $mediaPlacement = $meta['media_placement'] ?? null;
      $mediaUrlRaw = $meta['media_url'] ?? null;
      // helper to detect video by extension
      $isVideoUrl = function($u){ $ext = strtolower(pathinfo(parse_url($u, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)); return in_array($ext, ['mp4','webm','mov','m4v','ogv']); };
      if ($mediaPlacement === 'header' && !empty($mediaUrlRaw)) {
        $urls = array_map('trim', explode(',', $mediaUrlRaw));
        $u = $urls[0];
        if ($isVideoUrl($u)) {
          echo '<div class="header-media video-media mt-3"><video autoplay muted loop playsinline preload="auto" class="w-100" controls="" src="'.htmlspecialchars($u).'">Your browser does not support the video tag.</video></div>';
        } else {
          echo '<div class="header-media img-media mt-3"><img src="'.htmlspecialchars($u).'" alt="'.htmlspecialchars($page['title']??'').'" class="img-fluid w-100"/></div>';
        }
      }
    ?>
  </header>

  <div class="row">
    <div class="col-12 col-lg-7 section-text">
      <!-- Left-aligned main content: keep HTML from CMS but force left text alignment -->
      <div class="prose-left">
        <?php
          // If inline-top media, render before content
          if ($mediaPlacement === 'inline-top' && !empty($mediaUrlRaw)) {
            $urls = array_map('trim', explode(',', $mediaUrlRaw));
            $u = $urls[0];
            if ($isVideoUrl($u)) {
              echo '<div class="inline-media mb-3"><video controls class="w-100" src="'.htmlspecialchars($u).'">Video no soportado</video></div>';
            } else {
              echo '<div class="inline-media mb-3"><img src="'.htmlspecialchars($u).'" alt="" class="img-fluid"/></div>';
            }
          }
          echo $page['content'];
          // inline-bottom after content
          if ($mediaPlacement === 'inline-bottom' && !empty($mediaUrlRaw)) {
            $urls = array_map('trim', explode(',', $mediaUrlRaw));
            $u = $urls[0];
            if ($isVideoUrl($u)) {
              echo '<div class="inline-media mt-3"><video controls class="w-100" src="'.htmlspecialchars($u).'">Video no soportado</video></div>';
            } else {
              echo '<div class="inline-media mt-3"><img src="'.htmlspecialchars($u).'" alt="" class="img-fluid"/></div>';
            }
          }
        ?>
      </div>
    </div>

    <aside class="col-12 col-lg-5 section-cards" aria-label="Imágenes y tarjetas relacionadas">
      <?php
        // Build images list: prefer explicit gallery from meta when placement=gallery
        $images = $page['images'] ?? [];
        if ($mediaPlacement === 'gallery' && !empty($mediaUrlRaw)) {
          $parts = array_filter(array_map('trim', explode(',', $mediaUrlRaw)));
          foreach ($parts as $p) $images[] = ['src'=>$p,'alt'=>'','caption'=>''];
        }
        if (empty($images) && preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $page['content'] ?? '', $m)) {
          // fallback: collect images referenced inside content
          foreach ($m[1] as $s) $images[] = ['src'=>$s, 'alt'=>'', 'caption'=>''];
        }

        if (!empty($images)):
      ?>
        <div class="row g-3">
          <?php foreach ($images as $img):
            $srcRaw = $img['src'] ?? (is_string($img) ? $img : '');
            $src = htmlspecialchars($srcRaw);
            $alt = htmlspecialchars($img['alt'] ?? '');
            $caption = htmlspecialchars($img['caption'] ?? '');
            // Prefer explicit title/subtitle from image meta, otherwise fall back to page title/excerpt
            $idata_title = htmlspecialchars($img['title'] ?? $page['title'] ?? '');
            $idata_sub = htmlspecialchars($img['subtitle'] ?? ($page['excerpt'] ?? ''));
            $idata_desc = htmlspecialchars($img['desc'] ?? '');
            // If image is associated with a blog entry we may have a post slug
            $postHref = htmlspecialchars($img['post_href'] ?? '');
          ?>
            <div class="col-6 col-md-6">
              <figure class="card card-preview" role="button" tabindex="0" aria-label="Ver imagen: <?= $idata_title ?>" data-src="<?= $src ?>" data-title="<?= $idata_title ?>" data-subtitle="<?= $idata_sub ?>" data-desc="<?= $idata_desc ?>" <?= $postHref ? 'data-post-href="' . $postHref . '"' : '' ?> >
                <img src="<?= $src ?>" alt="<?= $alt ?>" class="card-img-top" data-title="<?= $idata_title ?>" data-subtitle="<?= $idata_sub ?>" data-desc="<?= $idata_desc ?>" />
                <?php if ($caption): ?><figcaption class="small text-muted p-2"><?= $caption ?></figcaption><?php endif; ?>
              </figure>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <!-- Placeholder: if no images available, show an invitation card -->
        <div class="card card-preview p-3 text-center">
          <div class="card-body">
            <h5 class="card-title">Imágenes y recursos</h5>
            <p class="card-text text-muted">No hay imágenes asociadas a esta sección. Puedes añadir una galería o tarjetas relacionadas en el CMS.</p>
          </div>
        </div>
      <?php endif; ?>
    </aside>
  </div>
</section>
 
<?php endif; ?>

<?php endif; ?>

<?php
// Record a page view (best-effort). We store page_id and slug and some request metadata.
try {
  require_once __DIR__ . '/../src/Model/Database.php';
  $pdo = Database::pdo();
  $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
  $pageId = isset($page['id']) ? intval($page['id']) : null;
  $slug = $page['slug'] ?? null;
  $ip = $_SERVER['REMOTE_ADDR'] ?? null;
  $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
  if ($driver === 'sqlite') {
    $ins = $pdo->prepare("INSERT INTO page_views (page_id, slug, ip, user_agent, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
    $ins->execute([$pageId, $slug, $ip, $ua]);
  } else {
    $ins = $pdo->prepare("INSERT INTO page_views (page_id, slug, ip, user_agent, created_at) VALUES (?, ?, ?, ?, NOW())");
    $ins->execute([$pageId, $slug, $ip, $ua]);
  }
} catch (Exception $e) {
  // ignore analytics failures
}
?>

<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
