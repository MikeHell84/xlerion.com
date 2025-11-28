<?php
require_once __DIR__.'/../../src/Model/Template.php';
require_once __DIR__.'/../../src/Helpers/HtmlSanitizer.php';
// fetch available templates for client-side loader
$availableTemplates = Template::findAll();
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'){
  // take HTML from the visual editor (data_html), sanitize and store inside data.json as ['html' => ...]
  $raw_html = $_POST['data_html'] ?? '';
  $html = HtmlSanitizer::sanitize_html($raw_html);
  $data = ['html' => $html];
  $id = Template::create([ 'name'=>$_POST['name'], 'description'=>$_POST['description'], 'author_id'=>$_SESSION['user_id'] ?? null, 'data'=>$data ]);
  header('Location: /admin/templates/edit.php?id='.$id);
  exit;
}
?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview"><?php include __DIR__ . '/../../public/admin/_nav.php'; ?></aside>
  <main class="admin-main">
    <div class="card-preview">
      <h2 class="section-title-lg">Nueva plantilla</h2>
      <form method="post" onsubmit="TemplatesEditor.syncBeforeSubmit(event)">
        <!-- Load site CSS and fallback editor styles for canvas preview -->
        <link rel="stylesheet" href="/css/styles.css">
        <link rel="stylesheet" href="/xlerion_cmr/public/styles.css">
        <link rel="stylesheet" href="/css/templates-editor-fallback.css">
        <div class="d-flex gap-3">
          <aside style="width:300px;min-width:220px;">
            <div class="card-preview p-2">
              <h4 class="mb-2">Paleta de componentes</h4>
              <div id="templatesPalette" class="d-flex flex-column gap-2">
                <div class="palette-group mb-2">
                  <h6 class="mb-1">Acciones rápidas</h6>
                  <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="hero" draggable="true">Insertar Hero</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="card" draggable="true">Insertar Card</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="two-col" draggable="true">Insertar Grid 2-col</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="header" draggable="true">Insertar Header</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="footer" draggable="true">Insertar Footer</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="section" draggable="true">Insertar Sección</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="resource" draggable="true">Insertar Recurso</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="image" draggable="true">Insertar Imagen</button>
                  </div>
                </div>
                <div class="palette-group mb-2">
                  <h6 class="mb-1">Estructura</h6>
                  <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="header" draggable="true">Header</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="hero" draggable="true">Hero</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="section" draggable="true">Section</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="footer" draggable="true">Footer</button>
                  </div>
                </div>
                <div class="palette-group mb-2">
                  <h6 class="mb-1">Layouts</h6>
                  <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="two-col" draggable="true">Grid 2-col</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="card" draggable="true">Card</button>
                  </div>
                </div>
                <div class="palette-group mb-2">
                  <h6 class="mb-1">Media y recursos</h6>
                  <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="image" draggable="true">Imagen</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="video" draggable="true">Video</button>
                    <button type="button" class="btn btn-sm btn-outline-light palette-item" data-insert="resource" draggable="true">Resource</button>
                  </div>
                </div>
                <div class="mt-2 small text-muted">Arrastra o haz clic para insertar en el editor. Las secciones agrupan componentes por función.</div>
              </div>
            </div>
          </aside>
          <div style="flex:1;">
        <div class="mb-3">
          <label class="form-label">Cargar plantilla existente</label>
          <div class="d-flex gap-2">
            <select id="templatesLoadSelect" class="form-select">
              <option value="">-- seleccionar --</option>
            </select>
            <button type="button" id="templatesLoadBtn" class="btn btn-outline-light">Cargar</button>
          </div>
          <small class="text-muted">Carga una maqueta existente para editarla o reutilizarla.</small>
        </div>
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input class="form-control" name="name" value="" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <input class="form-control" name="description" value="">
        </div>

        <div class="mb-3">
          <label class="form-label">Maqueta (Editor visual)</label>
                  <div class="templates-editor-toolbar mb-2 d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-light" data-action="toggle-outline">Mostrar contornos</button>
                    <button type="button" class="btn btn-sm btn-outline-light" data-action="toggle-preview">Previsualizar HTML</button>
                    <button type="button" class="btn btn-sm btn-outline-light" data-action="clear">Limpiar</button>
                    <div class="text-muted small">(Usa la paleta a la izquierda para insertar componentes)</div>
                  </div>

                  <div class="d-flex gap-2" style="min-height:220px;">
                    <div id="templatesViewer" style="flex:1;display:flex;flex-direction:column;border:1px solid var(--card-border);border-radius:6px;overflow:hidden;min-height:480px;">
                      <div class="p-1 d-flex gap-1 border-bottom" style="background:var(--card-bg)">
                        <button type="button" id="btnViewVisual" class="btn btn-sm btn-primary" data-action="view-visual">Visual</button>
                        <button type="button" id="btnViewHtml" class="btn btn-sm btn-outline-light" data-action="view-html">HTML</button>
                      </div>
                      <div style="position:relative;flex:1 1 auto;min-height:220px">
                        <div id="templatesCanvas" class="templates-canvas" style="position:absolute;inset:0;width:100%;height:100%;overflow:auto;"></div>
                        <!-- Template for block controls (inserted into each .tpl-block) -->
                        <template id="tplBlockControls">
                          <div class="tpl-controls" style="position:absolute;right:8px;top:8px;z-index:40;display:flex;flex-direction:column;gap:6px">
                            <button type="button" class="btn btn-sm btn-outline-light tpl-move-up" title="Subir">↑</button>
                            <button type="button" class="btn btn-sm btn-outline-light tpl-move-down" title="Bajar">↓</button>
                            <button type="button" class="btn btn-sm btn-outline-light tpl-remove" title="Eliminar">✕</button>
                          </div>
                        </template>
                        <pre id="templatesEditorHtmlPane" class="templates-htmlpane" style="position:absolute;inset:0;display:none;margin:0;padding:12px;white-space:pre-wrap;overflow:auto;"></pre>
                      </div>
                      </div>
                    </div>
                  </div>
                  <!-- Utilities: preview / clear (separated from insert toolbar) -->
                  <div class="mb-3 d-flex gap-2" style="margin-top:8px;">
                    <button type="button" class="btn btn-sm btn-outline-light" data-action="toggle-preview">Previsualizar HTML</button>
                    <button type="button" id="templatesRealPreviewBtn" class="btn btn-sm btn-outline-light">Preview real</button>
                    <button type="button" class="btn btn-sm btn-outline-light" data-action="clear">Limpiar</button>
                    <div style="flex:1"></div>
                    <button class="btn btn-sm btn-primary" type="submit">Crear</button>
                    <button type="button" id="btnOpenPreviewWindow" class="btn btn-sm btn-outline-light">Abrir en ventana</button>
                  </div>
                    </div>
                  </div>
                  <textarea id="templatesEditor" name="data_html" style="display:none"></textarea>
                  <style>
                    /* Editor outline helpers: apply to the editor element itself and its children (textarea needs direct outline) */
                    #templatesEditor.templates-editor-outline, #templatesEditor.templates-editor-outline * { outline: 1px dashed rgba(0,128,192,0.45); }
                    /* Also allow toggling outlines on the visual canvas container so the button affects the visible preview */
                    #templatesCanvas.templates-editor-outline, #templatesCanvas.templates-editor-outline * { outline: 1px dashed rgba(0,128,192,0.45); }
                    #templatesEditor .placeholder-img{opacity:.95;border:1px dashed rgba(0,0,0,0.06);padding:4px;border-radius:6px}
                    /* block reorder visuals */
                    /* keep wrapper neutral so Bootstrap components keep their own box model */
                    #templatesEditor .tpl-block{ border:0; padding:0; margin:8px 0; position:relative; display:block; box-sizing:border-box; width:100%; }
                    /* use outline for drop hint so we don't alter inner layout (avoid borders/padding) */
                    #templatesEditor .tpl-block.tpl-drop-before{ outline:3px solid rgba(0,123,255,0.18); }
                    #templatesEditor .tpl-block.is-dragging{ opacity:0.6; }
                    #templatesEditor .tpl-handle{ position:absolute; left:0; top:0; width:18px; bottom:0; background:transparent; cursor:grab; }
                    #templatesEditor .tpl-handle:active{ cursor:grabbing; }
                    #templatesEditor .tpl-ghost{ box-shadow:0 6px 18px rgba(0,0,0,0.18); border-radius:6px; opacity:.95; }
                    /* ensure images inside blocks scale properly, but avoid forcing max-width on all children (breaks .row gutters) */
                    #templatesEditor .tpl-block img{ max-width:100%; height:auto; display:block; }
                    #templatesEditor .tpl-block.just-inserted{ min-height:34px; }
                    #templatesEditor .tpl-block.tpl-neutral{ padding:0; margin:8px 0; border:0; }
                    /* Editor-scoped button sizing: keep buttons compact and match text height */
                    .templates-editor-toolbar .btn, #templatesViewer .btn, .tpl-controls .btn { padding: .25rem .45rem; line-height: 1.1; font-size: .86rem; }
                    .tpl-controls .btn { padding: .18rem .35rem; }
                    /* dark mode canvas adjustments */
                    :root.theme-dark #templatesCanvas, :root.theme-dark #templatesEditor.templates-editor-outline { background: #0b0b0b; color: #e6eef6; }
                    :root.theme-dark #templatesCanvas .tpl-block, :root.theme-dark #templatesEditor .tpl-block { background: #101214; color: #e6eef6; }
                    :root.theme-dark #templatesCanvas .tpl-content, :root.theme-dark #templatesEditor .tpl-content { color: #e6eef6; }
                    /* Palette group headings */
                    #templatesPalette .palette-group h6 { margin:0; font-size:0.86rem; color:var(--muted); }
                  </style>

                  <small class="text-muted d-block mt-2">El HTML aquí será guardado y reutilizable en nuevas páginas.</small>
                </div>

          <!-- Editor (single instance) -->
        </div>

        
      </form>
    </div>
    <script>
      // embed available templates for the editor to load
      window.__XLERION_AVAILABLE_TEMPLATES = <?php echo json_encode(array_map(function($t){ return ['id'=>$t['id'],'name'=>$t['name'],'html'=>isset($t['data']['html'])?$t['data']['html']:($t['html']??'')]; }, $availableTemplates), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
  window.__XLERION_ALLOWED_CLASSES = <?php echo json_encode(\HtmlSanitizer::getAllowedClasses(), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
  window.__XLERION_ALLOWED_CLASS_PATTERNS = <?php echo json_encode(\HtmlSanitizer::getAllowedClassPatterns(), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
    </script>
    <script src="/js/templates-editor.js" defer></script>
    <script>
      // Show preview inside a modal overlay with a sandboxed iframe. Provide an option to open in a new window.
      (function(){
        // Ensure modal styles exist
        if (!document.getElementById('tpl-preview-modal-style')){
          var st = document.createElement('style'); st.id = 'tpl-preview-modal-style';
          st.textContent = '\n#templatesRealPreviewModal{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;z-index:1050}\n#templatesRealPreviewModal .tpl-modal-backdrop{position:absolute;inset:0;background:rgba(0,0,0,0.55)}\n#templatesRealPreviewModal .tpl-modal-window{position:relative;width:90%;height:85%;max-width:1100px;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.3);z-index:1060;display:flex;flex-direction:column}\n#templatesRealPreviewModal .tpl-modal-header{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#f6f6f6;border-bottom:1px solid rgba(0,0,0,0.06)}\n#templatesRealPreviewModal .tpl-modal-title{font-weight:600}\n#templatesRealPreviewModal .tpl-modal-body{flex:1;display:block;padding:0;background:#fff}\n#templatesRealPreviewModal iframe{border:0;width:100%;height:100%}\n#templatesRealPreviewModal .tpl-modal-actions{display:flex;gap:8px}\n';
          document.head.appendChild(st);
        }

        function createModal(){
          var modal = document.createElement('div'); modal.id = 'templatesRealPreviewModal'; modal.style.display = 'none';
          modal.innerHTML = '\n+            <div class="tpl-modal-backdrop" data-close="1"></div>\n+            <div class="tpl-modal-window">\n+              <div class="tpl-modal-header">\n+                <div class="tpl-modal-title">Previsualización</div>\n+                <div class="tpl-modal-actions">\n+                  <button type="button" class="btn btn-sm btn-outline-light" id="tplPreviewOpenWindow">Abrir en ventana</button>\n+                  <button type="button" class="btn btn-sm btn-outline-danger" id="tplPreviewClose">Cerrar</button>\n+                </div>\n+              </div>\n+              <div class="tpl-modal-body"><iframe id="tplPreviewIframe" sandbox="allow-same-origin allow-forms" ></iframe></div>\n+            </div>\n+';
          document.body.appendChild(modal);
          // handlers
          modal.querySelector('[data-close]')?.addEventListener('click', function(){ hideModal(); });
          modal.querySelector('#tplPreviewClose')?.addEventListener('click', function(){ hideModal(); });
          modal.querySelector('#tplPreviewOpenWindow')?.addEventListener('click', function(){
            try{
              var html = document.getElementById('templatesEditor')?.value || '';
              var w = window.open('', '_blank');
              if (!w) return alert('No se pudo abrir la ventana. Revisa bloqueadores.');
              var doc = w.document; doc.open(); doc.write('<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Preview</title></head><body style="margin:0">');
              doc.write(html);
              doc.write('</body></html>'); doc.close();
            }catch(e){ alert('Error al abrir en ventana: ' + (e && e.message)); }
          });
          return modal;
        }

        function showModal(){
          var modal = document.getElementById('templatesRealPreviewModal') || createModal();
          var iframe = modal.querySelector('#tplPreviewIframe');
          var html = document.getElementById('templatesEditor')?.value || '';
          try{ iframe.contentDocument.open(); iframe.contentDocument.write(html); iframe.contentDocument.close(); }catch(e){
            // fallback: set srcdoc if available
            try{ iframe.setAttribute('srcdoc','' + html); }catch(ee){}
          }
          modal.style.display = 'flex';
        }

        function hideModal(){ var m = document.getElementById('templatesRealPreviewModal'); if (m) m.style.display='none'; }

        document.getElementById('templatesRealPreviewBtn')?.addEventListener('click', function(){ showModal(); });
      })();
    </script>
  </main>
</div>
