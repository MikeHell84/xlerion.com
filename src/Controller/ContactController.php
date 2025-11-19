<?php
class ContactController {
    protected $pdo;
    protected $limiter;
    public function __construct() { $this->pdo = Database::pdo(); $this->limiter = new RateLimiter(10,60); }

    public function show() {
        // CSRF token
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
        include __DIR__ . '/../../views/contact.php';
    }

    public function submit() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'anon';
        if (!$this->limiter->hit($ip)) { http_response_code(429); echo 'Rate limit. Try later.'; exit; }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $csrf = $_POST['csrf'] ?? '';
        $hp = $_POST['website'] ?? null; // honeypot

        if ($hp) { header('Location: /'); exit; }
        if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) { http_response_code(403); echo 'Invalid CSRF'; exit; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $_SESSION['flash'] = 'Email inválido'; header('Location: /contact'); exit; }
        if ($name === '' || $message === '') { $_SESSION['flash'] = 'Completa los campos'; header('Location: /contact'); exit; }

        // sanitize message html minimally
        require_once __DIR__ . '/../Security.php';
        $cleanMessage = Security::sanitizeHtml($message);

        // Insert into forms_submissions
        $stmt = $this->pdo->prepare('INSERT INTO forms_submissions (form_name,payload,ip,user_agent,is_read,created_at) VALUES (?, ?, ?, ?, 0, NOW())');
        $payload = json_encode(['name'=>$name,'email'=>$email,'message'=>$cleanMessage]);
        $stmt->execute(['contact_form',$payload,$_SERVER['REMOTE_ADDR'] ?? null,$_SERVER['HTTP_USER_AGENT'] ?? null]);

        // Send email via PHP mail (sendmail configured in cPanel)
        $admin = getenv('MAIL_ADMIN') ?: 'admin@xlerion.com';
        $subject = "Nuevo contacto desde sitio: " . ($name ?: 'sin nombre');
        // prevent header injection
        $safeEmail = preg_replace('/[\r\n]+/','',$email);
        $body = "Nombre: $name\nEmail: $safeEmail\n\nMensaje:\n".$cleanMessage;
        $headers = [];
        $headers[] = 'From: ' . ($safeEmail);
        $headers[] = 'Reply-To: ' . ($safeEmail);
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        @mail($admin, $subject, $body, implode("\r\n", $headers));

        $_SESSION['flash'] = 'Gracias, tu mensaje fue recibido.';
        header('Location: /contact');
    }
}
