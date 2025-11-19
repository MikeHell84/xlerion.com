const fs = require('fs');
const path = require('path');
const sharp = require('sharp');
(async ()=>{
  const srcDir = path.resolve(__dirname, '..', 'public', 'media', 'images', 'parallax');
  const outDir = path.resolve(srcDir, 'thumbs');
  if (!fs.existsSync(srcDir)) return console.error('source dir not found', srcDir);
  fs.mkdirSync(outDir, { recursive:true });
  const files = fs.readdirSync(srcDir).filter(f=>/\.(jpe?g|png)$/i.test(f));
  for (const f of files){
    const src = path.join(srcDir,f);
    const name = path.parse(f).name;
    try{
  await sharp(src).resize({width:800}).jpeg({quality:80}).toFile(path.join(outDir, name + '-800.jpg'));
  await sharp(src).resize({width:400}).jpeg({quality:70}).toFile(path.join(outDir, name + '-400.jpg'));
  // webp variants
  await sharp(src).resize({width:800}).webp({quality:80}).toFile(path.join(outDir, name + '-800.webp'));
  await sharp(src).resize({width:400}).webp({quality:70}).toFile(path.join(outDir, name + '-400.webp'));
      console.log('thumbs:', f);
    } catch(e){ console.error('err', f, e.message); }
  }
})();
