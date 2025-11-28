<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
$pdo = Database::pdo();

$perPage = 15; $page = max(1,intval($_GET['page']??1)); $offset = ($page-1)*$perPage; $q = trim($_GET['q']??'');
$params = [];
$sqlBase = 'WHERE 1=1';
if ($q!==''){
  $sqlBase .= ' AND (title LIKE ? OR slug LIKE ? OR excerpt LIKE ?)';
  $like = '%'.$q.'%'; $params[]=$like; $params[]=$like; $params[]=$like;
}

try{
  $count = $pdo->prepare('SELECT COUNT(*) FROM blog_posts '.$sqlBase);
  $i=1; foreach($params as $p){ $count->bindValue($i,$p); $i++; }
  $count->execute(); $total = (int)$count->fetchColumn();
}catch(Exception $e){ $total = 0; }

try{
  $sql = 'SELECT id,title,slug,status,published_at,views FROM blog_posts '.$sqlBase.' ORDER BY published_at DESC LIMIT :lim OFFSET :off';
  $stmt = $pdo->prepare($sql);
  $i=1; foreach($params as $p){ $stmt->bindValue($i,$p); $i++; }
  $stmt->bindValue(':lim',(int)$perPage,PDO::PARAM_INT); $stmt->bindValue(':off',(int)$offset,PDO::PARAM_INT);
  $stmt->execute(); $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){ $posts = []; }

$title = 'Admin - Entradas de blog'; $banner_title_class='section-title-lg'; ob_start();
?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview"><?php include __DIR__ . '/_nav.php'; ?></aside>
  <main class="admin-main">
    <header class="admin-header">
      <h1 class="section-title-lg"><?= htmlspecialchars($title) ?></h1>
      <div><a class="btn btn-primary" href="/admin/edit_post.php">Crear entrada</a></div>
    </header>

    <section class="mt-3">
      <form method="get" class="d-flex gap-2" role="search">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por título o slug" class="form-control">
        <button class="btn btn-outline-light">Buscar</button>
      </form>

      <div class="mt-3 list-group">
        <?php if (empty($posts)): ?>
          <div class="card-preview">No se encontraron entradas.</div>
        <?php else: foreach($posts as $p): ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong><?= htmlspecialchars($p['title']) ?></strong>
              <div class="small text-muted"><?= htmlspecialchars($p['slug']) ?> — <?= htmlspecialchars($p['published_at']??'') ?></div>
            </div>
            <div style="display:flex;gap:.5rem">
              <a class="btn btn-outline-light btn-sm" href="/blog/<?= rawurlencode($p['slug']) ?>" target="_blank">Ver</a>
              <a class="btn btn-outline-light btn-sm" href="/admin/edit_post.php?id=<?= intval($p['id']) ?>">Editar</a>
              <form method="post" action="/admin/delete_post.php" style="display:inline" onsubmit="return confirm('Eliminar entrada?');">
                <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf']??'')?>">
                <input type="hidden" name="id" value="<?= intval($p['id']) ?>">
                <input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
              </form>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>

      <nav class="mt-3" aria-label="Paginación posts">
        <?php $totalPages = max(1, ceil(($total?:0)/$perPage)); for($i=1;$i<=$totalPages;$i++): ?>
          <?php $href = '/admin/posts.php?page=' . $i . '&q=' . urlencode($q); ?>
          <a class="btn btn-outline-light btn-sm" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"<?= $i===$page? ' aria-current="page" style="font-weight:700"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
      </nav>
    </section>
  </main>
</div>
<?php $slot = ob_get_clean(); include __DIR__ . '/../../views/layout.php';
