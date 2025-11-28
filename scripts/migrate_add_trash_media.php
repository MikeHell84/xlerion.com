<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$driver = (strtolower(getenv('DB_CONNECTION')?: getenv('DB_DRIVER')?: 'sqlite'));
try {
  if ($driver === 'sqlite') {
    // SQLite: check columns exist
    $cols = $pdo->query("PRAGMA table_info('media_files')")->fetchAll(PDO::FETCH_ASSOC);
    $names = array_column($cols, 'name');
    if (!in_array('deleted_at',$names)) {
      $pdo->exec("ALTER TABLE media_files ADD COLUMN deleted_at TEXT");
      echo "added deleted_at\n";
    }
    if (!in_array('deleted_by',$names)) {
      $pdo->exec("ALTER TABLE media_files ADD COLUMN deleted_by INTEGER");
      echo "added deleted_by\n";
    }
  } else {
    // MySQL
    $pdo->exec("ALTER TABLE media_files ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL");
    $pdo->exec("ALTER TABLE media_files ADD COLUMN IF NOT EXISTS deleted_by INT NULL");
    echo "altered media_files for MySQL\n";
  }
  echo "Done\n";
} catch (Exception $e) {
  echo "Migration failed: " . $e->getMessage() . "\n";
}
