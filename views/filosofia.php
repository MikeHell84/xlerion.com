<?php
$title = 'Filosofía - Xlerion';
ob_start(); ?>

<!-- Filosofía (adaptada al tema del sitio con Bootstrap) -->
<section id="filosofia" role="region" aria-labelledby="filosofia-title" class="py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 id="filosofia-title" class="display-6 text-xlerion-deep">Filosofía</h1>
        <p class="lead text-muted">Principios que guían nuestro trabajo técnico-creativo: autonomía, modularidad y documentación como legado.</p>

        <p>La filosofía de Xlerion se funda en la convicción de que la innovación técnica y creativa debe estar guiada por principios que promuevan la autonomía, la colaboración y el impacto sostenible. Nuestras soluciones modulares optimizan procesos, anticipan fallos y empoderan comunidades mediante documentación y herramientas replicables.</p>

        <div class="row mt-4">
          <div class="col-sm-4">
            <h5 class="text-primary">Misión</h5>
            <p class="small text-muted">Impulsar el desarrollo técnico contemporáneo mediante soluciones modulares que anticipan fallos y optimizan flujos de trabajo.</p>
          </div>
          <div class="col-sm-4">
            <h5 class="text-primary">Visión</h5>
            <p class="small text-muted">Ser referente latinoamericano en toolkits inteligentes que integren técnica, creatividad y documentación.</p>
          </div>
          <div class="col-sm-4">
            <h5 class="text-primary">Valores</h5>
            <ul class="small text-muted">
              <li>Empatía aplicada</li>
              <li>Autosuficiencia creativa</li>
              <li>Documentación replicable</li>
            </ul>
          </div>
        </div>
      </div>

      <aside class="col-md-4 text-center">
        <img src="/media/Planos.png" alt="Arquitectura modular" class="img-fluid rounded mb-3" style="max-height:240px;object-fit:cover">
        <div class="contact-card p-3 text-start">
          <h6>Documentación como activo</h6>
          <p class="small text-muted">La documentación no es un extra: es la base de la replicabilidad y la transferencia de conocimiento.</p>
        </div>
      </aside>
    </div>

    <hr class="my-4">

    <div class="row">
      <div class="col-md-6">
        <h3>Metodología</h3>
        <p class="text-muted">Trabajamos con ciclos iterativos, validando hipótesis tempranas mediante prototipos y métricas claves que guían decisiones técnicas y de producto.</p>
        <ol class="small text-muted">
          <li>Exploración & descubrimiento</li>
          <li>Prototipado rápido</li>
          <li>Validación con usuarios</li>
          <li>Escalado y documentación</li>
        </ol>
      </div>
      <div class="col-md-6">
        <h3>Casos & evidencias</h3>
        <p class="text-muted small">Ejemplos concretos de cómo nuestras arquitecturas modulares reducen tiempo de entrega y facilitan la mantención.</p>
        <ul class="small text-muted">
          <li>Proyecto X — reducción del 30% en TTM</li>
          <li>Proyecto Y — integración IoT con panel web</li>
        </ul>
      </div>
    </div>

    <hr class="my-4">

    <div class="row">
      <div class="col-12">
        <h3>Galería y recursos</h3>
        <div class="d-flex flex-wrap gap-2 mt-2">
          <img src="/media/intro.jpg" alt="Trabajo territorial" style="height:80px;object-fit:cover;border-radius:6px">
          <img src="/media/images/parallax/blog-bitacora-parallax.jpg" alt="Documentación" style="height:80px;object-fit:cover;border-radius:6px">
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12 text-center">
        <a class="contact-cta" href="/contact">Quiero saber más</a>
      </div>
    </div>
  </div>
</section>

<!--
  Componente Card (reusable)
  - Uso: pegar dentro de <aside> o grid.
  - Acepta variables: src, alt, caption.
-->
<template id="filosofia-card-template">
  <figure class="card-fil card bg-[#0b1013] border border-[#2C2C2C] rounded-md overflow-hidden shadow-sm">
    <img class="w-full h-36 object-cover" src="" alt="">
    <figcaption class="p-3 text-sm text-gray-300"></figcaption>
  </figure>
</template>

<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
