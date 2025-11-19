const { spawn } = require('child_process');
const path = require('path');

const COLLECTOR = path.resolve(__dirname, 'collect_artifacts.js');
const MAX_RETRIES = 3;

function runCollector(url) {
  return new Promise((resolve, reject) => {
    const proc = spawn(process.execPath, [COLLECTOR, url], { stdio: 'inherit' });
    proc.on('close', code => code === 0 ? resolve() : reject(new Error('collector exit ' + code)));
  });
}

async function attempt(url) {
  let attempt = 0;
  let delay = 500; // ms
  while (attempt <= MAX_RETRIES) {
    try {
      attempt++;
      console.log(`Attempt ${attempt} -> ${url}`);
      await runCollector(url);
      console.log('Success', url);
      return;
    } catch (err) {
      console.error('Error on attempt', attempt, err.message || err);
      if (attempt > MAX_RETRIES) throw err;
      await new Promise(r => setTimeout(r, delay));
      delay *= 2;
    }
  }
}

if (require.main === module) {
  const url = process.argv[2] || 'http://127.0.0.1:8080/';
  attempt(url).catch(err => { console.error('Failed to capture', url, err); process.exit(1); });
}
