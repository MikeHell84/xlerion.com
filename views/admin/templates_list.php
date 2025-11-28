<?php
// Simple admin list view (requires auth)
require_once __DIR__.'/../../src/Model/Template.php';
$templates = Template::findAll();
?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview"><?php include __DIR__ . '/../../public/admin/_nav.php'; ?></aside>
  <main class="admin-main">
    <div class="card-preview">
      <h2 class="section-title-lg">Plantillas</h2>
      <div class="mb-3"><a class="btn btn-primary" href="/admin/templates/new.php">Crear nueva</a></div>
      <ul class="list-group">
      <?php foreach($templates as $t): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div><?php echo htmlspecialchars($t['name']); ?><div class="text-muted small"><?php echo htmlspecialchars($t['description'] ?? ''); ?></div></div>
          <div>
            <a class="btn btn-sm btn-outline-light" href="/admin/templates/edit.php?id=<?php echo $t['id']; ?>">Editar</a>
          </div>
        </li>
      <?php endforeach; ?>
      </ul>
    </div>
  </main>
</div>
