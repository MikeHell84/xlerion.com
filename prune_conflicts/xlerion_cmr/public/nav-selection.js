// nav-selection.js
// Guarda la selección de un dropdown del navbar y al recargar muestra la opción
// seleccionada como texto del trigger del grupo. Extrae solo el texto visible de
// los items ignorando iconos y mantiene los iconos intactos.
(function(){
  'use strict';

  var storageKeyPrefix = 'xlerion_nav_selected_';
  var idPrefix = 'navgroup-';

  function textOnly(el){
    // devuelve el texto de un elemento ignorando elementos con clase .nav-icon o .material-symbols-outlined
    if (!el) return '';
    var clone = el.cloneNode(true);
    var icons = clone.querySelectorAll('.nav-icon, .material-symbols-outlined');
    icons.forEach(function(i){ i.parentNode && i.parentNode.removeChild(i); });
    return (clone.textContent || '').trim();
  }

  function ensureTriggerLabel(trigger){
    if (!trigger) return;
    // if there's already a .nav-label span, use it
    var label = trigger.querySelector('.nav-label');
    if (!label){
      // create a span.nav-label and move existing plain text into it
      label = document.createElement('span');
      label.className = 'nav-label';
      // collect existing text nodes (not inside icons)
      var text = '';
      for (var i=0;i<trigger.childNodes.length;i++){
        var n = trigger.childNodes[i];
        if (n.nodeType === Node.TEXT_NODE && n.textContent.trim() !== ''){ text += ' ' + n.textContent.trim(); }
      }
      label.textContent = text.trim() ? (' ' + text.trim()) : '';
      // remove those text nodes
      for (var j=trigger.childNodes.length-1;j>=0;j--){
        var m = trigger.childNodes[j];
        if (m.nodeType === Node.TEXT_NODE && m.textContent.trim() !== ''){ trigger.removeChild(m); }
      }
      // append label after any icon span (so icons remain first)
      trigger.appendChild(label);
    }
    return label;
  }

  function getOrAssignDropdownId(trigger, index){
    if (!trigger) return null;
    var dd = trigger.closest && trigger.closest('.dropdown');
    if (!dd) return null;
    if (dd.getAttribute('data-dropdown-id')) return dd.getAttribute('data-dropdown-id');
    // create a stable id: prefer cleaned trigger text, fallback to numeric
    var clean = textOnly(trigger) || trigger.textContent || '';
    clean = clean.replace(/\s+/g,' ').trim();
    var id = clean ? clean : (idPrefix + index);
    // slugify to avoid spaces
    id = id.toLowerCase().replace(/[^a-z0-9-_]+/g,'-').replace(/^-+|-+$/g,'');
    dd.setAttribute('data-dropdown-id', id);
    return id;
  }

  function init(){
    // Assign ids and ensure label spans on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function(){
      var triggers = Array.prototype.slice.call(document.querySelectorAll('.nav-link.dropdown-toggle'));
      triggers.forEach(function(trigger, idx){
        getOrAssignDropdownId(trigger, idx);
        ensureTriggerLabel(trigger);
        // apply stored value if present
        var id = trigger.closest('.dropdown') && trigger.closest('.dropdown').getAttribute('data-dropdown-id');
        if (!id) return;
        var stored = null;
        try{ stored = sessionStorage.getItem(storageKeyPrefix + id); }catch(err){ stored = null; }
        if (stored){
          var lbl = ensureTriggerLabel(trigger);
          lbl.textContent = ' ' + stored;
        }
      });
    }, false);

    // Save selection when clicking a dropdown item
    document.addEventListener('click', function(e){
      var a = e.target && e.target.closest ? e.target.closest('a.dropdown-item') : null;
      if (!a) return;
      var dropdown = a.closest && a.closest('.dropdown');
      if (!dropdown) return;
      var trigger = dropdown.querySelector('.nav-link.dropdown-toggle');
      if (!trigger) return;
      var id = dropdown.getAttribute('data-dropdown-id');
      if (!id){ id = getOrAssignDropdownId(trigger, 0); }
      // extract item label text (ignore icons)
      var val = textOnly(a) || (a.textContent || '').trim();
      if (!val) return;
      try{ sessionStorage.setItem(storageKeyPrefix + id, val); }catch(err){/*ignore*/}
      // update immediately the trigger label
      var lbl = ensureTriggerLabel(trigger);
      if (lbl) lbl.textContent = ' ' + val;
    }, false);
  }

  init();
})();
