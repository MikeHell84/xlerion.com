<div id="template-picker" style="display:none;">
  <div class="tp-overlay"></div>
  <div class="tp-panel">
    <h3>Plantillas</h3>
    <div id="tp-list"></div>
    <button id="tp-create">Nueva plantilla</button>
    <button id="tp-close">Cerrar</button>
  </div>
</div>
<script src="/js/templates-api.js"></script>
<script src="/js/templates-integration.js"></script>
<script>
async function openTemplatePicker(){
  document.getElementById('template-picker').style.display = 'block';
  const list = await TemplatesAPI.list();
  const container = document.getElementById('tp-list');
  container.innerHTML = '';
  list.forEach(t => {
    const el = document.createElement('div');
    el.innerHTML = `<strong>${t.name}</strong> <button data-id="${t.id}" class="tp-insert">Insertar</button> <button data-id="${t.id}" class="tp-edit">Editar</button> <button data-id="${t.id}" class="tp-dup">Duplicar</button> <button data-id="${t.id}" class="tp-del">Eliminar</button>`;
    container.appendChild(el);
  });
  container.querySelectorAll('.tp-insert').forEach(b=>b.addEventListener('click', async e=>{
    const id = e.target.dataset.id;
    const t = await TemplatesAPI.get(id);
    // dispatch global event for editor to handle insert
    window.dispatchEvent(new CustomEvent('template:insert', { detail: t }));
    closeTemplatePicker();
  }));
  container.querySelectorAll('.tp-edit').forEach(b=>b.addEventListener('click', e=>{
    // open editor for template
    const id = e.target.dataset.id;
    window.location.href = '/admin/templates/edit.php?id=' + id;
  }));
  container.querySelectorAll('.tp-dup').forEach(b=>b.addEventListener('click', async e=>{
    const id = e.target.dataset.id;
    await TemplatesAPI.duplicate(id);
    openTemplatePicker();
  }));
  container.querySelectorAll('.tp-del').forEach(b=>b.addEventListener('click', async e=>{
    const id = e.target.dataset.id;
    if (!confirm('Eliminar plantilla?')) return;
    await TemplatesAPI.remove(id);
    openTemplatePicker();
  }));
}
function closeTemplatePicker(){ document.getElementById('template-picker').style.display='none'; }
document.getElementById('tp-close').addEventListener('click', closeTemplatePicker);
document.getElementById('tp-create').addEventListener('click', ()=>{ window.location.href='/admin/templates/new.php'; });
</script>
