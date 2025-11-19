<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
$id = $_GET['id'] ?? null; $pdo = Database::pdo(); $page = null;
if ($id) { $st = $pdo->prepare('SELECT * FROM cms_pages WHERE id = ? LIMIT 1'); $st->execute([$id]); $page = $st->fetch(); }

$title = $id ? 'Editar página' : 'Nueva página';
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
        <a class="btn btn-outline-light" href="/admin/pages.php">Volver</a>
      </div>
    </header>

    <section class="mt-3 card-preview">
      <form method="post" action="/admin/save_page.php">
        <input type="hidden" name="id" value="<?=htmlspecialchars($page['id']??'')?>">
        <div class="mb-3">
          <label class="form-label">Título</label>
          <input name="title" value="<?=htmlspecialchars($page['title']??'')?>" required class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Slug</label>
          <input name="slug" value="<?=htmlspecialchars($page['slug']??'')?>" required class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Extracto</label>
          <textarea name="excerpt" class="form-control"><?=htmlspecialchars($page['excerpt']??'')?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Contenido (HTML permitido)</label>
          <div class="editor" contenteditable="true" id="editor" style="min-height:240px;border:1px solid #ddd;padding:.75rem;border-radius:6px;"><?= $page['content'] ?? '' ?></div>
          <input type="hidden" name="content" id="contentField">
        </div>
        <div class="mb-3">
          <label class="form-label">Media / Imagen destacada</label>
          <?php
            $meta = [];
            if (!empty($page['meta'])) { $mm = json_decode($page['meta'], true); if (is_array($mm)) $meta = $mm; }
            $mediaPlacement = $meta['media_placement'] ?? 'none';
            $mediaUrl = $meta['media_url'] ?? '';
          ?>
          <select name="media_placement" class="form-select form-select-sm mb-2">
            <option value="none" <?= $mediaPlacement==='none' ? 'selected' : '' ?>>Sin media</option>
            <option value="header" <?= $mediaPlacement==='header' ? 'selected' : '' ?>>Encabezado (hero)</option>
            <option value="inline-top" <?= $mediaPlacement==='inline-top' ? 'selected' : '' ?>>Dentro del contenido (arriba)</option>
            <option value="inline-bottom" <?= $mediaPlacement==='inline-bottom' ? 'selected' : '' ?>>Dentro del contenido (abajo)</option>
            <option value="gallery" <?= $mediaPlacement==='gallery' ? 'selected' : '' ?>>Galería / Múltiples</option>
          </select>
          <label class="form-label small">URL de la imagen / video</label>
          <input type="text" name="media_url" value="<?= htmlspecialchars($mediaUrl) ?>" class="form-control form-control-sm" placeholder="/media/imagen.jpg o https://...">
          <div class="small text-muted mt-1">Puedes usar un archivo en /media o una URL externa. Si eliges "gallery" puedes luego añadir múltiples URLs separadas por comas.</div>
          <div class="mt-2">
            <label class="form-label small">Subir desde tu equipo</label>
            <div id="dropZone" class="border rounded p-2 small text-muted" style="background:#f8f9fa">Arrastra un archivo aquí o haz clic para seleccionar</div>
            <input type="file" id="mediaFileInput" class="form-control form-control-sm d-none">
            <div id="uploadStatus" class="small text-muted mt-1"></div>
            <div id="uploadProgress" class="progress mt-2" style="height:8px;display:none"><div id="uploadBar" class="progress-bar" role="progressbar" style="width:0%"></div></div>
          </div>
            <div class="mt-2">
              <button type="button" id="openPicker" class="btn btn-outline-light btn-sm">Seleccionar media existente</button>
            </div>
        </div>
        <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'] ?? '')?>">
        <button type="submit" class="btn btn-primary" onclick="document.getElementById('contentField').value=document.getElementById('editor').innerHTML">Guardar</button>
      </form>
    </section>
  </main>
</div>

<script>
// Clean pasted HTML to remove inline styles
var ed = document.getElementById('editor');
if (ed) ed.addEventListener('paste', function(e){
  e.preventDefault();
  var text = (e.clipboardData || window.clipboardData).getData('text/plain');
  document.execCommand('insertText', false, text);
});

