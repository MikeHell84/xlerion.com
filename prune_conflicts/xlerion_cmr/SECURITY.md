Security checklist applied to Vanilla CMR:

- Passwords hashed with password_hash() and PASSWORD_DEFAULT.
- CSRF tokens used in contact form and admin forms (session-based generate/verify) where applicable.
- Rate-limits: simple session-based attempt counters on login and contact submit.
- Input validation and sanitization: filter_var for emails; prepared statements for DB.
- Headers: .htaccess provides basic headers; recommend enabling CSP via server or adding to .htaccess.
- File permissions: storage writable only; do not place .env in public_html.
- OPcache: enable via cPanel PHP settings.
- Disable directory listing in Apache (via .htaccess).
