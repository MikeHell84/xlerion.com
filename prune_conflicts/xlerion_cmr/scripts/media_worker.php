<?php
// Simple media worker: run via CLI periodically or supervisor
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$logFile = __DIR__ . '/../storage/logs/media_worker.log';
if (!is_dir(dirname($logFile))) @mkdir(dirname($logFile), 0755, true);
$metricsFile = __DIR__ . '/../storage/logs/media_worker_metrics.json';
if (!is_dir(dirname($metricsFile))) @mkdir(dirname($metricsFile), 0755, true);
$once = in_array('--once', $argv ?? []);
$maxAttempts = intval(getenv('MEDIA_WORKER_MAX_ATTEMPTS') ?: 5);
$jobTimeout = intval(getenv('MEDIA_WORKER_JOB_TIMEOUT') ?: 120); // seconds
$lockDir = __DIR__ . '/../storage/locks'; if (!is_dir($lockDir)) @mkdir($lockDir,0755,true);
$workerId = getmypid() ?: uniqid('w');

function lw($msg) {
  global $logFile;
  $ts = date('c');
  file_put_contents($logFile, "[$ts] $msg\n", FILE_APPEND);
}

function incMetric($k, $n=1){ global $metricsFile; $m = @json_decode(@file_get_contents($metricsFile), true) ?: ['processed'=>0,'succeed'=>0,'failed'=>0]; $m[$k] = ($m[$k] ?? 0) + $n; file_put_contents($metricsFile, json_encode($m)); }

lw('worker started' . ($once ? ' (once mode)' : ''));

while (true) {
  $job = $pdo->query("SELECT * FROM media_jobs WHERE status='pending' OR (status='failed' AND attempts < " . intval($maxAttempts) . ") ORDER BY created_at ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
  if (!$job) {
    lw('no jobs, sleeping');
    if ($once) break;
    sleep(5); continue;
  }
  $jid = $job['id'];
  // optimistic lock: set running and increment attempts
  $pdo->prepare("UPDATE media_jobs SET status='running', attempts = attempts + 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$jid]);
  lw("processing job $jid (media {$job['media_id']}) attempts={$job['attempts']} ");

  // concurrency control: create lock file per job
  $lockFile = $lockDir . '/job_' . $jid . '.lock';
  if (file_exists($lockFile)) { lw("job $jid locked by other worker, skipping"); $pdo->prepare('UPDATE media_jobs SET status=? WHERE id=?')->execute(['pending',$jid]); if ($once) break; continue; }
  file_put_contents($lockFile, json_encode(['worker'=>$workerId,'ts'=>time()]));

  $media = $pdo->prepare('SELECT * FROM media_files WHERE id = ? LIMIT 1'); $media->execute([$job['media_id']]); $m = $media->fetch(PDO::FETCH_ASSOC);
  if (!$m) { lw("media not found for job $jid"); $pdo->prepare('UPDATE media_jobs SET status=? WHERE id=?')->execute(['failed',$jid]); if ($once) break; continue; }
  $source = __DIR__ . '/../public' . $m['url'];
  if (!is_file($source)) { lw("source missing: $source"); $pdo->prepare('UPDATE media_jobs SET status=? WHERE id=?')->execute(['failed',$jid]); if ($once) break; continue; }

  // Run ffmpeg with timeout using proc_open
  $ff = trim(shell_exec('where ffmpeg 2>nul || which ffmpeg 2>/dev/null')) ?: 'ffmpeg';
  $outWebm = dirname($source) . DIRECTORY_SEPARATOR . 'derived_' . pathinfo($m['filename'], PATHINFO_FILENAME) . '.webm';
  $cmd = escapeshellcmd($ff) . ' -y -i ' . escapeshellarg($source) . ' -c:v libvpx-vp9 -b:v 800k -c:a libopus -b:a 96k ' . escapeshellarg($outWebm);
  $descriptors = [1 => ['pipe','w'], 2 => ['pipe','w']];
  $proc = proc_open($cmd, $descriptors, $pipes);
  $ok = false; $out = '';
  if (is_resource($proc)) {
    $start = time();
    stream_set_blocking($pipes[1], false); stream_set_blocking($pipes[2], false);
    while (true) {
      $read = [$pipes[1], $pipes[2]]; $write = null; $except = null;
      $n = stream_select($read, $write, $except, 1, 0);
      if ($n > 0) {
        foreach ($read as $r) { $out .= stream_get_contents($r); }
      }
      $status = proc_get_status($proc);
      if (!$status['running']) break;
      if (time() - $start > $jobTimeout) {
        // timeout: terminate
        proc_terminate($proc);
        lw("job $jid ffmpeg timed out after {$jobTimeout}s");
        break;
      }
    }
    $rc = proc_close($proc);
    lw("ffmpeg rc=$rc output=".trim($out));
    if ($rc === 0 && file_exists($outWebm)) {
      $rel = str_replace(realpath(__DIR__ . '/../public'), '', realpath($outWebm));
      $rel = str_replace('\\', '/', $rel);
      $pdo->prepare('UPDATE media_files SET thumb720 = ? WHERE id = ?')->execute([$rel, $m['id']]);
      $pdo->prepare('UPDATE media_jobs SET status=? WHERE id=?')->execute(['done',$jid]);
      lw("job $jid done, output $rel");
      $ok = true; incMetric('processed',1); incMetric('succeed',1);
    } else {
      incMetric('processed',1); incMetric('failed',1);
    }
  } else {
    lw('proc_open failed to start ffmpeg');
  }

  // remove lock file
  @unlink($lockFile);

  if (!$ok) {
    $attempts = intval($job['attempts']) + 1;
    if ($attempts >= $maxAttempts) {
      $pdo->prepare('UPDATE media_jobs SET status=? WHERE id=?')->execute(['failed',$jid]);
      lw("job $jid failed permanently after $attempts attempts");
    } else {
      $pdo->prepare('UPDATE media_jobs SET status=? WHERE id=?')->execute(['pending',$jid]);
      $backoff = pow(2, $attempts);
      lw("job $jid will retry after {$backoff}s (attempt $attempts)");
      sleep($backoff);
    }
  }

  if ($once) break;
}

lw('worker exiting');
