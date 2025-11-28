<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
$pdo = Database::pdo();
$perPage = 12; $page = max(1,intval($_GET['page'] ?? 1)); $offset = ($page-1)*$perPage;
$files = [];
$pages = 1;
$db_error = null;
$title = 'Medios';
$banner_title_class = 'section-title-lg';
try{
  $stmt = $pdo->prepare('SELECT id, filename, path FROM media_files ORDER BY id DESC LIMIT :lim OFFSET :off');
  $stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
  $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
  $stmt->execute();
  $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $total = (int)$pdo->query('SELECT COUNT(*) FROM media_files')->fetchColumn();
  $pages = max(1, ceil($total/$perPage));
} catch (Exception $e){
  // fallback: no DB table -> list files from common media folders
  $db_error = $e->getMessage();
  $fallbackDirs = [__DIR__ . '/../images', __DIR__ . '/../media', __DIR__ . '/../../public/media', __DIR__ . '/../../public/images'];
  $found = [];
  foreach ($fallbackDirs as $d){
    if (!is_dir($d)) continue;
    $it = new DirectoryIterator($d);
    foreach ($it as $fi){
      if ($fi->isFile()){
        $base = basename($d);
        $webBase = in_array(strtolower($base), ['images','media']) ? '/' . $base : '/media';
        $found[] = ['id'=>null,'filename'=>$fi->getFilename(),'path'=> $webBase . '/' . $fi->getFilename(),'source_dir'=>$d];
      }
    }
  }
  usort($found, function($a,$b){ return strcmp($b['filename'],$a['filename']); });
  $files = array_slice($found, $offset, $perPage);
  $pages = max(1, ceil(max(1,count($found))/$perPage));
}
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
        <form method="post" action="/admin/upload_media.php" enctype="multipart/form-data" style="display:flex;gap:.5rem;align-items:center">
          <input type="file" name="file" required>
          <button type="submit" class="btn btn-primary">Subir</button>
        </form>
      </div>
    </header>

    <section class="mt-3">
      <?php if (!empty($db_error)): ?><div class="card-preview text-warning">Base de datos de medios no disponible: <?=htmlspecialchars($db_error)?></div><?php endif; ?>

      <div class="list-group">
      <?php foreach ($files as $f): 
        $p = $f['path'];
        require_once __DIR__ . '/../../src/MediaHelper.php';
        $p = MediaHelper::publicPath($p);
        $thumb = dirname($p) . '/thumb_' . basename($p);
      ?>
        <div class="list-group-item d-flex align-items-center justify-content-between">
          <div style="display:flex;align-items:center;gap:.75rem">
            <?php if (file_exists($thumb)): ?><img src="<?=htmlspecialchars(str_replace('\\','/',$thumb))?>" style="height:48px;vertical-align:middle;border-radius:4px"><?php endif; ?>
            <div>
              <div><strong><?=htmlspecialchars($f['filename'])?></strong></div>
              <div class="small text-muted"><?=htmlspecialchars($p)?></div>
            </div>
          </div>
          <div style="display:flex;gap:.5rem">
            <a class="btn btn-outline-light btn-sm" href="<?=htmlspecialchars(str_replace('\\','/',$p))?>" target="_blank">Ver</a>
            <form method="post" action="/admin/delete_media.php" style="display:inline" onsubmit="return confirm('Eliminar archivo?');">
              <input type="hidden" name="csrf" value="<?=htmlspecialchars($_SESSION['csrf'] ?? '')?>">
              <input type="hidden" name="id" value="<?=intval($f['id'])?>">
              <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
      </div>

      <nav class="mt-3" aria-label="Paginación de medios">
        <?php for ($i=1;$i<=$pages;$i++): ?>
          <a class="btn btn-outline-light btn-sm" href="?page=<?= $i ?>"<?=($i===$page)?' aria-current="page" style="font-weight:700"':''?>><?= $i ?></a>
        <?php endfor; ?>
      </nav>
    </section>
  </main>
</div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';

