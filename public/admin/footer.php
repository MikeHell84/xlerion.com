<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$pdo = Database::pdo();

$errors = [];
$saved = false;
// ensure $data exists to avoid undefined variable notices when actions run without form payload
$data = [];
// ensure settings table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS settings (k TEXT PRIMARY KEY, v TEXT)");
// create footer_variants table to store reusable footer configurations
$pdo->exec("CREATE TABLE IF NOT EXISTS footer_variants (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, data TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  // handle variant actions: create/update/delete/activate
  if (!empty($_POST['variant_action'])){
    $va = $_POST['variant_action'];
    if ($va === 'create' && !empty($_POST['variant_name'])){
      // Create a snapshot of current footer settings from DB (safe when form fields aren't posted)
      $name = trim($_POST['variant_name']);
      $snapshot = [];
      try{
        $ss = $pdo->query("SELECT k,v FROM settings WHERE k LIKE 'footer_%'");
        while($r = $ss->fetch(PDO::FETCH_ASSOC)) { $snapshot[$r['k']] = $r['v']; }
      } catch(Exception $e){ /* ignore and fall back to any posted $data */ }
      if (empty($snapshot) && !empty($data)) { $snapshot = $data; }
      $row = json_encode($snapshot, JSON_UNESCAPED_UNICODE);
      $ins = $pdo->prepare('INSERT INTO footer_variants (name,data) VALUES(?,?)');
      $ins->execute([$name,$row]);
      $saved = true;
    }
    if ($va === 'update' && !empty($_POST['variant_id'])){
      // Update variant: prefer posted $data, otherwise snapshot current settings
      $id = intval($_POST['variant_id']);
      $snapshot = [];
      try{
        $ss = $pdo->query("SELECT k,v FROM settings WHERE k LIKE 'footer_%'");
        while($r = $ss->fetch(PDO::FETCH_ASSOC)) { $snapshot[$r['k']] = $r['v']; }
      } catch(Exception $e){ /* ignore */ }
      if (empty($snapshot) && !empty($data)) { $snapshot = $data; }
      $row = json_encode($snapshot, JSON_UNESCAPED_UNICODE);
      $up = $pdo->prepare('UPDATE footer_variants SET name = ?, data = ? WHERE id = ?');
      $up->execute([trim($_POST['variant_name'] ?? ''), $row, $id]);
      $saved = true;
    }
    if ($va === 'delete' && !empty($_POST['variant_id'])){
      $id = intval($_POST['variant_id']);
      $pdo->prepare('DELETE FROM footer_variants WHERE id = ?')->execute([$id]);
      // if deleted variant was active, clear active_footer_id
      $active = $pdo->query("SELECT v FROM settings WHERE k='active_footer_id'")->fetchColumn();
      if ($active && intval($active) === $id){ $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)')->execute(['active_footer_id','']); }
      $saved = true;
    }
    if ($va === 'deactivate'){
      // Clear active_footer_id only if it matches provided id (or clear unconditionally)
      $id = isset($_POST['variant_id']) ? intval($_POST['variant_id']) : null;
      $active = $pdo->query("SELECT v FROM settings WHERE k='active_footer_id'")->fetchColumn();
      if ($id === null || ($active && intval($active) === $id)){
        $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)')->execute(['active_footer_id','']);
        $saved = true;
      } else {
        $errors[] = 'La variante solicitada no está activa.';
      }
    }
    if ($va === 'activate' && !empty($_POST['variant_id'])){
      $id = intval($_POST['variant_id']);
      $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)')->execute(['active_footer_id',(string)$id]);
      $saved = true;
    }
  }
  if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')){ $errors[] = 'CSRF inválido'; }

  $data = [];
  $data['footer_heading'] = trim($_POST['footer_heading'] ?? '');
  $data['footer_text'] = trim($_POST['footer_text'] ?? '');
  $data['footer_email'] = trim($_POST['footer_email'] ?? '');
  $data['footer_phone'] = trim($_POST['footer_phone'] ?? '');
  $data['footer_address'] = trim($_POST['footer_address'] ?? '');
  $data['footer_schedule'] = trim($_POST['footer_schedule'] ?? '');
  $data['footer_contact_cta_label'] = trim($_POST['footer_contact_cta_label'] ?? 'Enviar mensaje');
  $data['footer_contact_cta_url'] = trim($_POST['footer_contact_cta_url'] ?? '/contact');
  $data['footer_projects_cta_label'] = trim($_POST['footer_projects_cta_label'] ?? 'Ver proyectos');
  $data['footer_projects_cta_url'] = trim($_POST['footer_projects_cta_url'] ?? '/proyectos');
  $data['footer_links'] = trim($_POST['footer_links'] ?? ''); // stored as newline list (Texto|/ruta)
  $data['footer_social_linkedin'] = trim($_POST['footer_social_linkedin'] ?? '');
  $data['footer_social_instagram'] = trim($_POST['footer_social_instagram'] ?? '');
  $data['footer_social_facebook'] = trim($_POST['footer_social_facebook'] ?? '');
  $data['footer_social_behance'] = trim($_POST['footer_social_behance'] ?? '');
  $data['footer_social_indiegogo'] = trim($_POST['footer_social_indiegogo'] ?? '');
  $data['footer_social_kickstarter'] = trim($_POST['footer_social_kickstarter'] ?? '');
  $data['footer_social_patreon'] = trim($_POST['footer_social_patreon'] ?? '');
  $data['footer_subscribe_label'] = trim($_POST['footer_subscribe_label'] ?? 'Recibe novedades y convocatorias por correo.');
  $data['footer_subscribe_action'] = trim($_POST['footer_subscribe_action'] ?? '/subscribe');
  $data['footer_show_newsletter'] = isset($_POST['footer_show_newsletter']) ? '1' : '0';
  $data['footer_copyright'] = trim($_POST['footer_copyright'] ?? '© Xlerion - Todos los derechos reservados');

  if ($data['footer_email'] && !filter_var($data['footer_email'], FILTER_VALIDATE_EMAIL)){
    $errors[] = 'Email de footer no válido';
  }
  // validate social urls loosely (allow empty)
  $socials = [
    'footer_social_linkedin','footer_social_instagram','footer_social_facebook','footer_social_behance','footer_social_indiegogo','footer_social_kickstarter','footer_social_patreon'
  ];
  foreach($socials as $s){ if (!empty($data[$s]) && !filter_var($data[$s], FILTER_VALIDATE_URL)) { $errors[] = "URL inválida en $s"; break; } }

  if (empty($errors)){
    try{
      $up = $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)');
      foreach($data as $k=>$v){ $up->execute([$k, (string)$v]); }
      $saved = true;
    } catch (Exception $e){ $errors[] = 'No se pudo guardar: '.$e->getMessage(); }
  }
}

