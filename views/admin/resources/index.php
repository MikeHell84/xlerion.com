<?php $title = 'Recursos'; $banner_title_class = 'section-title-lg'; ob_start(); ?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview"><?php include __DIR__ . '/../../../public/admin/_nav.php'; ?></aside>
  <main class="admin-main">
    <header class="admin-header"><h1 class="section-title-lg">Recursos</h1><div class="actions"><a class="btn btn-primary" href="/admin/resources/create">Crear recurso</a></div></header>
    <section class="mt-3 card-preview">
      <?php if (empty($resources)): ?>
        <div class="small text-muted">No hay recursos aún. Crea uno nuevo usando el botón "Crear recurso".</div>
      <?php else: ?>
        <div class="list-group">
          <?php foreach ($resources as $r): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong><?= htmlspecialchars($r['title']) ?></strong>
                <div class="small text-muted"><?= htmlspecialchars($r['slug']) ?></div>
              </div>
              <div>
                <a href="/admin/resources/edit?id=<?= htmlspecialchars($r['id']) ?>" class="btn btn-sm btn-outline-light">Editar</a>
                <form action="/admin/resources/delete" method="post" style="display:inline-block;margin-left:.5rem">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>" />
                  <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>
</div>
<?php $slot = ob_get_clean(); include __DIR__ . '/../../layout.php'; ?>
