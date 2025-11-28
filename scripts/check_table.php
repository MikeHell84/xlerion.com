<?php
require_once __DIR__ . '/../src/Model/Database.php';
$pdo = Database::pdo();
$st = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='resources'");
$res = $st->fetchAll();
var_export($res);
