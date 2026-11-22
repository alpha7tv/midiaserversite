const path = require('path');
const crypto = require('crypto');
const express = require('express');
const compression = require('compression');
const { db, getSettings } = require('./db');
const { waLink, normalizePhone } = require('./services/messaging');
const trial = require('./services/trial');
const scheduler = require('./services/scheduler');

const app = express();
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, '..', 'views'));
app.set('trust proxy', 1);
app.disable('x-powered-by');
app.use(compression());
app.use(express.urlencoded({ extended: false, limit: '200kb' }));

app.use((req, res, next) => {
  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
  res.setHeader('X-Frame-Options', 'SAMEORIGIN');
  next();
});
app.use(express.static(path.join(__dirname, '..', 'public'), { maxAge: '7d' }));

// www -> domínio sem www
app.use((req, res, next) => {
  const h = req.headers.host || '';
  if (h.startsWith('www.')) return res.redirect(301, `${req.protocol}://${h.slice(4)}${req.originalUrl}`);
  next();
});

// 301 para variações de URL (barra final, maiúsculas)
app.use((req, res, next) => {
  if (req.method === 'GET' && req.path.length > 1 && (req.path.endsWith('/') || /[A-Z]/.test(req.path)) && !req.path.startsWith('/admin')) {
    const q = req.url.slice(req.path.length);
    return res.redirect(301, req.path.replace(/\/+$/, '').toLowerCase() + q);
  }
  next();
});

const brl = (v) => 'R$ ' + Number(v).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
const plansBy = (cat) => db.prepare('SELECT * FROM plans WHERE category=? AND active=1 ORDER BY sort, price').all(cat);

function seoFor(p) {
  return db.prepare('SELECT * FROM seo WHERE path=?').get(p) || { title: 'Cine Pulse', description: '', h1: '', wa: '' };
}

function render(res, view, pagePath, extra = {}) {
  const s = getSettings();
  const seo = extra.seo || seoFor(pagePath);
  const base = s.site_url.replace(/\/$/, '');
  res.render(view, {
    s, seo, brl, plansBy, base, pagePath,
    canonical: base + (pagePath === '/' ? '/' : pagePath),
    wa: (msg) => waLink(s, msg || seo.wa),
    noindex: false, breadcrumbs: null, ...extra,
  });
}

// ---------- Páginas públicas ----------
app.get('/', (req, res) => render(res, 'pages/home', '/'));
app.get('/planos', (req, res) => render(res, 'pages/planos', '/planos', { breadcrumbs: [['Planos', '/planos']] }));
app.get('/revenda-iptv', (req, res) => render(res, 'pages/revenda', '/revenda-iptv', { breadcrumbs: [['Revenda IPTV', '/revenda-iptv']] }));
app.get('/revenda-iptv/:slug', (req, res, next) => {
  const plan = db.prepare("SELECT * FROM plans WHERE category='revenda' AND slug=? AND active=1").get(req.params.slug);
  if (!plan) return next();
  const p = `/revenda-iptv/${plan.slug}`;
  const isUnl = !/^\d+$/.test(plan.credits || '');
  const custom = db.prepare('SELECT * FROM seo WHERE path=?').get(p);
  const seo = custom || {
    title: isUnl ? `Painel IPTV Ilimitado por ${brl(plan.price)}/mês | Cine Pulse` : `Painel IPTV ${plan.credits} Créditos por ${brl(plan.price)}/mês | Cine Pulse`,
    description: `${plan.name} na revenda IPTV Cine Pulse por ${brl(plan.price)} mensais. Créditos acumulativos, DNS exclusiva na contratação e suporte 24h direto com o dono.`,
    h1: isUnl ? 'Painel IPTV mensalista com créditos ilimitados' : `Painel IPTV mensalista com ${plan.credits} créditos`,
    wa: `Olá! Quero contratar o ${plan.name} (${brl(plan.price)}/mês) da Cine Pulse.`,
  };
  render(res, 'pages/plano-revenda', p, { plan, seo, breadcrumbs: [['Revenda IPTV', '/revenda-iptv'], [plan.name, p]] });
});
app.get('/apontamento-iptv', (req, res) => render(res, 'pages/apontamento', '/apontamento-iptv', { breadcrumbs: [['Apontamento IPTV', '/apontamento-iptv']] }));
app.get('/franquia-iptv', (req, res) => render(res, 'pages/franquia', '/franquia-iptv', { breadcrumbs: [['Franquia IPTV', '/franquia-iptv']] }));
app.get('/iptv-para-provedores', (req, res) => render(res, 'pages/provedores', '/iptv-para-provedores', { breadcrumbs: [['IPTV para provedores', '/iptv-para-provedores']] }));
app.get('/como-funciona-iptv', (req, res) => render(res, 'pages/como-funciona', '/como-funciona-iptv', { breadcrumbs: [['Como funciona', '/como-funciona-iptv']] }));
app.get('/dispositivos', (req, res) => render(res, 'pages/dispositivos', '/dispositivos', { breadcrumbs: [['Dispositivos', '/dispositivos']] }));
app.get('/teste-gratis', (req, res) => render(res, 'pages/teste', '/teste-gratis', { breadcrumbs: [['Teste grátis', '/teste-gratis']], tipo: req.query.tipo === 'revenda' ? 'revenda' : 'cliente', erro: null, form: {} }));
app.get('/contato', (req, res) => render(res, 'pages/contato', '/contato', { breadcrumbs: [['Contato', '/contato']], erro: null, form: {} }));
app.get('/politica-de-privacidade', (req, res) => render(res, 'pages/privacidade', '/politica-de-privacidade'));
app.get('/termos-de-uso', (req, res) => render(res, 'pages/termos', '/termos-de-uso'));
app.get('/obrigado', (req, res) => render(res, 'pages/obrigado', '/obrigado', { noindex: true, tipo: req.query.t || 'teste', seo: { title: 'Obrigado | Cine Pulse', description: '', h1: 'Pedido recebido!', wa: 'Olá! Acabei de me cadastrar no site da Cine Pulse.' } }));

