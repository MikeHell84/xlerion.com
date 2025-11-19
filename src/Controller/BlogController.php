<?php
class BlogController {
    public function index() { include __DIR__ . '/../../views/blog_index.php'; }
    public function show() {
        // simple slug from REQUEST_URI
        $parts = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $slug = end($parts);
        $_GET['slug'] = $slug;
        include __DIR__ . '/../../views/blog_post.php';
    }
}
