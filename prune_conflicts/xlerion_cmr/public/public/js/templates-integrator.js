// Fallback copy of templates-integrator.js to satisfy requests to /public/js/templates-integrator.js
// This file proxies to the canonical /js/templates-integrator.js by dynamically loading it.
(function(){
  try{
    var s = document.createElement('script');
    s.src = '/js/templates-integrator.js';
    s.defer = true;
    document.head && document.head.appendChild(s);
  }catch(e){
    // As a last resort, provide a minimal stub
    window.openTemplatePicker = window.openTemplatePicker || function(){ alert('Template picker not available'); };
    window.createTemplateFromEditor = window.createTemplateFromEditor || function(){ alert('Create template not available'); };
  }
})();