// Built-in templates: allow quick creation of a variant from a stored template
$builtTemplates = [
  'Default company' => [
    'footer_heading' => 'Contacto',
    'footer_text' => 'Conecta con nosotros para empezar tu proyecto.',
    'footer_email' => 'contactus@xlerion.com',
    'footer_phone' => '+57 320 860 5600',
    'footer_address' => 'Nocaima, Cundinamarca',
    'footer_schedule' => 'Horario: Lun–Vie, 9:00–18:00',
    'footer_contact_cta_label' => 'Enviar mensaje',
    'footer_contact_cta_url' => '/contact',
    'footer_projects_cta_label' => 'Ver proyectos',
    'footer_projects_cta_url' => '/proyectos',
    'footer_links' => "Inicio|/\nProyectos|/proyectos\nBlog|/blog\nEquipo|/equipo\nDocumentación|/documentacion",
    'footer_social_linkedin' => 'https://www.linkedin.com/company/xlerion',
    'footer_social_instagram' => 'https://www.instagram.com/ultimatexlerion/',
    'footer_social_facebook' => 'https://www.facebook.com/xlerionultimate',
    'footer_social_behance' => 'https://www.behance.net/xlerionultimate',
    'footer_subscribe_label' => 'Suscríbete para novedades',
    'footer_subscribe_action' => '/subscribe',
    'footer_show_newsletter' => '1',
    'footer_copyright' => '© Xlerion - Todos los derechos reservados'
  ]
];

