<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$pdo = Database::pdo();

$id = intval($_GET['id'] ?? 0);
$contact = null;
if ($id > 0) {
  try{
    $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
    $stmt->execute([$id]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (Exception $e) { $contact = null; }
}

$title = 'Ver contacto';
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
        <?php if ($contact): ?>
          <a class="btn btn-primary" href="/admin/edit_contact.php?id=<?= intval($contact['id']) ?>">Editar</a>
        <?php endif; ?>
      </div>
    </header>

    <section class="mt-3">
      <?php if (!$contact): ?>
        <div class="card-preview">Contacto no encontrado.</div>
      <?php else: ?>
        <div class="card-preview">
          <h3><?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?></h3>
          <p><strong>Email:</strong> <?= htmlspecialchars($contact['email']) ?></p>
          <p><strong>Teléfono:</strong> <?= htmlspecialchars($contact['phone'] ?? '') ?></p>
          <?php if (!empty($contact['company'])): ?><p><strong>Empresa:</strong> <?= htmlspecialchars($contact['company']) ?></p><?php endif; ?>
          <?php if (!empty($contact['notes'])): ?><p><strong>Notas:</strong><br><?= nl2br(htmlspecialchars($contact['notes'])) ?></p><?php endif; ?>
          <p class="small text-muted">Creado: <?= htmlspecialchars($contact['created_at'] ?? '') ?></p>

          <form method="post" action="/admin/delete_contact.php" onsubmit="return confirm('Eliminar contacto?');">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
            <input type="hidden" name="id" value="<?= intval($contact['id']) ?>">
            <button class="btn btn-danger">Eliminar</button>
          </form>
        </div>
      <?php endif; ?>
    </section>
  </main>
</div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';
