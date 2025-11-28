<?php
$title = 'Proyectos - Xlerion';
ob_start(); ?>

<div class="container my-5">
  <h1>Proyectos destacados</h1>
  <p class="text-muted">Proyectos que muestran la aplicación de nuestra filosofía modular y de documentación.</p>

  <div class="list-group mt-4">
    <!-- Project 1 -->
    <div class="list-group-item p-3 project-row" role="button"
         data-title="Total Darkness"
         data-subtitle="Experiencia inmersiva narrativa con herramientas modulares"
         data-image="/media/placeholder-hero.svg"
         data-images='["/media/placeholder-hero.svg","/media/placeholder-thumb.svg","/media/intro.jpg"]'
         data-description="Adaptación narrativa con decisiones ramificadas, entornos 3D y cinemáticas filosóficas; énfasis en experiencia y documentación."
         data-tech='["Unreal Engine","Blender","PHP","SQLite"]'
         data-links='{"case":"/proyectos/total-darkness","repo":"https://example.com/repo/td"}'>
      <div class="row align-items-center">
        <div class="col-md-4">
          <img src="/media/placeholder-hero.svg" alt="Total Darkness" class="project-thumb rounded">
        </div>
        <div class="col-md-8">
          <h4>Proyecto: Total Darkness</h4>
          <p class="text-muted">Subtítulo: Experiencia inmersiva narrativa con herramientas modulares y documentación integrada.</p>
        </div>
      </div>
    </div>

    <!-- Project 2 -->
    <div class="list-group-item p-3 project-row" role="button"
         data-title="Xlerion Toolkit"
         data-subtitle="Kit modular para diagnóstico y logging"
         data-image="/media/placeholder-thumb.svg"
         data-images='["/media/placeholder-thumb.svg","/media/Planos.png","/media/placeholder-hero.svg"]'
         data-description="Conjunto modular de herramientas para diagnóstico, logging y análisis de rendimiento en entornos exigentes."
         data-tech='["Python","C++","JSON-Logging"]'
         data-links='{"case":"/proyectos/toolkit","docs":"/documentacion/toolkit"}'>
      <div class="row align-items-center">
        <div class="col-md-4">
            <img src="/media/placeholder-thumb.svg" alt="Xlerion Toolkit" class="project-card-img rounded">
        </div>
        <div class="col-md-8">
          <h4>Proyecto: Xlerion Toolkit</h4>
          <p class="text-muted">Subtítulo: Kit modular para diagnóstico, logging y análisis de rendimiento en pipelines complejos.</p>
        </div>
      </div>
    </div>

    <!-- Project 3 -->
    <div class="list-group-item p-3 project-row" role="button"
         data-title="Participación Colombia 4.0"
         data-subtitle="Presentaciones institucionales y material de impacto"
         data-image="/media/intro.jpg"
         data-images='["/media/intro.jpg","/media/placeholder-thumb.svg","/media/Planos.png"]'
         data-description="Presentaciones institucionales que destacan el impacto cultural y técnico de Xlerion."
         data-tech='["Presentation","Datasets","Workshops"]'
         data-links='{"case":"/proyectos/colombia-4-0"}'>
      <div class="row align-items-center">
        <div class="col-md-4">
            <img src="/media/intro.jpg" alt="Colombia 4.0" class="project-card-img rounded">
        </div>
        <div class="col-md-8">
          <h4>Proyecto: Participación Colombia 4.0</h4>
          <p class="text-muted">Subtítulo: Presentaciones y material institucional que muestran impacto y colaboración territorial.</p>
        </div>
      </div>
    </div>

    <!-- Project 4 -->
    <div class="list-group-item p-3 project-row" role="button"
         data-title="CoCrea 2025"
         data-subtitle="Postulación cultural y validación territorial"
         data-image="/media/Planos.png"
         data-images='["/media/Planos.png","/media/placeholder-hero.svg","/media/placeholder-thumb.svg"]'
         data-description="Proyecto cultural enfocado en validación territorial y colaboración sostenible."
         data-tech='["Community","Field-Work","Documentation"]'
         data-links='{"case":"/proyectos/cocrea-2025"}'>
      <div class="row align-items-center">
        <div class="col-md-4">
            <img src="/media/Planos.png" alt="CoCrea 2025" class="project-card-img rounded">
        </div>
        <div class="col-md-8">
          <h4>Proyecto: CoCrea 2025</h4>
          <p class="text-muted">Subtítulo: Postulación cultural que valida modelos modulares en contextos territoriales.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-4 text-center">
    <a href="/contact" class="btn btn-outline-light">Contactar para colaboraciones</a>
  </div>
</div>

