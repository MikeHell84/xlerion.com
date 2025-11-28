<?php
require_once __DIR__.'/../../src/Model/Template.php';
require_once __DIR__.'/../../src/Helpers/HtmlSanitizer.php';
$id = $_GET['id'] ?? null;
$t = $id ? Template::find($id) : null;
$availableTemplates = Template::findAll();
if ((($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') && $id){
  $raw_html = $_POST['data_html'] ?? '';
  $html = HtmlSanitizer::sanitize_html($raw_html);
  $data = $t && isset($t['data']) ? $t['data'] : [];
  $data['html'] = $html;
  Template::update($id, ['name'=>$_POST['name'],'description'=>$_POST['description'],'data'=>$data]);
  header('Location: /admin/templates/edit.php?id='.$id);
  exit;
}
?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview"><?php include __DIR__ . '/../../public/admin/_nav.php'; ?></aside>
  <main class="admin-main">
    <div class="card-preview">
      <h2 class="section-title-lg">Editar plantilla</h2>
      <form method="post" onsubmit="TemplatesEditor.syncBeforeSubmit(event)">
        <?php if (!empty($t['id'])): ?>
          <input type="hidden" id="templatesCurrentId" value="<?php echo htmlspecialchars($t['id']); ?>" />
        <?php endif; ?>
        <!-- Load site CSS and fallback editor styles for canvas preview -->
        <link rel="stylesheet" href="/css/styles.css">
        <link rel="stylesheet" href="/xlerion_cmr/public/styles.css">
        <link rel="stylesheet" href="/css/templates-editor-fallback.css">
        <div class="d-flex gap-3">
          <aside style="width:260px;min-width:220px;">
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
                <div class="mt-2 small text-muted">Arrastra o haz clic para insertar en el editor.</div>
              </div>
              <div class="mt-2 small text-muted">Arrastra o haz clic para insertar en el editor.</div>
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
          <input class="form-control" name="name" value="<?php echo htmlspecialchars($t['name'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <input class="form-control" name="description" value="<?php echo htmlspecialchars($t['description'] ?? ''); ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Maqueta (Editor visual)</label>
          <div class="templates-editor-toolbar mb-2">
            <button type="button" class="btn btn-sm btn-outline-light" data-action="toggle-outline">Mostrar contornos</button>
            <button type="button" class="btn btn-sm btn-outline-light" data-action="toggle-preview">Previsualizar HTML</button>
            <button type="button" class="btn btn-sm btn-outline-light" id="templatesRealPreviewBtn">Preview real</button>
            <button type="button" class="btn btn-sm btn-outline-light" data-action="clear">Limpiar</button>
          </div>
  </div>

            <div class="d-flex gap-2" style="min-height:220px;">
            <div id="templatesViewer" style="flex:1;display:flex;flex-direction:column;border:1px solid var(--card-border);border-radius:6px;overflow:hidden;min-height:480px;">
              <div class="p-1 d-flex gap-1 border-bottom" style="background:var(--card-bg)">
                <button type="button" id="btnViewVisual" class="btn btn-sm btn-primary" data-action="view-visual">Visual</button>
                <button type="button" id="btnViewHtml" class="btn btn-sm btn-outline-light" data-action="view-html">HTML</button>
              </div>
              <div style="position:relative;flex:1 1 auto;min-height:220px">
                <div id="templatesCanvas" class="templates-canvas" style="position:absolute;inset:0;width:100%;height:100%;overflow:auto;"></div>
                <pre id="templatesEditorHtmlPane" class="templates-htmlpane" style="position:absolute;inset:0;display:none;margin:0;padding:12px;white-space:pre-wrap;overflow:auto;"></pre>
              </div>
            </div>
          </div>
          <!-- Action buttons placed outside the absolute canvas so they remain visible -->
          <div class="mb-3 d-flex gap-2" style="margin-top:8px;">
            <button class="btn btn-sm btn-primary" type="submit">Guardar</button>
            <button type="button" id="btnOpenPreviewWindow" class="btn btn-sm btn-outline-light">Abrir en ventana</button>
            <?php if (!empty($t['id'])): ?>
              <button type="button" id="btnDeleteTemplate" class="btn btn-sm btn-danger">Borrar plantilla</button>
            <?php endif; ?>
          </div>
          <textarea id="templatesEditor" name="data_html" style="display:none"><?php echo htmlspecialchars(isset($t['html']) ? $t['html'] : (isset($t['data']['html']) ? $t['data']['html'] : '<p><em>Escribe aquí la maqueta...</em></p>')); ?></textarea>
          <style>
            /* Editor outline helpers: apply to the editor element itself and its children (textarea needs direct outline) */
            #templatesEditor.templates-editor-outline, #templatesEditor.templates-editor-outline * { outline: 1px dashed rgba(0,128,192,0.45); }
            #templatesEditor .placeholder-img{opacity:.95;border:1px dashed rgba(0,0,0,0.06);padding:4px;border-radius:6px}
            /* block reorder visuals - keep inner layout intact (no borders/padding that change box model) */
            #templatesEditor .tpl-block{ border:0; padding:0; margin:8px 0; position:relative; display:block; box-sizing:border-box; width:100%; }
            #templatesEditor .tpl-block.tpl-drop-before{ outline:3px solid rgba(0,123,255,0.18); }
            #templatesEditor .tpl-block.is-dragging{ opacity:0.6; }
            #templatesEditor .tpl-handle{ position:absolute; left:0; top:0; width:18px; bottom:0; background:transparent; cursor:grab; }
            #templatesEditor .tpl-handle:active{ cursor:grabbing; }
            #templatesEditor .tpl-ghost{ box-shadow:0 6px 18px rgba(0,0,0,0.18); border-radius:6px; opacity:.95; }
            #templatesEditor .tpl-block img{ max-width:100%; height:auto; display:block; }
            #templatesEditor .tpl-block.just-inserted{ min-height:34px; }
            #templatesEditor .tpl-block.tpl-neutral{ padding:0; margin:8px 0; border:0; }
            /* Editor-scoped button sizing: keep buttons compact and match text height */
            .templates-editor-toolbar .btn, #templatesViewer .btn, .tpl-controls .btn { padding: .25rem .45rem; line-height: 1.1; font-size: .86rem; }
            .tpl-controls .btn { padding: .18rem .35rem; }
          </style>

          <small class="text-muted d-block mt-2">El HTML aquí será guardado y reutilizable en nuevas páginas.</small>
        </div>

        
      </form>
    </div>
    <script>
      window.__XLERION_AVAILABLE_TEMPLATES = <?php echo json_encode(array_map(function($t){ return [
        'id'=>$t['id'],
        'name'=>$t['name'] ?? '',
        'description'=>$t['description'] ?? '',
        'html'=>isset($t['data']['html'])?$t['data']['html']:($t['html']??'')
      ]; }, $availableTemplates), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
  window.__XLERION_ALLOWED_CLASSES = <?php echo json_encode(\HtmlSanitizer::getAllowedClasses(), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
  window.__XLERION_ALLOWED_CLASS_PATTERNS = <?php echo json_encode(\HtmlSanitizer::getAllowedClassPatterns(), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
    </script>
  <script src="/js/templates-editor.js" defer></script>
  </main>
</div>
