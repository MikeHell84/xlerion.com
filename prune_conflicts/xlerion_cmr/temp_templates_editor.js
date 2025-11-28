(function(){
  // Minimal editor helper for templates: insert predefined snippets and sync HTML to hidden textarea
  window.TemplatesEditor = (function(){
    function byId(id){ return document.getElementById(id); }
  // decode HTML entities like &lt; to < so iframe preview renders real HTML
  function decodeEntities(str){ try{ var ta = document.createElement('textarea'); ta.innerHTML = str; return ta.value; }catch(e){ return str; } }

    // Section-based, structure-only templates (no sample content)
    var __SECTION_TEMPLATES = [
      {
        id: 'base-inicio',
        name: 'Base: Inicio (estructura)',
        description: 'Estructura de portada: hero, franja destacada y grid de tarjetas.',
        html:
          '<section class="container">\n'
        + '  <!-- hero-content -->\n'
        + '  <div class="hero-content text-center"></div>\n'
        + '</section>\n'
        + '<div class="container my-3">\n'
        + '  <!-- section-stripe -->\n'
        + '  <div class="section-stripe"></div>\n'
        + '</div>\n'
        + '<section class="container my-5">\n'
        + '  <div class="row g-3">\n'
        + '    <div class="col-12 col-md-6 col-lg-4"><article class="card card-preview"></article></div>\n'
        + '    <div class="col-12 col-md-6 col-lg-4"><article class="card card-preview"></article></div>\n'
        + '    <div class="col-12 col-md-6 col-lg-4"><article class="card card-preview"></article></div>\n'
        + '  </div>\n'
        + '</section>'
      },
      {
        id: 'base-informacion',
        name: 'Base: Información (estructura)',
        description: 'Estructura de sección informativa con texto a la izquierda y tarjetas/imagen a la derecha.',
        html:
          '<section class="container section-grid my-4">\n'
        + '  <div class="row">\n'
        + '    <div class="col-lg-7 section-text">\n'
        + '      <div class="prose-left"><!-- texto principal --></div>\n'
        + '    </div>\n'
        + '    <div class="col-lg-5 section-cards"><!-- tarjetas/recursos --></div>\n'
        + '  </div>\n'
        + '</section>'
      },
      
      {
        id: 'base-contacto',
        name: 'Base: Contacto (estructura)',
        description: 'Dos columnas: formulario y detalles de contacto, sin contenido.',
        html:
          '<div class="container">\n'
        + '  <div class="row gx-4 gy-4">\n'
        + '    <div class="col-12 col-md-7"><section class="contact-card"><form class="needs-validation"><div class="mb-3"></div><div class="mb-3"></div><div class="mb-3"></div></form></section></div>\n'
        + '    <div class="col-12 col-md-5"><aside class="contact-card"><ul class="contact-list"></ul></aside></div>\n'
        + '  </div>\n'
        + '</div>'
      }
    ];


    var snippets = {
      'hero': '<section class="hero p-5 text-center">\n  <div class="container">\n    <h2 class="display-4">Título Hero</h2>\n    <p class="lead">Subtítulo o descripción breve.</p>\n    <a class="btn btn-primary" href="#">Llamada a la acción</a>\n  </div>\n</section>\n',
      'card': '<div class="card p-3">\n  <div class="card-body">\n    <h5 class="card-title">Card title</h5>\n    <p class="card-text">Texto descriptivo de la tarjeta.</p>\n    <a href="#" class="btn btn-outline-light">Leer más</a>\n  </div>\n</div>\n',
      'two-col': '<div class="row g-3">\n  <div class="col-md-6">\n    <div class="card p-3">\n      <h5>Columna 1</h5>\n      <p>Contenido.</p>\n    </div>\n  </div>\n  <div class="col-md-6">\n    <div class="card p-3">\n      <h5>Columna 2</h5>\n      <p>Contenido.</p>\n    </div>\n  </div>\n</div>\n'
    };
    // --- Canvas (visual blocks) helpers ---
    function getCanvas(){
      // prefer explicit id, fall back to class selector for robustness
      var el = document.getElementById('templatesCanvas');
      if (el) return el;
      try{ el = document.querySelector('.templates-canvas'); }catch(e){ el = null; }
      return el;
    }
    function stripScripts(html){ return (html||'').replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, ''); }
    function ensureCanvasStyles(){ var c = getCanvas(); if(!c || c._styled) return; c._styled=true; c.style.padding='8px'; c.style.background='#fff'; }
  function addBlockToCanvas(html){ var c = getCanvas(); console.debug('[TemplatesEditor] addBlockToCanvas called', !!c, html && html.substr ? html.substr(0,120) : html); if(!c) return; // prevent accidental duplicate inserts (drag/drop can fire twice in some browsers)
      try{
        var _trim = (html||'').replace(/\s+/g,' ').trim();
        var last = window.__templates_last_inserted || {content:'', time:0};
        if (last.content === _trim && (Date.now() - last.time) < 900) {
          return; // skip duplicate
        }
        window.__templates_last_inserted = { content: _trim, time: Date.now() };
      }catch(e){}
      removeCanvasHint(); var wrap = document.createElement('div'); wrap.className='tpl-block'; wrap.style.position='relative'; wrap.style.margin='8px 0'; wrap.style.display='block'; wrap.style.boxSizing='border-box';
  var handle = document.createElement('div'); handle.className='tpl-handle'; handle.setAttribute('aria-hidden','true'); handle.style.position='absolute'; handle.style.left='6px'; handle.style.top='10px'; handle.style.bottom='auto'; handle.style.width='14px'; handle.style.height='14px'; handle.style.cursor='grab'; handle.style.background='transparent';
  // make blocks draggable so reordering works immediately after insertion
  try{ wrap.setAttribute('draggable','true'); }catch(e){}
      var content = document.createElement('div'); content.className='tpl-content'; content.style.marginLeft='0'; content.innerHTML = stripScripts(html);
      // controls
      var controls = document.createElement('div'); controls.className='tpl-controls';
  var btnEdit = document.createElement('button'); btnEdit.setAttribute('type','button'); btnEdit.className='btn btn-sm btn-outline-secondary'; btnEdit.textContent='Editar';
  var btnDel = document.createElement('button'); btnDel.setAttribute('type','button'); btnDel.className='btn btn-sm btn-outline-danger'; btnDel.textContent='Borrar';
      controls.appendChild(btnEdit); controls.appendChild(btnDel);
  wrap.appendChild(handle); wrap.appendChild(content); wrap.appendChild(controls); c.appendChild(wrap); ensureControls(wrap); wireBlockDrag(wrap); ensureCanvasStyles(); adjustCanvasHeight(); try{ wireColumns(); }catch(e){} syncCanvasToTextarea(); try{ wrap.scrollIntoView({behavior:'smooth', block:'nearest'}); }catch(e){}
    }
    function removeCanvasHint(){ var c = getCanvas(); if(!c) return; var hint = c.querySelector('.tpl-canvas-hint'); if (hint) hint.parentNode.removeChild(hint); }
  function loadCanvasFromHtml(html){ var c = getCanvas(); if(!c) return; c.innerHTML=''; var tmp = document.createElement('div'); tmp.innerHTML = stripScripts(html||''); var nodes = Array.from(tmp.childNodes); if (!nodes.length){ var hint=document.createElement('div'); hint.className='tpl-canvas-hint text-muted'; hint.style.padding='12px'; hint.style.border='1px dashed rgba(0,0,0,0.15)'; hint.style.borderRadius='6px'; hint.textContent='Arrastra o inserta componentes aquí'; c.appendChild(hint); try{ wireColumns(); }catch(e){} return; } nodes.forEach(function(n){ if(n.nodeType===3 && /^\s*$/.test(n.textContent||'')) return; var w=document.createElement('div'); w.className='tpl-block'; w.style.position='relative'; w.style.margin='8px 0'; w.style.display='block'; w.style.boxSizing='border-box'; var h=document.createElement('div'); h.className='tpl-handle'; h.setAttribute('aria-hidden','true'); h.style.position='absolute'; h.style.left='0'; h.style.top='0'; h.style.bottom='0'; h.style.width='18px'; h.style.cursor='grab'; h.style.background='transparent'; var content=document.createElement('div'); content.className='tpl-content'; content.style.marginLeft='0'; content.appendChild(n);
      // add controls so loaded blocks have Edit/Delete buttons wired
      var controls = document.createElement('div'); controls.className='tpl-controls';
      var btnEdit = document.createElement('button'); btnEdit.setAttribute('type','button'); btnEdit.className='btn btn-sm btn-outline-secondary'; btnEdit.textContent='Editar';
      var btnDel = document.createElement('button'); btnDel.setAttribute('type','button'); btnDel.className='btn btn-sm btn-outline-danger'; btnDel.textContent='Borrar';
      controls.appendChild(btnEdit); controls.appendChild(btnDel);
  w.appendChild(h); w.appendChild(content); w.appendChild(controls); c.appendChild(w); ensureControls(w); wireBlockDrag(w); }); ensureCanvasStyles(); try{ wireColumns(); }catch(e){} }
    function serializeCanvas(){ var c = getCanvas(); if(!c) return ''; var out = []; Array.from(c.children).forEach(function(ch){ if (!ch.classList || !ch.classList.contains('tpl-block')) return; var cont = ch.querySelector('.tpl-content'); var html = cont ? cont.innerHTML : ch.innerHTML; // client-side defensive filter of classes
        try{
          var allowed = window.__XLERION_ALLOWED_CLASSES || [];
          var patterns = window.__XLERION_ALLOWED_CLASS_PATTERNS || [];
          html = html.replace(/class\s*=\s*("|')(.*?)\1/gi, function(m, q, v){ var parts = v.split(/\s+/).map(function(x){ return x.trim(); }).filter(Boolean); var keep = []; parts.forEach(function(c){ if (allowed.indexOf(c) !== -1) { keep.push(c); return; } for(var i=0;i<patterns.length;i++){ try{ var re = new RegExp(patterns[i]); if (re.test(c)){ keep.push(c); break; } }catch(e){} } }); if (keep.length) return 'class="'+keep.join(' ')+'"'; return ''; });
        }catch(e){}
        out.push(html);
    }); return stripScripts(out.join('\n')); }
    function syncCanvasToTextarea(){ try{ var ta=document.getElementById('templatesEditor'); if (ta){ ta.value = serializeCanvas(); updateHtmlPaneFromCanvas(); } }catch(e){} }
    function updateHtmlPaneFromCanvas(){ var pre = document.getElementById('templatesEditorHtmlPane'); if (pre) pre.textContent = serializeCanvas(); }
  function wireCanvasDnD(){ var c = getCanvas(); if(!c) return; c.addEventListener('dragenter', function(ev){ try{ ev.preventDefault(); ev.dataTransfer.dropEffect = 'copy'; }catch(e){} }); c.addEventListener('dragover', function(ev){ try{ ev.preventDefault(); ev.dataTransfer.dropEffect = 'copy'; }catch(e){ ev.preventDefault(); } }); c.addEventListener('drop', function(ev){ ev.preventDefault(); console.debug('[TemplatesEditor] canvas drop', ev); var key = null; try{ key = ev.dataTransfer && (ev.dataTransfer.getData('application/x-templates-palette') || ev.dataTransfer.getData('text/plain')); }catch(e){ try{ key = ev.dataTransfer && ev.dataTransfer.getData('text/plain'); }catch(e){ key = null; } }
  if ((!key || !snippets[key]) && window.__templates_current_drag){ key = window.__templates_current_drag; }
  if (!key || !snippets[key]) return;
        // If drop is over a column, delegate to column insertion to avoid double-insert
        try{
          var x = ev.clientX, y = ev.clientY; var elem = document.elementFromPoint(x,y); var col = findClosestColumn(elem, c);
          if (col){ // insert into column at pointer position
            ev.stopPropagation(); insertIntoColumnAtY(col, snippets[key], ev.clientY); return;
          }
        }catch(e){}
        // fallback: add to canvas root
        addBlockToCanvas(snippets[key]); }); }

  // Helper: find closest ancestor that looks like a Bootstrap column within the canvas
    function findClosestColumn(el, container){ try{
      var cur = el;
      while(cur && cur !== container){ if (cur.nodeType===1){ var cls = cur.className || ''; if (/\bcol(?:-[a-z]+)?-?\d*\b/.test(cls) || /\bcol\b/.test(cls)) return cur; } cur = cur.parentNode; } return null; }catch(e){ return null; } }

    // Helper: insert snippet HTML into a column element as a wrapped tpl-block
  function addHtmlIntoColumn(html, columnEl, beforeEl){ try{
      if (!columnEl) return;
      // guard: avoid inserting identical content into the same column multiple times quickly
      try{
        var _trim = (html||'').replace(/\s+/g,' ').trim();
        var last = window.__templates_last_inserted || {content:'', time:0, target:null};
        if (last.content === _trim && (Date.now() - last.time) < 900 && last.target === columnEl) { return; }
        window.__templates_last_inserted = { content: _trim, time: Date.now(), target: columnEl };
      }catch(e){}
      var c = getCanvas(); // ensure canvas context
      var wrapper = document.createElement('div'); wrapper.className = 'tpl-block'; wrapper.setAttribute('draggable','true'); wrapper.style.display='block'; wrapper.style.width='100%'; wrapper.style.boxSizing='border-box';
      var handle = document.createElement('div'); handle.className='tpl-handle'; handle.setAttribute('aria-hidden','true');
      var content = document.createElement('div'); content.className='tpl-content'; content.style.marginLeft='0'; content.innerHTML = stripScripts(html);
      var controls = document.createElement('div'); controls.className='tpl-controls';
      var btnEdit = document.createElement('button'); btnEdit.setAttribute('type','button'); btnEdit.className='btn btn-sm btn-outline-secondary'; btnEdit.textContent='Editar';
      var btnDel = document.createElement('button'); btnDel.setAttribute('type','button'); btnDel.className='btn btn-sm btn-outline-danger'; btnDel.textContent='Borrar';
      controls.appendChild(btnEdit); controls.appendChild(btnDel);
      wrapper.appendChild(handle); wrapper.appendChild(content); wrapper.appendChild(controls);
      // If a specific child target is provided and is inside the column, insert before it; otherwise append
      try{
        if (beforeEl && columnEl.contains(beforeEl)) { columnEl.insertBefore(wrapper, beforeEl); }
        else { columnEl.appendChild(wrapper); }
      }catch(e){ columnEl.appendChild(wrapper); }
      ensureControls(wrapper); wireBlockDrag(wrapper); try{ wireColumns(); }catch(e){} syncCanvasToTextarea(); try{ wrapper.scrollIntoView({behavior:'smooth', block:'nearest'}); }catch(e){}
    }catch(e){ console.error('addHtmlIntoColumn error', e); } }

    // Insert within column based on vertical cursor position
    function insertIntoColumnAtY(columnEl, html, clientY){ try{
      var children = Array.from(columnEl.children).filter(function(ch){ return ch && ch.nodeType===1; });
      var before = null;
      for (var i=0;i<children.length;i++){
        try{ var r = children[i].getBoundingClientRect(); if (clientY < r.top + r.height/2){ before = children[i]; break; } }catch(e){}
      }
      addHtmlIntoColumn(html, columnEl, before);
    }catch(e){ addHtmlIntoColumn(html, columnEl, null); } }

    // Wire drop zones on columns inside the canvas
    function wireColumns(){ try{
      var c = getCanvas(); if (!c) return;
      var cols = c.querySelectorAll('[class*="col-"], .col');
      cols.forEach(function(col){
        if (col._tplColWired) return; col._tplColWired = true;
  col.addEventListener('dragover', function(ev){ try{ ev.preventDefault(); ev.dataTransfer.dropEffect = 'copy'; col.classList.add('tpl-col-drop-hover'); }catch(e){ ev.preventDefault(); } });
  col.addEventListener('dragleave', function(ev){ try{ col.classList.remove('tpl-col-drop-hover'); }catch(e){} });
  col.addEventListener('drop', function(ev){ ev.preventDefault(); ev.stopPropagation(); console.debug('[TemplatesEditor] column drop', col); try{ col.classList.remove('tpl-col-drop-hover'); }catch(e){} var key = null; try{ key = ev.dataTransfer && (ev.dataTransfer.getData('application/x-templates-palette') || ev.dataTransfer.getData('text/plain')); }catch(e){ key = null; } if ((!key || !snippets[key]) && window.__templates_current_drag){ key = window.__templates_current_drag; } if (!key || !snippets[key]) return; insertIntoColumnAtY(col, snippets[key], ev.clientY); });
      });
      // Styling for hover
      if (!document.getElementById('tpl-col-hover-style')){
        var st = document.createElement('style'); st.id='tpl-col-hover-style'; st.textContent = '.tpl-col-drop-hover{ outline:2px dashed rgba(0,170,255,0.45); outline-offset:2px; }'; document.head.appendChild(st);
      }
    }catch(e){}
    }

  // Track last clicked column so palette clicks can insert into it
  var __templates_last_clicked_column = null;
  function clearLastClickedColumn(){ try{ if(__templates_last_clicked_column){ __templates_last_clicked_column.classList && __templates_last_clicked_column.classList.remove('tpl-column-selected'); } __templates_last_clicked_column = null; }catch(e){}
  }
  function setLastClickedColumnFromEvent(ev){ try{ var c = getCanvas(); if(!c) return; var target = ev.target || ev.srcElement; var col = findClosestColumn(target, c); clearLastClickedColumn(); if (col){ __templates_last_clicked_column = col; try{ __templates_last_clicked_column.classList.add('tpl-column-selected'); }catch(e){} } }catch(e){} }
  // add a tiny visual hint for selected column via editor-scoped CSS
  (function(){ var style = document.createElement('style'); style.textContent = '.tpl-column-selected{ outline:2px dashed rgba(0,123,255,0.35); }'; document.head.appendChild(style); })();

  // wire canvas click to select a column
  try{ var __c = getCanvas(); if (__c){ __c.addEventListener('click', function(ev){ setLastClickedColumnFromEvent(ev); }); __c.addEventListener('scroll', function(){ /* keep selection visible */ }); } }catch(e){}
    function wireBlockDrag(block){ try{ var c=getCanvas(); if(!c) return; var handle=block.querySelector('.tpl-handle'); if(!handle) return; handle.style.touchAction='none'; var indicator='tpl-drop-before'; function targetByY(y){ var bs=Array.from(c.querySelectorAll('.tpl-block')); for(var i=0;i<bs.length;i++){ var r=bs[i].getBoundingClientRect(); if (y < r.top + r.height/2) return bs[i]; } return null; }
      // helper: start a pointer-based drag for this block
      function startPointerDrag(startEvent){ try{
          startEvent.preventDefault(); startEvent.stopPropagation();
          var rect = block.getBoundingClientRect(); var offX = startEvent.clientX - rect.left; var offY = startEvent.clientY - rect.top;
          var ghost = block.cloneNode(true); ghost.classList.add('tpl-ghost'); ghost.style.position='fixed'; ghost.style.left = rect.left + 'px'; ghost.style.top = rect.top + 'px'; ghost.style.width = rect.width + 'px'; ghost.style.opacity = '0.9'; ghost.style.pointerEvents = 'none'; ghost.style.zIndex = 9999; document.body.appendChild(ghost);
          block.classList.add('is-dragging'); var current=null; var currentColumn=null; var currentBefore=null;
          function findColumnAndBefore(x,y){ try{ var el = document.elementFromPoint(x,y); if (!el) return {col:null,before:null}; var col = findClosestColumn(el, c); if (!col) return {col:null,before:null}; var children = Array.from(col.children).filter(function(ch){ return ch && ch.nodeType===1; }); var before=null; for(var i=0;i<children.length;i++){ try{ var r = children[i].getBoundingClientRect(); if (y < r.top + r.height/2){ before = children[i]; break; } }catch(e){} } return {col:col,before:before}; }catch(e){ return {col:null,before:null}; } }
          function onMove(ev){ ghost.style.left = (ev.clientX - offX) + 'px'; ghost.style.top = (ev.clientY - offY) + 'px'; var t = targetByY(ev.clientY); c.querySelectorAll('.tpl-block').forEach(function(x){ x.classList.remove(indicator); }); c.querySelectorAll('.tpl-col-drop-hover').forEach(function(x){ x.classList.remove('tpl-col-drop-hover'); }); if (t && t !== block){ t.classList.add(indicator); current = t; } else { current = null; } var cb = findColumnAndBefore(ev.clientX, ev.clientY); if (cb.col){ cb.col.classList.add('tpl-col-drop-hover'); currentColumn = cb.col; currentBefore = cb.before; } else { currentColumn = null; currentBefore = null; } }
          function onUp(){ window.removeEventListener('pointermove', onMove); window.removeEventListener('pointerup', onUp); if (ghost.parentNode) ghost.parentNode.removeChild(ghost); block.classList.remove('is-dragging'); c.querySelectorAll('.tpl-block').forEach(function(x){ x.classList.remove(indicator); }); c.querySelectorAll('.tpl-col-drop-hover').forEach(function(x){ x.classList.remove('tpl-col-drop-hover'); }); try{ if (currentColumn){ if (currentBefore && currentColumn.contains(currentBefore)) { currentColumn.insertBefore(block, currentBefore); } else { currentColumn.appendChild(block); } } else if (current && current.parentNode === c){ c.insertBefore(block, current); } else { c.appendChild(block); } syncCanvasToTextarea(); }catch(e){} }
          window.addEventListener('pointermove', onMove); window.addEventListener('pointerup', onUp);
        }catch(e){}
      }
      handle.addEventListener('pointerdown', function(start){ start.preventDefault(); start.stopPropagation(); var rect=block.getBoundingClientRect(); var offX=start.clientX-rect.left; var offY=start.clientY-rect.top; var ghost = block.cloneNode(true); ghost.classList.add('tpl-ghost'); ghost.style.position='fixed'; ghost.style.left=rect.left+'px'; ghost.style.top=rect.top+'px'; ghost.style.width=rect.width+'px'; ghost.style.opacity='0.9'; ghost.style.pointerEvents='none'; ghost.style.zIndex='9999'; document.body.appendChild(ghost); block.classList.add('is-dragging'); var current=null; var currentColumn=null; var currentBefore=null;
        function findColumnAndBefore(x,y){ try{ var el = document.elementFromPoint(x,y); if (!el) return {col:null,before:null}; var col = findClosestColumn(el, c); if (!col) return {col:null,before:null}; // find before element within column
              var children = Array.from(col.children).filter(function(ch){ return ch && ch.nodeType===1; }); var before = null; for(var i=0;i<children.length;i++){ try{ var r = children[i].getBoundingClientRect(); if (y < r.top + r.height/2){ before = children[i]; break; } }catch(e){} } return {col:col,before:before}; }catch(e){ return {col:null,before:null}; } }

        function onMove(ev){ ghost.style.left=(ev.clientX-offX)+'px'; ghost.style.top=(ev.clientY-offY)+'px'; var t=targetByY(ev.clientY); // clear indicators
          c.querySelectorAll('.tpl-block').forEach(function(x){ x.classList.remove(indicator); }); // remove column hover
          c.querySelectorAll('.tpl-col-drop-hover').forEach(function(x){ x.classList.remove('tpl-col-drop-hover'); });
          // highlight block target (for root canvas insertion)
          if (t && t!==block){ t.classList.add(indicator); current=t; } else { current=null; }
          // detect column under pointer and highlight it
          var cb = findColumnAndBefore(ev.clientX, ev.clientY); if (cb.col){ cb.col.classList.add('tpl-col-drop-hover'); currentColumn = cb.col; currentBefore = cb.before; } else { currentColumn = null; currentBefore = null; }
        }

        function onUp(){ window.removeEventListener('pointermove', onMove); window.removeEventListener('pointerup', onUp); if (ghost.parentNode) ghost.parentNode.removeChild(ghost); block.classList.remove('is-dragging'); c.querySelectorAll('.tpl-block').forEach(function(x){ x.classList.remove(indicator); }); c.querySelectorAll('.tpl-col-drop-hover').forEach(function(x){ x.classList.remove('tpl-col-drop-hover'); });
          try{
            if (currentColumn){ // insert into column at position
              if (currentBefore && currentColumn.contains(currentBefore)) { currentColumn.insertBefore(block, currentBefore); }
              else { currentColumn.appendChild(block); }
            } else if (current && current.parentNode===c){ c.insertBefore(block, current); } else { c.appendChild(block); }
            syncCanvasToTextarea();
          }catch(e){}
        }
        window.addEventListener('pointermove', onMove); window.addEventListener('pointerup', onUp); }); }catch(e){}
    }
    // ensure a block has .tpl-controls (edit/delete/move) and wire handlers
    function ensureControls(block){
      if (!block) return;
      var ctr = block.querySelector('.tpl-controls');
      if (!ctr){
        ctr = document.createElement('div'); ctr.className = 'tpl-controls';
        // move up/down buttons
        var btnUp = document.createElement('button'); btnUp.setAttribute('type','button'); btnUp.className='btn btn-sm btn-outline-light tpl-move-up'; btnUp.title='Subir'; btnUp.textContent='↑';
        var btnDown = document.createElement('button'); btnDown.setAttribute('type','button'); btnDown.className='btn btn-sm btn-outline-light tpl-move-down'; btnDown.title='Bajar'; btnDown.textContent='↓';
  var btnGrab = document.createElement('button'); btnGrab.setAttribute('type','button'); btnGrab.className='btn btn-sm btn-outline-light tpl-grab'; btnGrab.title='Arrastrar'; btnGrab.textContent='↕';
  var btnEdit = document.createElement('button'); btnEdit.setAttribute('type','button'); btnEdit.className='btn btn-sm btn-outline-secondary tpl-edit'; btnEdit.textContent='Editar';
  var btnDel = document.createElement('button'); btnDel.setAttribute('type','button'); btnDel.className='btn btn-sm btn-outline-danger tpl-remove'; btnDel.textContent='Borrar';
  var btnAttach = document.createElement('button'); btnAttach.setAttribute('type','button'); btnAttach.className='btn btn-sm btn-outline-primary tpl-attach'; btnAttach.textContent='Anexar medio'; btnAttach.title='Anexar medio (URL o subir)';
        // preset select (populated from server-side whitelist exposed to JS)
        var sel = document.createElement('select'); sel.className = 'form-select form-select-sm tpl-preset-select'; sel.style.display='inline-block'; sel.style.width='auto'; sel.style.marginLeft='8px';
        var emptyOpt = document.createElement('option'); emptyOpt.value=''; emptyOpt.text='Estilo...'; sel.appendChild(emptyOpt);
        try{
          var allowed = window.__XLERION_ALLOWED_CLASSES || [];
          ['bg-primary','bg-light','p-3','p-5','text-center'].forEach(function(k){ if (allowed.indexOf(k) !== -1){ var o=document.createElement('option'); o.value=k; o.text = k; sel.appendChild(o); } });
        }catch(e){}
  ctr.appendChild(btnUp); ctr.appendChild(btnDown); ctr.appendChild(btnGrab); ctr.appendChild(btnEdit); ctr.appendChild(btnDel); ctr.appendChild(sel);
  // attach button near actions
  ctr.appendChild(btnAttach);
        block.appendChild(ctr);
      } else {
        // ensure move buttons exist for older blocks
        if (!ctr.querySelector('.tpl-move-up')){
          var btnUp = document.createElement('button'); btnUp.setAttribute('type','button'); btnUp.className='btn btn-sm btn-outline-light tpl-move-up'; btnUp.title='Subir'; btnUp.textContent='↑'; ctr.insertBefore(btnUp, ctr.firstChild);
        }
        if (!ctr.querySelector('.tpl-move-down')){
          var btnDown = document.createElement('button'); btnDown.setAttribute('type','button'); btnDown.className='btn btn-sm btn-outline-light tpl-move-down'; btnDown.title='Bajar'; btnDown.textContent='↓'; ctr.insertBefore(btnDown, ctr.firstChild.nextSibling);
        }
        if (!ctr.querySelector('.tpl-grab')){
          var btnGrab = document.createElement('button'); btnGrab.setAttribute('type','button'); btnGrab.className='btn btn-sm btn-outline-light tpl-grab'; btnGrab.title='Arrastrar'; btnGrab.textContent='↕'; ctr.insertBefore(btnGrab, ctr.firstChild.nextSibling.nextSibling);
        }
        if (!ctr.querySelector('.tpl-attach')){
          var btnAttach = document.createElement('button'); btnAttach.setAttribute('type','button'); btnAttach.className='btn btn-sm btn-outline-primary tpl-attach'; btnAttach.textContent='Anexar medio'; btnAttach.title='Anexar medio (URL o subir)'; ctr.appendChild(btnAttach);
        }
  // add classes to existing edit/remove if missing
  // Prefer identifying by known style classes or button text to avoid mis-labeling move buttons
  var be = ctr.querySelector('button.tpl-edit, button.btn-outline-secondary');
  if (be && !be.classList.contains('tpl-edit')) be.classList.add('tpl-edit');
  var bd = ctr.querySelector('button.tpl-remove, button.btn-outline-danger');
  if (bd && !bd.classList.contains('tpl-remove')) bd.classList.add('tpl-remove');
      }
      // wire handlers
      wireBlockControls(block);
  // wire attach button
  try{ var attachBtn = block.querySelector('.tpl-attach'); if (attachBtn && !attachBtn._tpl_wired_attach){ attachBtn._tpl_wired_attach = true; attachBtn.addEventListener('click', function(ev){ ev.stopPropagation(); ev.preventDefault(); try{ if (window.TemplatesEditor && typeof window.TemplatesEditor.openMediaModalForBlock === 'function'){ window.TemplatesEditor.openMediaModalForBlock(block); } }catch(e){} }); } }catch(e){}
      // wire preset select change
      try{
        var presetSel = ctr.querySelector('.tpl-preset-select');
        if (presetSel && !presetSel._tpl_wired){ presetSel._tpl_wired = true; presetSel.addEventListener('change', function(){ try{
            var val = presetSel.value; var content = block.querySelector('.tpl-content'); if (!content) return;
            var allowed = window.__XLERION_ALLOWED_CLASSES || [];
            var before = (content.className||'').split(/\s+/).filter(Boolean);
            var keep = before.filter(function(c){ return allowed.indexOf(c) === -1; });
            if (val){ keep.push(val); }
            content.className = keep.join(' ');
            syncCanvasToTextarea(); }catch(e){}});
        }
      }catch(e){}
    }

    function wireBlockControls(block){ try{
      var ctr = block.querySelector('.tpl-controls'); if (!ctr) return;
  var btnEdit = ctr.querySelector('.tpl-edit'); var btnDel = ctr.querySelector('.tpl-remove'); var btnUp = ctr.querySelector('.tpl-move-up'); var btnDown = ctr.querySelector('.tpl-move-down'); var btnGrab = ctr.querySelector('.tpl-grab');
  if (btnEdit && !btnEdit._tpl_wired){ btnEdit._tpl_wired = true; btnEdit.setAttribute('type','button'); btnEdit.addEventListener('click', function(ev){ ev.stopPropagation(); ev.preventDefault(); // toggle edit mode
  if (block.classList.contains('editing')){ block.classList.remove('editing'); var content = block.querySelector('.tpl-content'); if (content && content._editor){ content.innerHTML = content._editor.value; try{ content._editor.parentNode.removeChild(content._editor); delete content._editor; }catch(e){} try{ ensureControls(block); }catch(e){} syncCanvasToTextarea(); } return; }
  // If block contains an image, video iframe or resource, open media modal instead of raw textarea
  var content = block.querySelector('.tpl-content'); var html = content ? (content.innerHTML || '') : '';
  var isImage = /<img\b/i.test(html) || content && content.querySelector && content.querySelector('img');
  var isVideo = /<iframe\b|<video\b/i.test(html) || content && content.querySelector && content.querySelector('iframe,video');
  var isResource = /resource-thumb|resource-card/i.test(html);
  if (isImage || isVideo || isResource){ openMediaModalForBlock(block); return; }
  // fallback to textarea editor
  block.classList.add('editing'); var ta = document.createElement('textarea'); ta.style.width='100%'; ta.style.minHeight='120px'; ta.value = html; content.innerHTML=''; content._editor = ta; content.appendChild(ta); ta.focus(); }); }
  if (btnDel && !btnDel._tpl_wired){ btnDel._tpl_wired = true; btnDel.setAttribute('type','button'); btnDel.addEventListener('click', function(ev){ ev.stopPropagation(); ev.preventDefault(); if (!confirm('Eliminar bloque?')) return; try{ block.parentNode.removeChild(block); syncCanvasToTextarea(); }catch(e){} }); }
  if (btnUp && !btnUp._tpl_wired){ btnUp._tpl_wired = true; btnUp.addEventListener('click', function(ev){ ev.stopPropagation(); ev.preventDefault(); try{ var prev = block.previousElementSibling; while(prev && !prev.classList.contains('tpl-block')) prev = prev.previousElementSibling; if (prev){ block.parentNode.insertBefore(block, prev); syncCanvasToTextarea(); block.scrollIntoView({behavior:'smooth', block:'nearest'}); } }catch(e){} }); }
  if (btnDown && !btnDown._tpl_wired){ btnDown._tpl_wired = true; btnDown.addEventListener('click', function(ev){ ev.stopPropagation(); ev.preventDefault(); try{ var next = block.nextElementSibling; while(next && !next.classList.contains('tpl-block')) next = next.nextElementSibling; if (next){ block.parentNode.insertBefore(next, block); syncCanvasToTextarea(); block.scrollIntoView({behavior:'smooth', block:'nearest'}); } }catch(e){} }); }
  if (btnGrab && !btnGrab._tpl_wired){ btnGrab._tpl_wired = true; btnGrab.addEventListener('pointerdown', function(ev){ ev.stopPropagation(); ev.preventDefault(); try{ // find handle inside block and synthesize pointerdown with coordinates
    var handle = block.querySelector('.tpl-handle');
    if (handle){
      var rect = handle.getBoundingClientRect();
      var pd = new PointerEvent('pointerdown', { bubbles: true, cancelable: true, clientX: rect.left + rect.width/2, clientY: rect.top + rect.height/2, pointerId: ev.pointerId || 1, pointerType: ev.pointerType || 'mouse' });
      handle.dispatchEvent(pd);
    } else {
      // fallback: call startPointerDrag directly if available
      if (typeof startPointerDrag === 'function') startPointerDrag(ev);
    }
    }catch(e){} }); }
    }catch(e){} }
    function adjustCanvasHeight(){ try{ var c = getCanvas(); if (!c) return; c.style.minHeight = '420px'; }catch(e){} }
    // extend snippets with header/footer/section/resource/image
    snippets['header'] = '<header class="site-header-sample p-3">\n  <div class="container">\n    <h2>Header</h2>\n    <p>Subtítulo del header</p>\n  </div>\n</header>\n';
    snippets['footer'] = '<footer class="site-footer-sample p-3">\n  <div class="container">\n    <p>Pie de página / Footer</p>\n  </div>\n</footer>\n';
    snippets['section'] = '<section class="section-sample p-3">\n  <div class="container">\n    <h3>Sección</h3>\n    <p>Contenido de sección</p>\n  </div>\n</section>\n';
    snippets['resource'] = '<div class="resource-card p-2">\n  <img class="resource-thumb" src="/images/placeholder.png" alt="Recurso"/>\n  <div class="resource-meta">\n    <h5>Recurso</h5>\n    <p>Descripción corta</p>\n  </div>\n</div>\n';
    snippets['image'] = '<img class="placeholder-img" src="data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'600\' height=\'200\'><rect width=\'100%\' height=\'100%\' fill=\'%23e9eef6\' /><text x=\'50%\' y=\'50%\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%2302132b\'>Imagen placeholder</text></svg>' + '" alt="Imagen" />\n';
  // video placeholder: will be replaced by user-provided URL/YouTube iframe when inserted
  snippets['video'] = '<div class="tpl-video-wrapper" style="max-width:100%;"><div style="position:relative;padding-top:56.25%;background:#000;color:#fff;display:flex;align-items:center;justify-content:center;">Video placeholder</div></div>\n';

    function insertHtmlAtCursor(el, html){
      // If the editor is a textarea, insert HTML at caret position inside the textarea
      if (el && el.tagName === 'TEXTAREA'){
        var start = el.selectionStart || 0;
        var end = el.selectionEnd || 0;
        var val = el.value || '';
        el.value = val.substring(0,start) + html + val.substring(end);
        // set caret after inserted HTML
    var pos = start + html.length;
    // update live preview if present
    try{ if (window.TemplatesEditor && window.TemplatesEditor.updateLivePreview) window.TemplatesEditor.updateLivePreview(); }catch(e){}
        try{ el.setSelectionRange(pos,pos); el.focus(); }catch(e){}
        return;
      }
      // Fallback: if contentEditable exists use the older method
      if (el && el.isContentEditable){
        el.focus();
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) {
          el.insertAdjacentHTML('beforeend', html);
          return;
        }
        var range = sel.getRangeAt(0);
        var node = range.commonAncestorContainer;
        if (!el.contains(node)) { el.insertAdjacentHTML('beforeend', html); return; }
        var frag = document.createDocumentFragment();
        var tmp = document.createElement('div'); tmp.innerHTML = html;
        var _insertedWrappers = [];
        while(tmp.firstChild){
          var node = tmp.firstChild; tmp.removeChild(node);
          var wrapper = document.createElement('div'); wrapper.className = 'tpl-block'; wrapper.setAttribute('draggable','true');
          wrapper.style.display = 'block'; wrapper.style.width = '100%'; wrapper.style.boxSizing = 'border-box';
          var handle = document.createElement('div'); handle.className = 'tpl-handle'; handle.setAttribute('aria-hidden','true');
          wrapper.appendChild(handle); wrapper.appendChild(node);
          try{ if (node.nodeType === 1 && /\b(card|row|col|container|container-fluid)\b/.test(node.className||'')) wrapper.classList.add('tpl-neutral'); }catch(e){}
          frag.appendChild(wrapper); _insertedWrappers.push(wrapper);
        }
        range.deleteContents(); range.insertNode(frag); range.collapse(false); sel.removeAllRanges(); sel.addRange(range);
        try{ wireBlocks(el); if (_insertedWrappers.length){ _insertedWrappers.forEach(function(w){ w.classList.add('just-inserted'); try{ w.scrollIntoView({behavior:'smooth', block:'nearest'}); }catch(e){} }); setTimeout(function(){ try{ wireBlocks(el); }catch(e){} _insertedWrappers.forEach(function(w){ w.classList.remove('just-inserted'); }); }, 120); } }catch(e){}
      }
    }

      // --- Video helpers ---
      function normalizeYouTubeEmbed(url){ try{
        if (!url) return null;
        url = url.trim();
        var m = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([A-Za-z0-9_\-]{6,})/i);
        if (m && m[1]) return 'https://www.youtube.com/embed/' + m[1];
        // already an embed or direct iframe URL?
        if (/^https?:\/\//.test(url) && url.indexOf('youtube') !== -1 && url.indexOf('embed') !== -1) return url;
        return null;
      }catch(e){ return null; } }

      function insertVideoByUrl(url, columnEl){ try{
        if (!url) return;
        var yt = normalizeYouTubeEmbed(url);
        var html = '';
        // detect data: mime
        var isData = false, dataMime = null;
        try{ if (typeof url === 'string' && url.indexOf('data:') === 0){ isData = true; var m = url.match(/^data:([^;,]+)/); if (m && m[1]) dataMime = m[1]; } }catch(e){}
        if (yt){
          html = '<div class="tpl-video-wrapper" style="position:relative;padding-top:56.25%;overflow:hidden;"><iframe src="'+yt+'" frameborder="0" allowfullscreen style="position:absolute;left:0;top:0;width:100%;height:100%;border:0"></iframe></div>';
        } else if (isData && dataMime && dataMime.indexOf('image/') === 0){
          html = '<img class="placeholder-img" src="'+url+'" alt="Imagen" />';
        } else if (isData && dataMime && dataMime.indexOf('video/') === 0){
          html = '<video controls style="width:100%;height:auto;max-width:100%;" src="'+url+'">Video no soportado</video>';
        } else if (/\.(mp4|webm|ogg)(?:\?|$)/i.test(url) || url.indexOf('/uploads/') !== -1 || url.indexOf('blob:') === 0){
          html = '<video controls style="width:100%;height:auto;max-width:100%;" src="'+url+'">Video no soportado</video>';
        } else {
          if (/^https?:\/\//i.test(url)) html = '<iframe src="'+url+'" frameborder="0" style="width:100%;height:360px;border:0"></iframe>'; else html = '<p>URL inválida</p>';
        }
        var canvas = getCanvas(); if (canvas){ if (columnEl) addHtmlIntoColumn(html, columnEl); else addBlockToCanvas(html); } else { var editor = document.getElementById('templatesEditor'); insertHtmlAtCursor(editor, html); }
      }catch(e){ console.error('insertVideoByUrl error', e); }
      }

    // Insert into the live iframe editor at caret if focused inside
    function insertHtmlInIframeAtCursor(html){
      try{
        var live = document.getElementById('templatesEditorLiveIframe'); if (!live) return false;
        var doc = live.contentDocument || live.contentWindow.document; if (!doc) return false;
        var sel = doc.getSelection && doc.getSelection(); if (!sel || sel.rangeCount === 0) { doc.body.insertAdjacentHTML('beforeend', html); syncIframeToTextarea(doc); return true; }
        var range = sel.getRangeAt(0);
        // Only allow insertion within body
        if (!doc.body.contains(range.commonAncestorContainer)) { doc.body.insertAdjacentHTML('beforeend', html); syncIframeToTextarea(doc); return true; }
        range.deleteContents();
        var tmp = doc.createElement('div'); tmp.innerHTML = html;
        var frag = doc.createDocumentFragment(); while(tmp.firstChild){ var n = tmp.firstChild; tmp.removeChild(n); frag.appendChild(n); }
        range.insertNode(frag); range.collapse(false); sel.removeAllRanges(); sel.addRange(range);
        syncIframeToTextarea(doc);
        return true;
      }catch(e){ return false; }
    }

    function wireToolbar(){
      // Bind toolbar behavior to any button that declares a data-action or data-insert
      // (some utility buttons live outside the visual toolbar in the view markup)
      var tb = document.querySelectorAll('button[data-action], button[data-insert]');
      tb.forEach(function(b){
        var ins = b.getAttribute('data-insert');
        var act = b.getAttribute('data-action');
        b.addEventListener('click', function(){
          var editor = byId('templatesEditor');
          if (ins && snippets[ins]){
            var canvas = getCanvas();
            if (canvas){ addBlockToCanvas(snippets[ins]); }
            else {
              // legacy: iframe/contenteditable/textarea
              var live = document.getElementById('templatesEditorLiveIframe');
              var did = false;
              try{ var doc = live && (live.contentDocument || live.contentWindow.document); if (doc && doc.activeElement && (doc.activeElement === doc.body || doc.body.contains(doc.activeElement))) { did = insertHtmlInIframeAtCursor(snippets[ins]); } }catch(e){}
              if (!did) insertHtmlAtCursor(editor, snippets[ins]);
            }
            return;
          }
          if (act === 'clear'){
            if (confirm('Limpiar el contenido del editor?')){
              var canvas = getCanvas();
              if (canvas){ canvas.innerHTML=''; syncCanvasToTextarea(); }
              else if (editor.tagName === 'TEXTAREA'){ editor.value = ''; try{ if (window.TemplatesEditor && window.TemplatesEditor.updateLivePreview) window.TemplatesEditor.updateLivePreview(); }catch(e){} }
              else { editor.innerHTML = ''; }
            }
            return;
          }
            if (act === 'toggle-outline'){
              try{
                // Toggle outline on both canvas and editor if present
                var c = document.getElementById('templatesCanvas');
                if (c) c.classList.toggle('templates-editor-outline');
                try{ if (editor) editor.classList.toggle('templates-editor-outline'); }catch(e){}
              }catch(e){}
              return;
            }
            if (act === 'toggle-preview'){
              try{
                var pre = document.getElementById('templatesEditorHtmlPane');
                if (!pre) return;
                // populate content: prefer serialized canvas if exists
                var canvas = getCanvas();
                var txt = '';
                if (canvas && canvas.children && canvas.children.length){ txt = serializeCanvas(); }
                else { txt = (editor.tagName === 'TEXTAREA') ? (editor.value||'') : (editor.innerHTML||''); }
                pre.textContent = txt.replace(/\s+$/,'');
                pre.style.display = (pre.style.display === 'block') ? 'none' : 'block';
              }catch(e){}
              return;
            }
        });
      });
      // populate load select if available templates embedded
      try{
        var list = (window.__XLERION_AVAILABLE_TEMPLATES || []).slice();
        // Merge section templates
        try{
          var existing = new Set(list.map(function(t){ return String(t.id); }));
          __SECTION_TEMPLATES.forEach(function(t){ if (!existing.has(String(t.id))) list.push(t); });
        }catch(e){}
        var sel = document.getElementById('templatesLoadSelect');
        var btn = document.getElementById('templatesLoadBtn');
        if (sel && list && list.length){
          list.forEach(function(t){
            var opt = document.createElement('option'); opt.value = t.id; opt.text = t.name || ('Plantilla ' + t.id);
            sel.appendChild(opt);
          });
        }
        if (btn && sel){
          btn.addEventListener('click', function(){
            var id = sel.value; if (!id) return alert('Seleccione una plantilla');
            var tmpl = (list || []).find(function(x){ return String(x.id) === String(id); });
            if (!tmpl) return alert('Plantilla no encontrada');
            if (!confirm('¿Cargar plantilla "' + (tmpl.name || '') + '"? Esto reemplazará el contenido actual.')) return;
            // strip script tags defensively and insert
            var safe = (tmpl.html || '').replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, '');
            var editor = document.getElementById('templatesEditor'); if (!editor) return;
            // Set name and description fields if present
            try{ var nameInput = document.querySelector('input[name="name"]'); if (nameInput && tmpl.name) nameInput.value = tmpl.name; }catch(e){}
            try{ var descInput = document.querySelector('input[name="description"]'); if (descInput && typeof tmpl.description === 'string') descInput.value = tmpl.description; }catch(e){}
            if (editor.tagName === 'TEXTAREA'){
              // decode before inserting if needed
              editor.value = decodeEntities(safe);
              try{
                var canvas = getCanvas();
                if (canvas){ canvas.innerHTML=''; loadCanvasFromHtml(editor.value); syncCanvasToTextarea(); }
                if (window.TemplatesEditor && window.TemplatesEditor.updateLivePreview) window.TemplatesEditor.updateLivePreview();
              }catch(e){}
            } else {
              editor.innerHTML = safe;
            }
          });
        }
      }catch(e){ /* ignore */ }
    }

    function syncBeforeSubmit(e){
      // editor may now be a textarea containing the HTML directly
      var ta = document.getElementById('templatesEditor');
      if (!ta) return true;
  // If there is a canvas, serialize its DOM order to ensure ordering persistence
  var canvas = getCanvas();
  var html = '';
  if (canvas && canvas.children && canvas.children.length){ html = serializeCanvas(); }
  else { html = ta.value.replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, ''); }
      // set hidden input if present
      var hidden = document.getElementById('templatesEditorData');
      if (hidden) hidden.value = html;
      // ensure the textarea value is the cleaned HTML
      ta.value = html;
      return true;
    }

  function wirePalette(){
      try{
        console.debug('[TemplatesEditor] wirePalette init');
        document.querySelectorAll('.palette-item').forEach(function(p){
          p.addEventListener('click', function(){
            console.debug('[TemplatesEditor] palette click', p.getAttribute('data-insert'));
            var key = p.getAttribute('data-insert');
            var editor = byId('templatesEditor');
            var canvas = getCanvas();
            if (key && key === 'video'){
              // prompt for URL (local or YouTube). If a column was last clicked, insert into it.
              var url = prompt('Insertar video - pega la URL (mp4 o YouTube) o deja vacío para usar el placeholder:');
              if (url === null) return; // cancelled
              var col = __templates_last_clicked_column || null; clearLastClickedColumn();
              insertVideoByUrl(url.trim(), col);
              try{ if (window.TemplatesEditor && window.TemplatesEditor.updateLivePreview) window.TemplatesEditor.updateLivePreview(); }catch(e){}
              return;
            }
            if (key && snippets[key]){
        // if a column was last clicked, insert into that column; otherwise add to canvas
        if (canvas && __templates_last_clicked_column){ addHtmlIntoColumn(snippets[key], __templates_last_clicked_column); clearLastClickedColumn(); }
        else if (canvas) { addBlockToCanvas(snippets[key]); }
              else {
                var live = document.getElementById('templatesEditorLiveIframe');
                var did = false;
                try{ var doc = live && (live.contentDocument || live.contentWindow.document); if (doc && doc.activeElement && (doc.activeElement === doc.body || doc.body.contains(doc.activeElement))) { did = insertHtmlInIframeAtCursor(snippets[key]); } }catch(e){}
                if (!did) insertHtmlAtCursor(editor, snippets[key]);
              }
            }
            try{ if (window.TemplatesEditor && window.TemplatesEditor.updateLivePreview) window.TemplatesEditor.updateLivePreview(); }catch(e){}
          });
          // ensure item is draggable in the DOM
          try{ p.setAttribute('draggable','true'); }catch(e){}
          p.addEventListener('dragstart', function(ev){
            console.debug('[TemplatesEditor] palette dragstart', p.getAttribute('data-insert'));
            try{ ev.dataTransfer.setData('application/x-templates-palette', p.getAttribute('data-insert')); }catch(e){ ev.dataTransfer.setData('text/plain', p.getAttribute('data-insert')); }
            try{ ev.dataTransfer.setDragImage(p, 10, 10); }catch(e){}
            try{ ev.dataTransfer.effectAllowed = 'copy'; }catch(e){}
            // fallback marker for environments that restrict custom dataTransfer types
            try{ window.__templates_current_drag = p.getAttribute('data-insert'); }catch(e){ window.__templates_current_drag = null; }
          });
          p.addEventListener('dragend', function(){ try{ window.__templates_current_drag = null; }catch(e){} });
        });
  var editor = byId('templatesEditor');
  // wire canvas drop too
  try{ wireCanvasDnD(); }catch(e){}
      }catch(e){ /* ignore */ }
    }

    // Wire .tpl-block elements to be draggable and support reordering inside the editor
    function wireBlocks(container){
      var editor = document.getElementById('templatesEditor');
      if (!editor) return;
      var blocks = editor.querySelectorAll('.tpl-block');
      blocks.forEach(function(b){
        if (b._tplWired) return; b._tplWired = true;
        b.addEventListener('dragstart', function(ev){
          ev.stopPropagation();
          ev.dataTransfer.setData('text/x-tpl-block', '1');
          window.__templates_dragging = b;
          b.classList.add('is-dragging');
          console.debug('[TemplatesEditor] block dragstart');
        });
        b.addEventListener('dragend', function(ev){
          window.__templates_dragging = null;
          b.classList.remove('is-dragging');
          console.debug('[TemplatesEditor] block dragend');
        });
        // pointer-based handle drag (more reliable immediately after insertion)
        try{
          var handle = b.querySelector('.tpl-handle');
          if (handle){
            handle.style.touchAction = 'none';
            handle.addEventListener('pointerdown', function(startEvent){
              startEvent.preventDefault(); startEvent.stopPropagation();
              var editor = document.getElementById('templatesEditor');
              var block = b;
              var rect = block.getBoundingClientRect();
              var offsetX = startEvent.clientX - rect.left;
              var offsetY = startEvent.clientY - rect.top;
              // create ghost
              var ghost = block.cloneNode(true);
              ghost.classList.add('tpl-ghost');
              ghost.style.position = 'fixed';
              ghost.style.left = (rect.left) + 'px';
              ghost.style.top = (rect.top) + 'px';
              ghost.style.width = rect.width + 'px';
              ghost.style.opacity = '0.9';
              ghost.style.pointerEvents = 'none';
              ghost.style.zIndex = 9999;
              document.body.appendChild(ghost);
              block.classList.add('is-dragging');
              var currentTarget = null;
              function onMove(ev){
                ghost.style.left = (ev.clientX - offsetX) + 'px';
                ghost.style.top = (ev.clientY - offsetY) + 'px';
                var after = getDropTarget(ev.clientX, ev.clientY);
                editor.querySelectorAll('.tpl-block').forEach(function(x){ x.classList.remove('tpl-drop-before'); });
                if (after && after !== block) { after.classList.add('tpl-drop-before'); currentTarget = after; } else { currentTarget = null; }
              }
              function onUp(ev){
                window.removeEventListener('pointermove', onMove);
                window.removeEventListener('pointerup', onUp);
                block.classList.remove('is-dragging');
                if (ghost.parentNode) ghost.parentNode.removeChild(ghost);
                editor.querySelectorAll('.tpl-block').forEach(function(x){ x.classList.remove('tpl-drop-before'); });
                try{
                  if (currentTarget && currentTarget.parentNode === editor){
                    editor.insertBefore(block, currentTarget);
                  } else {
                    editor.appendChild(block);
                  }
                }catch(e){ }
              }
              window.addEventListener('pointermove', onMove);
              window.addEventListener('pointerup', onUp);
            });
          }
        }catch(e){ /* ignore pointer handle errors */ }
      });

      if (!editor._tplReorderWired){
        editor._tplReorderWired = true;
        editor.addEventListener('dragover', function(ev){
          ev.preventDefault();
          var after = getDropTarget(ev.clientX, ev.clientY);
          editor.querySelectorAll('.tpl-block').forEach(function(x){ x.classList.remove('tpl-drop-before'); });
          if (after) after.classList.add('tpl-drop-before');
        });
        editor.addEventListener('dragleave', function(ev){
          editor.querySelectorAll('.tpl-block').forEach(function(x){ x.classList.remove('tpl-drop-before'); });
        });
        editor.addEventListener('drop', function(ev){
          ev.preventDefault();
          var dragging = window.__templates_dragging;
          var after = getDropTarget(ev.clientX, ev.clientY);
          if (dragging){
            if (after && after.parentNode === editor){
              editor.insertBefore(dragging, after);
            } else {
              editor.appendChild(dragging);
            }
          }
          editor.querySelectorAll('.tpl-block').forEach(function(x){ x.classList.remove('tpl-drop-before'); });
        });
      }
    }

    function getDropTarget(x,y){
      var editor = document.getElementById('templatesEditor'); if(!editor) return null;
      var blocks = Array.from(editor.querySelectorAll('.tpl-block'));
      for(var i=0;i<blocks.length;i++){
        var r = blocks[i].getBoundingClientRect();
        if (y < r.top + r.height/2) return blocks[i];
      }
      return null;
    }

    function insertBlockAtPosition(editor, html, x, y){
      // create wrapped block(s) same as insertHtmlAtCursor, then insert before computed target
      var tmp = document.createElement('div'); tmp.innerHTML = html;
      var after = getDropTarget(x,y);
      var created = [];
      while(tmp.firstChild){
        var node = tmp.firstChild; tmp.removeChild(node);
        var wrapper = document.createElement('div');
        wrapper.className = 'tpl-block';
        wrapper.setAttribute('draggable','true');
        wrapper.style.display='block'; wrapper.style.width='100%'; wrapper.style.boxSizing='border-box';
        var handle = document.createElement('div'); handle.className='tpl-handle'; handle.setAttribute('aria-hidden','true');
        wrapper.appendChild(handle); wrapper.appendChild(node);
        // neutral markers
        try{ if (node.nodeType===1 && /\b(card|row|col|container|container-fluid)\b/.test(node.className||'')) wrapper.classList.add('tpl-neutral'); }catch(e){}
        if (after){ editor.insertBefore(wrapper, after); } else { editor.appendChild(wrapper); }
        created.push(wrapper);
      }
      wireBlocks(editor);
      setTimeout(function(){ created.forEach(function(w){ try{ w.scrollIntoView({behavior:'smooth', block:'nearest'}); }catch(e){} }); }, 50);
    }

    function init(){
  document.addEventListener('DOMContentLoaded', function(){ console.debug('[TemplatesEditor] init'); wireToolbar(); wirePalette();
      // wire preview button
      try{ var rp = document.getElementById('templatesRealPreviewBtn'); if (rp) rp.addEventListener('click', openRealPreview); }catch(e){}
      // live preview wiring
    try{ var ta = document.getElementById('templatesEditor'); var canvas = getCanvas(); var live = document.getElementById('templatesEditorLiveIframe');
        if (ta && canvas){
          try{ if (ta.value && ta.value.indexOf('&lt;') !== -1){ ta.value = decodeEntities(ta.value); } }catch(e){}
          loadCanvasFromHtml(ta.value || ''); ensureCanvasStyles(); wireCanvasDnD(); syncCanvasToTextarea();
          // toggle buttons control canvas vs HTML pane
          try{
            var btnV = document.getElementById('btnViewVisual');
            var btnH = document.getElementById('btnViewHtml');
            var htmlPane = document.getElementById('templatesEditorHtmlPane');
            function showVisual(){ if (canvas) canvas.style.display='block'; if (htmlPane) htmlPane.style.display='none'; if (btnV){ btnV.classList.add('btn-primary'); btnV.classList.remove('btn-outline-light'); } if (btnH){ btnH.classList.remove('btn-primary'); btnH.classList.add('btn-outline-light'); } }
            function showHtml(){ updateHtmlPaneFromCanvas(); if (canvas) canvas.style.display='none'; if (htmlPane) htmlPane.style.display='block'; if (btnH){ btnH.classList.add('btn-primary'); btnH.classList.remove('btn-outline-light'); } if (btnV){ btnV.classList.remove('btn-primary'); btnV.classList.add('btn-outline-light'); } }
            if (btnV) btnV.addEventListener('click', function(){ showVisual(); });
            if (btnH) btnH.addEventListener('click', function(){ showHtml(); });
            showVisual();
          }catch(e){}
          // expose preview updater
          window.TemplatesEditor = window.TemplatesEditor || {}; window.TemplatesEditor.updateLivePreview = updateHtmlPaneFromCanvas;
        } else if (ta && live){
          // if textarea contains escaped HTML entities, decode them so preview renders correctly
          try{ if (ta.value && ta.value.indexOf('&lt;') !== -1){ ta.value = decodeEntities(ta.value); } }catch(e){}
          var deb = null;
    function renderLive(){ try{ var doc = live.contentDocument || live.contentWindow.document; doc.open(); var html = ta.value || ''; html = html.replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi,''); var css = '';
      css += '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">';
      css += '<link rel="stylesheet" href="/styles.css">';
      css += '<link rel="stylesheet" href="/xlerion_cmr/public/styles.css">';
      css += '<link rel="stylesheet" href="/css/templates-editor-fallback.css">';
      css += '<link rel="stylesheet" href="/xlerion_cmr/public/css/templates-editor-fallback.css">';
      doc.write('<!doctype html><html><head><meta charset="utf-8">'+css+'<style>body{padding:12px} img{max-width:100%;height:auto;}</style></head><body>'+html+'</body></html>'); doc.close(); try{ var isDark = document.documentElement.classList.contains('theme-dark'); doc.documentElement.classList.toggle('theme-dark', isDark); doc.documentElement.classList.toggle('theme-light', !isDark); enableIframeEditing(doc); }catch(e){} }catch(e){}
          }
          ta.addEventListener('input', function(){ clearTimeout(deb); deb = setTimeout(renderLive, 250); });
          // update on first load
          renderLive();
          // also expose a quick update for palette inserts
      window.TemplatesEditor = window.TemplatesEditor || {}; window.TemplatesEditor.updateLivePreview = renderLive;
          // wire viewer toggle buttons
          try{
            var btnV = document.getElementById('btnViewVisual');
            var btnH = document.getElementById('btnViewHtml');
            var htmlPane = document.getElementById('templatesEditorHtmlPane');
            function showVisual(){ if (!live) return; live.style.display='block'; if (htmlPane) htmlPane.style.display='none'; if (btnV){ btnV.classList.add('btn-primary'); btnV.classList.remove('btn-outline-light'); } if (btnH){ btnH.classList.remove('btn-primary'); btnH.classList.add('btn-outline-light'); } }
            function showHtml(){ try{ var doc = live.contentDocument || live.contentWindow.document; if (htmlPane){ htmlPane.textContent = (ta.value||''); } }catch(e){ if (htmlPane) htmlPane.textContent = (ta.value||''); } if (live) live.style.display='none'; if (htmlPane) htmlPane.style.display='block'; if (btnH){ btnH.classList.add('btn-primary'); btnH.classList.remove('btn-outline-light'); } if (btnV){ btnV.classList.remove('btn-primary'); btnV.classList.add('btn-outline-light'); } }
            if (btnV) btnV.addEventListener('click', function(){ showVisual(); });
            if (btnH) btnH.addEventListener('click', function(){ showHtml(); });
            // default to visual on load
            showVisual();
          }catch(e){}
        }
      }catch(e){}
  // Wire new editor-level buttons: preview in window & delete template
  try{ var openBtn = document.getElementById('btnOpenPreviewWindow'); if (openBtn){ openBtn.addEventListener('click', openPreviewWindow); } }catch(e){ }
  try{ var delBtn = document.getElementById('btnDeleteTemplate'); if (delBtn){ delBtn.addEventListener('click', deleteCurrentTemplate); } }catch(e){ }

  // wrap existing nodes so they are reorderable (deprecated when using textarea)
  try{ wrapExistingNodes(); }catch(e){}
    });
    }

    function enableIframeEditing(doc){
      try{
        doc.body.setAttribute('contenteditable','true');
        // basic focus style to indicate editable area
        var st = doc.createElement('style'); st.textContent = 'body:focus{outline:none} *[contenteditable]:focus{outline: 2px dashed rgba(0,123,255,.35);} img{max-width:100%;height:auto;}'; doc.head.appendChild(st);
        // sync textarea when editing inside iframe
        var sync = function(){ syncIframeToTextarea(doc); };
        doc.body.addEventListener('input', sync);
        doc.body.addEventListener('keyup', sync);
        // clicks inside should focus iframe so inserts go to caret
      }catch(e){}
    }

    function syncIframeToTextarea(doc){ try{ var ta = document.getElementById('templatesEditor'); if (!ta) return; ta.value = (doc.body && doc.body.innerHTML) || ''; }catch(e){} }

    function wrapExistingNodes(){
  var editor = document.getElementById('templatesEditor'); if(!editor || editor.tagName === 'TEXTAREA') return;
      // wrap immediate children that are not already tpl-block
      var children = Array.from(editor.childNodes);
      var changed = false;
  children.forEach(function(ch){
        if (ch.nodeType === 3 && !/^\s*$/.test(ch.textContent)){
          // text node with content -> wrap
          var w = document.createElement('div'); w.className='tpl-block'; w.setAttribute('draggable','true');
          w.style.display='block'; w.style.width='100%'; w.style.boxSizing='border-box';
          var handle = document.createElement('div'); handle.className='tpl-handle'; handle.setAttribute('aria-hidden','true');
          w.appendChild(handle);
          w.appendChild(ch.cloneNode(true));
          editor.replaceChild(w, ch);
          ensureControls(w);
          changed = true;
        } else if (ch.nodeType === 1 && !ch.classList.contains('tpl-block')){
          var w = document.createElement('div'); w.className='tpl-block'; w.setAttribute('draggable','true');
          w.style.display='block'; w.style.width='100%'; w.style.boxSizing='border-box';
          var handle = document.createElement('div'); handle.className='tpl-handle'; handle.setAttribute('aria-hidden','true');
          editor.replaceChild(w, ch);
          w.appendChild(handle);
          w.appendChild(ch);
          ensureControls(w);
          changed = true;
        }
      });
      if (changed) wireBlocks(editor);
    }

    init();

    // Defensive: ensure toggle-outline always wired, independent of earlier toolbar wiring
    try{
      document.querySelectorAll('button[data-action="toggle-outline"]').forEach(function(btn){
        if (btn._tpl_outline_wired) return; btn._tpl_outline_wired = true;
        btn.addEventListener('click', function(ev){ ev.preventDefault(); try{ toggleOutline(); }catch(e){} });
        // initialize ARIA/visual state
        try{ var cur = document.getElementById('templatesCanvas') || document.getElementById('templatesEditor'); var is = cur && cur.classList && cur.classList.contains('templates-editor-outline'); btn.setAttribute('aria-pressed', is ? 'true' : 'false'); if (is) btn.classList.add('active'); }catch(e){}
      });
    }catch(e){}

    // public API for other editors
    function insertHTML(html){
      var editor = byId('templatesEditor') || document.querySelector('[contenteditable]');
      if (!editor) return false;
      insertHtmlAtCursor(editor, html);
      return true;
    }

    function toggleOutline(){
      try{
        var c = document.getElementById('templatesCanvas'); var editor = byId('templatesEditor');
        var isSet = false;
        if (c){ c.classList.toggle('templates-editor-outline'); isSet = c.classList.contains('templates-editor-outline'); }
        if (editor){ editor.classList.toggle('templates-editor-outline'); isSet = isSet || editor.classList.contains('templates-editor-outline'); }
        // update all toggle buttons visual/aria state
        document.querySelectorAll('button[data-action="toggle-outline"]').forEach(function(b){ try{ b.setAttribute('aria-pressed', isSet ? 'true' : 'false'); if (isSet) b.classList.add('active'); else b.classList.remove('active'); }catch(e){} });
        return isSet;
      }catch(e){ return false; }
    }

    function buildSanitizedHtml(){
      var ta = document.getElementById('templatesEditor'); if(!ta) return '';
      // if textarea, return its value
      if (ta.tagName === 'TEXTAREA') return ta.value.replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, '');
      var clone = ta.cloneNode(true);
      clone.querySelectorAll('.tpl-handle').forEach(function(h){ h.parentNode && h.parentNode.removeChild(h); });
      clone.querySelectorAll('.tpl-block').forEach(function(b){
        while(b.firstChild) b.parentNode.insertBefore(b.firstChild, b);
        b.parentNode.removeChild(b);
      });
      var html = clone.innerHTML.replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, '');
      return html;
    }

    // Convert any blob: URLs found in given HTML to data: URIs by fetching the blob and reading as DataURL.
    // Returns a Promise that resolves with the transformed body HTML (not full document).
    function convertBlobUrlsToData(html){
      return new Promise(function(resolve){
        try{
          if (!html || html.indexOf('blob:') === -1) return resolve(html);
          var parser = new DOMParser();
          var d = parser.parseFromString('<!doctype html><html><body>'+html+'</body></html>', 'text/html');
          var elems = Array.prototype.slice.call(d.querySelectorAll('[src]'));
          var blobElems = elems.filter(function(el){ var s = el.getAttribute('src')||''; return s.indexOf('blob:') === 0; });
          if (!blobElems.length) return resolve(html);
          var tasks = blobElems.map(function(el){
            var src = el.getAttribute('src');
            return fetch(src).then(function(res){ return res.blob(); }).then(function(b){
              return new Promise(function(res2, rej){
                try{
                  var fr = new FileReader();
                  fr.onload = function(ev){ try{ el.setAttribute('src', ev.target.result); res2(); }catch(e){ res2(); } };
                  fr.onerror = function(){ res2(); };
                  fr.readAsDataURL(b);
                }catch(e){ res2(); }
              });
            }).catch(function(){ /* ignore individual failures */ });
          });
          Promise.all(tasks).then(function(){ try{ resolve(d.body.innerHTML); }catch(e){ resolve(html); } }).catch(function(){ resolve(html); });
        }catch(e){ resolve(html); }
      });
    }

    function openRealPreview(){
      var html = buildSanitizedHtml();
      var modal = document.getElementById('templatesRealPreviewModal');
      var cleanup = function(){
        try{ window.removeEventListener('keydown', onKey); }catch(e){}
        try{ modal && modal.parentNode && modal.parentNode.removeChild(modal); }catch(e){}
      };
      if (!modal){
        modal = document.createElement('div'); modal.id='templatesRealPreviewModal';
        modal.style.position='fixed'; modal.style.left=0; modal.style.top=0; modal.style.right=0; modal.style.bottom=0; modal.style.background='rgba(0,0,0,0.5)'; modal.style.zIndex=99999; modal.style.display='flex'; modal.style.alignItems='center'; modal.style.justifyContent='center';
        // box with toolbar and resizable content
        var box = document.createElement('div'); box.style.width='80%'; box.style.height='75%'; box.style.maxWidth='1200px'; box.style.background='#fff'; box.style.borderRadius='8px'; box.style.overflow='hidden'; box.style.position='relative'; box.style.display='flex'; box.style.flexDirection='column'; box.style.resize='both'; box.style.minWidth='400px'; box.style.minHeight='200px';
        var toolbar = document.createElement('div'); toolbar.style.display='flex'; toolbar.style.alignItems='center'; toolbar.style.justifyContent='space-between'; toolbar.style.padding='8px 12px'; toolbar.style.borderBottom='1px solid #eee';
        var title = document.createElement('div'); title.textContent = 'Preview real'; title.style.fontWeight='600';
        var tools = document.createElement('div');
        var closeBtn = document.createElement('button'); closeBtn.className='btn btn-sm btn-outline-dark'; closeBtn.textContent='Cerrar'; closeBtn.style.marginRight='8px';
        var fullscreenBtn = document.createElement('button'); fullscreenBtn.className='btn btn-sm btn-outline-secondary'; fullscreenBtn.textContent='Pantalla';
        tools.appendChild(closeBtn); tools.appendChild(fullscreenBtn);
        toolbar.appendChild(title); toolbar.appendChild(tools);

        var iframe = document.createElement('iframe'); iframe.style.width='100%'; iframe.style.height='100%'; iframe.style.border='0'; iframe.style.flex='1 1 auto';
        box.appendChild(toolbar); box.appendChild(iframe); modal.appendChild(box); document.body.appendChild(modal);

        // backdrop click closes
        modal.addEventListener('click', function(ev){ if (ev.target === modal) cleanup(); });
        // close button
        closeBtn.addEventListener('click', cleanup);
        // fullscreen toggle
        var fs = false; fullscreenBtn.addEventListener('click', function(){
          fs = !fs; if (fs){ box.style.width='98%'; box.style.height='96%'; box.style.maxWidth='none'; } else { box.style.width='80%'; box.style.height='75%'; box.style.maxWidth='1200px'; }
        });
        // ESC closes
        var onKey = function(ev){ if (ev.key === 'Escape') cleanup(); };
        window.addEventListener('keydown', onKey);
      }
      var iframe = modal.querySelector('iframe');
      try{
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        // convert blob: URLs to data: URIs before writing into iframe
        convertBlobUrlsToData(html).then(function(processedHtml){
          try{
            doc.open();
            var cssLinks = '';
            // Bootstrap (to support grid/card classes used in snippets)
            cssLinks += '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">';
            // Primary site CSS (document root) + fallback to project-prefixed path
            cssLinks += '<link rel="stylesheet" href="/styles.css">';
            cssLinks += '<link rel="stylesheet" href="/xlerion_cmr/public/styles.css">';
            // Editor fallback CSS (scoped helpers) under both possible bases
            cssLinks += '<link rel="stylesheet" href="/css/templates-editor-fallback.css">';
            cssLinks += '<link rel="stylesheet" href="/xlerion_cmr/public/css/templates-editor-fallback.css">';
            doc.write('<!doctype html><html><head><meta charset="utf-8">'+cssLinks+'<style>body{padding:20px;font-family:Arial,Helvetica,sans-serif} img{max-width:100%;height:auto;}</style></head><body>'+processedHtml+'</body></html>');
            doc.close();
            // Sync theme with parent (dark/light)
            try{ var isDark = document.documentElement.classList.contains('theme-dark'); doc.documentElement.classList.toggle('theme-dark', isDark); doc.documentElement.classList.toggle('theme-light', !isDark); }catch(e){}
            modal.style.display='flex';
          }catch(e){ alert('No se pudo abrir la previsualización: '+(e.message||e)); }
        }).catch(function(){ try{ doc.open(); doc.write('<!doctype html><html><head><meta charset="utf-8"></head><body>'+html+'</body></html>'); doc.close(); modal.style.display='flex'; }catch(e){ alert('No se pudo abrir la previsualización'); } });
    }

    /* Open current sanitized HTML in a separate browser window (new tab) */
    function openPreviewWindow(){
      try{
        var html = buildSanitizedHtml();
  var cssLinks = '';
  // Bootstrap + site CSS + editor fallback (cover both root and project-prefixed paths)
  cssLinks += '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">';
  cssLinks += '<link rel="stylesheet" href="/styles.css">';
  cssLinks += '<link rel="stylesheet" href="/xlerion_cmr/public/styles.css">';
  cssLinks += '<link rel="stylesheet" href="/css/templates-editor-fallback.css">';
  cssLinks += '<link rel="stylesheet" href="/xlerion_cmr/public/css/templates-editor-fallback.css">';
        var win = window.open('', '_blank');
        if (!win) { alert('El navegador bloqueó la apertura de la ventana. Permite ventanas emergentes para continuar.'); return; }
        win.document.open();
  win.document.write('<!doctype html><html><head><meta charset="utf-8">'+cssLinks+'<style>body{padding:18px;font-family:Arial,Helvetica,sans-serif} img{max-width:100%;height:auto;}</style></head><body>'+html+'</body></html>');
  win.document.close();
  // Sync theme to the new window
  try{ var isDark = document.documentElement.classList.contains('theme-dark'); win.document.documentElement.classList.toggle('theme-dark', isDark); win.document.documentElement.classList.toggle('theme-light', !isDark); }catch(e){}
      }catch(e){ console.error('openPreviewWindow error', e); alert('No se pudo abrir la vista previa: '+(e.message||e)); }
    }

    /* Delete current template via API. Expects global variable or server-side rendered ID in #templatesCurrentId */
    async function deleteCurrentTemplate(){
      try{
        var el = document.getElementById('templatesCurrentId');
        var id = el ? el.value : null;
        if (!id){ alert('ID de plantilla no disponible. No se puede borrar.'); return; }
        if (!confirm('¿Borrar la plantilla actual? Esta acción no se puede deshacer.')) return;
        var res = await fetch('/api/templates/'+encodeURIComponent(id), { method: 'DELETE', credentials: 'same-origin' });
        if (!res.ok){
          // try fallback: some servers don't accept DELETE; attempt POST with method override
          if (res.status === 404 || res.status === 405){
            try{
              // First attempt: POST to API endpoint with override header
              var res2 = await fetch('/api/templates/'+encodeURIComponent(id), { method: 'POST', credentials: 'same-origin', headers: {'X-HTTP-Method-Override':'DELETE','Content-Type':'application/json'}, body: JSON.stringify({}) });
              if (res2.ok){ window.location.href = '/admin/templates'; return; }
            }catch(e){ /* continue to next fallback */ }
            try{
              // Second attempt: some servers accept a direct POST to the admin delete handler
              var res3 = await fetch('/admin/templates/delete.php', { method: 'POST', credentials: 'same-origin', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: 'id=' + encodeURIComponent(id) });
              if (res3.ok){ // if JSON, respect it; otherwise redirect
                try{ var j = await res3.json(); if (j && j.ok) { window.location.href = '/admin/templates'; return; } }catch(e){ window.location.href = '/admin/templates'; return; }
              }
            }catch(e){ /* continue to final fallback */ }
            // final fallback: submit a form POST to server-side delete handler (traditional navigation)
            try{
              var form = document.createElement('form'); form.method = 'POST'; form.action = '/admin/templates/delete.php';
              var inp = document.createElement('input'); inp.type='hidden'; inp.name='id'; inp.value = id; form.appendChild(inp);
              document.body.appendChild(form); form.submit(); return;
            }catch(ff){ throw new Error('Error al borrar (fallback chain failed)'); }
          } else {
            var txt = await res.text(); throw new Error('Error al borrar: ' + res.status + ' ' + txt);
          }
        }
        // redirect to templates list
        window.location.href = '/admin/templates';
      }catch(e){ console.error('deleteCurrentTemplate', e); alert('No se pudo borrar la plantilla: '+(e.message||e)); }
    }

    function insertTemplateData(data){
      // if data.html exists, insert it; else attempt to render regions
      if (!data) return false;
      var html = data.html || '';
      if (!html && data.regions){ html = (data.regions.header||'') + (data.regions.content||'') + (data.regions.footer||''); }
      return insertHTML(html);
    }

  // export
  window.TemplatesEditor = window.TemplatesEditor || {};
  window.TemplatesEditor.insertHtmlAtCursor = insertHtmlAtCursor;
  window.TemplatesEditor.syncBeforeSubmit = syncBeforeSubmit;
  window.TemplatesEditor.insertHTML = insertHTML;
  window.TemplatesEditor.toggleOutline = toggleOutline;
  window.TemplatesEditor.insertTemplateData = insertTemplateData;
  window.TemplatesEditor.wireBlocks = wireBlocks;

  return { insertHtmlAtCursor: insertHtmlAtCursor, syncBeforeSubmit: syncBeforeSubmit, wireBlocks: wireBlocks };
  })();
})();