// Upload media from file input
var fileInput = document.getElementById('mediaFileInput');
if (fileInput) {
  var dropZone = document.getElementById('dropZone');
  var status = document.getElementById('uploadStatus');
  var progress = document.getElementById('uploadProgress');
  var bar = document.getElementById('uploadBar');
  var doUpload = function(file){
    if (!file) return;
    status.textContent = 'Subiendo...'; progress.style.display = 'block'; bar.style.width = '0%';
    var xhr = new XMLHttpRequest();
    var fd = new FormData(); fd.append('media', file);
    xhr.open('POST', '/admin/upload_media.php', true);
    xhr.upload.addEventListener('progress', function(e){ if (e.lengthComputable) { var p = Math.round((e.loaded / e.total) * 100); bar.style.width = p + '%'; } });
    xhr.onreadystatechange = function(){ if (xhr.readyState === 4) { try { var j = JSON.parse(xhr.responseText || '{}'); if (j.ok && j.url) { document.querySelector('input[name="media_url"]').value = j.url; status.textContent = 'Subida correcta: ' + j.url; } else { status.textContent = 'Error: ' + (j.error || 'unknown'); } } catch(e){ status.textContent = 'Error en respuesta'; } progress.style.display = 'none'; } };
    xhr.send(fd);
  };
  fileInput.addEventListener('change', function(){ doUpload(this.files[0]); });
  if (dropZone) {
    dropZone.addEventListener('click', function(){ fileInput.click(); });
    dropZone.addEventListener('dragover', function(e){ e.preventDefault(); this.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', function(e){ e.preventDefault(); this.classList.remove('dragover'); });
    dropZone.addEventListener('drop', function(e){ e.preventDefault(); this.classList.remove('dragover'); var f = e.dataTransfer.files[0]; if (f) doUpload(f); });
  }
}
</script>

<!-- Media picker modal -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Seleccionar media</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="d-flex mb-2">
          <input id="pickerSearch" class="form-control form-control-sm me-2" placeholder="Buscar...">
          <button id="pickerSearchBtn" class="btn btn-sm btn-primary">Buscar</button>
        </div>
        <div class="d-flex gap-2 mb-2">
          <button id="pickerSetHero" class="btn btn-sm btn-outline-primary">Set as hero</button>
          <button id="pickerAddGallery" class="btn btn-sm btn-outline-secondary">Add to gallery</button>
          <button id="pickerInsertContent" class="btn btn-sm btn-outline-light">Insert into content</button>
        </div>
        <div id="pickerResults" class="row g-2" style="min-height:200px"></div>
        <nav id="pickerPager" class="mt-2"></nav>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
    </div>
  </div>
</div>

<script>
(function(){
  var modalEl = document.getElementById('mediaPickerModal');
  var picker = new bootstrap.Modal(modalEl);
  document.getElementById('openPicker').addEventListener('click', function(){ picker.show(); loadPicker(1); });
  document.getElementById('pickerSearchBtn').addEventListener('click', function(){ loadPicker(1); });
  document.getElementById('pickerSearch').addEventListener('keydown', function(e){ if (e.key==='Enter'){ loadPicker(1); } });

  function loadPicker(page){
    var q = document.getElementById('pickerSearch').value || '';
    fetch('/admin/media_list_api.php?q='+encodeURIComponent(q)+'&page='+page).then(r=>r.json()).then(j=>{
      var root = document.getElementById('pickerResults'); root.innerHTML='';
      j.items.forEach(function(it){
        var col = document.createElement('div'); col.className='col-6 col-md-3';
        var card = document.createElement('div'); card.className='card card-preview p-2';
        var chk = document.createElement('input'); chk.type='checkbox'; chk.className='picker-select me-1'; chk.dataset.url = it.url; chk.dataset.id = it.id;
        var ext = it.filename.split('.').pop().toLowerCase();
        var preview = '';
        if (['jpg','jpeg','png','gif','webp'].indexOf(ext)!==-1) preview = '<img src="'+it.url+'" style="width:100%;max-height:120px;object-fit:cover"/>';
        else preview = '<div style="height:120px;display:flex;align-items:center;justify-content:center;background:#f3f3f3">'+ext.toUpperCase()+'</div>';
        card.innerHTML = '<div class="d-flex align-items-start">'+preview+'<div style="position:absolute;top:8px;left:8px">'+chk.outerHTML+'</div></div>';
        var btn = document.createElement('button'); btn.className='btn btn-sm btn-outline-light mt-2'; btn.textContent='Insertar';
        btn.addEventListener('click', function(){ insertMediaAtCursor(it.url); picker.hide(); });
        card.appendChild(btn); col.appendChild(card); root.appendChild(col);
      });
      // pager
      var pager = document.getElementById('pickerPager'); pager.innerHTML = '';
      var pages = Math.ceil(j.total / j.per);
      for (var p=1;p<=pages && p<=10;p++){
        var a = document.createElement('button'); a.className='btn btn-sm btn-outline-light me-1'; a.textContent=p; a.addEventListener('click',(function(pp){ return function(){ loadPicker(pp); }; })(p)); pager.appendChild(a);
      }
    });
  }

  function insertMediaAtCursor(url){
    var sel = window.getSelection();
    if (!sel || !sel.rangeCount) { document.getElementById('editor').focus(); document.execCommand('insertHTML', false, '<img src="'+url+'"/>'); return; }
    var range = sel.getRangeAt(0);
    var img = document.createElement('img'); img.src = url; img.style.maxWidth='100%';
    range.deleteContents(); range.insertNode(img);
  }

  // Helper: gather selected URLs from picker
  function pickerSelectedUrls(){
    var sel = Array.from(document.querySelectorAll('.picker-select:checked')).map(function(i){ return i.dataset.url; });
    return sel;
  }

  document.getElementById('pickerInsertContent').addEventListener('click', function(){
    var urls = pickerSelectedUrls(); if (!urls.length) return alert('Seleccione uno o más archivos');
    urls.forEach(function(u){ insertMediaAtCursor(u); }); picker.hide();
  });
  document.getElementById('pickerSetHero').addEventListener('click', function(){
    var urls = pickerSelectedUrls(); if (!urls.length) return alert('Seleccione un archivo como hero');
    // set media_placement to header and set media_url to first selected
    document.querySelector('select[name="media_placement"]').value = 'header';
    document.querySelector('input[name="media_url"]').value = urls[0]; picker.hide();
  });
  document.getElementById('pickerAddGallery').addEventListener('click', function(){
    var urls = pickerSelectedUrls(); if (!urls.length) return alert('Seleccione archivos para la galería');
    var input = document.querySelector('input[name="media_url"]');
    var curr = input.value ? input.value.split(',').map(function(s){ return s.trim(); }) : [];
    var merged = curr.concat(urls).filter(function(v,i,a){ return v && a.indexOf(v)===i; });
    input.value = merged.join(',');
    document.querySelector('select[name="media_placement"]').value = 'gallery'; picker.hide();
  });
})();
</script>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';
