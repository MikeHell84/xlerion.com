<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
$pdo = Database::pdo(); $id = $_GET['id'] ?? null; $post = null;
if ($id) { try{ $st=$pdo->prepare('SELECT * FROM blog_posts WHERE id = ? LIMIT 1'); $st->execute([intval($id)]); $post = $st->fetch(PDO::FETCH_ASSOC); } catch(Exception $e){ $post=null; } }

$title = $id ? 'Editar entrada' : 'Nueva entrada'; $banner_title_class='section-title-lg'; ob_start();
?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview"><?php include __DIR__ . '/_nav.php'; ?></aside>
  <main class="admin-main">
    <header class="admin-header">
      <h1 class="section-title-lg"><?= htmlspecialchars($title) ?></h1>
      <div class="actions">
        <a class="btn btn-outline-light" href="/admin/posts.php">Volver</a>
        <?php if (!empty($post['slug'])): ?>
          <a class="btn btn-outline-light btn-sm" href="/blog/<?= htmlspecialchars($post['slug']) ?>" target="_blank">Preview</a>
        <?php endif; ?>
      </div>
    </header>

    <section class="mt-3 card-preview">
      <form method="post" action="/admin/save_post.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']??'') ?>">
        <div class="mb-3"><label class="form-label">Título</label><input name="title" value="<?= htmlspecialchars($post['title']??'') ?>" required class="form-control"></div>
        <div class="mb-3"><label class="form-label">Slug</label><input name="slug" value="<?= htmlspecialchars($post['slug']??'') ?>" required class="form-control"></div>
        <div class="mb-3"><label class="form-label">Extracto</label><textarea name="excerpt" class="form-control"><?= htmlspecialchars($post['excerpt']??'') ?></textarea></div>
        <div class="mb-3">
          <label class="form-label">Contenido (HTML permitido)</label>
          <div class="d-flex align-items-start mb-2 gap-2">
            <div style="flex:1">
              <div class="editor" contenteditable="true" id="editor" style="min-height:260px;border:1px solid #ddd;padding:.75rem;border-radius:6px;"><?= $post['content'] ?? '' ?></div>
            </div>
            <div style="width:240px;flex:0 0 240px;display:flex;flex-direction:column;gap:.5rem;align-items:flex-start;">
              <!-- moved widget container inside the editor area so controls render under the label -->
              <div id="templates_widget_post" class="w-100"></div>
            </div>
          </div>
          <!-- Visual Templates Constructor (collapsible) -->
          <div class="card card-preview mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Constructor visual de plantillas</strong>
              </div>
              <div id="visualBuilderAreaPost" style="display:block;position:relative;border:1px solid #e6e6e6;padding:8px;border-radius:6px;min-height:260px">
                <script src="/js/templates-editor.js" defer></script>
                <div class="d-flex">
                  <div id="templatesPalettePost" class="d-flex flex-column me-3" style="width:260px;min-width:220px">
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
                    <div id="templatesEditorContainerPost" style="position:relative;min-height:180px">
                      <div id="templatesCanvasPost" class="templates-canvas" style="position:relative;min-height:220px;padding:12px;background:#fff;border:1px solid #f1f1f1;border-radius:6px"></div>
                      <pre id="templatesEditorHtmlPanePost" class="templates-htmlpane" style="display:none;margin:0;padding:12px;white-space:pre-wrap;overflow:auto;"></pre>
                    </div>
                    <textarea id="templatesEditorPost" name="data_html_post" style="display:none"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <input type="hidden" name="content" id="contentField">
        </div>
        <div class="mb-3"><label class="form-label">Estado</label>
          <select name="status" class="form-select form-select-sm">
            <option value="draft" <?= (isset($post['status']) && $post['status']==='draft')? 'selected':'' ?>>Borrador</option>
            <option value="published" <?= (isset($post['status']) && $post['status']==='published')? 'selected':'' ?>>Publicado</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Imagen destacada</label>
          <?php
            $meta = [];
            if (!empty($post['meta'])) {
              $meta = json_decode($post['meta'], true) ?: [];
            }
            $currentImage = $meta['image'] ?? ($post['image'] ?? '');
          ?>
          <?php if ($currentImage): ?>
            <div class="mb-2"><img src="<?= htmlspecialchars($currentImage) ?>" alt="Imagen actual" style="max-width:220px;height:auto;border-radius:6px;border:1px solid #ddd"></div>
          <?php endif; ?>
          <input type="file" name="image" accept="image/*" class="form-control form-control-sm">
          <div class="form-text">Sube una imagen para esta entrada (reemplaza la existente).</div>
        </div>
        <div class="mb-3"><label class="form-label">Fecha de publicación</label><input type="datetime-local" name="published_at" value="<?= isset($post['published_at'])? date('Y-m-d\TH:i', strtotime($post['published_at'])) : '' ?>" class="form-control form-control-sm"></div>
        <!-- Resources selector start -->
        <?php
          try {
            $resStmt = $pdo->query('SELECT id,slug,title,file_path,url FROM resources ORDER BY title ASC');
            $allResources = $resStmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (Exception $e) { $allResources = []; }
          $selectedResources = [];
          if (!empty($post['meta'])) { $tmp = json_decode($post['meta'], true); if (is_array($tmp) && !empty($tmp['resources']) && is_array($tmp['resources'])) $selectedResources = $tmp['resources']; }
        ?>
        <div class="mb-3 border-top pt-3">
          <h6 class="mb-2">Recursos asociados <a class="btn btn-sm btn-primary ms-2" href="/admin/resources/create">Agregar recurso</a></h6>
          <div class="small text-muted mb-2">Marca los recursos que deberían aparecer como relacionados en esta entrada.</div>
          <input type="hidden" name="resources_order" id="resources_order" value="<?= htmlspecialchars(implode(',', $selectedResources)) ?>">
          <div class="mb-2"><input id="resourcesFilter" class="form-control form-control-sm" placeholder="Filtrar recursos por título o slug..."></div>
          <div class="resources-panel">
            <?php if (empty($allResources)): ?>
              <div class="small text-muted">No hay recursos disponibles. Crea primero recursos en el menú "Recursos".</div>
            <?php else: ?>
              <div id="resourcesList" class="d-flex flex-column gap-2">
              <?php foreach ($allResources as $r): $isChecked = in_array($r['slug'], $selectedResources); $thumb = '/media/placeholder-thumb.svg'; try { if (!empty($r['file_path'])) $thumb = $r['file_path']; else if (!empty($r['url'])) $thumb = $r['url']; } catch(Exception $e){} ?>
                <div class="resource-item d-flex align-items-center p-2 border rounded" draggable="true" data-slug="<?= htmlspecialchars($r['slug']) ?>" data-id="<?= intval($r['id']) ?>">
                  <input class="form-check-input me-2" type="checkbox" value="<?= htmlspecialchars($r['slug']) ?>" id="res_post_<?= intval($r['id']) ?>" name="resources[]" <?= $isChecked ? 'checked' : '' ?> />
                  <img src="<?= htmlspecialchars($thumb) ?>" alt="thumb" class="resource-thumb"/>
                  <div style="flex:1;min-width:0">
                    <label class="form-check-label mb-0 text-truncate" for="res_post_<?= intval($r['id']) ?>"><?= htmlspecialchars($r['title'] ?: $r['slug']) ?></label>
                    <div class="small text-muted text-truncate"><?= htmlspecialchars($r['slug']) ?></div>
                  </div>
                  <div class="d-flex gap-1 align-items-center ms-2">
                    <a class="btn btn-sm btn-outline-light" href="/admin/resources/edit?id=<?= intval($r['id']) ?>" title="Editar recurso" target="_blank">Editar</a>
                    <button type="button" class="btn btn-sm btn-danger res-delete-btn" title="Eliminar recurso">Eliminar</button>
                  </div>
                  <div class="drag-handle text-muted" style="padding-left:.5rem" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M10 6h4M10 12h4M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </div>
                </div>
              <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <script>
        (function(){
          var list = document.getElementById('resourcesList');
          if (!list) return;
          var dragSrc = null;
          list.addEventListener('dragstart', function(e){ var it = e.target.closest('.resource-item'); if (!it) return; dragSrc = it; e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', it.dataset.slug || ''); it.style.opacity = '0.4'; });
          list.addEventListener('dragend', function(e){ if (dragSrc) dragSrc.style.opacity = ''; dragSrc = null; });
          list.addEventListener('dragover', function(e){ e.preventDefault(); var over = e.target.closest('.resource-item'); if (!over || over === dragSrc) return; var rect = over.getBoundingClientRect(); var after = (e.clientY - rect.top) > (rect.height/2); if (after) over.parentNode.insertBefore(dragSrc, over.nextSibling); else over.parentNode.insertBefore(dragSrc, over); });
          function updateOrder(){ var items = list.querySelectorAll('.resource-item'); var arr = []; items.forEach(function(it){ if (it) arr.push(it.dataset.slug || ''); }); document.getElementById('resources_order').value = arr.join(','); }
          list.addEventListener('change', updateOrder); list.addEventListener('pointerup', function(){ setTimeout(updateOrder,50); });
          var form = document.querySelector('form[action="/admin/save_post.php"]'); if (form) form.addEventListener('submit', function(){ updateOrder(); }); updateOrder();
        })();
        </script>

          <script>
          // AJAX delete for resources inside post editor
          (function(){
            var delBtns = document.querySelectorAll('.res-delete-btn');
            if (!delBtns || !delBtns.length) return;
            delBtns.forEach(function(b){ b.addEventListener('click', function(){
              if (!confirm('¿Eliminar este recurso? Esta acción no se puede deshacer.')) return;
              var item = b.closest('.resource-item'); if (!item) return;
              var id = item.dataset.id; if (!id) return;
              var fd = new FormData(); fd.append('id', id); fd.append('csrf', '<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>');
              fetch('/admin/resources/delete', { method: 'POST', body: fd }).then(function(r){ return r.text(); }).then(function(){
                item.parentNode && item.parentNode.removeChild(item);
                var list = document.getElementById('resourcesList'); if (list){ var arr = Array.from(list.querySelectorAll('.resource-item')).map(function(it){ return it.dataset.slug; }); document.getElementById('resources_order').value = arr.join(','); }
              }).catch(function(err){ alert('Error al eliminar: ' + (err.message || err)); });
            }); });
          })();
          </script>
        <script>
        (function(){ var input = document.getElementById('resourcesFilter'); var list = document.getElementById('resourcesList'); if (!input || !list) return; var timeout = null; input.addEventListener('input', function(e){ clearTimeout(timeout); timeout = setTimeout(function(){ var q = (input.value || '').toLowerCase().trim(); var items = list.querySelectorAll('.resource-item'); items.forEach(function(it){ var title = (it.querySelector('label')?.textContent || '').toLowerCase(); var slug = (it.dataset.slug || '').toLowerCase(); if (!q || title.indexOf(q) !== -1 || slug.indexOf(q) !== -1) { it.style.display = ''; } else { it.style.display = 'none'; } }); }, 180); }); })();
        </script>

        <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf']??'')?>">
        <button type="submit" class="btn btn-primary" onclick="document.getElementById('contentField').value=document.getElementById('editor').innerHTML">Guardar</button>
        
      </form>
    </section>
  </main>
</div>

<script>
var ed=document.getElementById('editor'); if(ed) ed.addEventListener('paste',function(e){ e.preventDefault(); var text=(e.clipboardData||window.clipboardData).getData('text/plain'); document.execCommand('insertText',false,text); });
</script>
<?php $slot = ob_get_clean(); include __DIR__ . '/../../views/layout.php';
