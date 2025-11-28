<?php
$title = 'Legal - Xlerion';
ob_start(); ?>

<div class="container my-5">
  <!-- Hero image -->
  <div class="mb-4">
    <img src="/media/placeholder-hero.svg" alt="Legal hero" class="img-fluid rounded shadow-sm" style="width:100%;height:auto;">
  </div>

  <h1>Legal & Privacidad</h1>

  <section class="mt-4">
    <h2>Política de privacidad de datos</h2>
    <p class="text-muted">Xlerion se compromete a proteger la información personal de sus usuarios. Los datos recogidos se manejan con confidencialidad y seguridad conforme a las normativas aplicables. Recopilamos únicamente la información necesaria para proporcionar servicios y responder a solicitudes de contacto.</p>
    <ul class="small text-muted">
      <li>Finalidad: Responder solicitudes, gestionar servicios y enviar comunicaciones relacionadas.</li>
      <li>Retención: Conservamos datos mientras exista una relación contractual o hasta que el usuario solicite su eliminación.</li>
      <li>Derechos: Acceso, rectificación, supresión y oposición. Contacto: admin@xlerion.com</li>
    </ul>
  </section>

  <section class="mt-4">
    <h2>Términos de uso</h2>
    <p class="text-muted">El acceso y uso del sitio están sujetos a los términos y condiciones que regulan el uso aceptable. El material aquí publicado tiene fines informativos y no constituye asesoramiento profesional.</p>
    <p class="text-muted small">Queda prohibida la reproducción parcial o total de contenidos sin autorización expresa. Para licencias de uso y distribución de toolkits y material técnico, consulte las secciones específicas de cada proyecto o solicite información a toolkit@xlerion.com.</p>

    <!-- thumb image -->
    <div class="mt-3">
      <img src="/media/placeholder-thumb.svg" alt="Legal thumbnail" class="img-fluid rounded" style="max-width:320px;">
    </div>
  </section>

  <section class="mt-4">
    <h2>Contacto legal</h2>
    <div class="row align-items-center">
      <div class="col-md-4 text-center">
        <img src="/media/placeholder-portrait.svg" alt="Legal contact" class="img-fluid rounded mb-2" style="max-height:180px;object-fit:cover">
      </div>
      <div class="col-md-8">
        <p class="small text-muted">Correos institucionales:</p>
        <ul class="small text-muted">
          <li>contactus@xlerion.com</li>
          <li>totaldarkness@xlerion.com</li>
          <li>support@xlerion.com</li>
          <li>sales@xlerion.com</li>
          <li>admin@xlerion.com</li>
        </ul>
      </div>
    </div>
  </section>

  <hr>
  <p class="small text-muted">Última actualización: Noviembre 2025</p>
</div>

<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
+