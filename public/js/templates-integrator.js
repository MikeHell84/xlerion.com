// Small Templates Integrator: provides openTemplatePicker() used by admin editors
(function(){
  function fetchJson(url){ return fetch(url, {credentials:'same-origin'}).then(function(r){ if(!r.ok) throw new Error('fetch failed'); return r.json(); }); }

  function buildModal(){
    if (document.getElementById('templatesPickerModal')) return document.getElementById('templatesPickerModal');
    var m = document.createElement('div'); m.className='modal fade'; m.id='templatesPickerModal'; m.tabIndex='-1'; m.setAttribute('aria-hidden','true');
    m.innerHTML = '<div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content">'
      + '<div class="modal-header"><h5 class="modal-title">Selector de plantillas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>'
  + '<div class="modal-body"><div class="d-flex mb-2 align-items-center"><input id="tplSearch" class="form-control form-control-sm me-2" placeholder="Buscar...">'
  + '<button id="tplSearchBtn" class="btn btn-sm btn-primary me-2">Buscar</button>'
  + '<button id="tplCreateBtn" class="btn btn-sm btn-success">Crear desde editor</button></div>'
      + '<div id="tplPickerList" class="row g-2" style="min-height:200px"></div>'
      + '</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>'
      + '</div></div>';
    document.body.appendChild(m);
    return m;
  }

  function openTemplatePicker(){
    var modalEl = buildModal();
    // createModal returns an object with .show() and .hide() to normalize bootstrap or a fallback
    function createModal(el){
      if (window.bootstrap && typeof window.bootstrap.Modal === 'function'){
        try{ var m = new bootstrap.Modal(el); return { show: function(){ m.show(); }, hide: function(){ m.hide(); } }; }catch(e){}
      }
      // fallback minimal modal behavior
      return {
        show: function(){ el.classList.add('show'); el.style.display = 'block'; el.setAttribute('aria-modal','true'); },
        hide: function(){ el.classList.remove('show'); el.style.display = 'none'; el.removeAttribute('aria-modal'); }
      };
    }
    var modal = createModal(modalEl);
  var listEl = modalEl.querySelector('#tplPickerList');
  var searchInput = modalEl.querySelector('#tplSearch');
  var searchBtn = modalEl.querySelector('#tplSearchBtn');
  var createBtn = modalEl.querySelector('#tplCreateBtn');

    function load(q){
      listEl.innerHTML = '<div class="col-12 text-center text-muted py-4">Cargando...</div>';
      var url = '/api/templates.php?action=list';
      if (q) url += '&q=' + encodeURIComponent(q);
      fetchJson(url).then(function(j){
        listEl.innerHTML = '';
        if (!j || !j.items || !j.items.length){ listEl.innerHTML = '<div class="col-12 text-center text-muted py-4">No hay plantillas</div>'; return; }
        j.items.forEach(function(t){
          var col = document.createElement('div'); col.className='col-12 col-md-6 col-lg-4';
          var card = document.createElement('div'); card.className='card p-2 card-preview';
          var title = document.createElement('div'); title.className='d-flex justify-content-between align-items-start';
          var h = document.createElement('strong'); h.textContent = t.name || ('Plantilla ' + (t.id||''));
          title.appendChild(h);
          card.appendChild(title);
          var desc = document.createElement('div'); desc.className='small text-muted mb-2'; desc.textContent = t.summary || (t.data && t.data.html ? (t.data.html.replace(/<[^>]*>/g,'').slice(0,120)) : '');
          card.appendChild(desc);
          var btns = document.createElement('div'); btns.className='d-flex gap-2';
          var preview = document.createElement('button'); preview.type='button'; preview.className='btn btn-sm btn-outline-light'; preview.textContent='Preview';
          preview.addEventListener('click', function(){ window.open('/api/templates.php?action=get&id='+encodeURIComponent(t.id)+'&preview=1','_blank'); });
          var insert = document.createElement('button'); insert.type='button'; insert.className='btn btn-sm btn-primary'; insert.textContent='Insertar';
          insert.addEventListener('click', function(){
            // fetch full template then dispatch event
            fetchJson('/api/templates.php?action=get&id='+encodeURIComponent(t.id)).then(function(full){
              var detail = full || t;
              // Normalize html
              var html = detail.data && detail.data.html ? detail.data.html : (detail.html || '');
              // Replace common title placeholders with current title input if present
              var titleInput = document.querySelector('input[name="title"]') || document.querySelector('input[name=title]');
              var titleVal = titleInput ? (titleInput.value || '') : '';
              if (titleVal){ html = html.replace(/\{\{\s*title\s*\}\}/ig, titleVal).replace(/%TITLE%/g, titleVal).replace(/%PAGE_TITLE%/g, titleVal).replace(/\{\{\s*page_title\s*\}\}/ig, titleVal); }
              // If TemplatesEditor canvas API exists, insert into it (prefer block-level insertion)
              try{
                if (window.TemplatesEditor && typeof window.TemplatesEditor.insertHTML === 'function'){
                  // if template has blocks array, insert each block
                  if (detail.blocks && Array.isArray(detail.blocks) && detail.blocks.length){
                    detail.blocks.forEach(function(b){ try{ var bh = (typeof b === 'string') ? b : (b.html || b); window.TemplatesEditor.insertHTML(bh); }catch(e){} });
                  } else {
                    window.TemplatesEditor.insertHTML(html);
                  }
                  showToast('Plantilla insertada en canvas','success');
                  modal.hide();
                  return;
                }
              }catch(e){}
              // Dispatch event for existing listeners as fallback
              var ev = new CustomEvent('template:insert', { detail: Object.assign({ id: t.id, name: t.name, html: html }, detail) });
              document.dispatchEvent(ev);
              modal.hide();
            }).catch(function(err){ alert('Error al cargar plantilla: ' + (err && err.message)); });
          });
          var edit = document.createElement('button'); edit.type='button'; edit.className='btn btn-sm btn-outline-primary'; edit.textContent='Editar';
          edit.addEventListener('click', function(){ window.location.href = '/admin/templates/edit?id=' + encodeURIComponent(t.id); });
          btns.appendChild(preview); btns.appendChild(insert); btns.appendChild(edit); card.appendChild(btns);
          col.appendChild(card); listEl.appendChild(col);
        });
      }).catch(function(){ listEl.innerHTML = '<div class="col-12 text-center text-danger py-4">Error al cargar</div>'; });
    }
    // Create template from current editor content
    createBtn.addEventListener('click', function(){
      try {
        var editor = document.getElementById('editor');
        var titleInput = document.querySelector('input[name="title"]') || document.querySelector('input[name=title]');
        var name = (titleInput && titleInput.value) ? ('Tpl de ' + titleInput.value) : ('Plantilla ' + new Date().toLocaleString());
        var html = editor ? editor.innerHTML : '';
        if (!html || html.trim() === '') { alert('El editor está vacío. Escribe contenido antes de crear la plantilla.'); return; }
        var fd = new FormData(); fd.append('action','save'); fd.append('name', name); fd.append('html', html);
        fetch('/api/templates.php', { method: 'POST', body: fd, credentials:'same-origin' }).then(function(r){ return r.json(); }).then(function(resp){
          if (!resp || !resp.id) { alert('No se pudo guardar la plantilla'); return; }
          alert('Plantilla creada: ' + (resp.name || name));
          load(searchInput.value || '');
        }).catch(function(err){ alert('Error al guardar: ' + (err && err.message)); });
      } catch(e){ alert('Error inesperado al crear plantilla'); }
    });

    searchBtn.addEventListener('click', function(){ load(searchInput.value || ''); });
    searchInput.addEventListener('keydown', function(e){ if (e.key === 'Enter'){ e.preventDefault(); load(searchInput.value || ''); } });

    // initial load
    load('');
    modal.show();
  }

  // expose globally
  window.openTemplatePicker = openTemplatePicker;
  // Auto-bind button if present across admin editors
  document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('openTemplatePickerBtn');
    if (btn) btn.addEventListener('click', function(){ openTemplatePicker(); });
    // Bind any create-template buttons (data-create-template="editor" or "footer")
    Array.from(document.querySelectorAll('[data-create-template]')).forEach(function(b){
      b.addEventListener('click', function(){
        var t = b.getAttribute('data-create-template') || 'editor';
        if (typeof window.createTemplateFromEditor === 'function') return window.createTemplateFromEditor(t);
        // fallback: use global function if defined later
        setTimeout(function(){ if (typeof window.createTemplateFromEditor === 'function') window.createTemplateFromEditor(t); }, 120);
      });
    });
  // Render inline widgets if containers exist
  if (document.getElementById('templates_widget_page')) window.renderTemplatesWidget('templates_widget_page','editor');
  if (document.getElementById('templates_widget_post')) window.renderTemplatesWidget('templates_widget_post','editor');
  if (document.getElementById('templates_widget_footer')) window.renderTemplatesWidget('templates_widget_footer','footer');
    // Wire Esquema panel buttons if present
    // Pages & Posts
    if (document.getElementById('tplCopyFromEditor')){
      document.getElementById('tplCopyFromEditor').addEventListener('click', function(){ var ed = document.getElementById('editor'); if (!ed) return; document.getElementById('tpl_region_content').value = ed.innerHTML; showToast('Contenido copiado al esquema','success'); });
    }
    if (document.getElementById('tplApplyToEditor')){
      document.getElementById('tplApplyToEditor').addEventListener('click', function(){ var ed = document.getElementById('editor'); if (!ed) return; var content = document.getElementById('tpl_region_content').value || ''; ed.innerHTML = content; showToast('Esquema aplicado al editor','success'); });
    }
    if (document.getElementById('tplSaveAsTemplate')){
      document.getElementById('tplSaveAsTemplate').addEventListener('click', function(){
        var hEl = document.getElementById('tpl_region_header');
        var cEl = document.getElementById('tpl_region_content');
        var fEl = document.getElementById('tpl_region_footer');
        var h = hEl ? hEl.value : '';
        var c = cEl ? cEl.value : '';
        var f = fEl ? fEl.value : '';
        var name = prompt('Nombre de la plantilla:','Plantilla ' + new Date().toLocaleString()); if (!name) return;
        var payload = { name: name, regions: { header: h, content: c, footer: f } };
        fetch('/api/templates.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}).then(function(r){ return r.json(); }).then(function(j){ if (j && j.id) { showToast('Plantilla guardada','success'); } else showToast('Error al guardar','danger'); }).catch(function(){ showToast('Error al guardar','danger'); });
      });
    }
    // Footer-specific buttons
    if (document.getElementById('tplCopyFromFooter')){
      document.getElementById('tplCopyFromFooter').addEventListener('click', function(){ var ta = document.getElementById('footer_text'); if (!ta) return; document.getElementById('tpl_region_content').value = ta.value; showToast('Texto copiado al esquema','success'); });
    }
    if (document.getElementById('tplApplyToFooter')){
      document.getElementById('tplApplyToFooter').addEventListener('click', function(){ var ta = document.getElementById('footer_text'); if (!ta) return; ta.value = document.getElementById('tpl_region_content').value || ''; showToast('Esquema aplicado al texto del footer','success'); });
    }
    if (document.getElementById('tplSaveFooterAsTemplate')){
      document.getElementById('tplSaveFooterAsTemplate').addEventListener('click', function(){
        var hEl = document.getElementById('tpl_region_header');
        var cEl = document.getElementById('tpl_region_content');
        var fEl = document.getElementById('tpl_region_footer');
        var h = hEl ? hEl.value : '';
        var c = cEl ? cEl.value : '';
        var f = fEl ? fEl.value : '';
        var name = prompt('Nombre de la plantilla (footer):','Footer ' + new Date().toLocaleString()); if (!name) return;
        var payload = { name: name, regions: { header: h, content: c, footer: f } };
        fetch('/api/templates.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}).then(function(r){ return r.json(); }).then(function(j){ if (j && j.id) { showToast('Plantilla guardada','success'); } else showToast('Error al guardar','danger'); }).catch(function(){ showToast('Error al guardar','danger'); });
      });
    }
  });

  // Default insertion handler for admins lacking a specific listener
  document.addEventListener('template:insert', function(e){
    var t = e.detail || {};
    var html = (t.data && t.data.html) ? t.data.html : (t.html || '');
    if (!html && t.data && t.data.regions){ html = (t.data.regions.content||'') + (t.data.regions.header||'') + (t.data.regions.footer||''); }
    if (!html) return;
    var editor = document.getElementById('editor');
    var footerTA = document.getElementById('footer_text');
    if (editor){ editor.focus(); try{ document.execCommand('insertHTML', false, html); }catch(err){ editor.innerHTML += html; } return; }
    if (footerTA){ footerTA.value += (footerTA.value ? "\n" : '') + html; return; }
  });

  // Create template from editor/footer content
  window.createTemplateFromEditor = function(kind){
    try{
      var el = (kind === 'footer') ? document.getElementById('footer_text') : document.getElementById('editor');
      if (!el){ alert('No se encontró el editor para crear la plantilla.'); return; }
      var html = (typeof el.value !== 'undefined') ? el.value : el.innerHTML;
      if (!html || !html.trim()){ alert('El editor está vacío. Agrega contenido antes de crear una plantilla.'); return; }
      var titleInput = document.querySelector('input[name="title"]') || document.querySelector('input[name=title]');
      var defaultName = (titleInput && titleInput.value) ? ('Tpl de ' + titleInput.value) : (kind === 'footer' ? 'Footer plantilla ' + new Date().toLocaleString() : 'Plantilla ' + new Date().toLocaleString());
      var name = prompt('Nombre de la plantilla:', defaultName);
      if (!name) return;
      var fd = new FormData(); fd.append('action','save'); fd.append('name', name); fd.append('html', html);
      fetch('/api/templates.php', { method: 'POST', body: fd, credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(j){
        if (j && j.id){ alert('Plantilla creada: ' + (j.name || name)); if (typeof openTemplatePicker === 'function') openTemplatePicker(); }
        else { alert('No se pudo crear la plantilla.'); }
      }).catch(function(err){ alert('Error al crear plantilla: ' + (err && err.message)); });
    }catch(e){ alert('Error inesperado al crear plantilla'); }
  };

  // Fetch list helper used by inline widgets
  function fetchTemplatesList(q){ var url = '/api/templates.php?action=list'; if (q) url += '&q=' + encodeURIComponent(q); return fetch(url, {credentials:'same-origin'}).then(function(r){ if (!r.ok) throw new Error('fetch failed'); return r.json(); }); }

  // Small inline widget: render into container with given id and target kind ('editor'|'footer')
  // Toast helper (Bootstrap 5) — container must exist in layout (we use body)
  function showToast(msg, type, actions){
    // type: 'success'|'danger'|'info'
    var toastRoot = document.getElementById('templates-toasts-root');
    if (!toastRoot){ toastRoot = document.createElement('div'); toastRoot.id = 'templates-toasts-root'; toastRoot.style.position = 'fixed'; toastRoot.style.right = '16px'; toastRoot.style.bottom = '16px'; toastRoot.style.zIndex = '2100'; document.body.appendChild(toastRoot); }
    var el = document.createElement('div'); el.className = 'toast align-items-center text-bg-'+(type||'info')+' border-0 show'; el.setAttribute('role','alert'); el.setAttribute('aria-live','assertive'); el.setAttribute('aria-atomic','true'); el.style.minWidth='200px';
    el.innerHTML = '<div class="d-flex"><div class="toast-body">'+(msg||'')+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';
    toastRoot.appendChild(el);
    if (actions && typeof actions.onUndo === 'function'){
      var undoBtn = document.createElement('button'); undoBtn.className='btn btn-sm btn-light ms-2'; undoBtn.textContent='Deshacer'; undoBtn.addEventListener('click', function(){ actions.onUndo(); el.remove(); }); el.querySelector('.d-flex').appendChild(undoBtn);
    }
    setTimeout(function(){ el.remove(); }, 8000);
  }

  window.renderTemplatesWidget = function(containerId, kind){
    var root = document.getElementById(containerId); if (!root) return;
    // Use wrapping flex and limit max-width so widget buttons don't overflow page area
    root.innerHTML = '<div class="d-flex gap-2 align-items-center flex-wrap" style="max-width:100%">'
      + '<select class="form-select form-select-sm" style="min-width:160px;max-width:48%" id="'+containerId+'-select"><option>Cargando plantillas...</option></select>'
      + '<select class="form-select form-select-sm" id="'+containerId+'-region" style="width:120px;min-width:120px">'
      + '<option value="content">Contenido</option><option value="header">Header</option><option value="footer">Footer</option></select>'
      + '<div style="display:inline-flex;gap:.375rem;flex-wrap:wrap">'
      + '<button class="btn btn-sm btn-primary" id="'+containerId+'-insert">Insertar</button>'
      + '<button class="btn btn-sm btn-outline-primary" id="'+containerId+'-edit">Editar</button>'
      + '<button class="btn btn-sm btn-danger" id="'+containerId+'-delete">Eliminar</button>'
      + '<button class="btn btn-sm btn-success" id="'+containerId+'-create">Crear</button></div>';
    var sel = document.getElementById(containerId+'-select');
    var insertBtn = document.getElementById(containerId+'-insert');
    var editBtn = document.getElementById(containerId+'-edit');
    var delBtn = document.getElementById(containerId+'-delete');
    var createBtn = document.getElementById(containerId+'-create');
  function load(){ fetchTemplatesList().then(function(j){ sel.innerHTML = '<option value="">-- Seleccionar plantilla --</option>'; (j.items||[]).forEach(function(t){ var opt = document.createElement('option'); opt.value = t.id; opt.textContent = (t.name||t.id); sel.appendChild(opt); }); }).catch(function(){ sel.innerHTML = '<option value="">Error al cargar</option>'; showToast('Error al cargar plantillas','danger'); }); }
  load();
  insertBtn.addEventListener('click', function(){ var id = sel.value; var regionSel = document.getElementById(containerId+'-region'); var region = regionSel?regionSel.value:'content'; if (!id) return showToast('Selecciona una plantilla','danger'); fetch('/api/templates.php?action=get&id='+encodeURIComponent(id),{credentials:'same-origin'}).then(function(r){ if (!r.ok) throw new Error('fetch'); return r.json(); }).then(function(t){ var html = (t.data&&t.data.html)?t.data.html:(t.html||''); var regionHtml = html; var titleInput = document.querySelector('input[name="title"]'); var titleVal = titleInput?titleInput.value:''; if(titleVal) regionHtml = regionHtml.replace(/\{\{\s*title\s*\}\}/ig,titleVal).replace(/%TITLE%/g,titleVal); if (t.data && t.data.regions && t.data.regions[region]) regionHtml = t.data.regions[region]; try{ if (window.TemplatesEditor && typeof window.TemplatesEditor.insertHTML === 'function'){ if (t.blocks && Array.isArray(t.blocks) && t.blocks.length){ t.blocks.forEach(function(b){ try{ var bh = (typeof b === 'string') ? b : (b.html || b); window.TemplatesEditor.insertHTML(bh); }catch(e){} }); } else { window.TemplatesEditor.insertHTML(regionHtml); } showToast('Plantilla insertada en canvas','success'); return; } }catch(e){} var ev = new CustomEvent('template:insert',{detail:{id:t.id,name:t.name,html:regionHtml,region:region,data:t.data}}); document.dispatchEvent(ev); showToast('Plantilla insertada','success'); }).catch(function(){ showToast('Error al cargar plantilla','danger'); }); });
    editBtn.addEventListener('click', function(){ var id = sel.value; if (!id) return alert('Selecciona una plantilla'); window.location.href = '/admin/templates/edit?id='+encodeURIComponent(id); });
  delBtn.addEventListener('click', function(){ var id = sel.value; if (!id) return showToast('Selecciona una plantilla','danger'); if (!confirm('Eliminar plantilla?')) return; fetch('/api/templates.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'delete',id:id})}).then(r=>r.json()).then(function(j){ if (j && j.ok){ showToast('Plantilla eliminada','success',{ onUndo: function(){ // attempt to restore deleted via save
          var d = j.deleted; if (!d) return showToast('No hay datos para deshacer','danger'); fetch('/api/templates.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:d.id,name:d.name,html:d.html,description:d.description,blocks:d.blocks,theme:d.theme})}).then(r=>r.json()).then(function(saved){ if (saved && saved.id) { showToast('Restaurada','success'); load(); } else showToast('Fallo al restaurar','danger'); }).catch(function(){ showToast('Fallo al restaurar','danger'); }); } }); load(); } else showToast('Error al eliminar','danger'); }).catch(function(){ showToast('Error al eliminar','danger'); }); });
  createBtn.addEventListener('click', function(){ window.createTemplateFromEditor(kind); setTimeout(load,500); });
  };
})();