// Handle creating or saving a variant from a template or from a saved variant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!empty($_POST['create_from_template']) || !empty($_POST['save_template_as_variant']) || !empty($_POST['use_template']) || !empty($_POST['use_template_save']))){
  // value comes from the select named 'use_template' and may be like 'tpl:Name' or 'var:ID' or legacy 'Name'
  $sel = $_POST['use_template'] ?? '';
  // determine if this action should activate the created variant
  $saveOnly = !empty($_POST['save_template_as_variant']) || !empty($_POST['use_template_save']);
  $activate = !$saveOnly;

  try{
    if (strpos($sel, 'tpl:') === 0){
      $tname = substr($sel, 4);
      if (!isset($builtTemplates[$tname])){ $errors[] = 'Plantilla desconocida'; }
      else {
        $tpl = $builtTemplates[$tname];
        $ins = $pdo->prepare('INSERT INTO footer_variants (name,data) VALUES(?,?)');
        $ins->execute([$tname, json_encode($tpl, JSON_UNESCAPED_UNICODE)]);
        $newId = $pdo->lastInsertId();
        if ($newId && $activate){ $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)')->execute(['active_footer_id', (string)$newId]); }
        header('Location: /admin/footer.php'); exit;
      }
    } elseif (strpos($sel, 'var:') === 0){
      $vid = intval(substr($sel, 4));
      $stmt = $pdo->prepare('SELECT name,data FROM footer_variants WHERE id = ?');
      $stmt->execute([$vid]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$row){ $errors[] = 'Variante no encontrada'; }
      else {
        // Duplicate the selected variant (create new row) using existing data
        $newName = $row['name'] . ' (copia)';
        $ins = $pdo->prepare('INSERT INTO footer_variants (name,data) VALUES(?,?)');
        $ins->execute([$newName, $row['data']]);
        $newId = $pdo->lastInsertId();
        if ($newId && $activate){ $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)')->execute(['active_footer_id', (string)$newId]); }
        header('Location: /admin/footer.php'); exit;
      }
    } else {
      // legacy: allow selecting by template name (no prefix)
      $tname = trim($sel);
      if ($tname !== '' && isset($builtTemplates[$tname])){
        $tpl = $builtTemplates[$tname];
        $ins = $pdo->prepare('INSERT INTO footer_variants (name,data) VALUES(?,?)');
        $ins->execute([$tname, json_encode($tpl, JSON_UNESCAPED_UNICODE)]);
        $newId = $pdo->lastInsertId();
      
        if ($newId && $activate){ $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)')->execute(['active_footer_id', (string)$newId]); }
        header('Location: /admin/footer.php'); exit;
      }
      $errors[] = 'Plantilla o variante no válida';
    }
  } catch(Exception $e){ $errors[] = 'No se pudo crear/guardar la variante: '.$e->getMessage(); }
}

// load current settings
$current = [];
try{ $stmt = $pdo->query("SELECT k,v FROM settings"); while($r = $stmt->fetch(PDO::FETCH_ASSOC)) $current[$r['k']] = $r['v']; } catch(Exception $e) {}
$get = function($k,$def='') use ($current){ return isset($current[$k]) ? $current[$k] : $def; };

