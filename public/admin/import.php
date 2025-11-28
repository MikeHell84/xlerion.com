<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$pdo = Database::pdo();

$errors = [];
$report = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')){ $errors[] = 'CSRF inválido'; }
  if (isset($_FILES['csv']) && is_uploaded_file($_FILES['csv']['tmp_name'])){
    $tmp = $_FILES['csv']['tmp_name'];
    if (($fh = fopen($tmp,'r')) !== false){
      $headers = fgetcsv($fh);
      // make sure contacts table exists
      try{ $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (id INTEGER PRIMARY KEY AUTOINCREMENT, first_name TEXT, last_name TEXT, email TEXT UNIQUE, phone TEXT, created_at TEXT)"); } catch(Exception $e) {}
      $insert = $pdo->prepare('INSERT OR IGNORE INTO contacts (first_name,last_name,email,phone,created_at) VALUES (?,?,?,?,datetime("now"))');
      $ok=0;$err=0; $rowErrors = [];
      while (($row = fgetcsv($fh)) !== false){
        $data = array_combine($headers,$row);
        $email = trim($data['email'] ?? '');
        if (empty($email) || !filter_var($email,FILTER_VALIDATE_EMAIL)) { $err++; $rowErrors[] = 'Invalid email: '.htmlspecialchars($email); continue; }
        try{ $insert->execute([$data['first_name'] ?? null, $data['last_name'] ?? null, $email, $data['phone'] ?? null]); $ok++; } catch(Exception $e){ $err++; $rowErrors[] = $e->getMessage(); }
      }
      fclose($fh);
      $report = "Import finished: $ok inserted, $err errors";
      if (!empty($rowErrors)) $errors = array_merge($errors, array_slice($rowErrors,0,20));
    } else {
      $errors[] = 'No se pudo leer el archivo CSV.';
    }
  } else {
    $errors[] = 'Archivo CSV no enviado.';
  }
}

$title = 'Importar CSV';
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
      <?php if ($report): ?><div class="card-preview"><?= htmlspecialchars($report) ?></div><?php endif; ?>
      <?php if (!empty($errors)): foreach($errors as $e): ?><div class="card-preview text-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; endif; ?>

      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
        <div class="mb-3">
          <label>Archivo CSV</label>
          <input type="file" name="csv" accept=".csv" class="form-control" required>
        </div>
        <div class="mb-3">
          <small class="form-text text-muted">El CSV debe contener columnas: first_name, last_name, email, phone (cabeceras exactas).</small>
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" type="submit">Importar</button>
          <a class="btn btn-outline-light" href="/admin/contacts.php">Volver a contactos</a>
        </div>
      </form>
    </section>
  </main>
</div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';
