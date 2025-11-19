<?php
$title = 'Filosofía - Xlerion';
ob_start(); ?>

<!-- Tailwind version of Filosofía (responsive, accessible, modular) -->
<section id="filosofia" role="region" aria-labelledby="filosofia-title"
         class="bg-[#121212] text-white py-12">
  <div class="mx-auto max-w-6xl px-4">
    <div class="flex flex-col lg:flex-row items-start gap-8">
      <!-- Left column: título + texto -->
      <div class="w-full lg:w-2/3">
        <!-- Icono simbólico: engranaje + nodo -->
        <div class="flex items-center gap-4">
          <div class="flex-shrink-0 w-14 h-14 rounded-lg
                      bg-gradient-to-br from-[#004080] to-[#00EEFF]
                      shadow-md flex items-center justify-center"
               aria-hidden="true">
            <!-- SVG simbólico: engranaje estilizado -->
            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M12 15.2A3.2 3.2 0 1 0 12 8.8a3.2 3.2 0 0 0 0 6.4z" fill="currentColor" />
              <path d="M19.4 13.1c.05-.35.1-.7.1-1.1s-.05-.75-.1-1.1l2.1-1.6a.4.4 0 0 0 .1-.52l-2-3.4a.4.4 0 0 0-.5-.17l-2.5 1a7.5 7.5 0 0 0-1.9-1.1l-.4-2.6A.4.4 0 0 0 13.9 2h-4a.4.4 0 0 0-.4.34l-.4 2.6c-.68.25-1.31.6-1.9 1.1l-2.5-1a.4.4 0 0 0-.5.17l-2 3.4a.4.4 0 0 0 .1.52L4.5 10c-.05.35-.1.7-.1 1.1s.05.75.1 1.1L2.4 13.8a.4.4 0 0 0-.1.52l2 3.4c.12.2.36.28.55.17l2.5-1c.58.5 1.22.85 1.9 1.1l.4 2.6c.05.2.24.34.44.34h4c.2 0 .38-.14.44-.34l.4-2.6c.68-.25 1.31-.6 1.9-1.1l2.5 1c.19.11.43.03.55-.17l2-3.4a.4.4 0 0 0-.1-.52l-2.1-1.6z" fill="currentColor" />
            </svg>
          </div>

          <!-- Title -->
          <div>
            <h1 id="filosofia-title" class="text-3xl md:text-4xl font-extrabold leading-tight text-[#00EEFF]">
              Filosofía
            </h1>
            <p class="mt-2 text-sm md:text-base text-gray-200 max-w-2xl">
              Principios que guían nuestro trabajo técnico-creativo: autonomía, modularidad y documentación como legado.
            </p>
          </div>
        </div>

        <!-- Body text: left-aligned, buena legibilidad -->
        <article class="mt-6 prose prose-sm md:prose-base max-w-none prose-invert">
          <!-- prose-left: forzar left alignment and readable line-height -->
          <div class="prose-left text-left text-gray-100 leading-relaxed space-y-4">
            <p>
              La filosofía de Xlerion se fundamenta en la convicción de que la innovación técnica y creativa debe estar guiada por
              principios sólidos que promuevan la autonomía, la colaboración y el impacto cultural sostenible.
              Creemos que las soluciones modulares no solo optimizan procesos y anticipan fallos, sino que también empoderan a las
              comunidades y fomentan un ecosistema de aprendizaje continuo y replicabilidad.
            </p>
            <!-- Subtítulos modulares -->
            <h2 class="text-lg font-semibold text-[#43FFFF] mt-4">Misión</h2>
            <p class="text-gray-200">Impulsar el desarrollo técnico contemporáneo mediante soluciones modulares que anticipan fallos, optimizan flujos de trabajo y fomentan la colaboración sostenible entre creadores, técnicos y comunidades.</p>

            <h2 class="text-lg font-semibold text-[#43FFFF] mt-4">Visión</h2>
            <p class="text-gray-200">Consolidarnos como referente latinoamericano en el diseño de toolkits inteligentes que integren técnica, creatividad, documentación y escalabilidad.</p>

            <h2 class="text-lg font-semibold text-[#43FFFF] mt-4">Valores</h2>
            <ul class="list-disc list-inside text-gray-200 space-y-1">
              <li><strong>Empatía aplicada:</strong> Tecnología con mirada humana.</li>
              <li><strong>Autosuficiencia creativa:</strong> Herramientas para autonomía técnica.</li>
              <li><strong>Documentación replicable:</strong> Material como legado.</li>
            </ul>
          </div>
        </article>
      </div>

      <!-- Right column: imagen / tarjetas (modular, reutilizable) -->
      <aside class="w-full lg:w-1/3">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Card component: reutilizable -->
          <figure class="card-fil card bg-[#0b1013] border border-[#2C2C2C] rounded-md overflow-hidden shadow-sm">
            <!-- Replace src with real image or CMS variable -->
            <img src="/media/Planos.png" alt="Arquitectura modular" class="w-full h-36 object-cover">
            <figcaption class="p-3 text-sm text-gray-300">
              Arquitectura modular conceptual
            </figcaption>
          </figure>

          <figure class="card-fil card bg-[#0b1013] border border-[#2C2C2C] rounded-md overflow-hidden shadow-sm">
            <img src="/media/intro.jpg" alt="Trabajo territorial" class="w-full h-36 object-cover">
            <figcaption class="p-3 text-sm text-gray-300">
              Trabajo territorial y colaboración
            </figcaption>
          </figure>

          <!-- Full width card -->
          <figure class="col-span-1 sm:col-span-2 card bg-[#0b1013] border border-[#2C2C2C] rounded-md overflow-hidden shadow-sm">
            <img src="/media/images/parallax/blog-bitacora-parallax.jpg" alt="Documentación" class="w-full h-40 object-cover">
            <figcaption class="p-3 text-sm text-gray-300">
              Documentación como activo cultural
            </figcaption>
          </figure>
        </div>
      </aside>
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
