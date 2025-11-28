<?php
$db = new PDO('sqlite:' . __DIR__ . '/../storage/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "SETTINGS:\n";
foreach($db->query("SELECT k,v FROM settings ORDER BY k") as $r) {
    echo $r['k'] . " => " . ($r['v'] ?? '') . "\n";
}

echo "\nFOOTER VARIANTS:\n";
foreach($db->query("SELECT id,name,substr(data,1,800) as data FROM footer_variants ORDER BY id") as $v) {
    echo $v['id'] . "\t" . $v['name'] . "\t" . $v['data'] . "\n\n";
}
