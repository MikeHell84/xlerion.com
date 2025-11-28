<?php $title = 'Recursos'; ?>
<section class="container my-5">
  <h1 class="section-title-lg">Recursos</h1>
  <div class="row">
    <?php foreach ($resources as $r): ?>
      <div class="col-12 col-md-6 mb-3">
        <div class="card card-preview">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($r['title']) ?></h5>
            <p class="card-text small text-muted"><?= htmlspecialchars($r['description'] ?? '') ?></p>
            <a class="btn btn-primary" href="/recursos/<?= htmlspecialchars($r['slug']) ?>">Ver recurso</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
