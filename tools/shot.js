// Uso: node tools/shot.js <url|arquivo> <saida.png> [largura] [altura] [fullpage 0|1] [fundoTransparente 0|1]
const { chromium } = require('/opt/node-tools/node_modules/playwright');
(async () => {
  const [src, out, w = '1280', h = '800', full = '0', transp = '0'] = process.argv.slice(2);
  const browser = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' }).catch(async () => chromium.launch());
  const ctx = await browser.newContext({ viewport: { width: +w, height: +h }, deviceScaleFactor: 1 });
  await ctx.addCookies([{ name: 'ms_consent', value: 'no', url: 'http://127.0.0.1:8081' }]);
  const page = await ctx.newPage();
  const url = /^https?:|^file:/.test(src) ? src : 'file://' + require('path').resolve(src);
  await page.goto(url, { waitUntil: 'networkidle' });
  if (full === '1') {
    await page.evaluate(async () => { for (let y = 0; y < document.body.scrollHeight; y += 500) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 60)); } window.scrollTo(0, 0); });
    await page.waitForTimeout(400);
  }
  await page.screenshot({ path: out, fullPage: full === '1', omitBackground: transp === '1' });
  await browser.close();
})().catch(e => { console.error(e.message); process.exit(1); });
