<?php
// CMS page renderer expects $page array with keys: title, content, excerpt, images (optional array)
$title = $page['title'] ?? 'Página';
ob_start(); ?>

<section class="section-grid container my-5" aria-labelledby="section-title">
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
            $src = htmlspecialchars($img['src'] ?? $img[0] ?? '');
            $alt = htmlspecialchars($img['alt'] ?? '');
            $caption = htmlspecialchars($img['caption'] ?? '');
          ?>
            <div class="col-6 col-md-6">
              <figure class="card card-preview">
                <img src="<?= $src ?>" alt="<?= $alt ?>" class="card-img-top"/>
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
