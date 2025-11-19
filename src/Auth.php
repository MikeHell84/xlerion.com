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
        if (!self::check()) { header('Location: /admin/login.php'); exit; }
        $u = self::user(); if (!$u || !$u['is_admin']) { http_response_code(403); echo 'Forbidden'; exit; }
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
