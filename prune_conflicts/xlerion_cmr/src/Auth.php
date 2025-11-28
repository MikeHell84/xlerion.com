<?php
require_once __DIR__ . '/Model/Database.php';
class Auth {
    public static function attempt($email, $password) {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) return false;
        if (!password_verify($password, $user['password'])) return false;
        // mark session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        return true;
    }
    public static function check() { return !empty($_SESSION['user_id']); }
    public static function user() {
        if (empty($_SESSION['user_id'])) return null;
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT id,name,email,is_admin FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function logout() {
        unset($_SESSION['user_id']); session_regenerate_id(true);
    }
    public static function requireAdmin() {
        // Allow API token auth: Authorization: Bearer <token>
        $bearer = null;
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $m = null; if (preg_match('/Bearer\s+(\S+)/i', $_SERVER['HTTP_AUTHORIZATION'], $m)) $bearer = $m[1];
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $m = null; if (preg_match('/Bearer\s+(\S+)/i', $_SERVER['REDIRECT_HTTP_AUTHORIZATION'], $m)) $bearer = $m[1];
        }
        $envToken = getenv('API_ADMIN_TOKEN');
        if ($envToken === false || $envToken === null) {
            // try loading from project .env
            $p = dirname(__DIR__) . '/.env';
            if (file_exists($p)) {
                foreach (file($p, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    if (strpos(trim($line), '#') === 0) continue;
                    [$k,$v] = array_map('trim', explode('=', $line, 2) + [1 => null]);
                    if ($k === 'API_ADMIN_TOKEN') { $envToken = trim($v, " \"'\n\r"); break; }
                }
            }
        }
        if ($bearer && $envToken && hash_equals($envToken, $bearer)) {
            // treat as authenticated admin for this request
            return true;
        }
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $isApi = (strpos($uri, '/api/') === 0);
        $isXhr = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

        if (!self::check()) {
            if ($isApi || stripos($accept, 'application/json') !== false || $isXhr) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'unauthenticated']);
                exit;
            }
            header('Location: /admin/login.php'); exit;
        }
        $u = self::user();
        if (!$u || !$u['is_admin']) {
            if ($isApi || stripos($accept, 'application/json') !== false || $isXhr) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'forbidden']);
                exit;
            }
            http_response_code(403); echo 'Forbidden'; exit;
        }
    }

    // Password reset tokens
    public static function generateResetToken($email) {
        $pdo = Database::pdo();
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1h
        $stmt = $pdo->prepare('INSERT INTO password_resets (email, token, expires_at, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$email, $token, $expires]);
        return $token;
    }
    public static function validateResetToken($token) {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT * FROM password_resets WHERE token = ? AND expires_at >= NOW() LIMIT 1');
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function resetPassword($token, $newPassword) {
        $pdo = Database::pdo();
        $row = self::validateResetToken($token);
        if (!$row) return false;
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
        $stmt->execute([$hash, $row['email']]);
        // remove token
        $pdo->prepare('DELETE FROM password_resets WHERE token = ?')->execute([$token]);
        return true;
    }
}
