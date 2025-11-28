<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../../../../xlerion_cmr/src/Model/Template.php';

// Basic guard: require POST and id
if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}
$id = $_POST['id'] ?? null;
if (!$id){
    http_response_code(400);
    echo 'Missing id';
    exit;
}

try{
    $ok = Template::delete($id);
    // If request is AJAX, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
        header('Content-Type: application/json');
        echo json_encode(['ok'=> (bool)$ok]);
        exit;
    }
    // Redirect back to templates list
    header('Location: /admin/templates');
    exit;
} catch (Exception $e){
    http_response_code(500);
    echo 'Error: ' . $e->getMessage();
    exit;
}
