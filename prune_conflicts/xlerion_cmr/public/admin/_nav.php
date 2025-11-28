<?php
if (session_status()!==PHP_SESSION_ACTIVE) session_start();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin/dashboard.php', PHP_URL_PATH);
$links = [
  ['/admin/dashboard.php', 'Resumen'],
  ['/admin/pages.php', 'Páginas'],
  ['/admin/posts.php', 'Entradas'],
  ['/admin/resources', 'Recursos'],
  ['/admin/contacts.php', 'Contactos'],
  ['/admin/media.php', 'Medios'],
  ['/admin/settings.php', 'Ajustes'],
  ['/admin/templates', 'Plantillas'],
  ['/admin/footer.php', 'Footer'],
  ['/admin/logout.php', 'Salir']
];
?>
<nav id="adminSidebarNav" aria-label="Menú administración">
    <?php foreach($links as [$href,$label]):
      $isActive = ($currentPath === $href);
      $class = 'nav-link'.($isActive?' active':'');
      $aria = $isActive ? ' aria-current="page"' : '';
      $iconSvg = '';
      switch(strtolower($label)){
        case 'resumen': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 5h16v14H4z" stroke="currentColor" stroke-width="1.2"/><path d="M4 9h16" stroke="currentColor" stroke-width="1.2"/><path d="M10 5v14" stroke="currentColor" stroke-width="1.2"/></svg>'; break;
        case 'páginas': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.2"/><path d="M9 7h6" stroke="currentColor" stroke-width="1.2"/><path d="M9 11h6" stroke="currentColor" stroke-width="1.2"/></svg>'; break;
        case 'entradas': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4h16v4H4zM4 10h16v10H4z" stroke="currentColor" stroke-width="1.2"/><path d="M8 14h8" stroke="currentColor" stroke-width="1.2"/></svg>'; break;
        case 'recursos': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3l8 4v4c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4z" stroke="currentColor" stroke-width="1.2" fill="none"/></svg>'; break;
        case 'contactos': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.2"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6" stroke="currentColor" stroke-width="1.2" fill="none"/></svg>'; break;
        case 'medios': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.2"/><path d="M10 9l5 3-5 3V9z" fill="currentColor"/></svg>'; break;
        case 'ajustes': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15.5A3.5 3.5 0 1112 8.5a3.5 3.5 0 010 7z" stroke="currentColor" stroke-width="1.2"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06A2 2 0 014.27 17.9l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06A2 2 0 016.1 4.27l.06.06a1.65 1.65 0 001.82.33H8.1A1.65 1.65 0 009.6 3.15V3a2 2 0 014 0v.15c.2.09.38.21.54.35h.01a1.65 1.65 0 001.82-.33l.06-.06A2 2 0 0117.73 6.1l-.06.06c-.15.17-.26.36-.33.56v.01c-.18.13-.37.25-.57.34H16.9A1.65 1.65 0 0018.4 9.6V10a1.65 1.65 0 001 1.51z" stroke="currentColor" stroke-width="0.9" fill="none"/></svg>'; break;
  case 'plantillas': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.2" fill="none"/><path d="M7 8h10" stroke="currentColor" stroke-width="1.2"/><path d="M7 12h10" stroke="currentColor" stroke-width="1.2"/><path d="M7 16h6" stroke="currentColor" stroke-width="1.2"/></svg>'; break;
  case 'footer': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="18" height="6" rx="1" fill="currentColor"/><rect x="3" y="11" width="12" height="2" rx="1" fill="currentColor"/><rect x="3" y="15" width="8" height="2" rx="1" fill="currentColor"/><rect x="3" y="19" width="18" height="2" rx="1" fill="currentColor"/></svg>'; break;
        case 'salir': $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4" stroke="currentColor" stroke-width="1.2"/><path d="M10 17l5-5-5-5M14.5 12H3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>'; break;
        default: $iconSvg = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.2"/></svg>';
      }
    ?>
      <a class="<?= $class ?>" href="<?= htmlspecialchars($href) ?>"<?= $aria ?>><span class="nav-icon" aria-hidden="true"><?= $iconSvg ?></span><span class="nav-label"><?= htmlspecialchars($label) ?></span></a>
    <?php endforeach; ?>
  </nav>
