<?php
$slug = $_GET['slug'] ?? null;
$pdo = Database::pdo();

// Try to fetch from blog_posts; if table doesn't exist, fallback to cms_pages with matching slug
try {
  $st = $pdo->prepare('SELECT * FROM blog_posts WHERE slug = ? AND status = ? LIMIT 1');
  $st->execute([$slug,'published']);
  $post = $st->fetch();
  if ($post && !empty($post['meta'])){
    $m = json_decode($post['meta'], true);
    if (is_array($m)){
      if (!empty($m['image'])) $post['image'] = $m['image'];
      if (!empty($m['author'])) $post['author'] = $m['author'];
    }
  }
  if (!$post) {
    http_response_code(404); echo 'Post no encontrado'; exit;
  }
  // increment views (best-effort)
  $pdo->prepare('UPDATE blog_posts SET views = views + 1 WHERE id = ?')->execute([$post['id']]);
  $title = $post['title'];
} catch (PDOException $e) {
  // fallback: read cms_pages where slug matches
  try {
    $st2 = $pdo->prepare('SELECT title,content,meta FROM cms_pages WHERE slug = ? LIMIT 1');
    $st2->execute([$slug]);
    $pageRow = $st2->fetch();
    if (!$pageRow) { http_response_code(404); echo 'Post no encontrado'; exit; }
    $post = [
      'title' => $pageRow['title'] ?? 'Entrada',
      'published_at' => null,
      'author' => 'Equipo Xlerion',
      'image' => null,
      'content' => $pageRow['content'] ?? ''
    ];
    $title = $post['title'];
  } catch (Exception $e2) {
    http_response_code(500); echo 'Error interno'; exit;
  }
}
ob_start(); ?>
<div class="container">
  <div class="row">
    <main class="col-12 col-lg-8">
      <article class="blog-post">
        <header class="mb-3">
          <h1 class="section-title-lg"><?= htmlspecialchars($post['title']) ?></h1>
          <div class="text-muted small">Publicado: <?= htmlspecialchars($post['published_at']) ?> · por <?= htmlspecialchars($post['author'] ?? 'Equipo') ?></div>
        </header>
        <?php if(!empty($post['image'])): ?>
          <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="project-thumb mb-3" />
        <?php endif; ?>
        <div class="prose-left">
          <?= $post['content'] ?>
        </div>
      </article>
    </main>
    <aside class="col-12 col-lg-4">
      <div class="card card-preview p-3 mb-3">
        <h5>Sobre el autor</h5>
        <p class="small text-muted"><?= htmlspecialchars($post['author'] ?? 'Equipo Xlerion') ?></p>
      </div>
      <div class="card card-preview p-3 mb-3">
        <h5>Artículos recientes</h5>
        <ul class="list-unstyled">
          <?php
            $recent = $pdo->query('SELECT title, slug FROM blog_posts WHERE status = "published" ORDER BY published_at DESC LIMIT 5')->fetchAll();
            foreach($recent as $r){
              echo '<li><a href="/blog/' . htmlspecialchars($r['slug']) . '">' . htmlspecialchars($r['title']) . '</a></li>';
            }
          ?>
        </ul>
      </div>
      <div class="card card-preview p-3">
        <h5>Leer más</h5>
        <p class="small text-muted">Explora otras entradas y recursos relacionados con Xlerion.</p>
      </div>
    </aside>
  </div>
</div>
<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
