<?php
require_once __DIR__ . '/../../src/Auth.php'; require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start(); Auth::requireAdmin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405); echo json_encode(['ok'=>false,'error'=>'method']); exit;
}

if (!isset($_FILES['media'])) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'no_file']); exit; }

$f = $_FILES['media'];
if ($f['error'] !== UPLOAD_ERR_OK) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'upload_error','code'=>$f['error']]); exit; }

// simple limits
// basic per-file size limit
$maxBytes = 20 * 1024 * 1024; // 20MB per file
if ($f['size'] > $maxBytes) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'too_large']); exit; }

// MIME detection
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $f['tmp_name']);
finfo_close($finfo);

$allowedExt = ['jpg','jpeg','png','gif','webp','mp4','webm','mov','ogg','m4v'];
$allowedMime = [
  'image/jpeg','image/png','image/gif','image/webp',
  'video/mp4','video/webm','video/quicktime','video/ogg','video/x-m4v'
];
$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
  http_response_code(400); echo json_encode(['ok'=>false,'error'=>'invalid_type','mime'=>$mime,'ext'=>$ext]); exit;
}

// quotas: per-user daily uploads and global storage quota
$uploadsDir = __DIR__ . '/../media/uploads';
if (!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0755, true); }

$userId = $_SESSION['user_id'] ?? null;
$prefix = ($userId ? 'u' . intval($userId) . '_' : '');

// count user uploads in the last 24 hours
$userLimit = 50; // max uploads per user per 24h
$userCount = 0;
$now = time();
foreach (glob($uploadsDir . DIRECTORY_SEPARATOR . $prefix . '*') as $file) {
  if ($now - filemtime($file) <= 24*3600) $userCount++;
}
if ($userId && $userCount >= $userLimit) { http_response_code(429); echo json_encode(['ok'=>false,'error'=>'user_quota_exceeded']); exit; }

// global storage quota
$globalMax = 1024 * 1024 * 1024; // 1GB total for uploads folder
$total = 0;
foreach (glob($uploadsDir . DIRECTORY_SEPARATOR . '*') as $file) { $total += filesize($file); }
if ($total + $f['size'] > $globalMax) { http_response_code(507); echo json_encode(['ok'=>false,'error'=>'global_quota_exceeded']); exit; }

$safeName = preg_replace('/[^A-Za-z0-9_\-\.]/','_',basename($f['name']));
$unique = $prefix . time() . '_' . bin2hex(random_bytes(6)) . '_' . $safeName;
$dest = $uploadsDir . DIRECTORY_SEPARATOR . $unique;
if (!move_uploaded_file($f['tmp_name'], $dest)) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'move_failed']); exit; }

$urlPath = '/media/uploads/' . $unique;
// if image, try to create thumbnails (320 and 720) using GD
$thumb320 = null; $thumb720 = null;
if (strpos($mime, 'image/') === 0 && function_exists('imagecreatefromstring')) {
  try {
    $imgData = file_get_contents($dest);
    $src = @imagecreatefromstring($imgData);
    if ($src) {
      $w = imagesx($src); $h = imagesy($src);
      foreach ([320,720] as $nw) {
        $nh = intval($h * ($nw / max(1,$w)));
        $dst = imagecreatetruecolor($nw, $nh);
        // preserve transparency for PNG/GIF
        if ($mime === 'image/png' || $mime === 'image/gif') {
          imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
          imagealphablending($dst, false);
          imagesavealpha($dst, true);
        }
        imagecopyresampled($dst, $src, 0,0,0,0,$nw,$nh,$w,$h);
        $thumbName = 'thumb_' . $nw . '_' . $unique;
        $thumbPath = $uploadsDir . DIRECTORY_SEPARATOR . $thumbName;
        // save as JPEG for compatibility/size
        imagejpeg($dst, $thumbPath, 80);
        imagedestroy($dst);
        if ($nw === 320) $thumb320 = '/media/uploads/' . $thumbName;
        if ($nw === 720) $thumb720 = '/media/uploads/' . $thumbName;
      }
      imagedestroy($src);
    }
  } catch (Exception $e) { /* ignore thumbnail failures */ }
}

echo json_encode(['ok'=>true,'url'=>$urlPath,'thumb320'=>$thumb320,'thumb720'=>$thumb720,'mime'=>$mime]);

// record in DB (best-effort)
try {
  require_once __DIR__ . '/../../src/Model/Database.php';
  $db = Database::pdo();
  $st = $db->prepare('INSERT INTO media_files (filename,url,thumb320,thumb720,mime,size,uploaded_by) VALUES (?,?,?,?,?,?,?)');
  $st->execute([ $unique, $urlPath, $thumb320, $thumb720, $mime, $f['size'], $_SESSION['user_id'] ?? null ]);
  $mediaId = $db->lastInsertId();
  // if video, enqueue a transcode job
  if (strpos($mime, 'video/') === 0) {
    $j = $db->prepare('INSERT INTO media_jobs (media_id,type,payload,status) VALUES (?,?,?,?)');
    $payload = json_encode(['source'=>$urlPath]);
    $j->execute([$mediaId,'transcode',$payload,'pending']);
  }
} catch (Exception $e) { /* ignore DB errors */ }

