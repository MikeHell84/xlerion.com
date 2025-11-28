<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$allowed = ['contacts','organizations','blog_posts'];
$table = $_REQUEST['table'] ?? '';
if (!in_array($table,$allowed)) { http_response_code(400); echo 'Invalid table'; exit; }

// If GET: render a confirmation form that POSTs with CSRF to perform export.
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Simple confirmation UI
    $title = 'Exportar: ' . htmlspecialchars($table);
    ob_start();
    ?>
    <div class="admin-dashboard">
        <aside class="admin-sidebar card-preview"><?php include __DIR__ . '/_nav.php'; ?></aside>
        <main class="admin-main">
            <header class="admin-header"><h1 class="section-title-lg"><?= $title ?></h1></header>
            <section class="mt-3 card-preview">
                <p>Confirma la exportación de la tabla <strong><?= htmlspecialchars($table) ?></strong> a CSV. Esto descargará todos los registros.</p>
                <form method="post">
                    <input type="hidden" name="table" value="<?= htmlspecialchars($table) ?>">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
                    <button class="btn btn-primary" type="submit">Exportar CSV</button>
                    <a class="btn btn-outline-light" href="/admin/contacts.php">Cancelar</a>
                </form>
            </section>
        </main>
    </div>
    <?php
    $slot = ob_get_clean(); include __DIR__ . '/../../views/layout.php';
    exit;
}

// POST: perform export, CSRF required
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) { http_response_code(403); echo 'Forbidden'; exit; }
$pdo = Database::pdo();
$st = $pdo->query("SELECT * FROM `{$table}`");
$cols = array_keys($st->fetch(PDO::FETCH_ASSOC) ?: []);
$st->execute();
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="'. $table .'.csv"');
$fh = fopen('php://output','w');
if ($cols) fputcsv($fh,$cols);
$st->execute();
while ($row = $st->fetch(PDO::FETCH_ASSOC)) { fputcsv($fh, array_values($row)); }
fclose($fh);
exit;
