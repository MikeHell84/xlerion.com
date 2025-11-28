<?php
$title = 'Acerca del creador - Xlerion';
ob_start(); ?>

<section class="container" aria-label="Acerca del creador" style="padding:1.5rem 0">
  <div class="row gx-4 gy-4 align-items-center">
    <div class="col-12 col-md-7">
      <h1 class="display-5">Miguel Rodríguez — Fundador & Director de Producto</h1>
      <p class="lead" style="margin-top:.5rem;color:#444;">Ingeniero, diseñador y emprendedor con más de 12 años liderando proyectos que combinan hardware y software. Dirijo Xlerion con foco en soluciones integradas, diseño centrado en el usuario y resultados medibles.</p>
      <p>Servicios: consultoría estratégica, desarrollo de producto, diseño industrial y lanzamiento go-to-market.</p>
      <div class="mt-3 d-flex gap-2">
        <a class="contact-cta" href="/contact">Contactar al equipo</a>
        <a class="btn" href="/proyectos">Ver proyectos</a>
      </div>
    </div>
    <div class="col-12 col-md-5 d-flex justify-content-end align-items-center">
      <style>
        /* Local laser border for profile image */
        .laser-wrap{position:relative;display:inline-block;padding:6px;border-radius:16px}
        .laser-wrap .laser-img{display:block;border-radius:12px;object-fit:cover;width:260px;height:260px}
        .laser-wrap .laser-ring{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:calc(100% + 20px);height:calc(100% + 20px);border-radius:18px;pointer-events:none;box-shadow:0 0 18px rgba(0,238,255,0.95),0 0 40px rgba(0,238,255,0.2);border:2px solid rgba(0,238,255,0.95);}
        @media (prefers-color-scheme: dark){
          .laser-wrap .laser-ring{box-shadow:0 0 22px rgba(0,238,255,1),0 0 48px rgba(0,238,255,0.28)}
        }
      </style>

      <div class="laser-wrap">
        <img class="laser-img" src="/media/MikeProfile.jpg" alt="Miguel Rodríguez - Xlerion">
        <span class="laser-ring" aria-hidden="true"></span>
      </div>
    </div>
  </div>

  <hr style="margin:1.5rem 0">

  <div class="row gx-4 gy-4">
    <div class="col-12 col-md-4">
      <h3>Resumen ejecutivo</h3>
      <p>Trayectoria en diseño de producto, desde la validación de mercado hasta la producción. Preferimos ciclos ágiles y entregas con foco en métricas de negocio.</p>
    </div>
    <div class="col-12 col-md-4">
      <h3>KPI</h3>
      <ul class="list-unstyled">
        <li><strong>12+</strong> años experiencia</li>
        <li><strong>50+</strong> proyectos entregados</li>
        <li><strong>+30</strong> clientes satisfechos</li>
      </ul>
    </div>
    <div class="col-12 col-md-4">
      <h3>Expertise</h3>
      <ul>
        <li>Diseño de producto & prototipado</li>
        <li>UX/UI y desarrollo web</li>
        <li>Integración hardware + software</li>
      </ul>
    </div>
  </div>

  <!-- Gráficos generados automáticamente a partir de KPI/Expertise -->
  <div class="row gx-4 gy-4 mt-3" id="charts-summary">
    <div class="col-12 col-md-6">
      <div class="card" style="padding:12px;border-radius:8px">
        <h4 style="margin-top:0">KPI</h4>
        <div class="chart-wrapper" style="height:260px;max-height:360px;">
          <canvas id="kpiDonut" width="600" height="260" style="display:block;width:100%;height:100%"></canvas>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="card" style="padding:12px;border-radius:8px">
        <h4 style="margin-top:0">Expertise</h4>
        <div class="chart-wrapper" style="height:260px;max-height:360px;">
          <canvas id="expBar" width="600" height="260" style="display:block;width:100%;height:100%"></canvas>
        </div>
      </div>
    </div>
    <div class="col-12 mt-3">
      <div class="card" style="padding:12px;border-radius:8px">
        <h4 style="margin-top:0">Proyectos (histórico ejemplo)</h4>
        <div class="chart-wrapper" style="height:300px;max-height:420px;">
          <canvas id="projLine" width="1000" height="300" style="display:block;width:100%;height:100%"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart.js CDN and inline script that parses DOM values -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    (function(){
      // Extract KPI numbers from the KPI list
      const kpiList = Array.from(document.querySelectorAll('#charts-summary ~ .row .col-12.col-md-4:nth-child(2) ul li'));
      // Fallback: find the KPI block by header text
      let kpiItems = [];
      if(kpiList.length === 0){
        const nodes = Array.from(document.querySelectorAll('.row.gx-4.gy-4 .col-12.col-md-4'));
        const kpiNode = nodes.find(n => n.querySelector('h3') && /KPI/i.test(n.querySelector('h3').innerText));
        if(kpiNode) kpiItems = Array.from(kpiNode.querySelectorAll('li'))
      } else {
        kpiItems = kpiList;
      }

      // Parse numbers (digits) from text
      function parseNumberFromText(text){
        const m = text.replace(/\+/g,'').match(/(\d+)/);
        return m ? parseInt(m[1],10) : 0;
      }

      const kpiNums = kpiItems.map(li => parseNumberFromText(li.innerText));
      const kpiLabels = kpiItems.map(li => li.innerText.replace(/\d+\+?/,'').trim());
      // If parsing failed, use defaults
      const defaultKpiNums = [12,50,30];
      const finalKpiNums = kpiNums.length ? kpiNums : defaultKpiNums;
      const finalKpiLabels = kpiLabels.length ? kpiLabels : ['Años experiencia','Proyectos entregados','Clientes satisfechos'];

      // Expertise labels
      const expNode = Array.from(document.querySelectorAll('.row.gx-4.gy-4 .col-12.col-md-4')).find(n=>n.querySelector('h3') && /Expertise/i.test(n.querySelector('h3').innerText));
      const expItems = expNode ? Array.from(expNode.querySelectorAll('ul li')) : [];
      const expLabels = expItems.length ? expItems.map(li=>li.innerText.trim()) : ['Diseño de producto & prototipado','UX/UI y desarrollo web','Integración hardware + software'];
      const expValues = expItems.length ? expItems.map(()=>75) : [80,70,60];

      // Projects historical example (keep small default)
      const projYears = ['2019','2020','2021','2022','2023','2024'];
      const projValues = [4,6,8,9,10,13];

      // Company palette from CSS variables or fallbacks
      const root = getComputedStyle(document.documentElement);
      const deep = root.getPropertyValue('--deep').trim() || '#004080';
      const accent = root.getPropertyValue('--accent').trim() || '#00EEFF';
      const accent2 = root.getPropertyValue('--accent-2').trim() || root.getPropertyValue('--accent2').trim() || '#43FFFF';
      const muted = root.getPropertyValue('--muted').trim() || '#2C2C2C';

      // KPI Donut
      const ctxKpi = document.getElementById('kpiDonut').getContext('2d');
      new Chart(ctxKpi, {
        type: 'doughnut',
        data: { labels: finalKpiLabels, datasets:[{ data: finalKpiNums, backgroundColor:[deep,accent,accent2], borderColor:'#fff', borderWidth:2 }] },
        options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}}, layout:{padding:8} }
      });

      // Expertise bar (horizontal)
      const ctxExp = document.getElementById('expBar').getContext('2d');
      new Chart(ctxExp, {
        type: 'bar',
        data:{ labels: expLabels, datasets:[{ label:'Nivel (0-100)', data: expValues, backgroundColor: [deep, accent, accent2] }] },
        options:{ indexAxis:'y', scales:{ x:{ max:100 } }, plugins:{ legend:{ display:false } }, responsive:true, maintainAspectRatio:false, layout:{padding:6} }
      });

      // Projects line
      const ctxProj = document.getElementById('projLine').getContext('2d');
      // Force transparent canvas background in dark theme to allow underlying card/bg show through
      const isDark = document.documentElement.classList.contains('theme-dark') || document.body.classList.contains('theme-dark') || window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      if(isDark){
        // Ensure parent cards have transparent background where appropriate
        const cards = document.querySelectorAll('#charts-summary .card');
        cards.forEach(c=>{ c.style.background = 'transparent'; c.style.boxShadow = 'none'; c.style.border = 'none'; });
      }

      new Chart(ctxProj, {
        type:'line',
        data:{ labels: projYears, datasets:[{ label:'Proyectos entregados', data: projValues, borderColor:accent, backgroundColor: isDark ? 'transparent' : 'rgba(0,238,255,0.12)', tension:0.22, fill:true, pointRadius:4 }] },
        options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'top' } }, elements:{ line:{borderWidth:2} } }
      });

    })();
  </script>

  <hr style="margin:1.5rem 0">

  <div class="row gx-4 gy-4">
    <div class="col-12 col-md-8">
      <h3>Proyectos destacados</h3>
      <article class="mb-3">
        <h4>Proyecto X — Plataforma de gestión</h4>
        <p>Dirección técnica, arquitectura y entrega de MVP para gestión de inventarios.</p>
      </article>
      <article>
        <h4>Proyecto Y — Dispositivo IoT</h4>
        <p>Diseño de prototipo, firmware y panel web de control.</p>
      </article>
    </div>
    <div class="col-12 col-md-4">
      <h3>Clientes & partners</h3>
      <ul class="list-unstyled">
        <li>Empresa A</li>
        <li>Entidad B</li>
        <li>Startup C</li>
      </ul>
    </div>
  </div>

  <hr style="margin:1.5rem 0">

  <div class="row gx-4 gy-4">
    <div class="col-12">
      <h3>Testimonios</h3>
      <blockquote class="contact-card">"Miguel transformó nuestra idea en un producto real en solo 4 meses. Su visión y disciplina técnica fueron clave."<footer class="small mt-2">— Cliente, Empresa A</footer></blockquote>
    </div>
  </div>

  <div class="row gx-4 gy-4 mt-4">
    <div class="col-12 text-center">
      <a class="contact-cta" href="/contact">Solicitar consultoría</a>
    </div>
  </div>
</section>

<?php $slot = ob_get_clean(); include __DIR__ . '/layout.php'; ?>
