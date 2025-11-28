<?php
require_once __DIR__ . '/../Model/Resource.php';
require_once __DIR__ . '/../Model/Database.php';
class ResourceController {
  // Public listing
  public function index() {
    $resources = \Xlerion\Model\Resource::all(100,0);
    include __DIR__ . '/../../views/resources/index.php';
  }

  // Public show by slug (uses Router named param :slug -> $_GET['slug'])
  public function show() {
    $slug = $_GET['slug'] ?? null;
    if (!$slug) { http_response_code(404); echo 'Recurso no encontrado'; return; }
    $res = \Xlerion\Model\Resource::findBySlug($slug);
    if (!$res) { http_response_code(404); echo 'Recurso no encontrado'; return; }
    include __DIR__ . '/../../views/resources/show.php';
  }

  /* Admin area: minimal CRUD - no auth checks (assume admin panel handles auth) */
  public function adminIndex() {
    $resources = \Xlerion\Model\Resource::all(200,0);
    include __DIR__ . '/../../views/admin/resources/index.php';
  }

  public function adminCreate() {
    include __DIR__ . '/../../views/admin/resources/form.php';
  }

  public function adminStore() {
    // normalize slug
    $rawSlug = $_POST['slug'] ?? '';
    $slug = $this->slugify($rawSlug ?: ($_POST['title'] ?? 'resource'));

    // ensure uniqueness
    $existing = \Xlerion\Model\Resource::findBySlug($slug);
    $suf = 1;
    while ($existing) {
      $slugCandidate = $slug . '-' . $suf++;
      if (!\Xlerion\Model\Resource::findBySlug($slugCandidate)) { $slug = $slugCandidate; break; }
      $existing = \Xlerion\Model\Resource::findBySlug($slugCandidate);
    }

    $filePath = $_POST['file_path'] ?? null;
    // handle uploaded file
    if (!empty($_FILES['file']['tmp_name'])) {
      $uploadDir = __DIR__ . '/../../public/media/resources';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
      $orig = basename($_FILES['file']['name']);
      $ext = pathinfo($orig, PATHINFO_EXTENSION);
      $safe = preg_replace('/[^a-z0-9._-]/i', '-', pathinfo($orig, PATHINFO_FILENAME));
      $targetName = $safe . '-' . time() . '-' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
      $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $targetName;
      if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        // store web-accessible path
        $filePath = '/media/resources/' . $targetName;
      }
    }

    $data = [
      'slug' => $slug,
      'title' => $_POST['title'] ?? '',
      'description' => $_POST['description'] ?? '',
      'file_path' => $filePath,
      'url' => $_POST['url'] ?? null
    ];
    \Xlerion\Model\Resource::create($data);
    header('Location: /admin/resources');
  }

  public function adminEdit() {
    $id = $_GET['id'] ?? null;
    if (!$id) { header('Location: /admin/resources'); return; }
    require_once __DIR__ . '/../Model/Resource.php';
    $pdo = \Database::pdo();
    $st = $pdo->prepare('SELECT * FROM resources WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $resource = $st->fetch(PDO::FETCH_ASSOC);
    include __DIR__ . '/../../views/admin/resources/form.php';
  }

  public function adminUpdate() {
    $id = $_POST['id'] ?? null;
    if (!$id) { header('Location: /admin/resources'); return; }
    // load existing resource to preserve file_path if needed
    $pdo = \Database::pdo();
    $st = $pdo->prepare('SELECT * FROM resources WHERE id = ? LIMIT 1');
    $st->execute([(int)$id]);
    $existing = $st->fetch(PDO::FETCH_ASSOC);

    $rawSlug = $_POST['slug'] ?? '';
    $slug = $this->slugify($rawSlug ?: ($_POST['title'] ?? 'resource'));
    // ensure uniqueness (allow same resource)
    $found = \Xlerion\Model\Resource::findBySlug($slug);
    $suf = 1;
    while ($found && (int)$found['id'] !== (int)$id) {
      $slugCandidate = $slug . '-' . $suf++;
      $found = \Xlerion\Model\Resource::findBySlug($slugCandidate);
      if (!$found) { $slug = $slugCandidate; break; }
    }

    $filePath = $_POST['file_path'] ?? ($existing['file_path'] ?? null);
    // handle uploaded file
    if (!empty($_FILES['file']['tmp_name'])) {
      $uploadDir = __DIR__ . '/../../public/media/resources';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
      $orig = basename($_FILES['file']['name']);
      $ext = pathinfo($orig, PATHINFO_EXTENSION);
      $safe = preg_replace('/[^a-z0-9._-]/i', '-', pathinfo($orig, PATHINFO_FILENAME));
      $targetName = $safe . '-' . time() . '-' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
      $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $targetName;
      if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        $filePath = '/media/resources/' . $targetName;
        // optionally remove old file - skipped to avoid accidental data loss
      }
    }

    $data = [
      'slug' => $slug,
      'title' => $_POST['title'] ?? '',
      'description' => $_POST['description'] ?? '',
      'file_path' => $filePath,
      'url' => $_POST['url'] ?? null
    ];
    \Xlerion\Model\Resource::update((int)$id, $data);
    header('Location: /admin/resources');
  }

  // simple slugify helper
  private function slugify($text) {
    // transliterate
    if (function_exists('iconv')) {
      $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    }
    $text = preg_replace('~[^\pL0-9]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    if (empty($text)) return 'resource-' . time();
    return $text;
  }

  public function adminDelete() {
    $id = $_POST['id'] ?? null;
    if ($id) \Xlerion\Model\Resource::delete((int)$id);
    header('Location: /admin/resources');
  }
}
