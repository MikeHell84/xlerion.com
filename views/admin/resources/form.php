<?php $title = isset($resource) ? 'Editar recurso' : 'Crear recurso'; $banner_title_class = 'section-title-lg'; ob_start(); ?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview"><?php include __DIR__ . '/../../../public/admin/_nav.php'; ?></aside>
  <main class="admin-main">
    <header class="admin-header"><h1 class="section-title-lg"><?= htmlspecialchars($title) ?></h1></header>
    <section class="mt-3 card-preview">
      <div class="row">
        <div class="col-12 col-lg-6">
          <form id="resourceForm" method="post" action="<?= isset($resource) ? '/admin/resources/update' : '/admin/resources/store' ?>" enctype="multipart/form-data">
        <?php if (isset($resource)): ?> <input type="hidden" name="id" value="<?= htmlspecialchars($resource['id']) ?>"/> <?php endif; ?>
        <div class="mb-3">
          <label class="form-label">Título</label>
          <input class="form-control" name="title" value="<?= htmlspecialchars($resource['title'] ?? '') ?>" />
        </div>
        <div class="mb-3">
          <label class="form-label">Slug</label>
          <input class="form-control" name="slug" value="<?= htmlspecialchars($resource['slug'] ?? '') ?>" />
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción (HTML permitido)</label>
          <textarea class="form-control" name="description" rows="6"><?= htmlspecialchars($resource['description'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Archivo (opcional) — sube un PDF o imagen</label>
          <?php if (!empty($resource['file_path'] ?? '')): ?>
            <div class="mb-2"><a href="<?= htmlspecialchars($resource['file_path']) ?>" target="_blank">Ver archivo actual</a></div>
          <?php endif; ?>
          <input type="file" name="file" class="form-control form-control-sm" />
          <div class="small text-muted">Si subes un archivo se guardará en <code>/media/resources/</code> y la ruta se almacenará automáticamente.</div>
        </div>
        <div class="mb-3">
          <label class="form-label">URL externa (opcional)</label>
          <input class="form-control" name="url" value="<?= htmlspecialchars($resource['url'] ?? '') ?>" />
        </div>
        <button class="btn btn-primary" type="submit">Guardar</button>
          </form>
        </div>
        <div class="col-12 col-lg-6">
          <div class="card card-preview p-3">
            <div class="card-body">
              <h6 class="mb-2">Vista previa</h6>
              <div id="resPreview">
                <h3 id="pvTitle"><?= htmlspecialchars($resource['title'] ?? 'Título del recurso') ?></h3>
                <div id="pvMedia" class="mb-2"><?php if (!empty($resource['file_path'])): ?><img src="<?= htmlspecialchars($resource['file_path']) ?>" style="max-width:100%;height:160px;object-fit:cover;border-radius:6px" alt="preview"/><?php endif; ?></div>
                <div id="pvDesc" class="small text-muted"><?= $resource['description'] ?? 'Descripción breve del recurso...' ?></div>
                <div class="mt-3"><a id="pvLink" class="btn btn-primary" href="<?= htmlspecialchars($resource['url'] ?? '#') ?>" target="_blank">Abrir recurso</a></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>
<?php $slot = ob_get_clean(); include __DIR__ . '/../../layout.php'; ?>

<script>
// Live preview for admin resource form
(function(){
  var title = document.querySelector('input[name="title"]');
  var desc = document.querySelector('textarea[name="description"]');
  var url = document.querySelector('input[name="url"]');
  var file = document.querySelector('input[type="file"][name="file"]');
  var pvTitle = document.getElementById('pvTitle');
  var pvDesc = document.getElementById('pvDesc');
  var pvLink = document.getElementById('pvLink');
  var pvMedia = document.getElementById('pvMedia');
  if (title) title.addEventListener('input', function(){ pvTitle.textContent = this.value || 'Título del recurso'; });
  if (desc) desc.addEventListener('input', function(){ pvDesc.textContent = this.value || 'Descripción breve del recurso...'; });
  if (url) url.addEventListener('input', function(){ pvLink.href = this.value || '#'; });
  if (file) file.addEventListener('change', function(){
    var f = this.files && this.files[0]; if (!f) return; var reader = new FileReader(); reader.onload = function(e){ pvMedia.innerHTML = '<img src="'+e.target.result+'" style="max-width:100%;height:160px;object-fit:cover;border-radius:6px"/>'; }; reader.readAsDataURL(f);
  });
})();
</script>
