const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');
const routes = ['','inicio','servicios','contact','contacto','blog','proyectos','documentacion'];
const base = 'http://127.0.0.1:8080';
const script = path.join(__dirname,'collect_artifacts.js');
const artifactsDir = path.join(__dirname,'..','artifacts');
const publicArtifacts = path.join(__dirname,'..','public','artifacts');
if(!fs.existsSync(publicArtifacts)) fs.mkdirSync(publicArtifacts,{recursive:true});
for(const r of routes){
  const routePath = r === '' ? '/' : `/${r}`;
  const url = base + routePath;
  try{
    const code = execSync(`curl -s -o /dev/null -w "%{http_code}" ${url}`,{encoding:'utf8',windowsHide:true}).trim();
    console.log(routePath,'->',code);
    if(code === '200'){
      console.log('Capturing',routePath);
      execSync(`node "${script}" "${url}"`,{stdio:'inherit'});
      // find latest artifact
      const dirs = fs.readdirSync(artifactsDir).map(d=>({d, p:path.join(artifactsDir,d), t:fs.statSync(path.join(artifactsDir,d)).mtime})).sort((a,b)=>b.t-a.t);
      if(dirs.length){
        const latest = path.join(dirs[0].p,'screenshot.png');
        if(fs.existsSync(latest)){
          const dest = path.join(publicArtifacts,`screenshot-${r||'root'}.png`);
          fs.copyFileSync(latest,dest);
          console.log('Saved',dest);
        }
      }
    }
  }catch(e){
    console.log(routePath,'-> ERROR',e.message.split('\n')[0]);
  }
}
console.log('done');
