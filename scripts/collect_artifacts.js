const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
(async ()=>{
  const url = process.argv[2] || 'http://127.0.0.1:8080/';
  const ts = new Date().toISOString().replace(/[:.]/g,'-');
  const dir = path.resolve(__dirname, '..', 'artifacts', 'run-' + ts);
  fs.mkdirSync(dir, { recursive:true });

  const browser = await puppeteer.launch({headless:true, args:['--no-sandbox','--disable-setuid-sandbox']});
  const page = await browser.newPage();

  const logs = [];
  const requests = [];

  page.on('console', msg => logs.push({type:'console', text: msg.text()}));
  page.on('pageerror', err => logs.push({type:'pageerror', text: err.message}));
  page.on('requestfailed', req => requests.push({type:'failed', url: req.url(), reason: req.failure()?.errorText}));
  page.on('response', res => { if (res.status() >= 400) requests.push({type:'response', url: res.url(), status: res.status()}); });

  try{
    await page.goto(url, {waitUntil:'networkidle2', timeout:20000});
    await new Promise(r=>setTimeout(r, 500));

    const title = await page.title();
    const html = await page.content();

    // write artifacts
    fs.writeFileSync(path.join(dir, 'title.txt'), title, 'utf8');
    fs.writeFileSync(path.join(dir, 'page.html'), html, 'utf8');
    fs.writeFileSync(path.join(dir, 'console.json'), JSON.stringify(logs, null, 2), 'utf8');
    fs.writeFileSync(path.join(dir, 'requests.json'), JSON.stringify(requests, null, 2), 'utf8');

    await page.screenshot({path: path.join(dir, 'screenshot.png'), fullPage:true});

    console.log('ARTIFACT_DIR:', dir);
    console.log('PAGE_TITLE:', title);
    console.log('CONSOLE_LINES:', logs.length);
    console.log('REQUEST_EVENTS:', requests.length);

    await browser.close();
    process.exit(0);
  } catch(e){
    console.error('ERROR:', e.message);
    await browser.close();
    process.exit(2);
  }
})();
