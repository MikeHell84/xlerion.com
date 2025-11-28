const https = require('https');
const crypto = require('crypto');

const urls = {
  css: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
  js:  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'
};

function fetch(url){
  return new Promise((res, rej) => {
    https.get(url, (r) => {
      if(r.statusCode !== 200) return rej(new Error('Status ' + r.statusCode));
      const chunks = [];
      r.on('data', c => chunks.push(c));
      r.on('end', () => res(Buffer.concat(chunks)));
    }).on('error', rej);
  });
}

(async () => {
  try {
    for(const k of Object.keys(urls)){
      const buf = await fetch(urls[k]);
      const h = crypto.createHash('sha384').update(buf).digest('base64');
      console.log(`${k}:sha384-${h}`);
    }
  } catch(e) {
    console.error('Error:', e.message);
    process.exit(2);
  }
})();
