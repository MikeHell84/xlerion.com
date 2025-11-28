<?php
$title = 'Contacto - Xlerion';
ob_start(); ?>
<div class="container">
  <div class="row gx-4 gy-4">
    <div class="col-12 col-md-7">
      <section aria-labelledby="contact-form-heading" class="contact-card">
        <h2 id="contact-form-heading">Envíanos un mensaje</h2>
        <p class="contact-meta">Respondemos en 24‑48 horas. Completa los campos y te contactaremos.</p>
        <?php if (!empty($_SESSION['flash'])): ?>
          <div class="alert alert-success" role="status" aria-live="polite"><?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
        <?php endif; ?>
        <?php $formErrors = $_SESSION['form_errors'] ?? []; $formOld = $_SESSION['form_old'] ?? []; unset($_SESSION['form_errors'], $_SESSION['form_old']); ?>
        <form method="post" action="/contact" class="needs-validation" novalidate aria-labelledby="contact-form-heading">
          <div class="mb-3">
            <label for="c-name" class="form-label">Nombre</label>
            <input id="c-name" name="name" type="text" class="form-control" required aria-required="true" aria-describedby="c-name-error" value="<?= htmlspecialchars($formOld['name'] ?? '') ?>">
            <div id="c-name-error" class="invalid-feedback" aria-hidden="true"><?= htmlspecialchars($formErrors['name'] ?? 'Por favor indica tu nombre.') ?></div>
          </div>
          <div class="mb-3">
            <label for="c-email" class="form-label">Correo electrónico</label>
            <input id="c-email" name="email" type="email" class="form-control" required aria-required="true" aria-describedby="c-email-error" value="<?= htmlspecialchars($formOld['email'] ?? '') ?>">
            <div id="c-email-error" class="invalid-feedback" aria-hidden="true"><?= htmlspecialchars($formErrors['email'] ?? 'Introduce un email válido.') ?></div>
          </div>
          <div class="mb-3">
            <label for="c-message" class="form-label">Mensaje</label>
            <textarea id="c-message" name="message" rows="6" class="form-control" required aria-required="true" aria-describedby="c-message-error"><?= htmlspecialchars($formOld['message'] ?? '') ?></textarea>
            <div id="c-message-error" class="invalid-feedback" aria-hidden="true"><?= htmlspecialchars($formErrors['message'] ?? 'Escribe tu mensaje.') ?></div>
          </div>

          <!-- honeypot: hidden but present for bots -->
          <input type="text" name="website" class="honeypot-hidden" aria-hidden="true" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">

          <div class="d-flex gap-2">
            <button type="submit" class="contact-cta" id="contact-submit">
              <span class="submit-label">Enviar mensaje</span>
              <span class="submit-spinner" aria-hidden="true" style="display:none;margin-left:.5rem">⏳</span>
            </button>
            <a class="btn" href="/proyectos">Ver proyectos</a>
          </div>
        </form>
      </section>
    </div>
    <div class="col-12 col-md-5">
      <aside class="contact-card" aria-labelledby="contact-details-heading">
        <h5 id="contact-details-heading">Otras formas de contacto</h5>
        <ul class="contact-list">
          <li class="contact-item"><span class="material-symbols-outlined" aria-hidden="true">place</span> Nocaima, Cundinamarca</li>
          <li class="contact-item"><span class="material-symbols-outlined" aria-hidden="true">mail</span> <a href="mailto:contactus@xlerion.com">contactus@xlerion.com</a></li>
          <li class="contact-item"><span class="material-symbols-outlined" aria-hidden="true">phone</span> <a href="tel:+573208605600">+57 320 860 5600</a></li>
        </ul>
        <p class="contact-meta">Horario: Lun–Vie, 9:00–18:00</p>
        <p><a class="contact-cta" href="tel:+573208605600">Llámanos</a></p>

        <div class="mt-3">
          <h6 class="mb-2">Redes sociales</h6>
          <div class="d-flex flex-wrap gap-2 social-links">
            <a class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" href="https://www.linkedin.com/company/xlerion" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
              <!-- LinkedIn SVG -->
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4.98 3.5C4.98 4.6 4.08 5.5 2.98 5.5C1.88 5.5 1 4.6 1 3.5C1 2.4 1.9 1.5 3 1.5C4.1 1.5 4.98 2.4 4.98 3.5ZM1.5 8.98H4.5V23H1.5V8.98ZM8.5 8.98H11.3V10.4H11.37C11.83 9.52 13.07 8.58 14.86 8.58C18.9 8.58 19.5 11.05 19.5 15.05V23H16.5V15.98C16.5 13.68 16.47 10.88 13.86 10.88C11.22 10.88 10.86 12.98 10.86 15.7V23H7.86V8.98H8.5Z" fill="currentColor"/></svg>
              <span class="d-none d-sm-inline">LinkedIn</span>
            </a>
            <a class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" href="https://www.instagram.com/ultimatexlerion/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
              <!-- Instagram SVG -->
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 7.2A4.8 4.8 0 1 0 12 16.8 4.8 4.8 0 0 0 12 7.2Zm0 7.92A3.12 3.12 0 1 1 12 8.88a3.12 3.12 0 0 1 0 6.24Z" fill="currentColor"/><path d="M17.8 6.2a1.12 1.12 0 1 1 0-2.24 1.12 1.12 0 0 1 0 2.24Z" fill="currentColor"/><path d="M18.5 2H5.5C3.57 2 2 3.57 2 5.5V18.5C2 20.43 3.57 22 5.5 22H18.5C20.43 22 22 20.43 22 18.5V5.5C22 3.57 20.43 2 18.5 2ZM20 18.5C20 19.33 19.33 20 18.5 20H5.5C4.67 20 4 19.33 4 18.5V5.5C4 4.67 4.67 4 5.5 4H18.5C19.33 4 20 4.67 20 5.5V18.5Z" fill="currentColor"/></svg>
              <span class="d-none d-sm-inline">Instagram</span>
            </a>
            <a class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" href="https://www.facebook.com/xlerionultimate" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
              <!-- Facebook SVG -->
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M22 12C22 6.48 17.52 2 12 2S2 6.48 2 12c0 4.84 3.44 8.84 7.94 9.8V14.7H7.1v-2.7h2.84V9.5c0-2.8 1.66-4.34 4.2-4.34 1.22 0 2.5.22 2.5.22v2.74h-1.4c-1.38 0-1.8.85-1.8 1.73v2.07h3.06l-.49 2.7h-2.57V21.8C18.56 20.84 22 16.84 22 12Z" fill="currentColor"/></svg>
              <span class="d-none d-sm-inline">Facebook</span>
            </a>
            <a class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" href="https://www.behance.net/xlerionultimate" target="_blank" rel="noopener noreferrer" aria-label="Behance">
              <!-- Behance SVG -->
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 7h6v2H3zM3 11h6v2H3zM12 7h3c1.66 0 3 1.34 3 3s-1.34 3-3 3h-3V7zm6 8h3v2h-3zM3 17h6v2H3z" fill="currentColor"/></svg>
              <span class="d-none d-sm-inline">Behance</span>
            </a>
            <a class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" href="https://www.indiegogo.com/es/profile/miguel_rodriguez-martinez_edb9?redirect_reason#/overview" target="_blank" rel="noopener noreferrer" aria-label="Indiegogo">
              <!-- Indiegogo SVG -->
              <svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.2" fill="none"/><text x="12" y="15" font-family="Arial, Helvetica, sans-serif" font-size="9" text-anchor="middle" fill="currentColor">IG</text></svg>
              <span class="d-none d-sm-inline">Indiegogo</span>
            </a>
            <a class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" href="https://www.kickstarter.com/profile/xlerionstudios" target="_blank" rel="noopener noreferrer" aria-label="Kickstarter">
              <!-- Kickstarter SVG -->
              <svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.2" fill="none"/><text x="12" y="15" font-family="Arial, Helvetica, sans-serif" font-size="9" font-weight="700" text-anchor="middle" fill="currentColor">K</text></svg>
              <span class="d-none d-sm-inline">Kickstarter</span>
            </a>
            <a class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" href="https://www.patreon.com/xlerionstudios" target="_blank" rel="noopener noreferrer" aria-label="Patreon">
              <!-- Patreon SVG -->
              <svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="currentColor"/><text x="12" y="15" font-family="Arial, Helvetica, sans-serif" font-size="9" font-weight="700" text-anchor="middle" fill="#fff">P</text></svg>
              <span class="d-none d-sm-inline">Patreon</span>
            </a>
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>
<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
<style>
  /* small spinner/disabled styles for contact submit */
  .contact-cta[disabled] { opacity: .65; cursor: not-allowed; }
  .submit-spinner { font-size: 0.95rem; display: inline-block; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function(){
  var form = document.querySelector('form.needs-validation[aria-labelledby="contact-form-heading"]');
  if (!form) return;
  // helper to show/hide invalid messages
  function showInvalid(el, msgId){
    var err = document.getElementById(msgId);
    if (err) { err.classList.add('d-block'); err.setAttribute('aria-hidden','false'); }
    el.setAttribute('aria-invalid','true');
  }
  function hideInvalid(el, msgId){
    var err = document.getElementById(msgId);
    if (err) { err.classList.remove('d-block'); err.setAttribute('aria-hidden','true'); }
    el.removeAttribute('aria-invalid');
  }

  form.addEventListener('submit', function(e){
    var firstInvalid = null;
    var fields = [
      {el: document.getElementById('c-name'), msg: 'c-name-error'},
      {el: document.getElementById('c-email'), msg: 'c-email-error'},
      {el: document.getElementById('c-message'), msg: 'c-message-error'}
    ];
    fields.forEach(function(f){
      if (!f.el) return;
      if (!f.el.checkValidity()) {
        showInvalid(f.el, f.msg);
        if (!firstInvalid) firstInvalid = f.el;
      } else {
        hideInvalid(f.el, f.msg);
      }
    });
    if (firstInvalid) {
      e.preventDefault();
      firstInvalid.focus();
      return false;
    }
    // disable submit and show spinner to avoid double submits
    var submit = document.getElementById('contact-submit');
    if (submit) {
      submit.setAttribute('disabled','true');
      var spinner = submit.querySelector('.submit-spinner');
      var label = submit.querySelector('.submit-label');
      if (label) label.textContent = 'Enviando...';
      if (spinner) spinner.style.display = 'inline-block';
    }
    // allow submit to proceed
  }, {passive:false});

  // live validation: hide error when corrected
  ['c-name','c-email','c-message'].forEach(function(id){
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', function(){
      var map = { 'c-name':'c-name-error','c-email':'c-email-error','c-message':'c-message-error' };
      if (el.checkValidity()) hideInvalid(el, map[id]);
    });
  });
});
</script>
<?php if (!empty($_SESSION['analytics_event'])): ?>
<script>
  // fire a minimal analytics event; this assumes GA gtag or similar is loaded on the page
  (function(){
    try{
      var ev = <?= json_encode($_SESSION['analytics_event']) ?>;
      if (window.gtag) {
        gtag('event', ev, { 'event_category': 'contact', 'non_interaction': false });
      } else if (window.dataLayer) {
        window.dataLayer = window.dataLayer || []; window.dataLayer.push({ event: ev });
      }
    } catch(e) { /* ignore */ }
  })();
</script>
<?php unset($_SESSION['analytics_event']); endif; ?>
