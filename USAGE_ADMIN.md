Admin quick usage

1. Create an admin user manually:
   INSERT INTO users (name,email,password,is_admin,created_at,updated_at) VALUES ('Admin','admin@xlerion.com','<hash>',1,NOW(),NOW());
   Generate hash with PHP: <?php echo password_hash('yourpassword', PASSWORD_DEFAULT); ?>

2. Login: /admin/login.php
3. Export: /admin/export.php?table=contacts
4. Import: /admin/import.php (CSV with headers first_name,last_name,email,phone)
5. Password reset: /admin/request_reset.php

Notes: For production, restrict access to /admin via HTTP auth or IP allowlist in .htaccess.
