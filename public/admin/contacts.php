<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$pdo = Database::pdo();

$perPage = 15;
$page = max(1, intval($_GET['page'] ?? 1));
$q = trim($_GET['q'] ?? '');
$offset = ($page - 1) * $perPage;

$where = 'WHERE 1=1';
$params = [];
if ($q !== ''){
  $where .= ' AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
  $like = '%' . $q . '%';
  $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
}

$total = 0;
try{
  $c = $pdo->prepare('SELECT COUNT(*) FROM contacts ' . $where);
  $c->execute($params);
  $total = (int) $c->fetchColumn();
} catch (Exception $e){ $total = 0; }

$contacts = [];
try{
  $sql = 'SELECT id, first_name, last_name, email, phone, created_at FROM contacts ' . $where . ' ORDER BY created_at DESC LIMIT :lim OFFSET :off';
  $stmt = $pdo->prepare($sql);
  $i = 1;
  foreach ($params as $pv){ $stmt->bindValue($i, $pv); $i++; }
  $stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
  $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
  $stmt->execute();
  $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e){ $contacts = []; }

$title = 'Admin - Contactos';
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
      <div style="display:flex;gap:.5rem;align-items:center">
        <a class="btn btn-primary" href="/admin/import.php">Importar CSV</a>
        <a class="btn btn-outline-secondary" href="/admin/export.php?table=contacts">Exportar CSV</a>
      </div>
    </header>

    <section class="mt-3">
      <form method="get" class="d-flex gap-2" role="search" aria-label="Buscar contactos">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar nombre, email o teléfono" class="form-control">
        <button class="btn btn-outline-light">Buscar</button>
      </form>

      <div class="mt-3 list-group">
        <?php if (empty($contacts)): ?>
          <div class="card-preview">No hay contactos.</div>
        <?php else: foreach ($contacts as $c): ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></strong>
              <div class="small text-muted"><?= htmlspecialchars($c['email']) ?> — <?= htmlspecialchars($c['phone']) ?></div>
            </div>
            <div style="display:flex;gap:0.5rem">
              <a class="btn btn-outline-light btn-sm" href="/admin/view_contact.php?id=<?= intval($c['id']) ?>">Ver</a>
              <a class="btn btn-outline-light btn-sm" href="/admin/edit_contact.php?id=<?= intval($c['id']) ?>">Editar</a>
              <form method="post" action="/admin/delete_contact.php" style="display:inline" onsubmit="return confirm('Eliminar contacto?');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
                <input type="hidden" name="id" value="<?= intval($c['id']) ?>">
                <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
              </form>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>

      <nav class="mt-3" aria-label="Paginación de contactos">
        <?php $totalPages = max(1, ceil(($total ?: 0)/$perPage)); for ($i=1;$i<=$totalPages;$i++): ?>
          <a class="btn btn-outline-light btn-sm" href="?page=<?= $i ?>&q=<?= urlencode($q) ?>"<?= $i===$page? ' aria-current="page" style="font-weight:700"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
      </nav>

    </section>
  </main>
</div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';
