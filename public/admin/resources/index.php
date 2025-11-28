<?php
// Fallback stub so Apache without mod_rewrite/AllowOverride can still serve /admin/resources
// This file simply bootstraps the app front controller to handle the request.
$docRoot = __DIR__ . '/../../..';
require_once $docRoot . '/public/index.php';
