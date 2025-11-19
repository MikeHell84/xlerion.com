<?php
$title = 'Blog - Xlerion';
$pdo = Database::pdo();
$perPage = 5;
$page = max(1,(int)($_GET['page']??1));
$offset = ($page-1)*$perPage;
$st = $pdo->prepare('SELECT id,title,slug,excerpt,published_at FROM blog_posts WHERE status = ? ORDER BY published_at DESC LIMIT ? OFFSET ?');
$st->execute(['published',$perPage,$offset]);
$posts = $st->fetchAll();
ob_start(); ?>
<h2>Blog</h2>
<?php foreach ($posts as $p): ?>
  <article>
    <h3><a href="/blog/<?= htmlspecialchars($p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a></h3>
    <p><?= htmlspecialchars($p['excerpt']) ?></p>
  </article>
<?php endforeach; ?>
<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
