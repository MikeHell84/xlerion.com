<?php
$dirs = ['storage','storage/backups','storage/ratelimit','storage/uploads'];
foreach ($dirs as $d) {
  if (!is_dir(__DIR__ . '/../' . $d)) mkdir(__DIR__ . '/../' . $d, 0755, true);
}
echo "Dirs created\n";