// --- Media modal helpers (outside main closure to allow late binding) ---
(function(){
  function createMediaModal(){
    if (document.getElementById('tplMediaModal')) return document.getElementById('tplMediaModal');
    var modal = document.createElement('div'); modal.id='tplMediaModal'; modal.style.position='fixed'; modal.style.inset='0'; modal.style.display='none'; modal.style.zIndex=12000; modal.style.alignItems='center'; modal.style.justifyContent='center';
    modal.innerHTML = '\n      <div style="position:relative;width:560px;max-width:calc(100% - 24px);background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 12px 40px rgba(0,0,0,.35);">\n        <div style="padding:12px;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between">\n          <div style="font-weight:600">Editar media</div>\n          <div><button id="tplMediaClose" class="btn btn-sm btn-outline-danger">Cerrar</button></div>\n        </div>\n        <div style="padding:12px;display:flex;flex-direction:column;gap:8px">\n          <label style="font-size:.9rem">URL pública (http/https o YouTube)</label>\n          <input id="tplMediaUrl" type="text" style="width:100%;padding:.5rem;border:1px solid #ddd;border-radius:6px">\n          <div style="display:flex;gap:8px;align-items:center">\n            <label class="btn btn-sm btn-outline-light" style="margin:0">Seleccionar archivo local<input id="tplMediaFile" type="file" accept="image/*,video/*" style="display:none"></label>\n            <div id="tplMediaPreview" style="flex:1;min-height:60px;padding:6px;border:1px dashed #eee;border-radius:6px;background:#fafafa;display:flex;align-items:center;justify-content:center;color:#666">Vista previa</div>\n          </div>\n          <div style="display:flex;gap:8px;justify-content:flex-end">\n            <button id="tplMediaCancel" class="btn btn-sm btn-outline-light">Cancelar</button>\n            <button id="tplMediaSave" class="btn btn-sm btn-primary">Guardar</button>\n          </div>\n        </div>\n      </div>\n    ';
    document.body.appendChild(modal);
  // wiring
  modal.querySelector('#tplMediaClose').addEventListener('click', function(){ hideModal(); });
  modal.querySelector('#tplMediaCancel').addEventListener('click', function(){ hideModal(); });
  var file = modal.querySelector('#tplMediaFile'); var urlIn = modal.querySelector('#tplMediaUrl'); var preview = modal.querySelector('#tplMediaPreview');
  // new: URL / Upload buttons
  var btnUrl = modal.querySelector('#tplMediaUseUrl'); var btnUpload = modal.querySelector('#tplMediaUploadPc');
  if (btnUrl){ btnUrl.addEventListener('click', function(){ try{ urlIn.focus(); urlIn.select(); }catch(e){} }); }
  if (btnUpload){ btnUpload.addEventListener('click', function(){ try{ file.click(); }catch(e){} }); }
  file.addEventListener('change', function(ev){ try{ var f = file.files && file.files[0]; if (!f) return; var reader = new FileReader(); reader.onload = function(e){ try{ var src = e.target.result; preview.innerHTML = ''; if (f.type.indexOf('image/')===0){ var img=document.createElement('img'); img.src=src; img.style.maxWidth='100%'; img.style.maxHeight='140px'; preview.appendChild(img); } else { var vid=document.createElement('video'); vid.src=src; vid.controls=true; vid.style.maxWidth='100%'; vid.style.maxHeight='140px'; preview.appendChild(vid); } urlIn.value = src; }catch(e){} }; reader.readAsDataURL(f); }catch(e){}});
  modal.querySelector('#tplMediaSave').addEventListener('click', function(){ try{ var url = urlIn.value && urlIn.value.trim(); var info = modal._tpl_targetBlock; if (!info) { hideModal(); return; } applyMediaToBlock(info.block, info.type, url); hideModal(); }catch(e){ hideModal(); } });
    function hideModal(){ var m=document.getElementById('tplMediaModal'); if (m) m.style.display='none'; m._tpl_targetBlock = null; }
    function showModalFor(block, type, currentUrl){ var m = createMediaModal(); m.style.display='flex'; m._tpl_targetBlock = { block:block, type:type }; var urlIn = m.querySelector('#tplMediaUrl'); var preview = m.querySelector('#tplMediaPreview'); urlIn.value = currentUrl || ''; preview.innerHTML = ''; try{ if (currentUrl){ if (/^data:|\.(mp4|webm|ogg)(\?|$)/i.test(currentUrl) || currentUrl.indexOf('blob:')===0){ if (/^data:image|image\//i.test(currentUrl)){ var img = document.createElement('img'); img.src=currentUrl; img.style.maxWidth='100%'; img.style.maxHeight='140px'; preview.appendChild(img); } else { var v = document.createElement('video'); v.src=currentUrl; v.controls=true; v.style.maxWidth='100%'; v.style.maxHeight='140px'; preview.appendChild(v); } } else if (/youtu/i.test(currentUrl) || /youtube\.com|youtu\.be/.test(currentUrl)){ var iframe = document.createElement('iframe'); iframe.src = (function(u){ var m=u.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([A-Za-z0-9_\-]{6,})/i); if (m && m[1]) return 'https://www.youtube.com/embed/'+m[1]; return u; })(currentUrl); iframe.style.width='100%'; iframe.style.height='140px'; iframe.style.border=0; preview.appendChild(iframe); } else { var link = document.createElement('a'); link.href=currentUrl; link.textContent = currentUrl; link.target='_blank'; preview.appendChild(link); } } }catch(e){}
      }
    // expose
    window.TemplatesEditor = window.TemplatesEditor || {};
    window.TemplatesEditor.openMediaModalForBlock = showModalFor;
    window.TemplatesEditor._tpl_applyMedia = applyMediaToBlock;
    return createMediaModal();
  }

  function openMediaModalForBlock(block){ try{
    var content = block.querySelector && block.querySelector('.tpl-content'); if (!content) return; var html = content.innerHTML || '';
    var type = 'image'; if (/iframe|video/i.test(html)) type='video'; else if (/resource-thumb|resource-card/i.test(html)) type='resource';
    // extract URL if present
    var url = '';
    try{ var m = html.match(/src\s*=\s*\"([^\"]+)\"/i) || html.match(/src\s*=\s*'([^']+)'/i); if (m && m[1]) url = m[1]; }catch(e){}
    var modal = createMediaModal(); modal.style.display='flex'; modal._tpl_targetBlock = { block:block, type:type };
    var urlIn = modal.querySelector('#tplMediaUrl'); var preview = modal.querySelector('#tplMediaPreview'); var file = modal.querySelector('#tplMediaFile'); urlIn.value = url || '';
    preview.innerHTML = '';
    try{ if (url){ if (/^data:image|image\//i.test(url) || /\.(jpg|jpeg|png|gif|svg)(\?|$)/i.test(url)){ var img=document.createElement('img'); img.src = url; img.style.maxWidth='100%'; img.style.maxHeight='140px'; preview.appendChild(img); } else if (/youtube|youtu\.be|youtube\.com/i.test(url)){ var iframe=document.createElement('iframe'); iframe.src = (function(u){ var m=u.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([A-Za-z0-9_\-]{6,})/i); if (m && m[1]) return 'https://www.youtube.com/embed/'+m[1]; return u; })(url); iframe.style.width='100%'; iframe.style.height='140px'; iframe.style.border=0; preview.appendChild(iframe); } else if (/\.(mp4|webm|ogg)(\?|$)/i.test(url)){ var v=document.createElement('video'); v.src=url; v.controls=true; v.style.maxWidth='100%'; v.style.maxHeight='140px'; preview.appendChild(v); } else { var a=document.createElement('a'); a.href=url; a.target='_blank'; a.textContent=url; preview.appendChild(a); } } }catch(e){}
    // close handlers
    modal.querySelector('#tplMediaClose').onclick = function(){ modal.style.display='none'; modal._tpl_targetBlock=null; };
    modal.querySelector('#tplMediaCancel').onclick = function(){ modal.style.display='none'; modal._tpl_targetBlock=null; };
    modal.querySelector('#tplMediaSave').onclick = function(){ var val = modal.querySelector('#tplMediaUrl').value.trim(); applyMediaToBlock(block, type, val); modal.style.display='none'; modal._tpl_targetBlock=null; };
  }catch(e){ console.error('openMediaModalForBlock', e); } }

  function applyMediaToBlock(block, type, url){ try{
    var content = block.querySelector && block.querySelector('.tpl-content'); if (!content) return;
    var html = '';
    if (!url){ // insert placeholder
      if (type==='image') html = snippets['image']; else if (type==='video') html = snippets['video']; else html = snippets['resource'];
    } else {
      if (/youtu/i.test(url) || /youtube\.com|youtu\.be/.test(url)){
        var m = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([A-Za-z0-9_\-]{6,})/i);
        var emb = (m && m[1]) ? ('https://www.youtube.com/embed/'+m[1]) : url;
        html = '<div class="tpl-video-wrapper" style="position:relative;padding-top:56.25%"><iframe src="'+emb+'" frameborder="0" allowfullscreen style="position:absolute;left:0;top:0;width:100%;height:100%;border:0"></iframe></div>';
      } else if (/\.(mp4|webm|ogg)(\?|$)/i.test(url) || url.indexOf('blob:')===0){
        html = '<video controls style="width:100%;height:auto;"> <source src="'+url+'"> </video>';
      } else {
        // data: URL handling (image/video)
        var isData = false; var dataMime = null; try{ if (typeof url === 'string' && url.indexOf('data:') === 0){ isData = true; var mm = url.match(/^data:([^;,]+)/); if (mm && mm[1]) dataMime = mm[1]; } }catch(e){}
        if (isData && dataMime && dataMime.indexOf('image/') === 0){ html = '<img class="placeholder-img" src="'+url+'" alt="Imagen" />'; }
  else if (isData && dataMime && dataMime.indexOf('video/') === 0){ html = '<video controls style="width:100%;height:auto;" src="'+url+'">Video no soportado</video>'; }
        else if (/(jpg|jpeg|png|gif|svg)(\?|$)/i.test(url)){
          html = '<img class="placeholder-img" src="'+url+'" alt="Imagen" />';
        } else {
          // fallback: link
          html = '<a href="'+url+'" target="_blank">'+url+'</a>';
        }
      }
    }
    content.innerHTML = html;
    try{ ensureControls(block); wireBlockDrag(block); }catch(e){}
    syncCanvasToTextarea();
  }catch(e){ console.error('applyMediaToBlock', e); } }

  // attach to global for external calls
  window.TemplatesEditor = window.TemplatesEditor || {};
  window.TemplatesEditor.openMediaModalForBlock = openMediaModalForBlock;
})();
