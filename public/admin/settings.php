<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$pdo = Database::pdo();

$errors = [];
$saved = false;
// Helper: ensure settings table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS settings (k TEXT PRIMARY KEY, v TEXT)");

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')){ $errors[] = 'CSRF inválido'; }

  // collect and validate inputs
  $data = [];
  $data['site_title'] = trim($_POST['site_title'] ?? '');
  $data['site_description'] = trim($_POST['site_description'] ?? '');
  $data['default_theme'] = in_array($_POST['default_theme'] ?? 'auto', ['auto','light','dark']) ? $_POST['default_theme'] : 'auto';
  $data['items_per_page'] = max(1, (int)($_POST['items_per_page'] ?? 10));
  $data['contact_email'] = trim($_POST['contact_email'] ?? '');
  $data['allow_registration'] = isset($_POST['allow_registration']) ? '1' : '0';
  $data['maintenance_mode'] = isset($_POST['maintenance_mode']) ? '1' : '0';
  $data['maintenance_message'] = trim($_POST['maintenance_message'] ?? '');
  $data['timezone'] = trim($_POST['timezone'] ?? '');
  $data['logo_url'] = trim($_POST['logo_url'] ?? '');
  $data['analytics_id'] = trim($_POST['analytics_id'] ?? '');

  // basic validation
  if ($data['contact_email'] && !filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)){
    $errors[] = 'El correo de contacto no es válido';
  }

  if (empty($errors)){
    try{
      $up = $pdo->prepare('INSERT OR REPLACE INTO settings (k,v) VALUES (?,?)');
      foreach($data as $k=>$v){ $up->execute([$k, (string)$v]); }
      $saved = true;
    } catch (Exception $e){
      $errors[] = 'No se pudo guardar: '.$e->getMessage();
    }
  }
}

// load current settings into $current array
$current = [];
try{
  $stmt = $pdo->query("SELECT k,v FROM settings");
  while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
    $current[$r['k']] = $r['v'];
  }
}catch(Exception $e){ /* ignore - empty settings */ }

// helper getters
$get = function($k,$def='') use ($current){ return isset($current[$k]) ? $current[$k] : $def; };

$title = 'Ajustes';
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
    </header>

    <section class="mt-3">
      <?php if ($saved): ?><div class="card-preview">Guardado.</div><?php endif; ?>
      <?php if (!empty($errors)): foreach($errors as $e): ?><div class="card-preview text-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; endif; ?>

      <form method="post">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">

        <div class="mb-3">
          <label>Título del sitio</label>
          <input name="site_title" value="<?= htmlspecialchars($get('site_title','')) ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label>Descripción corta</label>
          <input name="site_description" value="<?= htmlspecialchars($get('site_description','')) ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label>Tema por defecto</label>
          <select name="default_theme" class="form-control">
            <option value="auto" <?= $get('default_theme','auto')==='auto' ? 'selected': '' ?>>Auto (sistema)</option>
            <option value="light" <?= $get('default_theme','auto')==='light' ? 'selected': '' ?>>Claro</option>
            <option value="dark" <?= $get('default_theme','auto')==='dark' ? 'selected': '' ?>>Oscuro</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Elementos por página (listas)</label>
          <input type="number" min="1" name="items_per_page" value="<?= htmlspecialchars($get('items_per_page',10)) ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label>Correo de contacto</label>
          <input type="email" name="contact_email" value="<?= htmlspecialchars($get('contact_email','')) ?>" class="form-control">
        </div>

        <div class="mb-3 form-check">
          <input type="checkbox" id="allow_registration" name="allow_registration" class="form-check-input" <?= $get('allow_registration','0')==='1' ? 'checked' : '' ?>>
          <label class="form-check-label" for="allow_registration">Permitir registro público</label>
        </div>

        <div class="mb-3 form-check">
          <input type="checkbox" id="maintenance_mode" name="maintenance_mode" class="form-check-input" <?= $get('maintenance_mode','0')==='1' ? 'checked' : '' ?>>
          <label class="form-check-label" for="maintenance_mode">Modo mantenimiento (sitio deshabilitado)</label>
        </div>

        <div class="mb-3">
          <label>Mensaje de mantenimiento (opcional)</label>
          <textarea name="maintenance_message" class="form-control" rows="3"><?= htmlspecialchars($get('maintenance_message','')) ?></textarea>
        </div>

        <div class="mb-3">
          <label>Zona horaria</label>
          <select name="timezone" class="form-control">
            <?php
              $tzs = DateTimeZone::listIdentifiers();
              $cur = $get('timezone', date_default_timezone_get());
              foreach($tzs as $tz){
                $sel = ($tz === $cur) ? 'selected' : '';
                echo "<option value=\"".htmlspecialchars($tz)."\" $sel>".htmlspecialchars($tz)."</option>\n";
              }
            ?>
          </select>
          <small class="form-text text-muted">Selecciona la zona horaria del sitio.</small>
        </div>

        <div class="mb-3">
          <label>URL del logo (cabecera)</label>
          <input name="logo_url" value="<?= htmlspecialchars($get('logo_url','')) ?>" class="form-control">
        </div>

        <div class="mb-3">
          <label>ID de Analytics / Tag (opcional)</label>
          <input name="analytics_id" value="<?= htmlspecialchars($get('analytics_id','')) ?>" class="form-control">
        </div>

        <button class="btn btn-primary">Guardar ajustes</button>
      </form>
    </section>
  </main>
</div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';
