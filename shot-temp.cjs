const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 375, height: 800 });

    await page.goto('http://obl.test/login', { waitUntil: 'networkidle0' });
    await page.type('#email', 'qa-temp-viewport@example.com');
    await page.type('input[type="password"]', 'password123');
    const [submitBtn] = await page.$$('form button[type="submit"], form button:not([type])');
    await page.evaluate(el => el.click(), submitBtn);
    await new Promise(r => setTimeout(r, 2000));
    console.log('URL after click:', page.url());

    await page.goto('http://obl.test/bookmarks', { waitUntil: 'networkidle0' });
    await page.screenshot({ path: 'C:/Users/DELL/AppData/Local/Temp/claude/d--laragon-www-obl-obl/4a9c83cd-84e2-4853-a32d-93dbb7c4f316/scratchpad/bookmarks-mobile.png', fullPage: false });

    await browser.close();
    console.log('done');
})().catch(e => { console.error(e); process.exit(1); });
