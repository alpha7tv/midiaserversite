(function () {
  'use strict';
  var dl = window.dataLayer = window.dataLayer || [];
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  // menu móvel + submenus
  var burger = $('#burger'), nav = $('#nav');
  function setMenu(open) {
    if (!burger || !nav) return;
    nav.classList.toggle('open', open);
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    burger.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
    document.body.classList.toggle('menu-open', open);
  }
  if (burger) burger.addEventListener('click', function () { setMenu(!nav.classList.contains('open')); });
  $$('.nav-btn').forEach(function (b) {
    b.addEventListener('click', function () {
      var li = b.parentNode, open = !li.classList.contains('open');
      $$('.has-sub.open').forEach(function (o) { if (o !== li) { o.classList.remove('open'); o.firstElementChild.setAttribute('aria-expanded', 'false'); } });
      li.classList.toggle('open', open);
      b.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { setMenu(false); $$('.has-sub.open').forEach(function (o) { o.classList.remove('open'); }); }
  });
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.has-sub') && window.innerWidth >= 1280) $$('.has-sub.open').forEach(function (o) { o.classList.remove('open'); });
    var a = e.target.closest('.nav a'); if (a && nav && nav.classList.contains('open')) setMenu(false);
  });

  // eventos (dataLayer) — event_id permite deduplicar Pixel x Conversions API
  function uid() { return (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : String(Date.now()) + Math.random().toString(16).slice(2); }
  function push(name, data) { var o = { event: name, event_id: uid(), page_path: location.pathname }; for (var k in data) o[k] = data[k]; dl.push(o); }
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-ev]'); if (!el) return;
    var ev = el.getAttribute('data-ev'), d = {};
    ['where', 'pid', 'plan', 'price', 'file', 'model'].forEach(function (k) { var v = el.getAttribute('data-' + k); if (v) d[k] = v; });
    if (ev === 'begin_checkout') { d.currency = 'BRL'; d.value = parseFloat(d.price || '0') || 0; d.items = [{ item_id: d.pid, item_name: d.plan, price: d.value }]; }
    push(ev, d);
  }, true);

  // visualização de produto
  var plans = $$('.plan [data-pid]');
  if (plans.length) push('view_item', { items: plans.map(function (p) { return { item_id: p.getAttribute('data-pid'), item_name: p.getAttribute('data-plan') }; }) });

  // consentimento + GTM (só carrega com aceite)
  function getC() { var m = document.cookie.match(/(?:^|; )ms_consent=([^;]+)/); return m ? m[1] : null; }
  function setC(v) { document.cookie = 'ms_consent=' + v + '; max-age=31536000; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : ''); }
  function loadGtm() {
    var id = window.MS && window.MS.gtm; if (!id || window.__gtm) return; window.__gtm = 1;
    dl.push({ 'gtm.start': Date.now(), event: 'gtm.js' });
    var s = document.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(id); document.head.appendChild(s);
  }
  var box = $('#consent'), c = getC();
  if (c === 'yes') loadGtm(); else if (!c && box) box.hidden = false;
  $$('[data-consent]').forEach(function (b) {
    b.addEventListener('click', function () {
      var v = b.getAttribute('data-consent'); setC(v); if (box) box.hidden = true; if (v === 'yes') loadGtm();
    });
  });
})();
