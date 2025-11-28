<?php
$title = $res['title'] ?? 'Recurso';
// Capture view into $slot so layout.php renders header/footer and site chrome
ob_start();
?>
<nav aria-label="breadcrumb" class="mt-3">
  <div class="container">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="/">Inicio</a></li>
      <li class="breadcrumb-item"><a href="/documentacion">Documentación</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($res['title'] ?? 'Recurso'); ?></li>
    </ol>
  </div>
</nav>

<section class="container my-5">
  <div class="row g-4">
    <div class="col-12 col-lg-8">
      <article class="card shadow-sm">
        <div class="card-body">
          <h1 class="h3 mb-2"><?= htmlspecialchars($res['title']) ?></h1>
          <p class="text-muted small mb-3">Publicado: <?= htmlspecialchars($res['created_at'] ?? '') ?></p>

          <?php if (!empty($res['file_path'])): ?>
            <figure class="mb-3">
              <a href="<?= htmlspecialchars($res['file_path']) ?>" data-bs-toggle="modal" data-bs-target="#xlerionImageModal">
                <img src="<?= htmlspecialchars($res['file_path']) ?>" alt="<?= htmlspecialchars($res['title']) ?>" class="img-fluid rounded project-thumb" style="width:100%;height:auto;object-fit:cover;" />
              </a>
            </figure>
          <?php endif; ?>

          <div class="prose-left small"> <?= $res['description'] ?? '' ?> </div>

          <div class="mt-4 d-flex flex-wrap gap-2">
            <?php if (!empty($res['file_path'])): ?>
              <a class="btn btn-outline-primary" href="<?= htmlspecialchars($res['file_path']) ?>" download>Descargar</a>
            <?php endif; ?>
            <?php if (!empty($res['url'])): ?>
              <a class="btn btn-primary" href="<?= htmlspecialchars($res['url']) ?>" target="_blank" rel="noopener">Abrir recurso</a>
            <?php endif; ?>
            <a class="btn btn-secondary" href="/documentacion">Volver a Documentación</a>
          </div>
        </div>
      </article>
    </div>

    <aside class="col-12 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="mb-3">Detalles</h6>
          <dl class="row small">
            <dt class="col-5 text-muted">Slug</dt><dd class="col-7"><?= htmlspecialchars($res['slug']) ?></dd>
            <dt class="col-5 text-muted">Tipo</dt><dd class="col-7"><?= !empty($res['file_path']) ? 'Archivo' : (!empty($res['url']) ? 'Enlace' : 'Desconocido') ?></dd>
            <dt class="col-5 text-muted">ID</dt><dd class="col-7"><?= htmlspecialchars($res['id'] ?? '') ?></dd>
            <?php if (!empty($res['author'])): ?><dt class="col-5 text-muted">Autor</dt><dd class="col-7"><?= htmlspecialchars($res['author']) ?></dd><?php endif; ?>
            <?php if (!empty($res['created_at'])): ?><dt class="col-5 text-muted">Fecha</dt><dd class="col-7"><?= htmlspecialchars($res['created_at']) ?></dd><?php endif; ?>
          </dl>
        </div>
      </div>
    </aside>
  </div>
</section>

<?php
$slot = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
