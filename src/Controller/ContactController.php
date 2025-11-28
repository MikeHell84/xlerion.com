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

    // Normalise and sanitize raw inputs
    $rawName = $_POST['name'] ?? '';
    $rawEmail = $_POST['email'] ?? '';
    $rawMessage = $_POST['message'] ?? '';
    $name = substr(trim(filter_var($rawName, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)), 0, 200);
    $email = trim(filter_var($rawEmail, FILTER_SANITIZE_EMAIL));
    $message = trim($rawMessage);
    $csrf = $_POST['csrf'] ?? '';
    $hp = $_POST['website'] ?? null; // honeypot

    $errors = [];
    if ($hp) { header('Location: /'); exit; }
    if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) { http_response_code(403); echo 'Invalid CSRF'; exit; }
    if ($name === '') $errors['name'] = 'El nombre es obligatorio.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Introduce un correo válido.';
    if ($message === '' || strlen($message) < 10) $errors['message'] = 'El mensaje debe tener al menos 10 caracteres.';
    if (!empty($errors)) { $_SESSION['form_errors'] = $errors; $_SESSION['form_old'] = ['name'=>$rawName,'email'=>$rawEmail,'message'=>$rawMessage]; header('Location: /contact'); exit; }

        // sanitize message html minimally
        require_once __DIR__ . '/../Security.php';
        $cleanMessage = Security::sanitizeHtml($message);

        // Insert into forms_submissions
        $stmt = $this->pdo->prepare('INSERT INTO forms_submissions (form_name,payload,ip,user_agent,is_read,created_at) VALUES (?, ?, ?, ?, 0, datetime("now"))');
        if ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
            $stmt = $this->pdo->prepare('INSERT INTO forms_submissions (form_name,payload,ip,user_agent,is_read,created_at) VALUES (?, ?, ?, ?, 0, NOW())');
        }
        $payload = json_encode(['name'=>$name,'email'=>$email,'message'=>$cleanMessage]);
        $stmt->execute(['contact_form',$payload,$_SERVER['REMOTE_ADDR'] ?? null,$_SERVER['HTTP_USER_AGENT'] ?? null]);

        // Track a page_view / event for analytics (best-effort)
        try {
            if ($this->pdo) {
                if ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
                    $pv = $this->pdo->prepare('INSERT INTO page_views (page_id, slug, ip, user_agent, created_at) VALUES (?, ?, ?, ?, datetime("now"))');
                    $pv->execute([null, 'contact_form', $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null]);
                } else {
                    $pv = $this->pdo->prepare('INSERT INTO page_views (page_id, slug, ip, user_agent, created_at) VALUES (?, ?, ?, ?, NOW())');
                    $pv->execute([null, 'contact_form', $_SERVER['REMOTE_ADDR'] ?? null, $_SERVER['HTTP_USER_AGENT'] ?? null]);
                }
            }
        } catch (Exception $e) {
            // ignore analytics failure
        }

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

    // On success: set flash and a short-lived analytics flag consumed by the view JS
    $_SESSION['flash'] = 'Gracias, tu mensaje fue recibido.';
    $_SESSION['analytics_event'] = 'contact_form_submitted';
    header('Location: /contact');
    }
}