<!-- Project modal (Bootstrap) -->
<div class="modal fade" id="projectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="projectModalLabel">Project</h5>
        <div class="d-flex align-items-center">
          <button id="pm-gallery-btn" type="button" class="btn btn-sm btn-outline-primary me-2" style="display:none">Ver galería</button>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-6">
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
              <img id="pm-image" src="" alt="" class="pm-main-image mb-3">
            </div>
          </div>
          <div class="col-lg-6">
            <h3 id="pm-title"></h3>
            <p id="pm-subtitle" class="text-muted"></p>
            <p id="pm-description"></p>
            <h6>Tecnologías</h6>
            <ul id="pm-tech"></ul>
            <div id="pm-links" class="mt-3"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Gallery overlay -->
<div id="pm-gallery-overlay" role="dialog" aria-modal="true">
  <button class="g-btn" aria-label="Cerrar galería" style="background:transparent;border:none;color:#fff;font-size:18px;right:16px;top:12px;position:absolute;">Cerrar</button>
  <button class="g-prev" aria-label="Anterior">◀</button>
  <button class="g-next" aria-label="Siguiente">▶</button>
  <img id="pm-gallery-img" src="" alt="">
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const modalEl = document.getElementById('projectModal');
  const bsModal = new bootstrap.Modal(modalEl);
  const galleryOverlay = document.getElementById('pm-gallery-overlay');
  const galleryImg = document.getElementById('pm-gallery-img');
  const galleryClose = galleryOverlay.querySelector('.g-btn');
  const galleryPrev = galleryOverlay.querySelector('.g-prev');
  const galleryNext = galleryOverlay.querySelector('.g-next');
  let galleryImages = [];
  let galleryIndex = 0;

  function openGallery(idx){
    galleryIndex = idx || 0;
    galleryImg.src = galleryImages[galleryIndex] || '';
    galleryOverlay.classList.add('show');
    try{ galleryOverlay.requestFullscreen?.(); }catch(e){}
  }
  function closeGallery(){
    galleryOverlay.classList.remove('show');
    if (document.fullscreenElement) document.exitFullscreen?.();
  }
  function nextGallery(){ galleryIndex = (galleryIndex+1) % galleryImages.length; galleryImg.src = galleryImages[galleryIndex]; }
  function prevGallery(){ galleryIndex = (galleryIndex-1+galleryImages.length) % galleryImages.length; galleryImg.src = galleryImages[galleryIndex]; }

  galleryClose.addEventListener('click', closeGallery);
  galleryNext.addEventListener('click', nextGallery);
  galleryPrev.addEventListener('click', prevGallery);
  document.addEventListener('keydown', function(e){ if(galleryOverlay.classList.contains('show')){ if(e.key==='ArrowRight') nextGallery(); if(e.key==='ArrowLeft') prevGallery(); if(e.key==='Escape') closeGallery(); }});

  document.querySelectorAll('.project-row').forEach(function(row){
    row.addEventListener('click', function(){
      const title = this.dataset.title || '';
      const subtitle = this.dataset.subtitle || '';
      const image = this.dataset.image || '';
      const description = this.dataset.description || '';
      const tech = JSON.parse(this.dataset.tech || '[]');
      const links = JSON.parse(this.dataset.links || '{}');
      // images array support: prefer data-images JSON, fallback to single data-image
      let images = [];
      try{ images = JSON.parse(this.dataset.images || 'null'); }catch(e){ images = null; }
      if (!images || !Array.isArray(images)) images = image ? [image] : [];
      galleryImages = images;

      document.getElementById('projectModalLabel').textContent = title;
      document.getElementById('pm-title').textContent = title;
      document.getElementById('pm-subtitle').textContent = subtitle;
      document.getElementById('pm-image').src = images.length ? images[0] : '';
      document.getElementById('pm-description').textContent = description;

      const techList = document.getElementById('pm-tech');
      techList.innerHTML = '';
      tech.forEach(function(t){ const li = document.createElement('li'); li.textContent = t; techList.appendChild(li); });

      const linksDiv = document.getElementById('pm-links');
      linksDiv.innerHTML = '';
      for (const k in links) { const a = document.createElement('a'); a.href = links[k]; a.className = 'btn btn-sm btn-outline-primary me-2'; a.textContent = k; a.target = '_blank'; linksDiv.appendChild(a); }

      // gallery button visibility
      const galleryBtn = document.getElementById('pm-gallery-btn');
      if (galleryImages.length > 1){ galleryBtn.style.display = 'inline-block'; galleryBtn.textContent = 'Ver galería ('+galleryImages.length+')'; galleryBtn.onclick = function(e){ e.stopPropagation(); openGallery(0); }; }
      else { galleryBtn.style.display = 'none'; galleryBtn.onclick = null; }

      bsModal.show();
    });
  });
});
</script>

<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>