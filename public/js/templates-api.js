// Minimal Templates API for editor integration
const TemplatesAPI = (function(){
    const base = '/api/templates.php'; // PHP endpoint expects action query params or POST body

    async function list(){
        const r = await fetch('/api/templates.php?action=list');
        if (!r.ok) throw new Error('Failed to fetch templates');
        return r.json();
    }

    async function get(id){
        const r = await fetch('/api/templates.php?action=get&id=' + encodeURIComponent(id));
        if (!r.ok) throw new Error('Template not found');
        return r.json();
    }

    // create or update (POST). The server uses id in payload to replace when present.
    async function save(payload){
        const r = await fetch('/api/templates.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        if (!r.ok) throw new Error('Failed to save template');
        return r.json();
    }

    // remove using server-side file list modification (not implemented by older PHP API) - implement basic remove by fetching all and rewriting without id
    async function remove(id){
        // best-effort: call GET list and POST filtered list back (the PHP API doesn't expose DELETE)
        const listData = await list();
        const filtered = (listData || []).filter(function(t){ return (t.id || '') !== id; });
        // write back full list
        const r = await fetch('/api/templates.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ templates: filtered, name:'_sync_' }) });
        return r.json();
    }

    async function duplicate(id){
        var t = await get(id);
        if (!t) throw new Error('Not found');
        var copy = Object.assign({}, t);
        copy.id = undefined; copy.name = (t.name || 'Copy') + ' (copy)';
        return save(copy);
    }

    return { list, get, save, remove, duplicate };
})();

window.TemplatesAPI = TemplatesAPI;
