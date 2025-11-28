<?php
$title = 'Blog - Xlerion';
$pdo = Database::pdo();
$perPage = 12;
$page = max(1,(int)($_GET['page']??1));
$offset = ($page-1)*$perPage;

// Try to load from blog_posts table; if table is missing, fallback to cms_pages entry with slug 'blog'
try {
  $st = $pdo->prepare('SELECT id,title,slug,excerpt,published_at,meta FROM blog_posts WHERE status = ? ORDER BY published_at DESC LIMIT ? OFFSET ?');
  $st->execute(['published',$perPage,$offset]);
  $posts = $st->fetchAll();
  // decode meta JSON for each post if present (image, author)
  foreach ($posts as &$pp) {
    if (!empty($pp['meta'])){
      $m = json_decode($pp['meta'], true);
      if (is_array($m)){
        if (!empty($m['image'])) $pp['image'] = $m['image'];
        if (!empty($m['author'])) $pp['author'] = $m['author'];
      }
    }
  }
  unset($pp);
} catch (PDOException $e) {
  // fallback: read pre-rendered blog index from cms_pages
  try {
    $st2 = $pdo->prepare('SELECT content,title FROM cms_pages WHERE slug = ? LIMIT 1');
    $st2->execute(['blog']);
    $pageRow = $st2->fetch();
    if ($pageRow && !empty($pageRow['content'])) {
      $slot = $pageRow['content'];
      // Use page title if available
      $title = $pageRow['title'] ?? $title;
      include __DIR__ . '/layout.php';
      return;
    }
  } catch (Exception $e2) {
    // continue to render an empty list below
  }
  // If fallback also failed, continue with empty posts array to avoid fatal error
  $posts = [];
}

ob_start(); ?>
<div class="section-header d-flex align-items-center justify-content-between">
  <h2>Blog</h2>
  <a class="btn btn-sm btn-outline-light" href="/blog">Ver todo</a>
</div>
<div class="row g-3 mt-3">
  <?php foreach ($posts as $p): ?>
    <div class="col-12 col-md-6 col-lg-4">
      <article class="card card-preview h-100">
        <?php if (!empty($p['image'])): ?>
          <img src="<?= htmlspecialchars($p['image']) ?>" class="card-img-top project-card-img" alt="<?= htmlspecialchars($p['title']) ?>">
        <?php else: ?>
          <div class="card-img-top placeholder-thumb" style="background:#f3f4f6;height:160px;display:flex;align-items:center;justify-content:center;color:#888">Sin imagen</div>
        <?php endif; ?>
        <div class="card-body">
          <h3 class="h5 card-title"><a href="/blog/<?= htmlspecialchars($p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a></h3>
          <p class="card-text small text-muted"><?= htmlspecialchars($p['excerpt'] ?? '') ?></p>
        </div>
        <div class="card-footer bg-transparent border-0">
          <a class="btn btn-sm btn-primary" href="/blog/<?= htmlspecialchars($p['slug']) ?>">Leer</a>
        </div>
      </article>
    </div>
  <?php endforeach; ?>
  </div>

  <nav class="mt-4" aria-label="Paginación del blog">
    <?php
      // simple pager
      try {
        $cntSt = $pdo->prepare('SELECT COUNT(*) FROM blog_posts WHERE status = ?'); $cntSt->execute(['published']); $total = (int)$cntSt->fetchColumn();
      } catch(Exception $e){ $total = count($posts); }
      $pages = max(1, ceil($total / $perPage));
      for ($pi=1;$pi<=$pages;$pi++){
        $href = '/blog' . ($pi>1 ? ('?page=' . $pi) : '');
        echo '<a class="btn btn-sm btn-outline-light me-1" href="'.htmlspecialchars($href).'"'.($pi==$page? ' aria-current="page" style="font-weight:700"' : '').'>'.$pi.'</a>';
      }
    ?>
  </nav>

<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
