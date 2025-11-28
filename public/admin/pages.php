<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$pdo = Database::pdo();

$perPage = 12;
$page = max(1, intval($_GET['page'] ?? 1));
$q = trim($_GET['q'] ?? '');
$offset = ($page - 1) * $perPage;

$sqlBase = 'WHERE 1=1';
$params = [];
if ($q !== ''){
  $sqlBase .= ' AND (title LIKE ? OR slug LIKE ? OR content LIKE ?)';
  $like = '%' . $q . '%';
  $params[] = $like; $params[] = $like; $params[] = $like;
}

$total = 0;
try{
  $countStmt = $pdo->prepare('SELECT COUNT(*) FROM cms_pages ' . $sqlBase);
  $countStmt->execute($params);
  $total = (int) $countStmt->fetchColumn();
} catch(Exception $e){
  $total = 0;
}

// Build SELECT with proper LIMIT/OFFSET
try{
  $selectSql = 'SELECT id, slug, title, is_published, created_at FROM cms_pages ' . $sqlBase . ' ORDER BY id DESC LIMIT :lim OFFSET :off';
  $stmt = $pdo->prepare($selectSql);
  // bind search params (1..n)
  $i = 1;
  foreach ($params as $pval) { $stmt->bindValue($i, $pval); $i++; }
  // bind limit/offset as integers
  $stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
  $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
  $stmt->execute();
  $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e){
  $pages = [];
}

$title = 'Admin - Páginas';
$banner_title_class = 'section-title-lg';
ob_start();
?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview">
    <?php include __DIR__ . '/_nav.php'; ?>
  </aside>

  <main class="admin-main">
    <header class="admin-header">
      <h1 class="section-title-lg"><?= htmlspecialchars($title) ?></h1>
      <div>
        <a class="btn btn-primary" href="/admin/edit_page.php">Crear página</a>
      </div>
    </header>

    <section class="mt-3">
      <form method="get" class="d-flex gap-2" role="search" aria-label="Buscar páginas">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por título, slug o contenido" class="form-control">
        <button class="btn btn-outline-light">Buscar</button>
      </form>

      <div class="mt-3 list-group">
        <?php if (empty($pages)): ?>
          <div class="card-preview">No se encontraron páginas.</div>
        <?php else: foreach ($pages as $p): ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong><?= htmlspecialchars($p['title']) ?></strong>
              <div class="small text-muted"><?= htmlspecialchars($p['slug']) ?> — <?= htmlspecialchars($p['created_at'] ?? '') ?></div>
            </div>
            <div style="display:flex;gap:0.5rem">
              <a class="btn btn-outline-light btn-sm" href="/<?= rawurlencode($p['slug']) ?>" target="_blank">Ver</a>
              <a class="btn btn-outline-light btn-sm" href="/admin/edit_page.php?id=<?= intval($p['id']) ?>">Editar</a>
              <form method="post" action="/admin/delete_page.php" style="display:inline" onsubmit="return confirm('Eliminar página?');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
                <input type="hidden" name="id" value="<?= intval($p['id']) ?>">
                <input type="hidden" name="return_to" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
              </form>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>

      <nav class="mt-3" aria-label="Paginación de páginas">
        <?php $totalPages = max(1, ceil(($total ?: 0)/$perPage)); for ($i=1;$i<=$totalPages;$i++): ?>
          <?php $href = '/admin/pages.php?page=' . $i . '&q=' . urlencode($q); ?>
          <a class="btn btn-outline-light btn-sm" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"<?= $i===$page? ' aria-current="page" style="font-weight:700"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
      </nav>

    </section>
  </main>
</div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';