// Redirecionamentos amigáveis (rotas comuns -> canônicas)
const redirects = { '/teste': '/teste-gratis', '/revenda': '/revenda-iptv', '/painel-mensalista': '/revenda-iptv', '/painel-iptv': '/revenda-iptv', '/franquia': '/franquia-iptv', '/apontamento': '/apontamento-iptv', '/provedores': '/iptv-para-provedores', '/assinar': '/planos' };
for (const [from, to] of Object.entries(redirects)) app.get(from, (req, res) => res.redirect(301, to));

// ---------- Formulários ----------
const hits = new Map();
function rateLimited(ip, max = 5, windowMs = 60 * 60 * 1000) {
  const now = Date.now();
  const arr = (hits.get(ip) || []).filter((t) => now - t < windowMs);
  arr.push(now); hits.set(ip, arr);
  return arr.length > max;
}
const validEmail = (e) => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(e);
const utmOf = (b) => JSON.stringify(Object.fromEntries(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'].filter((k) => b[k]).map((k) => [k, String(b[k]).slice(0, 200)])));

app.post('/teste-gratis', async (req, res) => {
  const b = req.body;
  const form = { name: (b.name || '').trim().slice(0, 100), email: (b.email || '').trim().toLowerCase().slice(0, 150), whatsapp: (b.whatsapp || '').trim().slice(0, 30), tipo: b.tipo === 'revenda' ? 'revenda' : 'cliente' };
  const fail = (erro) => render(res.status(400), 'pages/teste', '/teste-gratis', { breadcrumbs: [['Teste grátis', '/teste-gratis']], tipo: form.tipo, erro, form });
  if (b.website) return res.redirect('/obrigado'); // honeypot
  if (!form.name || !validEmail(form.email)) return fail('Informe seu nome e um e-mail válido.');
  const phone = normalizePhone(form.whatsapp);
  if (phone.length < 12 || phone.length > 13) return fail('Informe um WhatsApp válido com DDD.');
  if (b.consent !== '1') return fail('Para enviar o teste precisamos do seu consentimento.');
  if (rateLimited(req.ip)) return fail('Muitas solicitações. Tente novamente mais tarde ou fale no WhatsApp.');
  const dup = db.prepare("SELECT id FROM leads WHERE source='teste' AND (email=? OR whatsapp=?) AND created_at > datetime('now','-30 days')").get(form.email, phone);
  if (dup) return fail('Já existe um teste para este e-mail ou WhatsApp nos últimos 30 dias. Fale com a gente no WhatsApp para ajuda.');
  const r = db.prepare("INSERT INTO leads(name,email,whatsapp,type,source,status,consent,unsub_token,utm,ip) VALUES(?,?,?,?,'teste','novo',1,?,?,?)")
    .run(form.name, form.email, phone, form.tipo, crypto.randomBytes(16).toString('hex'), utmOf(b), req.ip);
  trial.processNewTrial(Number(r.lastInsertRowid)).catch((e) => console.error('trial', e));
  res.redirect(303, '/obrigado?t=teste');
});

app.post('/contato', (req, res) => {
  const b = req.body;
  const form = { name: (b.name || '').trim().slice(0, 100), email: (b.email || '').trim().toLowerCase().slice(0, 150), whatsapp: (b.whatsapp || '').trim().slice(0, 30), interesse: b.interesse || 'cliente', message: (b.message || '').trim().slice(0, 2000) };
  const fail = (erro) => render(res.status(400), 'pages/contato', '/contato', { breadcrumbs: [['Contato', '/contato']], erro, form });
  if (b.website) return res.redirect('/obrigado?t=contato');
  if (!form.name || (!validEmail(form.email) && normalizePhone(form.whatsapp).length < 12)) return fail('Informe seu nome e um e-mail ou WhatsApp válido.');
  if (rateLimited(req.ip, 10)) return fail('Muitas mensagens. Tente novamente mais tarde.');
  const tipos = ['cliente', 'revenda', 'apontamento', 'franquia', 'provedor', 'site'];
  db.prepare("INSERT INTO leads(name,email,whatsapp,type,source,message,status,consent,unsub_token,utm,ip) VALUES(?,?,?,?,'contato',?,'novo',?,?,?,?)")
    .run(form.name, form.email || null, normalizePhone(form.whatsapp), tipos.includes(form.interesse) ? form.interesse : 'cliente', form.message, b.consent === '1' ? 1 : 0, crypto.randomBytes(16).toString('hex'), utmOf(b), req.ip);
  res.redirect(303, '/obrigado?t=contato');
});

app.get('/descadastrar/:token', (req, res) => {
  const lead = db.prepare('SELECT id FROM leads WHERE unsub_token=?').get(req.params.token);
  if (lead) db.prepare("UPDATE leads SET seq_active=0, status=CASE WHEN status='convertido' THEN status ELSE 'descadastrado' END WHERE id=?").run(lead.id);
  render(res, 'pages/obrigado', '/descadastrar', { noindex: true, tipo: 'descadastro', seo: { title: 'Descadastro | Cine Pulse', h1: 'Pronto, você não receberá mais nossos e-mails.', wa: 'Olá!' } });
});

// ---------- SEO técnico ----------
app.get('/robots.txt', (req, res) => {
  const base = getSettings().site_url.replace(/\/$/, '');
  res.type('text/plain').send(`User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /obrigado\nDisallow: /descadastrar/\n\nSitemap: ${base}/sitemap.xml\n`);
});
app.get('/sitemap.xml', (req, res) => {
  const base = getSettings().site_url.replace(/\/$/, '');
  const paths = ['/', '/planos', '/teste-gratis', '/revenda-iptv', ...plansBy('revenda').map((p) => `/revenda-iptv/${p.slug}`), '/apontamento-iptv', '/franquia-iptv', '/iptv-para-provedores', '/como-funciona-iptv', '/dispositivos', '/contato', '/politica-de-privacidade', '/termos-de-uso'];
  const today = new Date().toISOString().slice(0, 10);
  res.type('application/xml').send(`<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${paths.map((p) => `  <url><loc>${base}${p}</loc><lastmod>${today}</lastmod></url>`).join('\n')}\n</urlset>\n`);
});
app.get('/site.webmanifest', (req, res) => res.json({ name: 'Cine Pulse', short_name: 'Cine Pulse', icons: [{ src: '/img/icon-192.png', sizes: '192x192', type: 'image/png' }, { src: '/img/icon-512.png', sizes: '512x512', type: 'image/png' }], theme_color: '#0b0b0b', background_color: '#0b0b0b', display: 'standalone', lang: 'pt-BR' }));
app.get('/healthz', (req, res) => res.send('ok'));

// ---------- Admin ----------
app.use('/admin', require('./admin'));

app.use((req, res) => render(res.status(404), 'pages/404', req.path, { noindex: true, seo: { title: 'Página não encontrada | Cine Pulse', h1: 'Página não encontrada', wa: 'Olá!' } }));
app.use((err, req, res, next) => { console.error(err); res.status(500).send('Erro interno'); });

const PORT = Number(process.env.PORT) || 3000;
app.listen(PORT, () => { console.log(`Cine Pulse rodando na porta ${PORT}`); scheduler.start(); });
