<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$pdo = Database::pdo();

$id = intval($_GET['id'] ?? 0);
$contact = ['first_name'=>'','last_name'=>'','email'=>'','phone'=>'','company'=>'','notes'=>''];
if ($id > 0) {
  try{
    $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
    $stmt->execute([$id]);
    $f = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($f) $contact = array_merge($contact, $f);
  } catch (Exception $e) { /* ignore */ }
}

// Handle POST save
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')){ $errors[] = 'CSRF inválido'; }
  $first = trim($_POST['first_name'] ?? '');
  $last = trim($_POST['last_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  if ($first === '' || $email === '') $errors[] = 'Nombre y email son requeridos';
  if (empty($errors)){
    try{
      if ($id > 0){
        $u = $pdo->prepare('UPDATE contacts SET first_name=?, last_name=?, email=?, phone=?, company=?, notes=? WHERE id=?');
        $u->execute([$first,$last,$email,$_POST['phone'] ?? '', $_POST['company'] ?? '', $_POST['notes'] ?? '', $id]);
      } else {
        $i = $pdo->prepare('INSERT INTO contacts (first_name,last_name,email,phone,company,notes,created_at) VALUES (?,?,?,?,?,?,datetime("now"))');
        $i->execute([$first,$last,$email,$_POST['phone'] ?? '', $_POST['company'] ?? '', $_POST['notes'] ?? '']);
        $id = $pdo->lastInsertId();
      }
      header('Location: /admin/view_contact.php?id=' . intval($id)); exit;
    } catch (Exception $e){ $errors[] = 'Error al guardar'; }
  }
}

$title = $id>0 ? 'Editar contacto' : 'Crear contacto';
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
        <a class="btn btn-outline-light" href="/admin/contacts.php">Volver</a>
      </div>
    </header>

    <section class="mt-3">
      <?php if (!empty($errors)): foreach($errors as $err): ?>
        <div class="card-preview text-danger"><?= htmlspecialchars($err) ?></div>
      <?php endforeach; endif; ?>

      <form method="post">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
        <div class="mb-3">
          <label>Nombre</label>
          <input name="first_name" value="<?= htmlspecialchars($contact['first_name'] ?? '') ?>" class="form-control">
        </div>
        <div class="mb-3">
          <label>Apellido</label>
          <input name="last_name" value="<?= htmlspecialchars($contact['last_name'] ?? '') ?>" class="form-control">
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input name="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>" class="form-control">
        </div>
        <div class="mb-3">
          <label>Teléfono</label>
          <input name="phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>" class="form-control">
        </div>
        <div class="mb-3">
          <label>Empresa</label>
          <input name="company" value="<?= htmlspecialchars($contact['company'] ?? '') ?>" class="form-control">
        </div>
        <div class="mb-3">
          <label>Notas</label>
          <textarea name="notes" class="form-control"><?php echo htmlspecialchars($contact['notes'] ?? '') ?></textarea>
        </div>
        <button class="btn btn-primary">Guardar</button>
      </form>
    </section>
  </main>
</div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';
