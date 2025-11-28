<?php
// Basic site search over published cms_pages
// Input: $_GET['q']
// Renders using the site layout

$q = trim($_GET['q'] ?? '');
$title = 'Buscar: ' . ($q ?: '');
ob_start();
?>
<div class="py-3">
  <h2 class="mb-3">Resultados de búsqueda</h2>
  <form method="get" action="/search" class="mb-3">
    <div class="input-group">
      <input name="q" id="q" type="search" class="form-control" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar...">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </div>
  </form>

  <?php if ($q === ''): ?>
    <p class="text-muted">Introduce términos para buscar en títulos y extractos.</p>
  <?php else: ?>
    <?php
      try {
        require_once __DIR__ . '/../src/Model/Database.php';
        $pdo = Database::pdo();

        // Pagination
        $perPage = 10;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        // Detect driver: sqlite uses FTS5 if available; otherwise fallback to LIKE
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $results = [];
        $total = 0;

        if ($driver === 'sqlite') {
          // try FTS5 match against cms_pages_fts (virtual table). If it doesn't exist, fallback to LIKE.
          try {
            // ensure we have a cms_pages_fts table
            $check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='cms_pages_fts'")->fetchColumn();
            if ($check) {
              // use FTS5 snippet to extract short highlighted context
              $countSt = $pdo->prepare("SELECT count(*) FROM cms_pages_fts WHERE cms_pages_fts MATCH :q");
              $countSt->execute([':q' => $q]);
              $total = (int)$countSt->fetchColumn();

              $st = $pdo->prepare("SELECT page_id as id, title, excerpt, snippet(cms_pages_fts, 2, '<em>', '</em>', '...', 64) as snippet_html FROM cms_pages_fts WHERE cms_pages_fts MATCH :q ORDER BY rank LIMIT :lim OFFSET :off");
              // Note: FTS5 'rank' requires an auxiliary rank function; if not present we'll omit ORDER BY rank
              // bind params
              $st->bindValue(':q', $q, PDO::PARAM_STR);
              $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
              $st->bindValue(':off', $offset, PDO::PARAM_INT);
              $st->execute();
              $results = $st->fetchAll(PDO::FETCH_ASSOC);
            } else {
              // fallback to LIKE on sqlite
              $like = '%' . str_replace('%','\\%',$q) . '%';
              $countSt = $pdo->prepare("SELECT count(*) FROM cms_pages WHERE is_published = 1 AND (title LIKE :q OR excerpt LIKE :q OR content LIKE :q)");
              $countSt->execute([':q' => $like]);
              $total = (int)$countSt->fetchColumn();

              $sql = "SELECT id, slug, title, excerpt, substr(content, instr(lower(content), lower(:rawq)) - 60, 160) as snippet_html FROM cms_pages WHERE is_published = 1 AND (title LIKE :q OR excerpt LIKE :q OR content LIKE :q) ORDER BY id DESC LIMIT :lim OFFSET :off";
              $st = $pdo->prepare($sql);
              $st->bindValue(':q', $like, PDO::PARAM_STR);
              $st->bindValue(':rawq', $q, PDO::PARAM_STR);
              $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
              $st->bindValue(':off', $offset, PDO::PARAM_INT);
              $st->execute();
              $results = $st->fetchAll(PDO::FETCH_ASSOC);
            }
          } catch (Exception $e) {
            $results = [];
            $total = 0;
          }
        } else {
          // MySQL / other: try FULLTEXT if available (not implemented here), fallback to LIKE
          try {
            $like = '%' . str_replace('%','\\%',$q) . '%';
            $countSt = $pdo->prepare("SELECT count(*) FROM cms_pages WHERE is_published = 1 AND (title LIKE :q OR excerpt LIKE :q OR content LIKE :q)");
            $countSt->execute([':q' => $like]);
            $total = (int)$countSt->fetchColumn();

            $sql = "SELECT id, slug, title, excerpt, '' as snippet_html FROM cms_pages WHERE is_published = 1 AND (title LIKE :q OR excerpt LIKE :q OR content LIKE :q) ORDER BY id DESC LIMIT :lim OFFSET :off";
            $st = $pdo->prepare($sql);
            $st->bindValue(':q', $like, PDO::PARAM_STR);
            $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
            $st->bindValue(':off', $offset, PDO::PARAM_INT);
            $st->execute();
            $results = $st->fetchAll(PDO::FETCH_ASSOC);
          } catch (Exception $e) {
            $results = [];
            $total = 0;
          }
        }

        // Record analytics (best-effort, ignore errors)
        try {
          $ip = $_SERVER['REMOTE_ADDR'] ?? null;
          $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
          if ($driver === 'sqlite') {
            $aq = $pdo->prepare("INSERT INTO search_queries (q, ip, user_agent, result_count, created_at) VALUES (?, ?, ?, ?, datetime('now'))");
            $aq->execute([$q, $ip, $ua, $total]);
          } else {
            // MySQL and others: use NOW()
            $aq = $pdo->prepare("INSERT INTO search_queries (q, ip, user_agent, result_count, created_at) VALUES (?, ?, ?, ?, NOW())");
            $aq->execute([$q, $ip, $ua, $total]);
          }
        } catch (Exception $e) { /* ignore analytics failures */ }

      } catch (Exception $e) {
        $results = [];
        $total = 0;
      }
    ?>

    <div class="list-group">
      <?php if (empty($results)): ?>
        <div class="alert alert-info">No se encontraron resultados para: <strong><?= htmlspecialchars($q) ?></strong></div>
      <?php else: ?>
        <?php foreach ($results as $r): ?>
          <?php
            $slug = htmlspecialchars($r['slug'] ?? ($r['id'] ?? '#'));
            $titleText = $r['title'] ?? '';
            // if snippet_html exists, trust it partially (it may contain <em> from FTS snippet);
            // to be safe, escape then replace escaped <em> markers produced by our own snippet generator.
            $snippet = '';
            if (!empty($r['snippet_html'])) {
              // FTS snippet may already include <em> tags; sanitize by escaping then allowing <em>
              $escaped = htmlspecialchars($r['snippet_html'], ENT_NOQUOTES, 'UTF-8');
              // allow <em> and </em> by unescaping their entities
              $escaped = str_replace(['&lt;em&gt;', '&lt;/em&gt;'], ['<em>', '</em>'], $escaped);
              $snippet = $escaped;
            } elseif (!empty($r['excerpt'])) {
              $snippet = htmlspecialchars($r['excerpt']);
            }
          ?>
          <a class="list-group-item list-group-item-action" href="/<?= $slug ?>">
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1"><?= htmlspecialchars($titleText) ?></h5>
            </div>
            <?php if ($snippet): ?>
              <p class="mb-1 text-muted"><?= $snippet ?></p>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?php
      // Pagination UI
      $totalPages = max(1, (int)ceil($total / $perPage));
      if ($totalPages > 1):
        $baseUrl = '/search?q=' . urlencode($q);
    ?>
      <nav aria-label="Paginación de búsqueda" class="mt-3">
        <ul class="pagination">
          <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>"><a class="page-link" href="<?= ($page <= 1) ? '#' : $baseUrl . '&page=' . ($page - 1) ?>">Anterior</a></li>
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p > 1 && $p < $page - 2) { if ($p !== 2) { continue; } } ?>
            <?php if ($p < $totalPages && $p > $page + 2) { if ($p !== $totalPages - 1) { continue; } } ?>
            <li class="page-item <?= ($p === $page) ? 'active' : '' ?>"><a class="page-link" href="<?= $baseUrl . '&page=' . $p ?>"><?= $p ?></a></li>
          <?php endfor; ?>
          <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>"><a class="page-link" href="<?= ($page >= $totalPages) ? '#' : $baseUrl . '&page=' . ($page + 1) ?>">Siguiente</a></li>
        </ul>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php';
