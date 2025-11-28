<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /admin/posts.php'); exit; }
$pdo = Database::pdo();
$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? ''); $slug = trim($_POST['slug'] ?? ''); $excerpt = $_POST['excerpt'] ?? null; $content = $_POST['content'] ?? null;
$status = $_POST['status'] ?? 'draft'; $published_at = $_POST['published_at'] ?? null;
try{
  // Handle image upload if provided
  $uploadedImagePath = null;
  if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $f = $_FILES['image'];
    if ($f['error'] === UPLOAD_ERR_OK) {
      $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mtype = finfo_file($finfo, $f['tmp_name']);
      finfo_close($finfo);
      if (!in_array($mtype, $allowed)) {
        throw new Exception('Tipo de archivo no permitido');
      }
      $uploadsDir = __DIR__ . '/../media/uploads';
      if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
      $ext = pathinfo($f['name'], PATHINFO_EXTENSION) ?: 'jpg';
      $safe = preg_replace('/[^a-z0-9\-_.]/i','', pathinfo($slug ?: $title, PATHINFO_FILENAME));
      $filename = $safe . '-' . time() . '.' . $ext;
      $dest = $uploadsDir . '/' . $filename;
      if (!move_uploaded_file($f['tmp_name'], $dest)) {
        throw new Exception('No se pudo mover el archivo subido');
      }
      // public-accessible path
      $uploadedImagePath = '/media/uploads/' . $filename;
    }
  }

  // If updating, merge meta and update; if inserting, create meta with image when present
  if ($id){
    // fetch existing meta
    $st0 = $pdo->prepare('SELECT meta FROM blog_posts WHERE id = ? LIMIT 1');
    $st0->execute([$id]);
    $row = $st0->fetch();
    $meta = [];
    if ($row && !empty($row['meta'])) {
      $meta = json_decode($row['meta'], true) ?: [];
    }
    // handle resources sent from form
    $resources_order = trim($_POST['resources_order'] ?? '');
    $postedResources = $_POST['resources'] ?? [];
    // normalize to array of slugs
    $orderArr = $resources_order !== '' ? array_filter(array_map('trim', explode(',', $resources_order))) : [];
    // ensure checked ones are included in the order (append missing)
    foreach ($postedResources as $pr){ if ($pr && !in_array($pr, $orderArr)) $orderArr[] = $pr; }
    // validate against resources table
    if (!empty($orderArr)){
      $placeholders = implode(',', array_fill(0, count($orderArr), '?'));
      $stRes = $pdo->prepare("SELECT slug FROM resources WHERE slug IN ($placeholders)");
      $stRes->execute($orderArr);
      $valid = $stRes->fetchAll(PDO::FETCH_COLUMN, 0);
      $valid = array_values(array_unique($valid));
      // keep order from orderArr but only valid ones
      $final = array_values(array_filter($orderArr, function($s) use ($valid){ return in_array($s, $valid); }));
      if (count($final) !== count($orderArr)) { $_SESSION['flash'] = 'Se eliminaron recursos no válidos automáticamente.'; }
      if (!empty($final)) $meta['resources'] = $final; else unset($meta['resources']);
    } else {
      // none selected
      unset($meta['resources']);
    }
    if ($uploadedImagePath) $meta['image'] = $uploadedImagePath;
    $metaJson = !empty($meta) ? json_encode($meta) : null;
    $st = $pdo->prepare('UPDATE blog_posts SET title=?,slug=?,excerpt=?,content=?,status=?,published_at=?,meta=?,updated_at=DATETIME("now") WHERE id = ?');
    $st->execute([$title,$slug,$excerpt,$content,$status,$published_at,$metaJson,$id]);
  } else {
    $meta = [];
    // handle resources for new post
    $resources_order = trim($_POST['resources_order'] ?? '');
    $postedResources = $_POST['resources'] ?? [];
    $orderArr = $resources_order !== '' ? array_filter(array_map('trim', explode(',', $resources_order))) : [];
    foreach ($postedResources as $pr){ if ($pr && !in_array($pr, $orderArr)) $orderArr[] = $pr; }
    if (!empty($orderArr)){
      $placeholders = implode(',', array_fill(0, count($orderArr), '?'));
      $stRes = $pdo->prepare("SELECT slug FROM resources WHERE slug IN ($placeholders)");
      $stRes->execute($orderArr);
      $valid = $stRes->fetchAll(PDO::FETCH_COLUMN, 0);
      $valid = array_values(array_unique($valid));
      $final = array_values(array_filter($orderArr, function($s) use ($valid){ return in_array($s, $valid); }));
      if (count($final) !== count($orderArr)) { $_SESSION['flash'] = 'Se eliminaron recursos no válidos automáticamente.'; }
      if (!empty($final)) $meta['resources'] = $final;
    }
    if ($uploadedImagePath) $meta['image'] = $uploadedImagePath;
    $metaJson = !empty($meta) ? json_encode($meta) : null;
    $st = $pdo->prepare('INSERT INTO blog_posts (title,slug,excerpt,content,status,published_at,meta,created_at) VALUES (?,?,?,?,?,?,?,DATETIME("now"))');
    $st->execute([$title,$slug,$excerpt,$content,$status,$published_at,$metaJson]);
  }
}catch(Exception $e){ error_log('save_post: '.$e->getMessage()); }
header('Location: /admin/posts.php'); exit;
