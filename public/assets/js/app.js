(function () {
  'use strict';
  var dl = window.dataLayer = window.dataLayer || [];
  var cfg = window.MS || {};
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  function gtag() { dl.push(arguments); }

  /* ---------- menu móvel e submenus ---------- */
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

  /* ---------- atribuição de campanha (gclid, UTMs): só na sessão, sem cookie ---------- */
  var ATTR_KEYS = ['gclid', 'gbraid', 'wbraid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'matchtype', 'network'];
  function readAttr() {
    var out = {}, q = new URLSearchParams(location.search), has = false;
    ATTR_KEYS.forEach(function (k) { var v = q.get(k); if (v) { out[k] = v.slice(0, 120); has = true; } });
    try {
      if (has) { sessionStorage.setItem('ms_attr', JSON.stringify(out)); }
      else { out = JSON.parse(sessionStorage.getItem('ms_attr') || '{}'); }
    } catch (e) { /* sem storage */ }
    return out;
  }
  var attr = readAttr();
  window.MS_ATTR = attr;
  $$('input[name="attribution"]').forEach(function (i) { i.value = JSON.stringify(attr); });

  /* ---------- consentimento + Consent Mode v2 ---------- */
  var DENIED = { ad_storage: 'denied', analytics_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied', wait_for_update: 500 };
  var GRANTED = { ad_storage: 'granted', analytics_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted' };
  gtag('consent', 'default', DENIED);

  function getC() { var m = document.cookie.match(/(?:^|; )ms_consent=([^;]+)/); return m ? m[1] : null; }
  function setC(v) {
    // cookie no domínio-pai: o WHMCS (cliente-area.) lê o mesmo consentimento para medir a compra
    var dom = /(^|\.)midiaserver\.com\.br$/.test(location.hostname) ? '; domain=.midiaserver.com.br' : '';
    document.cookie = 'ms_consent=' + v + '; max-age=31536000; path=/; SameSite=Lax' + dom + (location.protocol === 'https:' ? '; Secure' : '');
  }
  var consent = getC() === 'yes';

  function loadScript(src) { var s = document.createElement('script'); s.async = true; s.src = src; document.head.appendChild(s); }
  var loaded = false;
  function loadTags() {
    if (loaded) return; loaded = true;
    gtag('consent', 'update', GRANTED);
    if (cfg.gtm) {
      dl.push({ 'gtm.start': Date.now(), event: 'gtm.js' });
      loadScript('https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(cfg.gtm));
    } else if (cfg.ga4 || cfg.ads) {
      loadScript('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(cfg.ga4 || cfg.ads));
      gtag('js', new Date());
      if (cfg.ga4) gtag('config', cfg.ga4);
      if (cfg.ads) gtag('config', cfg.ads, { allow_enhanced_conversions: true });
    }
    if (cfg.pixel) {
      !function (f, b, e, v, n, t, s) { if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); }; if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t = b.createElement(e); t.async = !0; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s); }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', cfg.pixel);
      fbq('track', 'PageView', {}, { eventID: 'pv-' + uid() });
    }
  }

  /* ---------- eventos ---------- */
  var META = { whatsapp_click: 'Contact', begin_checkout: 'InitiateCheckout', view_item: 'ViewContent', view_demo: 'ViewContent', generate_lead: 'Lead' };
  function uid() { return (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : String(Date.now()) + Math.random().toString(16).slice(2); }
  function push(name, data) {
    var o = { event: name, event_id: uid(), page_path: location.pathname };
    for (var k in data) o[k] = data[k];
    for (var a in attr) if (a.indexOf('utm_') === 0) o[a] = attr[a];
    dl.push(o);
    if (!consent) return;
    if (!cfg.gtm && (cfg.ga4 || cfg.ads)) {
      var p = {}; for (var k2 in data) p[k2] = data[k2]; p.event_id = o.event_id;
      gtag('event', name, p);
      if (cfg.conv && cfg.conv[name]) {
        gtag('event', 'conversion', { send_to: cfg.conv[name], value: data.value || 0, currency: 'BRL', transaction_id: o.event_id });
      }
    }
    if (cfg.pixel && window.fbq && META[name]) {
      var mp = {}; if (data.value) { mp.value = data.value; mp.currency = 'BRL'; }
      fbq('track', META[name], mp, { eventID: o.event_id });
    }
  }
  window.msTrack = push;

  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-ev]'); if (!el) return;
    var ev = el.getAttribute('data-ev'), d = {};
    ['where', 'pid', 'plan', 'price', 'file', 'model'].forEach(function (k) { var v = el.getAttribute('data-' + k); if (v) d[k] = v; });
    if (ev === 'begin_checkout') { d.currency = 'BRL'; d.value = parseFloat(d.price || '0') || 0; d.items = [{ item_id: d.pid, item_name: d.plan, price: d.value }]; }
    push(ev, d);
  }, true);

  var plans = $$('.plan [data-pid]');
  if (plans.length) {
    var seen = {}, items = [];
    plans.forEach(function (p) { var id = p.getAttribute('data-pid'); if (!seen[id]) { seen[id] = 1; items.push({ item_id: id, item_name: p.getAttribute('data-plan') }); } });
    window.__viewItem = { items: items };
  }
  // conversão de lead: a página de obrigado dispara uma vez
  var thanks = $('[data-lead-thanks]');
  var firedLead = false;
  function fireOnPage() {
    if (window.__viewItem) push('view_item', window.__viewItem);
    if (thanks && !firedLead) { firedLead = true; push('generate_lead', { where: thanks.getAttribute('data-lead-thanks') || 'lp', value: 0 }); }
  }

  var box = $('#consent'), c = getC();
  if (c === 'yes') { loadTags(); }
  else if (!c && box) { box.hidden = false; }
  fireOnPage();
  $$('[data-consent]').forEach(function (b) {
    b.addEventListener('click', function () {
      var v = b.getAttribute('data-consent'); setC(v); if (box) box.hidden = true;
      if (v === 'yes') { consent = true; loadTags(); fireOnPage(); }
    });
  });

  /* ---------- amostras de áudio: um player por vez ---------- */
  var cur = null;
  function fmtPlay(box, on) { box.classList.toggle('playing', on); var b = box.querySelector('[data-sample-btn]'); if (b) b.classList.toggle('on', on); }
  $$('[data-sample]').forEach(function (box) {
    var btn = box.querySelector('[data-sample-btn]'), au = box.querySelector('audio'), bar = box.querySelector('.sample-bar i');
    btn.addEventListener('click', function () {
      if (cur && cur !== box) { var o = cur.querySelector('audio'); o.pause(); fmtPlay(cur, false); }
      if (!au.getAttribute('src')) au.setAttribute('src', au.getAttribute('data-src'));
      if (au.paused) { au.play().then(function () { cur = box; fmtPlay(box, true); push('play_sample', { where: 'conteudos', file: btn.getAttribute('data-name') }); }).catch(function () { fmtPlay(box, false); }); }
      else { au.pause(); fmtPlay(box, false); }
    });
    au.addEventListener('timeupdate', function () { if (au.duration) bar.style.width = (au.currentTime / au.duration * 100) + '%'; });
    au.addEventListener('ended', function () { fmtPlay(box, false); bar.style.width = '0'; });
    au.addEventListener('error', function () { fmtPlay(box, false); });
  });

  /* ---------- formulário de lead: evita duplo envio ---------- */
  $$('form[data-lead]').forEach(function (f) {
    var t0 = Date.now(), ti = f.querySelector('input[name="elapsed"]');
    f.addEventListener('submit', function () {
      if (ti) ti.value = String(Date.now() - t0);
      var b = f.querySelector('button[type="submit"]'); if (b) { b.disabled = true; b.textContent = 'Enviando...'; }
    });
  });
})();
