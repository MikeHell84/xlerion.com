<?php
// integration_test_sanitizer.php
// Posts test payloads to /api/templates and verifies sanitizer output in DB
require_once __DIR__ . '/../src/Model/Database.php';
function post($payload){
    $ch = curl_init('http://127.0.0.1:8080/api/templates');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer devtoken123','Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $res = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return [$info['http_code'],$res];
}

$payload = [
    'name' => 'Sanitizer Integration',
    'description' => 'Integration test',
    'data' => ['html' => "<div onclick=\"alert('x')\"><a href=\"javascript:alert(1)\">bad</a><img src=\"data:image/png;base64,AAA\" data-custom=\"keep\"><iframe src=\"https://evil.example.com/embed/1\"></iframe><iframe src=\"https://www.youtube.com/embed/abc\" allowfullscreen></iframe></div>"]
];
list($code,$res) = post($payload);
echo "POST HTTP: $code\nResponse: $res\n";
// Query DB for latest inserted row
$pdo = Database::pdo();
$st = $pdo->query('SELECT id,data FROM templates ORDER BY id DESC LIMIT 1');
$row = $st->fetch(PDO::FETCH_ASSOC);
if (!$row) { echo "No row found\n"; exit(1); }
$data = json_decode($row['data'], true);
echo "Stored HTML:\n" . ($data['html'] ?? '') . "\n";
// Basic assertions
$stored = $data['html'] ?? '';
$results = [];
$results['onclick_removed'] = (strpos($stored, 'onclick') === false);
$results['javascript_href_removed'] = (strpos($stored, 'javascript:') === false);
$results['data_attr_present'] = (strpos($stored, 'data-custom') !== false);
$results['youtube_iframe_present'] = (strpos($stored, 'youtube.com') !== false);
$results['evil_iframe_removed'] = (strpos($stored, 'evil.example.com') === false);
foreach ($results as $k => $v) echo "$k: " . ($v ? 'OK' : 'FAIL') . "\n";