$title = 'Footer';
$banner_title_class = 'section-title-lg';
// load variants early so templates dropdown can include saved variants
$variants = [];
try{ $variants = $pdo->query('SELECT id,name,data,created_at FROM footer_variants ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e) {}
$activeId = $pdo->query("SELECT v FROM settings WHERE k='active_footer_id'")->fetchColumn();
ob_start();
?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview">
    <?php include __DIR__ . '/_nav.php'; ?>
  </aside>

  <main class="admin-main">
    <header class="admin-header">
      <h1 class="section-title-lg"><?= htmlspecialchars($title) ?></h1>
      <p class="text-muted">Edita los textos y enlaces del pie de página del sitio.</p>
    </header>

    <section class="mt-3">
      <?php if ($saved): ?><div class="card-preview">Guardado.</div><?php endif; ?>
      <?php if (!empty($errors)): foreach($errors as $e): ?><div class="card-preview text-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; endif; ?>

      <form method="post">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">

        <div class="mb-3">
          <label>Encabezado del footer</label>
          <input name="footer_heading" value="<?= htmlspecialchars($get('footer_heading','')) ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label>Texto del footer (HTML permitido)</label>
          <div class="d-flex align-items-center gap-2 mb-2">
            <div id="templates_widget_footer" class="me-2"></div>
            <span class="small text-muted">Insertar o crear plantillas desde el texto del footer</span>
          </div>
          <!-- Visual Templates Constructor (collapsible) -->
          <div class="card card-preview mt-2 mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Constructor visual de plantillas (Footer)</strong>
              </div>
              <div id="visualBuilderAreaFooter" style="display:block;position:relative;border:1px solid #e6e6e6;padding:8px;border-radius:6px;min-height:260px">
                <script src="/js/templates-editor.js" defer></script>
                <div class="d-flex">
                  <div id="templatesPaletteFooter" class="d-flex flex-column me-3" style="width:260px;min-width:220px">
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
                    <div id="templatesEditorContainerFooter" style="position:relative;min-height:180px">
                      <div id="templatesCanvasFooter" class="templates-canvas" style="position:relative;min-height:220px;padding:12px;background:#fff;border:1px solid #f1f1f1;border-radius:6px"></div>
                      <pre id="templatesEditorHtmlPaneFooter" class="templates-htmlpane" style="display:none;margin:0;padding:12px;white-space:pre-wrap;overflow:auto;"></pre>
                    </div>
                    <textarea id="templatesEditorFooter" name="data_html_footer" style="display:none"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <textarea name="footer_text" id="footer_text" class="form-control" rows="4"><?= htmlspecialchars($get('footer_text','')) ?></textarea>
          <div class="card card-preview mt-2 p-2">
            <div class="d-flex justify-content-between align-items-center mb-2"><strong>Esquema / Plantilla</strong>
              <div class="d-flex gap-2">
                <button type="button" id="tplCopyFromFooter" class="btn btn-sm btn-outline-secondary">Copiar desde el texto</button>
                <button type="button" id="tplApplyToFooter" class="btn btn-sm btn-outline-primary">Aplicar al texto</button>
                <button type="button" id="tplSaveFooterAsTemplate" class="btn btn-sm btn-success">Guardar como plantilla</button>
              </div>
            </div>
            <div class="row g-2">
              <div class="col-12 col-md-4"><label class="small">Header (HTML)</label><textarea id="tpl_region_header" class="form-control form-control-sm" rows="3"></textarea></div>
              <div class="col-12 col-md-4"><label class="small">Contenido (HTML)</label><textarea id="tpl_region_content" class="form-control form-control-sm" rows="3"></textarea></div>
              <div class="col-12 col-md-4"><label class="small">Footer (HTML)</label><textarea id="tpl_region_footer" class="form-control form-control-sm" rows="3"></textarea></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6 mb-3">
            <label>Email de contacto en footer</label>
            <input name="footer_email" value="<?= htmlspecialchars($get('footer_email','')) ?>" class="form-control" type="email">
          </div>
          <div class="col-12 col-md-6 mb-3">
            <label>Teléfono</label>
            <input name="footer_phone" value="<?= htmlspecialchars($get('footer_phone','')) ?>" class="form-control">
          </div>
        </div>

        <div class="mb-3">
          <label>Dirección</label>
          <input name="footer_address" value="<?= htmlspecialchars($get('footer_address','Nocaima, Cundinamarca')) ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label>Horario / meta info</label>
          <input name="footer_schedule" value="<?= htmlspecialchars($get('footer_schedule','Horario: Lun–Vie, 9:00–18:00 · Respuesta en 24‑48 horas')) ?>" class="form-control">
        </div>

        <div class="row">
          <div class="col-12 col-md-6 mb-3">
            <label>Etiqueta CTA contacto</label>
            <input name="footer_contact_cta_label" value="<?= htmlspecialchars($get('footer_contact_cta_label','Enviar mensaje')) ?>" class="form-control">
          </div>
          <div class="col-12 col-md-6 mb-3">
            <label>URL CTA contacto</label>
            <input name="footer_contact_cta_url" value="<?= htmlspecialchars($get('footer_contact_cta_url','/contact')) ?>" class="form-control">
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-6 mb-3">
            <label>Etiqueta CTA proyectos</label>
            <input name="footer_projects_cta_label" value="<?= htmlspecialchars($get('footer_projects_cta_label','Ver proyectos')) ?>" class="form-control">
          </div>
          <div class="col-12 col-md-6 mb-3">
            <label>URL CTA proyectos</label>
            <input name="footer_projects_cta_url" value="<?= htmlspecialchars($get('footer_projects_cta_url','/proyectos')) ?>" class="form-control">
          </div>
        </div>

        <div class="mb-3">
          <label>Enlaces del footer (una por línea, formato: Texto|/ruta)</label>
          <textarea name="footer_links" class="form-control" rows="5"><?= htmlspecialchars($get('footer_links','')) ?></textarea>
        </div>

        <h6>Redes sociales (URL)</h6>
        <div class="row mb-3">
          <div class="col-12 col-md-6 mb-2"><input name="footer_social_linkedin" placeholder="LinkedIn" value="<?= htmlspecialchars($get('footer_social_linkedin','https://www.linkedin.com/company/xlerion')) ?>" class="form-control"></div>
          <div class="col-12 col-md-6 mb-2"><input name="footer_social_instagram" placeholder="Instagram" value="<?= htmlspecialchars($get('footer_social_instagram','https://www.instagram.com/ultimatexlerion/')) ?>" class="form-control"></div>
          <div class="col-12 col-md-6 mb-2"><input name="footer_social_facebook" placeholder="Facebook" value="<?= htmlspecialchars($get('footer_social_facebook','https://www.facebook.com/xlerionultimate')) ?>" class="form-control"></div>
          <div class="col-12 col-md-6 mb-2"><input name="footer_social_behance" placeholder="Behance" value="<?= htmlspecialchars($get('footer_social_behance','https://www.behance.net/xlerionultimate')) ?>" class="form-control"></div>
          <div class="col-12 col-md-6 mb-2"><input name="footer_social_indiegogo" placeholder="Indiegogo" value="<?= htmlspecialchars($get('footer_social_indiegogo','https://www.indiegogo.com/es/profile/miguel_rodriguez-martinez_edb9')) ?>" class="form-control"></div>
          <div class="col-12 col-md-6 mb-2"><input name="footer_social_kickstarter" placeholder="Kickstarter" value="<?= htmlspecialchars($get('footer_social_kickstarter','https://www.kickstarter.com/profile/xlerionstudios')) ?>" class="form-control"></div>
          <div class="col-12 col-md-6 mb-2"><input name="footer_social_patreon" placeholder="Patreon" value="<?= htmlspecialchars($get('footer_social_patreon','https://www.patreon.com/xlerionstudios')) ?>" class="form-control"></div>
        </div>

        <div class="mb-3">
          <label>Texto de suscripción</label>
          <input name="footer_subscribe_label" value="<?= htmlspecialchars($get('footer_subscribe_label','Recibe novedades y convocatorias por correo.')) ?>" class="form-control">
        </div>
        <div class="mb-3">
          <label>Acción del formulario de suscripción (URL)</label>
          <input name="footer_subscribe_action" value="<?= htmlspecialchars($get('footer_subscribe_action','/subscribe')) ?>" class="form-control">
        </div>

        <div class="mb-3 form-check">
          <input type="checkbox" id="footer_show_newsletter" name="footer_show_newsletter" class="form-check-input" <?= $get('footer_show_newsletter','0')==='1' ? 'checked' : '' ?>>
          <label class="form-check-label" for="footer_show_newsletter">Mostrar formulario de newsletter en el footer</label>
        </div>

        <div class="mb-3">
          <label>Texto de copyright</label>
          <input name="footer_copyright" value="<?= htmlspecialchars($get('footer_copyright','© Xlerion - Todos los derechos reservados')) ?>" class="form-control">
        </div>

        <button class="btn btn-primary">Guardar footer</button>
      </form>

      <div class="mt-4 card-preview">
        <h4>Plantillas de footer</h4>
        <form method="post" class="d-flex gap-2 align-items-center">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
          <select id="templateSelect" name="use_template" class="form-select" style="max-width:360px">
            <option value="">-- Selecciona una plantilla o variante --</option>
            <?php foreach(array_keys($builtTemplates) as $bn): ?>
              <option value="tpl:<?= htmlspecialchars($bn) ?>"><?= htmlspecialchars($bn) ?> (plantilla)</option>
            <?php endforeach; ?>
            <?php if (!empty($variants)): foreach($variants as $vopt): ?>
              <option value="var:<?= intval($vopt['id']) ?>"><?= htmlspecialchars($vopt['name']) ?> (variante guardada)</option>
            <?php endforeach; endif; ?>
          </select>
          <button type="submit" class="btn btn-outline-secondary" name="create_from_template" value="1">Crear variante desde plantilla/variante</button>
          <button type="submit" class="btn btn-outline-secondary" name="save_template_as_variant" value="1">Guardar como variante</button>
          <button type="button" id="loadTemplateBtn" class="btn btn-outline-primary">Cargar plantilla</button>
        </form>
        <div class="small text-muted mt-2">Esto añadirá una variante prellenada que podrás activar o editar. También puedes cargar la plantilla para rellenar los campos del formulario.</div>

        <script>
          // Built-in templates data from server
          var _builtFooterTemplates = <?php echo json_encode($builtTemplates, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?> || {};
            // Attempt to fetch templates saved via the templates API (file or DB) and append to the select
            (function(){
              try{
                // use fetch if available
                if (!window.fetch) return;
                fetch('/api/templates.php?action=list').then(function(r){ if (!r.ok) throw new Error('no'); return r.json(); }).then(function(list){
                  try{
                    if (!list || !list.length) return;
                    var sel = document.getElementById('templateSelect'); if (!sel) return;
                    // append templates as options with prefix 'tplfile:' to distinguish from built/var
                    list.forEach(function(t){ try{
                      var id = t.id || t.name || ''; if (!id) return;
                      // avoid duplicates: check values already present
                      var existing = Array.from(sel.options).some(function(o){ return o.value === ('file:'+id) || o.value === ('tpl:'+id) || o.value === ('var:'+id); });
                      if (existing) return;
                      var opt = document.createElement('option'); opt.value = 'file:'+id; opt.textContent = (t.name || id) + ' (plantilla)'; sel.appendChild(opt);
                    }catch(e){} });
                  }catch(e){ }
                }).catch(function(){ });
              }catch(e){}
            })();
          (function(){
            var btn = document.getElementById('loadTemplateBtn');
            if (!btn) return;
            btn.addEventListener('click', function(){
              var sel = document.getElementById('templateSelect');
              if (!sel) return alert('Selecciona una plantilla primero');
              var key = sel.value;
              if (!key) return alert('Selecciona una plantilla');
              var tpl = _builtFooterTemplates[key];
              if (!tpl) return alert('Plantilla no encontrada');

              // Helper to set input/textarea by name if exists
              function setField(name, value){
                var el = document.querySelector('[name="'+name+'"]');
                if (!el) return;
                if (el.type === 'checkbox') {
                  el.checked = (value === '1' || value === 1 || value === true || value === 'true');
                } else {
                  el.value = value === null || value === undefined ? '' : value;
                }
              }

              // Map template keys to form fields
              var mapping = [
                'footer_heading','footer_text','footer_email','footer_phone','footer_address','footer_schedule',
                'footer_contact_cta_label','footer_contact_cta_url','footer_projects_cta_label','footer_projects_cta_url',
                'footer_links','footer_social_linkedin','footer_social_instagram','footer_social_facebook','footer_social_behance',
                'footer_social_indiegogo','footer_social_kickstarter','footer_social_patreon','footer_subscribe_label','footer_subscribe_action','footer_show_newsletter','footer_copyright'
              ];

              mapping.forEach(function(k){ if (tpl.hasOwnProperty(k)) setField(k, tpl[k]); });

              // Inform the user
              var note = document.createElement('div'); note.className = 'small text-success mt-2'; note.textContent = 'Campos rellenados desde la plantilla: '+key;
              btn.parentNode.appendChild(note);
              setTimeout(function(){ try{ note.remove(); } catch(e){} }, 4000);
            });
          })();
        </script>
      </div>

  <hr>
  <h3 class="mt-4">Variantes de footer</h3>
      <div class="mb-3">
        <form method="post" class="d-flex gap-2 align-items-center">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
          <input type="text" name="variant_name" class="form-control" placeholder="Nombre de la nueva variante">
          <button name="variant_action" value="create" class="btn btn-outline-primary">Crear variante desde valores actuales</button>
        </form>
      </div>

      <?php if (!empty($variants)): ?>
        <div class="list-group">
          <?php foreach($variants as $v): $vd = json_decode($v['data'], true) ?: []; ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong><?= htmlspecialchars($v['name']) ?></strong>
                <div class="small text-muted">Creado: <?= htmlspecialchars($v['created_at']) ?></div>
              </div>
              <div class="d-flex gap-2">
                <?php if ((string)$v['id'] === (string)$activeId): ?>
                  <span class="badge bg-success">Activo</span>
                  <form method="post" style="display:inline;margin-left:.5rem">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
                    <input type="hidden" name="variant_id" value="<?= intval($v['id']) ?>">
                    <button name="variant_action" value="deactivate" class="btn btn-sm btn-outline-secondary">Desactivar</button>
                  </form>
                <?php else: ?>
                  <form method="post" style="display:inline">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
                    <input type="hidden" name="variant_id" value="<?= intval($v['id']) ?>">
                    <button name="variant_action" value="activate" class="btn btn-sm btn-outline-success">Activar</button>
                  </form>
                <?php endif; ?>
                <form method="post" style="display:inline">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
                  <input type="hidden" name="variant_id" value="<?= intval($v['id']) ?>">
                  <input type="hidden" name="variant_name" value="<?= htmlspecialchars($v['name']) ?>">
                  <button name="variant_action" value="update" class="btn btn-sm btn-outline-primary">Actualizar</button>
                </form>
                <!-- Edit variant in-place: loads JSON into the form fields for editing -->
                <button type="button" class="btn btn-sm btn-outline-secondary edit-variant-btn" data-variant='<?= json_encode($vd, JSON_HEX_APOS|JSON_HEX_QUOT) ?>'>Editar</button>
                <form method="post" style="display:inline" onsubmit="return confirm('Eliminar variante?');">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
                  <input type="hidden" name="variant_id" value="<?= intval($v['id']) ?>">
                  <button name="variant_action" value="delete" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="small text-muted">No hay variantes guardadas.</div>
      <?php endif; ?>
      <script>
        // Expose built templates and saved variants to client JS, and handle Load / Edit actions
        (function(){
          // Built-in templates data from server
          var _builtFooterTemplates = <?php echo json_encode($builtTemplates, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?> || {};
          // Saved variants data (id -> data)
          var _savedFooterVariants = <?php
            $tmp = [];
            foreach($variants as $vv){ $tmp[intval($vv['id'])] = json_decode($vv['data'], true) ?: []; }
            echo json_encode($tmp, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
          ?> || {};

          function setField(name, value){
            var el = document.querySelector('[name="'+name+'"]');
            if (!el) return;
            if (el.type === 'checkbox') el.checked = (value === '1' || value === 1 || value === true || value === 'true');
            else el.value = value === null || value === undefined ? '' : value;
          }

          // Load template/variant into the form when clicking "Cargar plantilla"
          var loadBtn = document.getElementById('loadTemplateBtn');
          if (loadBtn){
            loadBtn.addEventListener('click', function(){
              var sel = document.getElementById('templateSelect');
              if (!sel) return alert('Selecciona una plantilla primero');
              var key = sel.value;
              if (!key) return alert('Selecciona una plantilla o variante');
              var tpl = null;
              if (key.indexOf('tpl:') === 0){ var tname = key.substr(4); tpl = _builtFooterTemplates[tname]; }
              else if (key.indexOf('var:') === 0){ var vid = key.substr(4); tpl = _savedFooterVariants[vid]; }
              else if (key.indexOf('file:') === 0){
                // fetch lightweight template stored by templates API and map to footer fields where possible
                var tid = key.substr(5);
                try{
                  fetch('/api/templates.php?action=get&id=' + encodeURIComponent(tid)).then(function(r){ if (!r.ok) throw new Error('notfound'); return r.json(); }).then(function(tdat){
                    if (!tdat) return alert('Plantilla no encontrada');
                    // map blocks/html into footer fields by searching for simple markers (best-effort)
                    var mappingObj = {};
                    // if template contains blocks array, try to find a block with footer-like content
                    if (Array.isArray(tdat.blocks) && tdat.blocks.length){
                      // join HTML pieces to a single string and try to extract text nodes for footer fields
                      var big = tdat.blocks.map(function(b){ return b.html || (b.html||''); }).join('\n');
                      mappingObj['footer_text'] = big.replace(/<[^>]+>/g,'').slice(0,800);
                    } else if (tdat.data && tdat.data.html){ mappingObj['footer_text'] = tdat.data.html.replace(/<[^>]+>/g,'').slice(0,800); }
                    // apply mapping
                    var keys2 = ['footer_text','footer_heading','footer_email','footer_phone'];
                    keys2.forEach(function(k){ if (mappingObj[k]) setField(k, mappingObj[k]); });
                    var note = document.createElement('div'); note.className = 'small text-success mt-2'; note.textContent = 'Campos rellenados desde plantilla: '+(tdat.name || tid);
                    loadBtn.parentNode.appendChild(note); setTimeout(function(){ try{ note.remove(); }catch(e){} },4000);
                  }).catch(function(){ alert('No se pudo cargar la plantilla'); });
                }catch(e){ alert('Error cargando plantilla'); }
                return;
              }
              if (!tpl) return alert('Plantilla o variante no encontrada');

              var mapping = [
                'footer_heading','footer_text','footer_email','footer_phone','footer_address','footer_schedule',
                'footer_contact_cta_label','footer_contact_cta_url','footer_projects_cta_label','footer_projects_cta_url',
                'footer_links','footer_social_linkedin','footer_social_instagram','footer_social_facebook','footer_social_behance',
                'footer_social_indiegogo','footer_social_kickstarter','footer_social_patreon','footer_subscribe_label','footer_subscribe_action','footer_show_newsletter','footer_copyright'
              ];
              mapping.forEach(function(k){ if (tpl.hasOwnProperty(k)) setField(k, tpl[k]); else setField(k, ''); });

              var note = document.createElement('div'); note.className = 'small text-success mt-2'; note.textContent = 'Campos rellenados desde: '+(key.indexOf('tpl:')===0? 'plantilla '+key.substr(4) : 'variante '+key.substr(4));
              loadBtn.parentNode.appendChild(note);
              setTimeout(function(){ try{ note.remove(); } catch(e){} }, 4000);
            });
          }

          // Attach edit buttons behavior (load variant JSON into form for editing)
          document.querySelectorAll('.edit-variant-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
              var data = {};
              try{ data = JSON.parse(btn.getAttribute('data-variant')); } catch(e){ alert('No se pudo leer la variante'); return; }
              var keys = [
                'footer_heading','footer_text','footer_email','footer_phone','footer_address','footer_schedule',
                'footer_contact_cta_label','footer_contact_cta_url','footer_projects_cta_label','footer_projects_cta_url',
                'footer_links','footer_social_linkedin','footer_social_instagram','footer_social_facebook','footer_social_behance',
                'footer_social_indiegogo','footer_social_kickstarter','footer_social_patreon','footer_subscribe_label','footer_subscribe_action','footer_show_newsletter','footer_copyright'
              ];
              keys.forEach(function(k){ if (data.hasOwnProperty(k)) setField(k, data[k]); else setField(k, ''); });
              var form = document.querySelector('form[method="post"]'); if (form) { form.scrollIntoView({behavior:'smooth'}); form.style.boxShadow='0 0 0 3px rgba(0,238,255,0.12)'; setTimeout(function(){ form.style.boxShadow=''; }, 2200); }
            });
          });

        })();
      </script>
    </section>
  </main>
</div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';
