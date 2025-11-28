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
      <div class="actions">
        <a class="btn btn-outline-light" href="/admin/pages.php">Volver</a>
        <?php if (!empty($page['slug'])): ?>
          <a class="btn btn-outline-light btn-sm" href="/<?= htmlspecialchars($page['slug']) ?>" target="_blank">Preview</a>
        <?php endif; ?>
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
          <div class="form-check">
            <?php $subtitle_uppercase = isset($meta['subtitle_uppercase']) ? (bool)$meta['subtitle_uppercase'] : true; /* default to true for new pages */ ?>
            <input class="form-check-input" type="checkbox" name="subtitle_uppercase" id="subtitle_uppercase" value="1" <?= $subtitle_uppercase ? 'checked' : '' ?> />
            <label class="form-check-label" for="subtitle_uppercase">Subtítulos en MAYÚSCULAS</label>
            <div class="small text-muted">Si está activado, los subtítulos de sección (h3) se mostrarán en mayúsculas.</div>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Contenido (HTML permitido)</label>
          <div class="mb-2">
            <?php
              // load templates for selection
              $templates = [];
              try { $templates = $pdo->query('SELECT id,name FROM templates ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e) { $templates = []; }
              $assignedHeader = '';
              $assignedFooter = '';
              // check template_assignments if page exists
              if (!empty($page['id'])){
                try{ $a = $pdo->prepare('SELECT section,template_id FROM template_assignments WHERE page_id = ?'); $a->execute([$page['id']]); $as = $a->fetchAll(PDO::FETCH_ASSOC); foreach($as as $r){ if ($r['section']==='header') $assignedHeader = $r['template_id']; if ($r['section']==='footer') $assignedFooter = $r['template_id']; } }catch(Exception $e){}
              }
            ?>
            <div class="d-flex gap-2 align-items-center">
              <label class="form-label small mb-0">Header</label>
              <select name="header_template_id" class="form-select form-select-sm" style="width:220px">
                <option value="">-- Ninguno --</option>
                <?php foreach($templates as $t): ?>
                  <option value="<?= intval($t['id']) ?>" <?= (string)$t['id']===(string)$assignedHeader ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                <?php endforeach; ?>
              </select>
              <label class="form-label small mb-0">Footer</label>
              <select name="footer_template_id" class="form-select form-select-sm" style="width:220px">
                <option value="">-- Ninguno --</option>
                <?php foreach($templates as $t): ?>
                  <option value="<?= intval($t['id']) ?>" <?= (string)$t['id']===(string)$assignedFooter ? 'selected' : '' ?>>=<?= htmlspecialchars($t['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="d-flex gap-2 mb-2">
            <div id="templates_widget_page" class="me-2"></div>
            <div class="small text-muted align-self-center">Insertar o crear plantillas desde el editor</div>
          </div>
          <!-- Visual Templates Constructor (collapsible) -->
          <div class="card card-preview mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Constructor visual de plantillas</strong>
              </div>
              <div id="visualBuilderArea" style="display:block;position:relative;border:1px solid #e6e6e6;padding:8px;border-radius:6px;min-height:260px">
                <script src="/js/templates-editor.js" defer></script>
                <div class="d-flex">
                  <div id="templatesPalettePage" class="d-flex flex-column me-3" style="width:260px;min-width:220px">
                    <div class="palette-group mb-2"><h6 class="small">Bloques</h6>
                      <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="hero">Hero</button>
                      <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="card">Card</button>
                      <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="two-col">Grid 2-col</button>
                      <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="section">Sección</button>
                      <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="image">Imagen</button>
                    </div>
                    <div class="palette-group mb-2"><h6 class="small">Estructura</h6>
                      <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="header">Header</button>
                      <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="footer">Footer</button>
                    </div>
                  </div>
                  <div style="flex:1;min-width:0">
                    <div id="templatesEditorContainer" style="position:relative;min-height:180px">
                      <div id="templatesCanvas" class="templates-canvas" style="position:relative;min-height:220px;padding:12px;background:#fff;border:1px solid #f1f1f1;border-radius:6px"></div>
                      <pre id="templatesEditorHtmlPane" class="templates-htmlpane" style="display:none;margin:0;padding:12px;white-space:pre-wrap;overflow:auto;"></pre>
                    </div>
                    <textarea id="templatesEditor" name="data_html" style="display:none"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
        <div class="mb-3 border-top pt-3">
          <h6 class="mb-2">Menú principal</h6>
          <?php
            // prepare menu-related meta values
            $menu_visible = isset($meta['menu_visible']) ? (bool)$meta['menu_visible'] : false;
            $menu_group = $meta['menu_group'] ?? '';
            $menu_parent = isset($meta['menu_parent']) ? (int)$meta['menu_parent'] : 0;
            $menu_order = isset($meta['menu_order']) ? (int)$meta['menu_order'] : 0;
            // fetch available pages to allow choosing a parent (exclude self)
            try{
              $allPages = $pdo->query('SELECT id,slug,title,meta FROM cms_pages WHERE is_published=1 ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e){ $allPages = []; }
            // derive existing groups from pages meta (menu_group values)
            $existingGroups = [];
            foreach ($allPages as $ap){ if (!empty($ap['meta'])){ $m = json_decode($ap['meta'], true); if (is_array($m) && !empty($m['menu_group'])) $existingGroups[$m['menu_group']] = true; } }
            // sensible default groups to show when none exist yet
            $defaultGroups = ['Información','Servicios','Recursos','Contacto'];
            foreach ($defaultGroups as $dg) { if (!isset($existingGroups[$dg])) $existingGroups[$dg] = true; }
            // if a parent is selected and current page doesn't have a group, inherit parent's group for convenience
            if (!$menu_group && $menu_parent) {
              foreach ($allPages as $ap){ if (intval($ap['id'])===intval($menu_parent) && !empty($ap['meta'])){ $pm = json_decode($ap['meta'], true); if (is_array($pm) && !empty($pm['menu_group'])) { $menu_group = $pm['menu_group']; break; } } }
            }
          ?>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="menu_visible" id="menu_visible" value="1" <?= $menu_visible? 'checked' : '' ?> />
            <label class="form-check-label" for="menu_visible">Mostrar en el menú principal</label>
          </div>

          <div class="mb-2">
            <label class="form-label small">Grupo / Sección del menú (submenu)</label>
            <select name="menu_group" class="form-select form-select-sm mb-1">
              <option value="">-- Seleccionar grupo existente --</option>
              <?php foreach (array_keys($existingGroups) as $g): ?>
                <option value="<?= htmlspecialchars($g) ?>" <?= $g === $menu_group ? 'selected' : '' ?>><?= htmlspecialchars($g) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="menu_group_new" value="" class="form-control form-control-sm" placeholder="Crear nuevo grupo (si no existe)" />
            <div class="small text-muted mt-1">Puedes seleccionar un grupo existente o escribir uno nuevo para crear un submenu.</div>
          </div>

          <div class="mb-2">
            <label class="form-label small">Padre en el menú (opcional)</label>
            <select name="menu_parent" class="form-select form-select-sm">
              <option value="0">-- Ninguno (top-level) --</option>
              <?php foreach ($allPages as $ap): if ((int)($page['id']??0) === (int)$ap['id']) continue; // skip self ?>
                <option value="<?= intval($ap['id']) ?>" <?= intval($ap['id']) === $menu_parent ? 'selected' : '' ?>><?= htmlspecialchars($ap['title']) ?> (<?= htmlspecialchars($ap['slug']) ?>)</option>
              <?php endforeach; ?>
            </select>
            <div class="small text-muted">Si eliges un padre, esta página aparecerá como item dentro de ese submenu.</div>
          </div>

          <div class="mb-2">
            <label class="form-label small">Orden en el menú (número, menor = primero)</label>
            <input type="number" name="menu_order" value="<?= htmlspecialchars($menu_order) ?>" class="form-control form-control-sm" />
          </div>
        </div>
        <div class="mb-3 border-top pt-3">
          <h6 class="mb-2">Recursos asociados <a class="btn btn-sm btn-primary ms-2" href="/admin/resources/create">Agregar recurso</a></h6>
          <?php
            // load available resources from the resources table
            try {
              $resStmt = $pdo->query('SELECT id,slug,title FROM resources ORDER BY title ASC');
              $allResources = $resStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) { $allResources = []; }
            // existing selected resources from meta (array of slugs)
            $selectedResources = [];
            if (!empty($page['meta'])) { $tmp = json_decode($page['meta'], true); if (is_array($tmp) && !empty($tmp['resources']) && is_array($tmp['resources'])) $selectedResources = $tmp['resources']; }
          ?>
          <div class="small text-muted mb-2">Marca los recursos que deberían aparecer como relacionados en esta página.</div>
          <input type="hidden" name="resources_order" id="resources_order" value="<?= htmlspecialchars(implode(',', $selectedResources)) ?>">
          <div class="mb-2"><input id="resourcesFilter" class="form-control form-control-sm" placeholder="Filtrar recursos por título o slug..."></div>
          <div class="resources-panel">
            <?php if (empty($allResources)): ?>
              <div class="small text-muted">No hay recursos disponibles. Crea primeros recursos en el menú "Recursos".</div>
            <?php else: ?>
              <div id="resourcesList" class="d-flex flex-column gap-2">
              <?php foreach ($allResources as $r): $isChecked = in_array($r['slug'], $selectedResources); $thumb = '/media/placeholder-thumb.svg'; try { if (!empty($r['file_path'])) $thumb = $r['file_path']; else if (!empty($r['url'])) $thumb = $r['url']; } catch(Exception $e){} ?>
                <div class="resource-item d-flex align-items-center p-2 border rounded" draggable="true" data-slug="<?= htmlspecialchars($r['slug']) ?>" data-id="<?= intval($r['id']) ?>">
                  <input class="form-check-input me-2" type="checkbox" value="<?= htmlspecialchars($r['slug']) ?>" id="res_<?= intval($r['id']) ?>" name="resources[]" <?= $isChecked ? 'checked' : '' ?> />
                  <img src="<?= htmlspecialchars($thumb) ?>" alt="thumb" class="resource-thumb"/>
                  <div style="flex:1;min-width:0">
                    <label class="form-check-label mb-0 text-truncate" for="res_<?= intval($r['id']) ?>"><?= htmlspecialchars($r['title'] ?: $r['slug']) ?></label>
                    <div class="small text-muted text-truncate"><?= htmlspecialchars($r['slug']) ?></div>
                  </div>
                  <div class="d-flex gap-1 align-items-center ms-2">
                    <a class="btn btn-sm btn-outline-light" href="/admin/resources/edit?id=<?= intval($r['id']) ?>" title="Editar recurso" target="_blank">Editar</a>
                    <button type="button" class="btn btn-sm btn-danger res-delete-btn" title="Eliminar recurso">Eliminar</button>
                  </div>
                  <div class="drag-handle text-muted" style="padding-left:.5rem" aria-hidden="true">
                    <!-- small SVG handle -->
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M10 6h4M10 12h4M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </div>
                </div>
              <?php endforeach; ?>
              </div>
            <?php endif; ?>
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

<script>
// Template picker integration: open and insert into page editor
(function(){
  var btn = document.getElementById('openTemplatePickerBtn');
  if (btn) btn.addEventListener('click', function(){ if (typeof openTemplatePicker === 'function') openTemplatePicker(); else alert('Selector de plantillas no disponible'); });

  // when a template is inserted via the picker, place into the page editor
  window.addEventListener('template:insert', function(e){
    var t = e.detail || {};
    var html = t.data && t.data.html ? t.data.html : (t.html || '');
    if (!html && t.data && t.data.regions){ html = (t.data.regions.header||'') + (t.data.regions.content||'') + (t.data.regions.footer||''); }
    if (!html) return;
    // if TemplatesEditor API is present, use it
    if (window.TemplatesEditor && typeof window.TemplatesEditor.insertHTML === 'function'){
      window.TemplatesEditor.insertHTML(html);
      return;
    }
    // otherwise, try to insert into the focused contenteditable
    var editor = document.getElementById('editor'); if (!editor) return;
    editor.focus();
    try{ document.execCommand('insertHTML', false, html); }catch(err){ editor.innerHTML += html; }
  });
})();
</script>

<script>
// Drag & drop ordering for resources list
(function(){
  var list = document.getElementById('resourcesList');
  if (!list) return;
  var dragSrc = null;
  list.addEventListener('dragstart', function(e){ var it = e.target.closest('.resource-item'); if (!it) return; dragSrc = it; e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', it.dataset.slug || ''); it.style.opacity = '0.4'; });
  list.addEventListener('dragend', function(e){ if (dragSrc) dragSrc.style.opacity = ''; dragSrc = null; });
  list.addEventListener('dragover', function(e){ e.preventDefault(); var over = e.target.closest('.resource-item'); if (!over || over === dragSrc) return; var rect = over.getBoundingClientRect(); var after = (e.clientY - rect.top) > (rect.height/2); if (after) over.parentNode.insertBefore(dragSrc, over.nextSibling); else over.parentNode.insertBefore(dragSrc, over); });

  // update hidden ordering input
  function updateOrder(){ var items = list.querySelectorAll('.resource-item'); var arr = []; items.forEach(function(it){ if (it) arr.push(it.dataset.slug || ''); }); document.getElementById('resources_order').value = arr.join(','); }
  // update order when checkboxes change (so initial order reflects current selection)
  list.addEventListener('change', updateOrder);
  // update order on pointerup (after possible drop)
  list.addEventListener('pointerup', function(){ setTimeout(updateOrder,50); });
  // update order before form submit
  var form = document.querySelector('form[action="/admin/save_page.php"]'); if (form) form.addEventListener('submit', function(){ updateOrder(); });
  // initialize
  updateOrder();
})();
</script>

<script>
// AJAX delete for resources inside edit page (prevents nested forms)
(function(){
  var delBtns = document.querySelectorAll('.res-delete-btn');
  if (!delBtns || !delBtns.length) return;
  delBtns.forEach(function(b){ b.addEventListener('click', function(){
    if (!confirm('¿Eliminar este recurso? Esta acción no se puede deshacer.')) return;
    var item = b.closest('.resource-item'); if (!item) return;
    var id = item.dataset.id; if (!id) return;
    var fd = new FormData(); fd.append('id', id); fd.append('csrf', '<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>');
    fetch('/admin/resources/delete', { method: 'POST', body: fd }).then(function(r){ return r.text(); }).then(function(txt){
      // assume successful redirect or empty body -> remove node
      item.parentNode && item.parentNode.removeChild(item);
      // update ordering hidden input
      var list = document.getElementById('resourcesList'); if (list){ var arr = Array.from(list.querySelectorAll('.resource-item')).map(function(it){ return it.dataset.slug; }); document.getElementById('resources_order').value = arr.join(','); }
    }).catch(function(err){ alert('Error al eliminar: ' + (err.message || err)); });
  }); });
})();
</script>

<script>
// Client-side filter for resources list (debounced)
(function(){
  var input = document.getElementById('resourcesFilter');
  var list = document.getElementById('resourcesList');
  if (!input || !list) return;
  var timeout = null;
  input.addEventListener('input', function(e){
    clearTimeout(timeout);
    timeout = setTimeout(function(){
      var q = (input.value || '').toLowerCase().trim();
      var items = list.querySelectorAll('.resource-item');
      items.forEach(function(it){
        var title = (it.querySelector('label')?.textContent || '').toLowerCase();
        var slug = (it.dataset.slug || '').toLowerCase();
        if (!q || title.indexOf(q) !== -1 || slug.indexOf(q) !== -1) { it.style.display = ''; } else { it.style.display = 'none'; }
      });
    }, 180);
  });
})();
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
  // Defer Modal/Bootstrap-dependent initialization until bootstrap is present.
  function initPickerBindings(){
    var modalEl = document.getElementById('mediaPickerModal');
    if (!modalEl) return;
    var openBtn = document.getElementById('openPicker');
    var searchBtn = document.getElementById('pickerSearchBtn');
    var searchInput = document.getElementById('pickerSearch');

    function onOpenClick(){
      // Prefer Bootstrap modal if available, otherwise fall back to a minimal show behavior
      if (window.bootstrap && typeof window.bootstrap.Modal === 'function'){
        try{ var picker = new bootstrap.Modal(modalEl); picker.show(); }catch(e){ /* ignore */ }
        try{ loadPicker(1); }catch(e){}
        return;
      }
      // Fallback: toggle basic visibility (not full Bootstrap modal behavior)
      try{ modalEl.classList.add('show'); modalEl.style.display = 'block'; loadPicker(1); }catch(e){}
    }

    if (openBtn) openBtn.addEventListener('click', onOpenClick);
    if (searchBtn) searchBtn.addEventListener('click', function(){ loadPicker(1); });
    if (searchInput) searchInput.addEventListener('keydown', function(e){ if (e.key === 'Enter'){ e.preventDefault(); loadPicker(1); } });

    // If Bootstrap becomes available later, no-op: handler will use it on click.
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initPickerBindings); else initPickerBindings();

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
