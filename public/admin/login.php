<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/RateLimiter.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$limiter = new RateLimiter(5,300); // 5 attempts per 5 minutes per IP
$err = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    // simple rate-limit by IP
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'anon';
    if (!$limiter->hit($ip)) { $err = 'Demasiados intentos desde tu IP, intenta más tarde.'; }
    else {
        if (Auth::attempt($email, $password)) { header('Location: /admin/dashboard.php'); exit; }
        $_SESSION['admin_attempts'] = ($_SESSION['admin_attempts'] ?? 0) + 1;
        $err = 'Credenciales inválidas.';
    }
}

// Banner tweaks: uppercase title and hide the banner subtitle on the login page
$title = strtoupper('Admin - Login');
$hide_banner_subtitle = true; // layout.php will skip rendering the subtitle when present
$banner_title_class = 'section-title-lg';
ob_start();
?>
  <div class="row justify-content-center my-5">
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card-preview">
        <div style="padding:1.2rem">
          <h3 style="margin-top:0;margin-bottom:0.6rem">Ingreso Administrador</h3>
          <?php if ($err): ?><div class="flash" style="color:#7a1717; background: rgba(255,240,240,0.9); border-left-color:#ff6b6b"><?=htmlspecialchars($err)?></div><?php endif; ?>
          <form method="post" novalidate style="display:flex;flex-direction:column;gap:0.75rem;margin-top:0.6rem">
            <label class="mb-2">Email
              <input class="form-control" name="email" type="email" required>
            </label>
            <label class="mb-2">Contraseña
              <input class="form-control" name="password" type="password" required>
            </label>
            <div style="display:flex;gap:0.5rem;align-items:center;justify-content:space-between;margin-top:0.25rem">
              <button class="btn btn-primary" type="submit">Entrar</button>
              <a class="btn btn-outline-light" href="/admin/request_reset.php">Olvidé mi contraseña</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';

