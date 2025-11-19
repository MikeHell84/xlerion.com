const puppeteer = require('puppeteer');
(async ()=>{
  const url = process.argv[2] || 'http://127.0.0.1:8080/';
  const browser = await puppeteer.launch({headless:true, args:['--no-sandbox','--disable-setuid-sandbox']});
  const page = await browser.newPage();
  const logs = [];
  const requests = [];
  page.on('console', msg => logs.push({type:'console', text: msg.text()}));
  page.on('pageerror', err => logs.push({type:'error', text: err.message}));
  page.on('requestfailed', req => requests.push({url: req.url(), errorText: req.failure()?.errorText}));
  page.on('response', res => {
    if (res.status() >= 400) requests.push({url: res.url(), status: res.status()});
  });
  try{
    await page.goto(url, {waitUntil:'networkidle2', timeout:20000});
  await new Promise(r => setTimeout(r, 500));
    const title = await page.title();
    console.log('PAGE_TITLE:', title);
    if (logs.length) console.log('CONSOLE_LOGS:', logs.slice(0,20));
    if (requests.length) console.log('FAILED_REQUESTS:', requests.slice(0,20));
    const body = await page.content();
    // quick search for 'integrity' related errors in console logs captured
    const integrityWarnings = logs.filter(l => /integrity|SRI|subresource|blocked/i.test(l.text));
    if (integrityWarnings.length) console.log('INTEGRITY_WARNINGS:', integrityWarnings);
    await browser.close();
    process.exit(0);
  } catch(e){
    console.error('SCRIPT_ERROR:', e.message);
    await browser.close();
    process.exit(2);
  }
})();
