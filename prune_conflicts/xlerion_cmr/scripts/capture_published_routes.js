const { execSync, spawnSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const phpList = path.join(__dirname,'list_pages.php');
const collector = path.join(__dirname,'collect_artifacts.js');
const artifactsRoot = path.join(__dirname,'..','artifacts');
const publicArtifacts = path.join(__dirname,'..','public','artifacts');
if (!fs.existsSync(publicArtifacts)) fs.mkdirSync(publicArtifacts, { recursive: true });

function getLatestArtifactDir(){
  if (!fs.existsSync(artifactsRoot)) return null;
  const dirs = fs.readdirSync(artifactsRoot).map(d=>({d, p: path.join(artifactsRoot,d), t: fs.statSync(path.join(artifactsRoot,d)).mtime})).filter(x=>fs.statSync(x.p).isDirectory()).sort((a,b)=>b.t - a.t);
  if (dirs.length===0) return null;
  return dirs[0].p;
}

try{
  const out = execSync(`php "${phpList}"`,{encoding:'utf8'}).trim();
  if(!out){ console.error('No pages listed'); process.exit(1); }
  const lines = out.split(/\r?\n/);
  for(const line of lines){
    // expected format: P\tslug\ttitle
    const parts = line.split('\t');
    if(parts.length<3) continue;
    const status = parts[0].trim();
    const slug = (parts[1]||'').trim();
    const title = (parts[2]||'').trim();
    if(status !== 'P') continue;
    const route = slug === 'inicio' || slug === '' ? '/' : `/${slug}`;
    const url = `http://127.0.0.1:8080${route}`;
    console.log('Capturing', url);
    // run collector
    const res = spawnSync('node',[collector,url],{stdio:'inherit'});
    if(res.error){ console.error('collector failed for',url,res.error); continue; }
    // find latest artifact
    const latest = getLatestArtifactDir();
    if(!latest){ console.error('no artifact dir after capture for',url); continue; }
    const src = path.join(latest,'screenshot.png');
    if(!fs.existsSync(src)){ console.error('no screenshot produced for',url); continue; }
    const safeSlug = slug === '' ? 'root' : slug.replace(/[^a-z0-9\-]/ig,'_');
    const dest = path.join(publicArtifacts,`screenshot-${safeSlug}.png`);
    fs.copyFileSync(src,dest);
    console.log('Saved',dest);
  }
  console.log('Done');
}catch(e){
  console.error('Failed:', e.message);
  process.exit(2);
}
