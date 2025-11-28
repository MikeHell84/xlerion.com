<?php
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Model/Database.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
Auth::requireAdmin();
$pdo = Database::pdo();
$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
// Analytics queries - tolerate missing optional tables in lightweight local DB
$contacts_new = 0;
$interactions_week = 0;
$tasks_open = 0;
$opps_by_stage = [];
try {
  if ($driver === 'sqlite') {
    $contacts_new = $pdo->query("SELECT COUNT(*) FROM contacts WHERE created_at >= datetime('now','-7 days')")->fetchColumn();
  } else {
    $contacts_new = $pdo->query("SELECT COUNT(*) FROM contacts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
  }
} catch (Exception $e) { /* missing contacts table => 0 */ }
try {
  if ($driver === 'sqlite') {
    $interactions_week = $pdo->query("SELECT COUNT(*) FROM interactions WHERE performed_at >= datetime('now','-7 days')")->fetchColumn();
  } else {
    $interactions_week = $pdo->query("SELECT COUNT(*) FROM interactions WHERE performed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
  }
} catch (Exception $e) { /* missing interactions table => 0 */ }
try {
  $tasks_open = $pdo->query("SELECT COUNT(*) FROM tasks WHERE status != 'done'")->fetchColumn();
} catch (Exception $e) { /* missing tasks table => 0 */ }
try {
  $opps_by_stage = $pdo->query("SELECT stage, COUNT(*) as cnt FROM opportunities GROUP BY stage")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { /* missing opportunities => empty */ }

// Page view aggregates (last 7 days) and top pages (best-effort)
$pageViews = [];
$topPages = [];
try {
  if ($driver === 'sqlite') {
    $pageViews = $pdo->query("SELECT COALESCE(slug,'(sin slug)') as slug, DATE(created_at) as day, COUNT(*) as cnt FROM page_views WHERE created_at >= datetime('now','-7 days') GROUP BY slug, day ORDER BY day ASC")->fetchAll(PDO::FETCH_ASSOC);
    $topPages = $pdo->query("SELECT COALESCE(slug,'(sin slug)') as slug, COUNT(*) as cnt FROM page_views WHERE created_at >= datetime('now','-30 days') GROUP BY slug ORDER BY cnt DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $pageViews = $pdo->query("SELECT COALESCE(slug,'(sin slug)') as slug, DATE(created_at) as day, COUNT(*) as cnt FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY slug, day ORDER BY day ASC")->fetchAll(PDO::FETCH_ASSOC);
    $topPages = $pdo->query("SELECT COALESCE(slug,'(sin slug)') as slug, COUNT(*) as cnt FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY slug ORDER BY cnt DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
  }
} catch (Exception $e) { /* ignore missing page_views */ }

$title = 'Admin - Dashboard';
$banner_title_class = 'section-title-lg';
ob_start();
?>
<div class="admin-dashboard">
  <aside class="admin-sidebar card-preview">
    <?php include __DIR__ . '/_nav.php'; ?>
  </aside>

  <main class="admin-main">
    <header class="admin-header">
      <h1 class="section-title-lg"><?= htmlspecialchars($title) ?></h1>
      <p class="text-muted">Panel administrativo — vista rápida de métricas y acciones.</p>
    </header>

    <style>
      /* Dashboard spacing & mobile centering adjustments */
      .admin-header { margin-bottom: .75rem; }
      .admin-stats.row { margin-top: .25rem; }
      @media (max-width: 991px) {
        .admin-main { padding-top: .5rem; }
        .admin-dashboard .admin-stats .card-preview { margin-bottom: .75rem; }
        .admin-dashboard .admin-stats .display-4 { font-size:1.7rem !important; }
        .admin-dashboard .admin-pages h2.h5 { margin-top:1.25rem; }
        .admin-dashboard .admin-pages .row.mb-3 { margin-bottom:1rem !important; }
        .admin-dashboard .admin-pages .card-preview { margin-bottom:1rem !important; }
        .admin-dashboard .admin-main { max-width: 920px; margin-left:auto; margin-right:auto; }
        .admin-dashboard .admin-main .row { margin-left:0; margin-right:0; }
        .admin-dashboard .list-group-item { padding:.75rem .9rem; }
        /* Prevent horizontal overflow */
        .admin-dashboard { overflow-x:hidden; }
      }
    </style>

    <section class="admin-stats row" aria-label="Estadísticas rápidas">
      <div class="col-12 col-md-4">
        <div class="card-preview">
          <div class="card-title">Contactos nuevos (7d)</div>
          <div class="card-text display-4" style="font-weight:800;font-size:2rem"><?= intval($contacts_new) ?></div>
        </div>
      </div>

      <style>
        /* Make the menu preview sticky on large screens */
        @media(min-width:992px){
          .admin-menu-preview { position: -webkit-sticky; position: sticky; top: 1rem; max-height: calc(100vh - 4rem); overflow:auto; }
        }
        .menu-list .menu-item { display:flex; align-items:center; }
        .menu-item .drag-handle { cursor:grab; margin-right:.5rem; color:#888; }
      </style>
      <div class="col-12 col-md-4">
        <div class="card-preview">
          <div class="card-title">Interacciones (7d)</div>
          <div class="card-text display-4" style="font-weight:800;font-size:2rem"><?= intval($interactions_week) ?></div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="card-preview">
          <div class="card-title">Tareas abiertas</div>
          <div class="card-text display-4" style="font-weight:800;font-size:2rem"><?= intval($tasks_open) ?></div>
        </div>
      </div>
    </section>

    <section class="admin-pipeline mt-3">
      <h2 class="h5">Pipeline</h2>
      <ul>
        <?php if (empty($opps_by_stage)): ?>
          <li>No hay oportunidades registradas</li>
        <?php else: foreach ($opps_by_stage as $o): ?>
          <li><?= htmlspecialchars($o['stage']) ?> — <?= intval($o['cnt']) ?></li>
        <?php endforeach; endif; ?>
      </ul>
    </section>

    <section class="admin-pages mt-4">
      <style>
        /* Inline enforcement for Activity cards to ensure immediate visual changes */
        .admin-pages .card-preview { padding:1rem !important; border-radius:12px !important; }
        .admin-pages .col-lg-8 .card-preview { min-height:360px !important; display:flex !important; flex-direction:column !important; }
        .admin-pages .col-lg-8 canvas { width:100% !important; min-height:300px !important; height:auto !important; }
        .admin-pages .col-lg-4 canvas { width:100% !important; min-height:220px !important; height:auto !important; }
        .admin-pages .card-preview h3.h6 { font-weight:800 !important; color:var(--fg) !important; margin-bottom:.5rem !important; }
      </style>
      <h2 class="h5">Actividad del sitio</h2>
      <div class="row mb-3">
        <div class="col-12 col-lg-8">
          <div class="card-preview p-3">
            <h3 class="h6">Vistas por sección (últimos 7 días)</h3>
            <canvas id="chartPageViews" width="600" height="240"></canvas>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="card-preview p-3">
            <h3 class="h6">Páginas más visitadas (30d)</h3>
            <canvas id="chartTopPages" width="300" height="240"></canvas>
          </div>
        </div>
      </div>

      <?php
        // Ensure $allPages is defined before emitting it to JS. In some renders
        // the variable was assigned later in the template which caused the
        // inline script to receive `null` and throw, preventing the Activity
        // section scripts from running correctly in the admin dashboard.
        try {
          $allPages = $pdo->query('SELECT id,slug,title FROM cms_pages WHERE is_published = 1 ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { $allPages = []; }
      ?>
      <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
      <script>
      // Build hierarchical menu data from PHP-provided pages array
      const pages = <?php echo json_encode($allPages); ?>;

      function buildMenuTree(pages) {
        const byId = {};
        pages.forEach(p => {
          const meta = p.meta ? JSON.parse(p.meta) : {};
          byId[p.id] = Object.assign({}, p, { meta });
          byId[p.id].children = [];
        });

        const groups = {};

        // place nodes under group -> parent
        Object.values(byId).forEach(p => {
          const group = p.meta.menu_group || p.menu_group || 'main';
          const parent = p.meta.menu_parent || p.menu_parent || null;
          groups[group] = groups[group] || { id: group, title: group, items: [] };
          if (parent && byId[parent]) {
            byId[parent].children.push(p);
          } else {
            groups[group].items.push(p);
          }
        });

        return Object.values(groups);
      }

      function renderList(items) {
        const ul = document.createElement('ul');
        ul.className = 'menu-list list-unstyled';
        items.forEach(item => {
          const li = document.createElement('li');
          li.className = 'menu-item mb-1 p-2 border rounded';
          li.dataset.pageId = item.id;
          li.innerHTML = `<div class="d-flex justify-content-between"><div>${escapeHtml(item.title)}</div><div class="text-muted small">ID:${item.id}</div></div>`;
          if (item.children && item.children.length) {
            const childContainer = renderList(item.children);
            childContainer.classList.add('ml-3');
            li.appendChild(childContainer);
          }
          ul.appendChild(li);
        });
        return ul;
      }

      function escapeHtml(s){return String(s).replace(/[&<>"']/g, function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

      function initPreview(){
        const root = document.getElementById('menuPreviewRoot');
        root.innerHTML = '';
        const groups = buildMenuTree(pages);
        groups.forEach(g => {
          const card = document.createElement('div');
          card.className = 'mb-2 p-2 border rounded';
          const h = document.createElement('div'); h.className='font-weight-bold'; h.textContent = g.title; card.appendChild(h);
          const list = renderList(g.items);
          card.appendChild(list);
          root.appendChild(card);

          // make top-level list sortable
          new Sortable(list, {
            group: 'menu',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: function(evt){ /* keep simple; order will be read when saving */ }
          });
          // also make nested lists sortable
          card.querySelectorAll('ul').forEach(u=>{
            new Sortable(u, { group: 'menu', animation: 150, fallbackOnBody: true });
          });
        });
      }

      function collectOrder(){
        // collect hierarchical order as array of {id, children: [...]}
        function collectFromUl(ul){
          const out = [];
          Array.from(ul.children).forEach(li => {
            if (!li.dataset || !li.dataset.pageId) return;
            const id = li.dataset.pageId;
            const childUl = li.querySelector(':scope > ul');
            out.push({ id: id, children: childUl ? collectFromUl(childUl) : [] });
          });
          return out;
        }
        const root = document.getElementById('menuPreviewRoot');
        const groups = [];
        root.querySelectorAll(':scope > div').forEach(groupCard => {
          const groupName = groupCard.querySelector('.font-weight-bold').textContent;
          const ul = groupCard.querySelector('ul');
          groups.push({ group: groupName, items: ul ? collectFromUl(ul) : [] });
        });
        return groups;
      }

      document.getElementById('btnSaveMenuOrder').addEventListener('click', function(){
        const payload = collectOrder();
        fetch('/admin/save_menu_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ order: payload })
        }).then(r=>r.json()).then(j=>{
          if (j.ok) { alert('Orden guardado'); location.reload(); } else { alert('Error: '+(j.error||'unknown')); }
        }).catch(e=>{ alert('Error: '+e.message); });
      });

      initPreview();
      </script>

      <h2 class="h5">Páginas asignadas al menú</h2>
      <?php
        // list pages that already have a menu_group assignment
        try {
          $assigned = $pdo->query("SELECT id, title, slug, meta FROM cms_pages WHERE meta IS NOT NULL AND meta != '' ORDER BY title ASC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { $assigned = []; }
      ?>
      <?php if (!empty($assigned)): ?>
        <div class="list-group mb-3">
          <?php foreach ($assigned as $ap):
            $m = [];
            if (!empty($ap['meta'])) { $mm = json_decode($ap['meta'], true); if (is_array($mm)) $m = $mm; }
            $mg = $m['menu_group'] ?? '(sin grupo)';
          ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <strong><?= htmlspecialchars($ap['title']) ?></strong>
                <div class="small text-muted"><?= htmlspecialchars($ap['slug']) ?> — Grupo: <?= htmlspecialchars($mg) ?></div>
              </div>
              <div>
                <a class="btn btn-outline-light btn-sm" href="/admin/edit_page.php?id=<?= intval($ap['id']) ?>">Editar</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="small text-muted mb-3">No hay páginas asignadas al menú todavía.</div>
      <?php endif; ?>

      <h2 class="h5">Vista previa del menú</h2>
  <div class="card-preview mb-3 p-3 admin-menu-preview">
        <p class="small text-muted">Arrastra para reordenar menús y submenús. Haz clic en "Guardar orden" para persistir.</p>
        <div id="menuPreviewRoot">
          <!-- menu preview will be injected by JS -->
        </div>
        <div class="mt-2">
          <button id="btnSaveMenuOrder" class="btn btn-primary btn-sm">Guardar orden</button>
        </div>
      </div>

      <h2 class="h5">Páginas CMS recientes</h2>
      <?php
        // Local menu groups mapping (must stay in sync with layout.php groupsDef)
        $groupsDef = [
          'Información' => ['slugs' => ['inicio','filosofia','acerca-del-creador','legal'], 'icon' => 'info'],
          'Servicios' => ['slugs' => ['servicios','soluciones','proyectos'], 'icon' => 'build'],
          'Recursos' => ['slugs' => ['documentacion','blog','convocatorias-alianzas'], 'icon' => 'menu_book'],
          'Contacto' => ['slugs' => ['contact','contacto'], 'icon' => 'mail']
        ];

        $perPage = 12; $cp = max(1,intval($_GET['page'] ?? 1)); $off = ($cp-1)*$perPage;
        $pagesList = $pdo->prepare('SELECT id,slug,title,is_published,meta FROM cms_pages ORDER BY id DESC LIMIT ? OFFSET ?'); $pagesList->execute([$perPage,$off]);
        $pages = $pagesList->fetchAll();
        // load all pages for parent select
        $allPages = $pdo->query('SELECT id,slug,title FROM cms_pages WHERE is_published = 1 ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC);
        $totalPages = max(1, ceil(@($pdo->query('SELECT COUNT(*) FROM cms_pages')->fetchColumn() ?: 0)/$perPage));
      ?>
      <div class="list-group">
        <?php foreach ($pages as $p): ?>
          <?php
            // meta may be null or JSON string
            $meta = [];
            if (!empty($p['meta'])) {
              $m = json_decode($p['meta'], true);
              if (is_array($m)) $meta = $m;
            }
            $currentGroup = $meta['menu_group'] ?? null;
            $currentParent = $meta['menu_parent'] ?? null;
          ?>
          <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <strong><?= htmlspecialchars($p['title']) ?></strong>
                <div class="small text-muted">Slug: <?= htmlspecialchars($p['slug']) ?> — <?= $p['is_published'] ? 'Publicado' : 'Borrador' ?></div>
                <div class="small mt-1">Menu: <strong><?= htmlspecialchars($currentGroup ?? '(Asignar)') ?></strong>
                  <?php if ($currentParent): ?> • Submenu de <?= htmlspecialchars($currentParent) ?><?php endif; ?></div>
              </div>
              <div style="min-width:260px">
                <form method="post" action="/admin/save_page_menu.php" class="d-flex gap-2 align-items-center">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'] ?? '') ?>">
                  <input type="hidden" name="id" value="<?= intval($p['id']) ?>">
                  <select name="menu_group" class="form-select form-select-sm">
                    <option value="">-- Grupo del menú --</option>
                    <?php foreach ($groupsDef as $gTitle => $gDef): ?>
                      <option value="<?= htmlspecialchars($gTitle) ?>" <?= ($currentGroup === $gTitle) ? 'selected' : '' ?>><?= htmlspecialchars($gTitle) ?></option>
                    <?php endforeach; ?>
                    <option value="Otros" <?= ($currentGroup === 'Otros') ? 'selected' : '' ?>>Otros</option>
                  </select>
                  <select name="menu_parent" class="form-select form-select-sm">
                    <option value="">-- Sin padre (no submenu) --</option>
                    <?php foreach ($allPages as $ap): if ($ap['id'] == $p['id']) continue; ?>
                      <option value="<?= htmlspecialchars($ap['slug']) ?>" <?= ($currentParent === $ap['slug']) ? 'selected' : '' ?>><?= htmlspecialchars($ap['title']) ?> (<?= htmlspecialchars($ap['slug']) ?>)</option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn btn-primary btn-sm" type="submit">Guardar</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <nav class="mt-3" aria-label="Paginación de páginas CMS">
        <?php for ($i=1;$i<=$totalPages;$i++): ?>
          <a class="btn btn-outline-light btn-sm" href="?page=<?= $i ?>"<?= $i===$cp? ' aria-current="page" style="font-weight:700"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
      </nav>
    </section>
  </main>
</div>
<?php
$slot = ob_get_clean();
include __DIR__ . '/../../views/layout.php';

// Chart.js and small init script. Rendered after the layout so canvases exist.
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function(){
    try{
      // raw is the day-by-day page views; we aggregate per-slug for the pie chart
      var raw = <?= json_encode($pageViews) ?>;
      var top = <?= json_encode($topPages) ?>;
      // build aggregate counts per slug on the client to keep PHP changes minimal
      var agg = {};
      raw.forEach(function(r){
        var slug = r.slug || '(sin slug)';
        var cnt = parseInt(r.cnt || 0, 10) || 0;
        agg[slug] = (agg[slug] || 0) + cnt;
      });
      var labels = Object.keys(agg);
      var values = labels.map(function(l){ return agg[l]; });

      // Generate a color palette
      function colorFor(i){ return 'hsl(' + ((i*47)%360) + ',68%,52%)'; }
      var colors = labels.map(function(_,i){ return colorFor(i); });

      // Render a doughnut (pie) chart for Vistas por sección
      var ctx = document.getElementById('chartPageViews');
      if (ctx && labels.length>0){
        new Chart(ctx.getContext('2d'), {
          type: 'doughnut',
          data: {
            labels: labels,
            datasets: [{ data: values, backgroundColor: colors, hoverOffset: 8 }]
          },
          options: {
            responsive:true,
            plugins: {
              legend: { position: 'right', labels: { boxWidth:14, boxHeight:14 } },
              tooltip: { callbacks: { label: function(ctx){ return ctx.label + ': ' + ctx.raw + ' vistas'; } } }
            }
          }
        });
      } else {
        // fallback: if no aggregated data, show a small placeholder message inside the card
        if (ctx && ctx.parentNode) {
          ctx.parentNode.querySelector('h3')?.insertAdjacentHTML('afterend','<div class="small text-muted mt-2">No hay datos de vistas para los últimos 7 días.</div>');
          ctx.remove();
        }
      }

      // Keep the top pages bar chart as before
      var ctx2 = document.getElementById('chartTopPages');
      if (ctx2 && top.length>0) new Chart(ctx2.getContext('2d'), { type:'bar', data:{ labels: top.map(function(t){ return t.slug; }), datasets:[{ label:'Vistas', data: top.map(function(t){ return parseInt(t.cnt,10); }), backgroundColor: 'rgba(54,162,235,0.6)' }] }, options:{ indexAxis:'y', responsive:true, plugins:{legend:{display:false}} } });
    } catch(e) { console.warn('charts init failed', e); }
  })();
</script>

<?php

