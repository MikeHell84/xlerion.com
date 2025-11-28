(function(){
  'use strict';

  function byId(id){ return document.getElementById(id); }
  function getCanvas(){ return document.querySelector('.templates-canvas') || byId('templatesCanvas') || byId('templatesCanvasPost') || byId('templatesCanvasFooter'); }

  // basic mapping from palette keys to simple HTML snippets so the editor feels interactive
  var insertTemplates = {
    'hero': '<section class="tpl-hero" style="padding:24px;background:#f5f7fa;border-radius:6px"><h2>Hero title</h2><p>Hero subtitle / description</p></section>',
    'card': '<div class="tpl-card" style="display:flex;gap:8px"><div style="width:120px;height:80px;background:#eee;border-radius:6px"></div><div><h4>Card title</h4><p class="small text-muted">Card description</p></div></div>',
    'two-col': '<div class="tpl-row" style="display:flex;gap:12px"><div style="flex:1">Col 1 content</div><div style="flex:1">Col 2 content</div></div>',
    'section': '<section class="tpl-section"><h3>Section title</h3><p>Section content here...</p></section>',
    'image': '<div class="tpl-image"><img src="/media/placeholder-thumb.svg" alt="Imagen" style="max-width:100%"/></div>',
    'header': '<header class="tpl-header"><h1>Site header</h1></header>',
    'footer': '<footer class="tpl-footer"><small>Site footer</small></footer>'
  };

  // Track last insert to avoid duplicates when drop+click both trigger
  var _lastInsert = { payload: '', ts: 0 };
  function generateInsertId(){
    return 'ins_' + Date.now().toString(36) + '_' + Math.floor(Math.random()*100000).toString(36);
  }

  function syncCanvasToTextarea(){
    try{
      var ta = byId('templatesEditor'); if(!ta) return;
      var c = getCanvas(); if(!c) return;
      ta.value = Array.from(c.querySelectorAll('.tpl-block')).map(function(b){ var ct = b.querySelector('.tpl-content'); return ct ? ct.innerHTML : b.innerHTML; }).join('\n');
    }catch(e){ console.error('TemplatesEditor sync', e); }
  }

  function addBlockToCanvas(html, insertId, targetContainer){
    try{
      var c = getCanvas(); if(!c) { console.warn('TemplatesEditor: no canvas found'); return null; }
      // if insertId provided and an element with same id already exists inside the canvas, skip
      try{
        if (insertId) {
          if (c.querySelector('[data-insert-id="' + insertId + '"]')) { console.debug('TemplatesEditor: skipping duplicate insert for id', insertId); return null; }
        }
      }catch(e){}
      var wrapper = document.createElement('div'); wrapper.className = 'tpl-block';
      var toolbar = document.createElement('div'); toolbar.className = 'tpl-toolbar'; toolbar.style.cssText = 'display:flex;gap:.35rem;align-items:center;margin-bottom:6px';
      var btnEdit = document.createElement('button'); btnEdit.type='button'; btnEdit.className='btn btn-sm btn-outline-secondary'; btnEdit.textContent='Editar';
      var btnUp = document.createElement('button'); btnUp.type='button'; btnUp.className='btn btn-sm btn-outline-secondary'; btnUp.textContent='▲';
      var btnDown = document.createElement('button'); btnDown.type='button'; btnDown.className='btn btn-sm btn-outline-secondary'; btnDown.textContent='▼';
      var btnDelete = document.createElement('button'); btnDelete.type='button'; btnDelete.className='btn btn-sm btn-danger'; btnDelete.textContent='Eliminar';
      var dragHandle = document.createElement('span'); dragHandle.className='drag-handle'; dragHandle.title='Arrastrar'; dragHandle.style.cssText='cursor:grab;padding:4px;color:#666'; dragHandle.textContent='⣿';
      toolbar.appendChild(dragHandle); toolbar.appendChild(btnEdit); toolbar.appendChild(btnUp); toolbar.appendChild(btnDown); toolbar.appendChild(btnDelete);

      var content = document.createElement('div'); content.className = 'tpl-content';
      // if html is a known key, replace with snippet
      var payload = String(html || '');
      // determine final HTML that'll actually be inserted
      var finalHtml = payload && insertTemplates[payload.trim()] ? insertTemplates[payload.trim()] : payload;
      // normalize finalHtml for comparison
      var normHtml = (finalHtml || '').replace(/\s+/g,' ').trim();
      try{
        var now = Date.now();
        if (normHtml && _lastInsert.payload === normHtml && (now - _lastInsert.ts) < 1200) {
          console.debug('TemplatesEditor: skipped duplicate insert (guard)');
          return null;
        }
      }catch(e){}
      content.innerHTML = finalHtml;
      // assemble
      wrapper.appendChild(toolbar);
      wrapper.appendChild(content);
      if (insertId) {
        try{ wrapper.setAttribute('data-insert-id', insertId); }catch(e){}
      }
      // if dark theme detected, mark the block so CSS can style it
      try{ if (window.TemplatesEditor && window.TemplatesEditor._isDark) wrapper.classList.add('tpl-dark'); }catch(e){}
      try{ wrapper.setAttribute('draggable','true'); }catch(e){}
      // block drag - moving existing blocks
      wrapper.addEventListener('dragstart', function(ev){ try{ var id = wrapper.getAttribute('data-insert-id') || generateInsertId(); ev.dataTransfer.setData('application/x-templates-move', id); wrapper.style.opacity = '0.4'; }catch(e){} });
      wrapper.addEventListener('dragend', function(ev){ try{ wrapper.style.opacity=''; }catch(e){} });
      // toolbar actions
      btnDelete.addEventListener('click', function(){ try{ wrapper.parentNode && wrapper.parentNode.removeChild(wrapper); syncCanvasToTextarea(); }catch(e){} });
      btnEdit.addEventListener('click', function(){ try{ var newHtml = prompt('Editar HTML del bloque:', content.innerHTML); if (newHtml !== null) { content.innerHTML = newHtml; syncCanvasToTextarea(); } }catch(e){} });
      btnUp.addEventListener('click', function(){ try{ var p = wrapper.parentNode; if (!p) return; if (wrapper.previousElementSibling) p.insertBefore(wrapper, wrapper.previousElementSibling); syncCanvasToTextarea(); }catch(e){} });
      btnDown.addEventListener('click', function(){ try{ var p = wrapper.parentNode; if (!p) return; if (wrapper.nextElementSibling) p.insertBefore(wrapper.nextElementSibling, wrapper); syncCanvasToTextarea(); }catch(e){} });

      // insert into target container if provided, otherwise append to main canvas
      var target = targetContainer || c;
      target.appendChild(wrapper);
      try{ _lastInsert.payload = (content.innerHTML||'').replace(/\s+/g,' ').trim() || ''; _lastInsert.ts = Date.now(); }catch(e){}
      syncCanvasToTextarea();
      console.debug('TemplatesEditor: block added', { payload: payload.slice(0,120), insertId: insertId });
      // if this block contains a grid row, mark columns droppable
      try{
        if (wrapper.querySelector && wrapper.querySelector('.tpl-row')){
          var cols = wrapper.querySelectorAll('.tpl-row > div');
          cols.forEach(function(col, idx){
            col.classList.add('tpl-col'); col.setAttribute('data-col-index', idx);
            col.addEventListener('dragover', function(ev){ ev.preventDefault(); col.style.outline = '2px dashed rgba(0,0,0,0.12)'; });
            col.addEventListener('dragleave', function(ev){ try{ col.style.outline=''; }catch(e){} });
            col.addEventListener('drop', function(ev){ ev.preventDefault(); try{ col.style.outline=''; var idFrom = ev.dataTransfer && (ev.dataTransfer.getData('application/x-templates-palette-id') || ev.dataTransfer.getData('application/x-templates-move')) || ''; var key = ev.dataTransfer && (ev.dataTransfer.getData('application/x-templates-palette') || ev.dataTransfer.getData('text/plain')) || ''; if (!key && idFrom){ // moving existing block
                  var moving = document.querySelector('[data-insert-id="' + idFrom + '"]'); if (moving){ moving.parentNode && moving.parentNode.removeChild(moving); col.appendChild(moving); syncCanvasToTextarea(); } return; } if (!key) return; // palette insert
                // create new block inside this column
                var insertId = ev.dataTransfer.getData('application/x-templates-palette-id') || null;
                addBlockToCanvas(key, insertId, col);
              }catch(e){ console.error('tpl-col drop', e); } });
          });
        }
      }catch(e){}
      return wrapper;
    }catch(e){ console.error('TemplatesEditor add', e); return null; }
  }

  function wirePalette(){
    try{
      var items = document.querySelectorAll('.palette-item');
      if (!items || !items.length) { console.debug('TemplatesEditor: no palette items found'); }
      items.forEach(function(p){
        try{ if (p._tplClick) p.removeEventListener('click', p._tplClick); }catch(e){}
        // click handler — reuse drag id if available so click+drop are idempotent
        var cb = function(ev){
          try{ ev && ev.preventDefault && ev.preventDefault(); }catch(e){}
          var key = p.getAttribute('data-html') || p.getAttribute('data-insert') || p.textContent || '';
          var idToUse = null;
          if (p._wasDragged && p._lastDragId) idToUse = p._lastDragId;
          if (!idToUse) idToUse = generateInsertId();
          console.debug('TemplatesEditor: palette click', key, 'insertId', idToUse);
          addBlockToCanvas(key, idToUse);
        };
        p.addEventListener('click', cb);
        p._tplClick = cb;
        try{
          p.addEventListener('dragstart', function(ev){
            try{
              var keyVal = p.getAttribute('data-html') || p.getAttribute('data-insert') || p.textContent || '';
              ev.dataTransfer.setData('application/x-templates-palette', keyVal);
              var dragId = generateInsertId();
              ev.dataTransfer.setData('application/x-templates-palette-id', dragId);
              p._lastDragId = dragId;
            }catch(e){}
            // mark as dragged so the following click can reuse the id
            p._wasDragged = true;
          });
          p.addEventListener('dragend', function(){ setTimeout(function(){ p._wasDragged = false; }, 60); });
          p.setAttribute('draggable','true');
        }catch(e){}
      });
    }catch(e){ console.error('TemplatesEditor wirePalette', e); }
  }

  function wireCanvasDnD(){
    try{
      var c = getCanvas(); if(!c) return;
          c.addEventListener('dragover', function(ev){ ev.preventDefault(); });
          c.addEventListener('drop', function(ev){
            ev.preventDefault();
            var key=''; var idFromTransfer = null;
            try{ key = ev.dataTransfer && (ev.dataTransfer.getData('application/x-templates-palette') || ev.dataTransfer.getData('text/plain')) || ''; }catch(e){}
            try{ idFromTransfer = ev.dataTransfer && (ev.dataTransfer.getData('application/x-templates-palette-id') || ''); }catch(e){}
            if (!key) return;
            addBlockToCanvas(key, idFromTransfer || null);
          });
      c.addEventListener('click', function(){ try{ c.focus && c.focus(); }catch(e){} });
    }catch(e){ console.error('TemplatesEditor wireCanvasDnD', e); }
  }

  // detect if the admin UI is using a dark background and inject minimal styles
  function isDarkTheme(){
    try{
      var el = document.documentElement || document.body;
      var cs = window.getComputedStyle(el);
      var bg = cs && cs.backgroundColor || '';
      if (!bg) return false;
      // parse rgb(a)
      var m = bg.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
      if (!m) return false;
      var r = parseInt(m[1],10), g = parseInt(m[2],10), b = parseInt(m[3],10);
      // relative luminance approximation
      var lum = (0.2126*r + 0.7152*g + 0.0722*b) / 255;
      return lum < 0.6; // threshold, fall back to dark if background is darker than medium
    }catch(e){ return false; }
  }

  function injectDarkStyles(){
    var css = '\n.templates-canvas.tpl-dark{ background:#0b0b0b !important; border-color:#222 !important; color:#eee !important; }\n.templates-canvas.tpl-dark .tpl-block{ background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #eee; }\n.templates-canvas.tpl-dark .tpl-content, .templates-canvas.tpl-dark .tpl-content *{ color: #eee !important; }\n.tpl-block.tpl-dark{ background: rgba(255,255,255,0.02); }\n';
    try{
      var s = document.createElement('style'); s.type = 'text/css'; s.appendChild(document.createTextNode(css)); document.head && document.head.appendChild(s);
    }catch(e){ console.warn('TemplatesEditor: could not inject dark styles', e); }
  }

  function init(){
    try{
  wirePalette(); wireCanvasDnD();
  // detect dark theme and mark canvas
  var dark = isDarkTheme();
  window.TemplatesEditor = window.TemplatesEditor || {};
  window.TemplatesEditor._isDark = !!dark;
  if (dark){ try{ var c = getCanvas(); if(c) c.classList.add('tpl-dark'); injectDarkStyles(); }catch(e){} }
      window.TemplatesEditor = window.TemplatesEditor || {};
      window.TemplatesEditor.insertHTML = addBlockToCanvas;
      window.TemplatesEditor.syncBeforeSubmit = function(){ syncCanvasToTextarea(); return true; };
      window.TemplatesEditor._init = init;
      syncCanvasToTextarea();
      console.info('TemplatesEditor ready');
    }catch(e){ console.error('TemplatesEditor init', e); }
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();

})();