<style>
  .admin-sidebar.card-preview { width:210px; padding:.35rem .55rem .90rem; }
  @media (min-width: 992px){
    /* Limit height and allow scroll on desktop to avoid clipped links */
  .admin-sidebar.card-preview { max-height:100vh; overflow-y:auto; margin-top:0; }
  .admin-sidebar.card-preview::after { content:''; position:sticky; bottom:0; display:block; height:16px; background:linear-gradient(to bottom, rgba(0,0,0,0), rgba(0,0,0,0.15)); pointer-events:none; }
  }
  #adminSidebarNav a.nav-link { display:flex; align-items:center; gap:.55rem; padding:.5rem .55rem; border-radius:6px; font-weight:600; text-decoration:none; color:inherit; }
  #adminSidebarNav a.nav-link:hover { background:rgba(255,255,255,0.08); }
  #adminSidebarNav a.nav-link.active { background:var(--accent); color:#000; }
  #adminSidebarNav .nav-icon { display:inline-flex; }
  @media (max-width: 991px){ .admin-sidebar.card-preview { display:none !important; } }
  /* Mobile dialog */
  @media (max-width: 991px){
    .admin-sidebar-toggle-btn { position:fixed; left:14px; bottom:18px; z-index:12000; display:inline-flex; align-items:center; justify-content:center; background:var(--accent); color:#000; border:none; border-radius:999px; padding:.65rem .9rem; font-size:1.05rem; font-weight:600; cursor:pointer; box-shadow:0 8px 18px rgba(0,0,0,0.28); }
    .admin-sidebar-toggle-btn:focus { outline:2px solid #000; outline-offset:2px; }
    #adminPanelDialog { position:fixed; top:50%; left:50%; transform:translate(-50%, -50%) scale(.92); width:min(420px,92vw); max-height:80vh; overflow:auto; background:var(--bg); color:var(--fg); border-radius:14px; padding:1rem 1rem 1.2rem; box-shadow:0 26px 70px -10px rgba(0,0,0,0.45); z-index:13000; opacity:0; pointer-events:none; transition:opacity .25s ease, transform .25s ease; }
    #adminPanelDialog.show { opacity:1; transform:translate(-50%, -50%) scale(1); pointer-events:auto; }
    #adminPanelBackdrop { position:fixed; inset:0; background:rgba(0,0,0,0.55); backdrop-filter:blur(2px); z-index:11000; opacity:0; transition:opacity .25s ease; pointer-events:none; }
    #adminPanelBackdrop.show { opacity:1; pointer-events:auto; }
    #adminPanelDialog nav a { display:flex; align-items:center; gap:.55rem; padding:.55rem .6rem; border-radius:6px; font-weight:600; text-decoration:none; color:inherit; }
    #adminPanelDialog nav a.active { background:var(--accent); color:#000; }
    #adminPanelDialog .panel-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.5rem; }
    #adminPanelDialog .panel-title { font-size:1.05rem; font-weight:700; }
    #adminPanelDialog .panel-close { background:var(--accent); color:#000; border:none; border-radius:6px; padding:.4rem .7rem; font-weight:600; cursor:pointer; }
    #adminPanelDialog .panel-close:focus { outline:2px solid #000; outline-offset:2px; }
  }
</style>
<script>
  (function(){
    // Sticky offset below main header (desktop)
    function updateSidebarTop(){
      try {
        if (window.matchMedia('(max-width: 991px)').matches) return;
        var nav = document.getElementById('adminSidebarNav');
        if(!nav) return;
  var aside = document.querySelector('aside.admin-sidebar.card-preview');
        if(!aside) return;
        var header = document.querySelector('header.site-header') || document.querySelector('.admin-header');
        var headerHeight = header ? Math.ceil(header.getBoundingClientRect().height) : 0;
        // Use sticky so normal document flow preserved and links remain visible
  aside.style.position = 'sticky';
  // Minimal gap below header
  var topOffset = Math.max(0, headerHeight - 4);
  aside.style.top = topOffset + 'px';
  aside.style.transform = 'none';
        aside.style.zIndex = 1020;
        // Compute available height below header for scrolling
  var available = window.innerHeight - topOffset - 12;
        if (available > 200){
          aside.style.maxHeight = available + 'px';
          aside.style.overflowY = 'auto';
        } else {
          aside.style.maxHeight = (window.innerHeight - topOffset - 4) + 'px';
          aside.style.overflowY = 'auto';
        }
        // Modest internal padding (avoid pushing content too far down)
  aside.style.paddingTop = '4px';
  aside.style.paddingBottom = '14px';
      } catch(e){ /* ignore */ }
    }
    document.addEventListener('DOMContentLoaded', updateSidebarTop);
    window.addEventListener('resize', updateSidebarTop);
    setTimeout(updateSidebarTop, 300);
    // Mobile dialog
    function initAdminPanel(){
      if (window.innerWidth >= 992) return;
      if (document.getElementById('adminSidebarToggleBtn')) return;
      var btn=document.createElement('button');
      btn.id='adminSidebarToggleBtn';
      btn.className='admin-sidebar-toggle-btn';
      btn.type='button';
      btn.setAttribute('aria-controls','adminPanelDialog');
      btn.setAttribute('aria-expanded','false');
      btn.setAttribute('title','Abrir menú admin');
      btn.innerHTML='☰';
      document.body.appendChild(btn);
      var backdrop=document.createElement('div'); backdrop.id='adminPanelBackdrop'; document.body.appendChild(backdrop);
      var dialog=document.createElement('div'); dialog.id='adminPanelDialog'; dialog.setAttribute('role','dialog'); dialog.setAttribute('aria-modal','true'); dialog.setAttribute('aria-label','Panel de administración');
      var header=document.createElement('div'); header.className='panel-header';
      var title=document.createElement('div'); title.className='panel-title'; title.textContent='Panel admin';
      var close=document.createElement('button'); close.type='button'; close.className='panel-close'; close.textContent='Cerrar'; close.setAttribute('aria-label','Cerrar panel');
      header.appendChild(title); header.appendChild(close); dialog.appendChild(header);
      var navWrap=document.createElement('nav'); navWrap.setAttribute('aria-label','Menú admin móvil');
      var list=document.createElement('div'); list.className='panel-links'; navWrap.appendChild(list); dialog.appendChild(navWrap);
      document.body.appendChild(dialog);
      // Populate links from desktop nav
      var links=Array.from(document.querySelectorAll('#adminSidebarNav a.nav-link'));
      links.forEach(function(a){ list.appendChild(a.cloneNode(true)); });
      var lastFocused=null;
      function open(){ lastFocused=document.activeElement; dialog.classList.add('show'); backdrop.classList.add('show'); btn.setAttribute('aria-expanded','true'); btn.innerHTML='×'; btn.setAttribute('title','Cerrar menú admin'); setTimeout(function(){ dialog.focus(); },30); }
      function closePanel(){ dialog.classList.remove('show'); backdrop.classList.remove('show'); btn.setAttribute('aria-expanded','false'); btn.innerHTML='☰'; btn.setAttribute('title','Abrir menú admin'); if(lastFocused && lastFocused.focus) lastFocused.focus(); }
      btn.addEventListener('click', function(){ (btn.getAttribute('aria-expanded')==='true')?closePanel():open(); });
      close.addEventListener('click', closePanel); backdrop.addEventListener('click', closePanel); document.addEventListener('keydown', function(e){ if(e.key==='Escape') closePanel(); });
      window.addEventListener('resize', function(){ if(window.innerWidth>=992){ closePanel(); dialog.remove(); backdrop.remove(); btn.remove(); } });
    }
    document.addEventListener('DOMContentLoaded', initAdminPanel);
  })();
</script>
