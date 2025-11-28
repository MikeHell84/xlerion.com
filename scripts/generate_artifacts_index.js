const fs = require('fs');
const path = require('path');

const ARTIFACTS_DIR = path.resolve(__dirname, '..', 'public', 'artifacts');
const OUT_FILE = path.join(ARTIFACTS_DIR, 'index.html');

function isoLocal(dt) {
  return dt.toISOString().replace('T', ' ').replace(/:\d{2}\.\d+Z$/, '');
}

function buildHtml(items) {
  return `<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Previews - Artefacts</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;padding:18px;background:#f6f7fb;color:#111}
    h1{margin:0 0 12px;font-size:20px}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px}
    .card{background:#fff;border-radius:8px;padding:8px;box-shadow:0 1px 4px rgba(0,0,0,.06);display:flex;flex-direction:column}
    .card img{width:100%;height:140px;object-fit:cover;border-radius:6px}
    .meta{display:flex;justify-content:space-between;align-items:center;margin-top:8px;font-size:13px;color:#444}
    .meta a{color:#0b69ff;text-decoration:none}
    .meta time{font-size:12px;color:#666}
    .note{margin-top:14px;font-size:13px;color:#333}
  </style>
</head>
<body>
  <h1>Previsualizaciones de pantallas (public/artifacts)</h1>
  <p class="note">Si no ves una imagen actualizada, refresca la página o revisa el fichero en disco.</p>
  <div class="grid">
    ${items.map(it => `
    <div class="card">
      <a href="/artifacts/${it.name}"><img src="/artifacts/${it.name}" alt="${it.slug}"></a>
      <div class="meta"><a href="${it.url}">${it.url}</a><time datetime="${it.iso}">${it.label}</time></div>
    </div>
    `).join('\n')}
  </div>
</body>
</html>`;
}

function slugFromName(name) {
  const m = name.match(/^screenshot-(.+?)\./);
  return m ? m[1] : name;
}

function urlForSlug(slug) {
  if (!slug || slug === 'inicio' || slug === 'latest') return '/';
  return '/' + slug;
}

async function main() {
  if (!fs.existsSync(ARTIFACTS_DIR)) {
    console.error('Artifacts dir not found:', ARTIFACTS_DIR);
    process.exit(1);
  }

  const entries = fs.readdirSync(ARTIFACTS_DIR)
    .filter(f => /^screenshot-.*\.(png|jpg|jpeg|webp)$/.test(f))
    .map(name => {
      const stat = fs.statSync(path.join(ARTIFACTS_DIR, name));
      const iso = stat.mtime.toISOString();
      const dt = new Date(stat.mtime);
      const label = dt.toLocaleString('es-ES', {year:'numeric',month:'2-digit',day:'2-digit',hour:'2-digit',minute:'2-digit'});
      const slug = slugFromName(name);
      return { name, iso, label, slug, url: urlForSlug(slug) };
    })
    .sort((a,b) => a.name.localeCompare(b.name));

  const html = buildHtml(entries);
  fs.writeFileSync(OUT_FILE, html, 'utf8');
  console.log('Wrote', OUT_FILE);
}

main().catch(err => { console.error(err); process.exit(1); });
