<?php
$slug = $_GET['slug'] ?? null;
$pdo = Database::pdo();
$st = $pdo->prepare('SELECT * FROM blog_posts WHERE slug = ? AND status = ? LIMIT 1');
$st->execute([$slug,'published']);
$post = $st->fetch();
if (!$post) { http_response_code(404); echo 'Post no encontrado'; exit; }
// increment views (best-effort)
$pdo->prepare('UPDATE blog_posts SET views = views + 1 WHERE id = ?')->execute([$post['id']]);
$title = $post['title'];
ob_start(); ?>
<article>
  <h2><?= htmlspecialchars($post['title']) ?></h2>
  <p><em>Publicado: <?= htmlspecialchars($post['published_at']) ?></em></p>
  <div><?= $post['content'] ?></div>
</article>
<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
