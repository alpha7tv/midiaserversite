// Mede LCP, FCP, CLS e peso de uma página em perfil de celular (CPU 4x mais lenta, rede ~Slow 4G).
// Uso: node tools/perf.js <url> [url...]
const { chromium } = require('/opt/node-tools/node_modules/playwright');
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  for (const url of process.argv.slice(2)) {
    const ctx = await b.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, isMobile: true, userAgent: 'Mozilla/5.0 (Linux; Android 12) Chrome/120 Mobile Safari/537.36' });
    await ctx.addCookies([{ name: 'ms_consent', value: 'no', url: new URL(url).origin }]);
    const p = await ctx.newPage();
    const cdp = await ctx.newCDPSession(p);
    await cdp.send('Network.enable');
    await cdp.send('Network.emulateNetworkConditions', { offline: false, latency: 150, downloadThroughput: 1.6 * 1024 * 1024 / 8, uploadThroughput: 750 * 1024 / 8 });
    await cdp.send('Emulation.setCPUThrottlingRate', { rate: 4 });
    let bytes = 0, reqs = 0;
    cdp.on('Network.loadingFinished', e => { bytes += e.encodedDataLength; reqs++; });
    await p.addInitScript(() => {
      window.__m = { lcp: 0, cls: 0, fcp: 0 };
      new PerformanceObserver(l => { for (const e of l.getEntries()) window.__m.lcp = e.startTime; }).observe({ type: 'largest-contentful-paint', buffered: true });
      new PerformanceObserver(l => { for (const e of l.getEntries()) if (!e.hadRecentInput) window.__m.cls += e.value; }).observe({ type: 'layout-shift', buffered: true });
      new PerformanceObserver(l => { for (const e of l.getEntries()) if (e.name === 'first-contentful-paint') window.__m.fcp = e.startTime; }).observe({ type: 'paint', buffered: true });
    });
    await p.goto(url, { waitUntil: 'load' });
    await p.waitForTimeout(1500);
    const m = await p.evaluate(() => window.__m);
    console.log(`${url.replace(/^https?:\/\/[^/]+/, '').padEnd(28)} FCP ${Math.round(m.fcp)} ms | LCP ${Math.round(m.lcp)} ms | CLS ${m.cls.toFixed(3)} | ${reqs} req | ${(bytes / 1024).toFixed(0)} KB transferidos`);
    await ctx.close();
  }
  await b.close();
})();
