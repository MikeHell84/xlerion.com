<?php
header('Content-Type: application/json; charset=utf-8');
// Simple templates API: list/get/save/delete
// Storage: ../data/templates.json (created automatically)

session_start();

function dataFilePath(){
    $path = __DIR__ . '/../data/templates.json';
    $dir = dirname($path);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    if (!file_exists($path)) @file_put_contents($path, json_encode(new stdClass()));
    return $path;
}

function readTemplates(){
    $path = dataFilePath();
    $fp = fopen($path, 'c+');
    if (!$fp) return [];
    flock($fp, LOCK_SH);
    $raw = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    $arr = json_decode($raw, true);
    if (!is_array($arr)) return [];
    return $arr;
}

function writeTemplates($arr){
    $path = dataFilePath();
    $fp = fopen($path, 'c+');
    if (!$fp) return false;
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    rewind($fp);
    $ok = fwrite($fp, json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $ok !== false;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'list';

try{
    if ($action === 'list'){
        $items = array_values(readTemplates());
        echo json_encode(['ok'=>true,'data'=>$items]);
        exit;
    }

    if ($action === 'get'){
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$id){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'missing id']); exit; }
        $all = readTemplates();
        $item = isset($all[$id]) ? $all[$id] : null;
        echo json_encode(['ok'=>true,'data'=>$item]);
        exit;
    }

    if ($action === 'save'){
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true);
        if (!is_array($body)){
            http_response_code(400);
            echo json_encode(['ok'=>false,'error'=>'invalid json']);
            exit;
        }
        $all = readTemplates();
        $id = isset($body['id']) && $body['id'] ? $body['id'] : uniqid('tpl_', true);
        $now = date(DATE_ATOM);
        if (!isset($body['created_at']) || !$body['created_at']) $body['created_at'] = $now;
        $body['updated_at'] = $now;
        $all[$id] = $body;
        $ok = writeTemplates($all);
        if ($ok) echo json_encode(['ok'=>true,'id'=>$id]); else { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'write_failed']); }
        exit;
    }

    if ($action === 'delete'){
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        if (!$id){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'missing id']); exit; }
        $all = readTemplates();
        if (!isset($all[$id])){ http_response_code(404); echo json_encode(['ok'=>false,'error'=>'not_found']); exit; }
        unset($all[$id]);
        $ok = writeTemplates($all);
        if ($ok) echo json_encode(['ok'=>true]); else { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'write_failed']); }
        exit;
    }

    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'invalid_action']);

}catch(Exception $e){
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}

?><?php
// Minimal templates API - stores templates as JSON files in ../storage
header('Content-Type: application/json; charset=utf-8');
$storageDir = __DIR__ . '/../../storage';
if (!is_dir($storageDir)) { @mkdir($storageDir, 0755, true); }
$file = $storageDir . '/templates.json';
if (!file_exists($file)) { file_put_contents($file, json_encode([])); }

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';
    $data = json_decode(@file_get_contents($file), true) ?: [];
    if ($action === 'get' && !empty($_GET['id'])) {
        foreach ($data as $t) { if (($t['id'] ?? '') === $_GET['id']) { 
            // if preview requested, return html field directly if present
            if (!empty($_GET['preview']) && isset($t['html'])){ header('Content-Type: text/html; charset=utf-8'); echo $t['html']; exit; }
            echo json_encode($t); exit; } }
        echo json_encode([ 'error' => 'not_found' ]); exit;
    }
    // default: list — return wrapped items array for client convenience
    echo json_encode(['items' => array_values($data)]); exit;
}

if ($method === 'POST') {
    $body = file_get_contents('php://input');
    $payload = json_decode($body, true);
    if (!$payload) { // try form-encoded fallback
        $payload = $_POST;
    }
    // support delete action
    if (!empty($payload['action']) && $payload['action'] === 'delete' && !empty($payload['id'])){
        $data = json_decode(@file_get_contents($file), true) ?: [];
        $deleted = null;
        foreach($data as $i=>$t){ if (($t['id'] ?? '') === $payload['id']){ $deleted = $t; array_splice($data,$i,1); break; } }
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        echo json_encode(['ok' => (bool)$deleted, 'deleted' => $deleted]); exit;
    }
    // minimal validation
    $name = trim($payload['name'] ?? ($payload['template']['name'] ?? 'Untitled'));
    if ($name === '') { http_response_code(400); echo json_encode(['error'=>'name_required']); exit; }

    $data = json_decode(@file_get_contents($file), true) ?: [];
    $id = $payload['id'] ?? uniqid('tpl_', true);
    $template = [
        'id' => $id,
        'name' => $name,
        'description' => $payload['description'] ?? ($payload['template']['description'] ?? ''),
        'author' => $payload['author'] ?? ($_SESSION['user'] ?? 'system'),
        'createdAt' => date('c'),
        'blocks' => $payload['blocks'] ?? ($payload['template']['blocks'] ?? []),
        'theme' => $payload['theme'] ?? ($payload['template']['theme'] ?? null),
    // accept a raw html field for simple templates
    'html' => $payload['html'] ?? ($payload['template']['html'] ?? null),
    // accept named regions (header/content/footer) for schema-driven templates
    'regions' => isset($payload['regions']) ? $payload['regions'] : (isset($payload['template']['regions']) ? $payload['template']['regions'] : null),
    ];

    // replace if exists
    $found = false;
    foreach ($data as $i => $t) { if (($t['id'] ?? '') === $id) { $data[$i] = $template; $found = true; break; } }
    if (!$found) $data[] = $template;
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode($template);
    exit;
}

http_response_code(405);
echo json_encode(['error'=>'method_not_allowed']);
