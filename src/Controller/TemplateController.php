<?php
require_once __DIR__ . '/../Model/Template.php';
require_once __DIR__ . '/../Auth.php';

class TemplateController {
    public function index() {
        header('Content-Type: application/json');
        echo json_encode(Template::findAll());
    }

    public function show($id) {
        header('Content-Type: application/json');
        $t = Template::find($id);
        if (!$t) { http_response_code(404); echo json_encode(['error'=>'not found']); return; }
        echo json_encode($t);
    }

    public function create() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    Auth::requireAdmin();
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = Template::create($input);
        header('Content-Type: application/json');
        echo json_encode(['id'=>$id]);
    }

    public function update($id) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    Auth::requireAdmin();
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $ok = Template::update($id, $input);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>$ok]);
    }

    public function delete($id) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    Auth::requireAdmin();
    $ok = Template::delete($id);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>$ok]);
    }

    public function duplicate($id) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    Auth::requireAdmin();
    $newId = Template::duplicate($id, $_SESSION['user_id'] ?? null);
        header('Content-Type: application/json');
        echo json_encode(['id'=>$newId]);
    }
}
