<?php
$title = 'Inicio - Xlerion';
ob_start(); ?>
<?php // ensure hero id is unique when layout also renders a hero; prefix with page slug if available ?>
<?php $hero_id = isset($hero_id) ? $hero_id : ('hero-title-' . ($slug ?? 'inicio')); ?>
<div class="hero-content text-center" role="main" aria-labelledby="<?php echo $hero_id; ?>">
  <h1 id="<?php echo $hero_id; ?>">Xlerion – Ingeniería Modular para la Cultura y la Tecnología</h1>
  <p class="lead">Soluciones que transforman. Diagnósticos que empoderan.</p>

  <div class="hero-body container">
    <p class="mb-3">Desde Nocaima, Cundinamarca, emerge Xlerion como una iniciativa independiente, empírica y neurodivergente que redefine la creación, automatización y documentación de soluciones técnicas para la industria cultural y tecnológica. Más que una empresa, Xlerion es una filosofía modular orientada al impacto territorial, la autosuficiencia creativa y la transferencia de conocimiento.</p>

    <div class="d-flex justify-content-center gap-3 flex-wrap" role="region" aria-label="Botones principales">
      <a class="btn btn-primary" href="/proyectos" role="button" aria-label="Explorar portafolio">Explorar portafolio</a>
      <a class="btn btn-outline-light" href="/contact" role="button" aria-label="Contactar al fundador">Contactar al fundador</a>
      <a class="btn btn-secondary" href="/media/dossier.pdf" role="button" aria-label="Descargar dossier institucional" download>Descargar dossier institucional</a>
    </div>
  </div>
</div>


<!-- Section stripe: highlighted band with title + excerpt -->
<div class="container my-3">
  <div class="section-stripe" role="region" aria-label="Resumen destacado de secciones">
    <div class="ss-title">Secciones destacadas</div>
    <div class="ss-excerpt">Explora nuestras áreas principales: Filosofía, Soluciones, Documentación y Proyectos. Haz clic en cualquier tarjeta para leer la sección completa.</div>
  </div>
</div>

<!-- Section cards: small excerpts for main sections -->
<section class="container my-5" aria-label="Resumen de secciones">
  <div class="row g-3">
    <div class="col-12 col-md-6 col-lg-4">
      <article class="card h-100 shadow-sm card-preview dark" data-title="Filosofía" data-content="La filosofía de Xlerion promueve autonomía, colaboración e impacto cultural sostenible. Impulsa soluciones modulares y documentación replicable." data-href="/filosofia">
        <img src="/media/images/parallax/blog-bitacora-parallax.jpg" class="card-img-top" alt="Filosofía">
        <div class="card-body">
          <h5 class="card-title">Filosofía</h5>
          <p class="card-text">Principios que promueven la autonomía, la colaboración y el impacto cultural sostenible.</p>
          <div class="mt-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-light btn-sm preview-btn">Vista rápida</button>
            <a href="/filosofia" class="btn btn-link">Leer más →</a>
          </div>
        </div>
      </article>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
      <article class="card h-100 shadow-sm card-preview dark" data-title="Soluciones" data-content="Toolkits modulares y sistemas de diagnóstico para videojuegos, multimedia y pipelines avanzados." data-href="/soluciones">
        <img src="/media/images/parallax/servicios-productos-parallax.jpg" class="card-img-top" alt="Soluciones">
        <div class="card-body">
          <h5 class="card-title">Soluciones</h5>
          <p class="card-text">Toolkits modulares, diagnósticos y herramientas para entornos de alta exigencia.</p>
          <div class="mt-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-light btn-sm preview-btn">Vista rápida</button>
            <a href="/soluciones" class="btn btn-link">Leer más →</a>
          </div>
        </div>
      </article>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
      <article class="card h-100 shadow-sm card-preview dark" data-title="Documentación" data-content="Guías, diagramas y manuales por módulo que aseguran continuidad y transferencia de conocimiento." data-href="/documentacion">
        <img src="/media/images/parallax/documentacion-parallax.jpg" class="card-img-top" alt="Documentación">
        <div class="card-body">
          <h5 class="card-title">Documentación</h5>
          <p class="card-text">Manuales, diagramas y guías que facilitan la implementación y el mantenimiento.</p>
          <div class="mt-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-light btn-sm preview-btn">Vista rápida</button>
            <a href="/documentacion" class="btn btn-link">Leer más →</a>
          </div>
        </div>
      </article>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
      <article class="card h-100 shadow-sm card-preview dark" data-title="Proyectos" data-content="Proyectos como 'Total Darkness' muestran la aplicación de la filosofía modular en producciones reales." data-href="/proyectos">
        <img src="/media/images/parallax/proyectos-parallax.jpg" class="card-img-top" alt="Proyectos">
        <div class="card-body">
          <h5 class="card-title">Proyectos</h5>
          <p class="card-text">Proyectos destacados que combinan técnica, narración y documentación rigurosa.</p>
          <div class="mt-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-light btn-sm preview-btn">Vista rápida</button>
            <a href="/proyectos" class="btn btn-link">Leer más →</a>
          </div>
        </div>
      </article>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
      <article class="card h-100 shadow-sm card-preview dark" data-title="Acerca del creador" data-content="Miguel Eduardo Rodríguez Martínez — desarrollador autodidacta con enfoque neurodivergente. Fundador de Xlerion TechLab." data-href="/acerca-del-creador">
        <img src="/media/intro.jpg" class="card-img-top" alt="Acerca del creador">
        <div class="card-body">
          <h5 class="card-title">Acerca del creador</h5>
          <p class="card-text">Trayectoria, filosofía personal y enfoque técnico-creativo del fundador.</p>
          <div class="mt-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-light btn-sm preview-btn">Vista rápida</button>
            <a href="/acerca-del-creador" class="btn btn-link">Leer más →</a>
          </div>
        </div>
      </article>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
      <article class="card h-100 shadow-sm card-preview dark" data-title="Convocatorias y alianzas" data-content="Convocatorias, postulaciones y espacio para aliados institucionales." data-href="/convocatorias-alianzas">
        <img src="/media/images/parallax/contacto-parallax.jpg" class="card-img-top" alt="Convocatorias y alianzas">
        <div class="card-body">
          <h5 class="card-title">Convocatorias y alianzas</h5>
          <p class="card-text">Oportunidades de colaboración y convocatoria a proyectos culturales y tecnológicos.</p>
          <div class="mt-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-light btn-sm preview-btn">Vista rápida</button>
            <a href="/convocatorias-alianzas" class="btn btn-link">Leer más →</a>
          </div>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- Modal for quick previews -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Vista rápida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <h4 id="pm-title"></h4>
        <p id="pm-content"></p>
      </div>
      <div class="modal-footer">
        <a id="pm-link" class="btn btn-primary" href="#">Ir a la sección</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
