<?php
require_once __DIR__ . '/../../src/Auth.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();

require_once __DIR__ . '/../../src/Model/Database.php';
$pdo = Database::pdo();

$q = trim($_GET['q'] ?? '');
$userFilter = intval($_GET['user'] ?? 0);
$page = max(1,intval($_GET['page'] ?? 1)); $per = 24; $offset = ($page-1)*$per;

$where = [];
$params = [];
if ($q !== '') { $where[] = '(filename LIKE ? OR url LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($userFilter > 0) { $where[] = 'uploaded_by = ?'; $params[] = $userFilter; }
// date filters
if (!empty($_GET['from'])) { $where[] = 'date(created_at) >= ?'; $params[] = $_GET['from']; }
if (!empty($_GET['to'])) { $where[] = 'date(created_at) <= ?'; $params[] = $_GET['to']; }

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$total = $pdo->prepare("SELECT COUNT(*) FROM media_files $whereSql"); $total->execute($params); $totalCount = $total->fetchColumn();
$files = $pdo->prepare("SELECT * FROM media_files $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?");
array_push($params, $per, $offset);
$files->execute($params);
$files = $files->fetchAll(PDO::FETCH_ASSOC);

$title = 'Media Manager'; ob_start();
?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview"><?php include __DIR__ . '/_nav.php'; ?></aside>
  <main class="admin-main">
    <header class="admin-header">
      <h1 class="section-title-lg"><?php echo htmlspecialchars($title) ?></h1>
      <form class="d-flex gap-2 mt-2" method="get">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="buscar filename o url" class="form-control form-control-sm" />
        <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>" />
        <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>" />
        <button class="btn btn-sm btn-primary" type="submit">Filtrar</button>
      </form>
    </header>
    <section class="card-preview">
      <form id="bulkForm" method="post" action="/admin/bulk_trash_media.php" onsubmit="return confirm('Confirmar acción en archivos seleccionados?');">
        <div class="d-flex justify-content-between mb-2 align-items-center">
          <div class="d-flex gap-2">
            <select id="bulkAction" name="action" class="form-select form-select-sm">
              <option value="trash">Mover a papelera</option>
              <option value="restore">Restaurar</option>
              <option value="permadelete">Eliminar permanentemente</option>
              <option value="export">Exportar CSV</option>
              <option value="editmeta">Editar metadatos</option>
            </select>
            <button id="bulkApply" class="btn btn-sm btn-secondary" type="submit">Aplicar</button>
          </div>
          <div class="d-flex gap-2 align-items-center">
            <label class="form-check-label small"><input type="checkbox" id="showTrashed" <?= isset($_GET['show_trashed']) && $_GET['show_trashed']=='1' ? 'checked' : '' ?> /> Mostrar papelera</label>
            <div class="small text-muted">Mostrando <?= count($files) ?> de <?= intval($totalCount) ?> resultados</div>
          </div>
        </div>
        <div class="row g-3">
          <?php if (empty($files)): ?>
            <div class="col-12">No hay archivos que mostrar.</div>
          <?php else: foreach ($files as $f): ?>
            <div class="col-6 col-md-3">
              <div class="card card-preview p-2">
                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="files[]" value="<?= intval($f['id']) ?>"></div>
                <?php $ext = strtolower(pathinfo($f['filename'], PATHINFO_EXTENSION)); ?>
                <?php if (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                  <img src="<?= htmlspecialchars($f['url']) ?>" alt="" class="img-fluid mb-2" style="max-height:140px;object-fit:cover;width:100%"/>
                <?php else: ?>
                  <div class="small text-muted mb-2" style="height:140px;display:flex;align-items:center;justify-content:center;background:#f3f3f3"><?= htmlspecialchars(strtoupper($ext)) ?></div>
                <?php endif; ?>
                <div class="small text-muted"><?= htmlspecialchars($f['filename']) ?> &middot; <?= round(($f['size']?:0)/1024,1) ?> KB</div>
                <div class="mt-2 d-flex gap-2">
                  <a class="btn btn-sm btn-outline-light" href="<?= htmlspecialchars($f['url']) ?>" target="_blank">Abrir</a>
                  <a class="btn btn-sm btn-outline-light" href="/admin/edit_page.php?insert_media=<?= intval($f['id']) ?>">Usar</a>
                  <form method="post" action="/admin/delete_media.php" onsubmit="return confirm('Eliminar archivo?');">
                    <input type="hidden" name="file" value="<?= htmlspecialchars($f['filename']) ?>">
                    <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
        </form>
        <script>
        (function(){
          const form = document.getElementById('bulkForm');
          const actionSel = document.getElementById('bulkAction');
          const showTrash = document.getElementById('showTrashed');
          showTrash.addEventListener('change', ()=>{
            const u = new URL(window.location.href);
            if (showTrash.checked) u.searchParams.set('show_trashed','1'); else u.searchParams.delete('show_trashed');
            window.location.href = u.toString();
          });
          form.addEventListener('submit', (e)=>{
            const act = actionSel.value;
            if (act === 'trash') form.action = '/admin/bulk_trash_media.php';
            else if (act === 'restore') form.action = '/admin/bulk_restore_media.php';
            else if (act === 'permadelete') form.action = '/admin/bulk_permadelete_media.php';
            else if (act === 'export') form.action = '/admin/bulk_export_media.php';
            else if (act === 'editmeta') form.action = '/admin/bulk_editmeta_media.php';
            // for export we allow default submit
          });
        })();
        </script>
      <nav class="mt-3" aria-label="Paginación">
        <?php $pages = ceil($totalCount / $per); for ($i=1;$i<=$pages;$i++): ?>
          <a class="btn btn-outline-light btn-sm" href="?page=<?= $i ?>&q=<?= urlencode($q) ?>"<?= $i===$page? ' aria-current="page" style="font-weight:700"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
      </nav>
    </section>
  </main>
</div>
<?php $slot = ob_get_clean(); include __DIR__ . '/../../views/layout.php'; ?>
