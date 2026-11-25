const crypto = require('crypto');
const express = require('express');
const { db, getSettings, setSetting, hashPassword, checkPassword } = require('./db');
const { sendEmail, sendWhatsApp } = require('./services/messaging');
const trial = require('./services/trial');
const scheduler = require('./services/scheduler');

const router = express.Router();
const sessions = new Map(); // token -> expires
const SESSION_MS = 12 * 60 * 60 * 1000;
const loginHits = new Map();

function cookies(req) {
  return Object.fromEntries((req.headers.cookie || '').split(';').map((c) => c.trim().split('=')).filter((p) => p[0]).map(([k, ...v]) => [k, decodeURIComponent(v.join('='))]));
}
function authed(req) {
  const t = cookies(req).cp_admin;
  const exp = t && sessions.get(t);
  if (!exp || exp < Date.now()) return false;
  sessions.set(t, Date.now() + SESSION_MS);
  return true;
}

router.use((req, res, next) => {
  res.setHeader('X-Robots-Tag', 'noindex, nofollow');
  res.setHeader('Cache-Control', 'no-store');
  // Proteção CSRF: POST precisa vir do próprio site
  if (req.method === 'POST') {
    const origin = req.headers.origin || req.headers.referer || '';
    const host = req.headers['x-forwarded-host'] || req.headers.host;
    if (origin && !origin.replace(/^https?:\/\//, '').startsWith(host)) return res.status(403).send('Origem inválida');
  }
  next();
});

const view = (res, v, data = {}) => res.render('admin/' + v, { s: getSettings(), msg: null, ...data });

router.get('/login', (req, res) => view(res, 'login', { erro: null }));
router.post('/login', (req, res) => {
  const now = Date.now();
  const arr = (loginHits.get(req.ip) || []).filter((t) => now - t < 15 * 60 * 1000);
  if (arr.length >= 8) return view(res.status(429), 'login', { erro: 'Muitas tentativas. Aguarde 15 minutos.' });
  arr.push(now); loginHits.set(req.ip, arr);
  if (!checkPassword(String(req.body.password || ''), getSettings().admin_password_hash)) return view(res.status(401), 'login', { erro: 'Senha incorreta.' });
  const token = crypto.randomBytes(32).toString('hex');
  sessions.set(token, now + SESSION_MS);
  const secure = req.secure ? '; Secure' : '';
  res.setHeader('Set-Cookie', `cp_admin=${token}; HttpOnly; SameSite=Strict; Path=/admin; Max-Age=${SESSION_MS / 1000}${secure}`);
  res.redirect('/admin');
});
router.get('/sair', (req, res) => { sessions.delete(cookies(req).cp_admin); res.setHeader('Set-Cookie', 'cp_admin=; Path=/admin; Max-Age=0'); res.redirect('/admin/login'); });

router.use((req, res, next) => (authed(req) ? next() : res.redirect('/admin/login')));

// ---- Dashboard ----
router.get('/', (req, res) => {
  const c = (sql) => db.prepare(sql).get().c;
  const stats = {
    hoje: c("SELECT COUNT(*) c FROM leads WHERE date(created_at)=date('now')"),
    total: c('SELECT COUNT(*) c FROM leads'),
    testes: c("SELECT COUNT(*) c FROM leads WHERE source='teste'"),
    pendentes: c("SELECT COUNT(*) c FROM leads WHERE status='aguardando_teste'"),
    convertidos: c("SELECT COUNT(*) c FROM leads WHERE status='convertido'"),
    sequencia: c('SELECT COUNT(*) c FROM leads WHERE seq_active=1'),
  };
  const recentes = db.prepare('SELECT * FROM leads ORDER BY id DESC LIMIT 10').all();
  const erros = db.prepare("SELECT * FROM message_log WHERE status='erro' ORDER BY id DESC LIMIT 8").all();
  view(res, 'dashboard', { stats, recentes, erros });
});

// ---- Leads ----
router.get('/leads', (req, res) => {
  const { q = '', status = '', tipo = '' } = req.query;
  let sql = 'SELECT * FROM leads WHERE 1=1'; const args = [];
  if (q) { sql += ' AND (name LIKE ? OR email LIKE ? OR whatsapp LIKE ?)'; args.push(`%${q}%`, `%${q}%`, `%${q}%`); }
  if (status) { sql += ' AND status=?'; args.push(status); }
  if (tipo) { sql += ' AND type=?'; args.push(tipo); }
  const leads = db.prepare(sql + ' ORDER BY id DESC LIMIT 500').all(...args);
  view(res, 'leads', { leads, q, status, tipo });
});
router.get('/leads.csv', (req, res) => {
  const rows = db.prepare('SELECT id,created_at,name,email,whatsapp,type,source,status,seq_day,message,utm FROM leads ORDER BY id DESC').all();
  const esc = (v) => `"${String(v ?? '').replace(/"/g, '""')}"`;
  res.type('text/csv').attachment('leads-cinepulse.csv').send('﻿' + [Object.keys(rows[0] || { id: 1 }).join(';'), ...rows.map((r) => Object.values(r).map(esc).join(';'))].join('\n'));
});
router.get('/leads/:id', (req, res) => {
  const lead = db.prepare('SELECT * FROM leads WHERE id=?').get(req.params.id);
  if (!lead) return res.redirect('/admin/leads');
  const logs = db.prepare('SELECT * FROM message_log WHERE lead_id=? ORDER BY id DESC').all(lead.id);
  view(res, 'lead', { lead, logs, msg: req.query.msg || null });
});
router.post('/leads/:id', async (req, res) => {
  const id = Number(req.params.id); const b = req.body;
  const back = (m) => res.redirect(`/admin/leads/${id}?msg=${encodeURIComponent(m)}`);
  if (b.action === 'salvar') {
    db.prepare('UPDATE leads SET name=?, email=?, whatsapp=?, type=?, status=?, test_user=?, test_pass=?, test_info=?, notes=? WHERE id=?')
      .run(b.name, b.email, b.whatsapp, b.type, b.status, b.test_user, b.test_pass, b.test_info, b.notes, id);
    if (b.status === 'convertido' || b.status === 'descadastrado') db.prepare('UPDATE leads SET seq_active=0 WHERE id=?').run(id);
    return back('Salvo.');
  }
  if (b.action === 'enviar_teste') { await trial.deliverTest(id); return back('Dados de acesso enviados (veja o histórico).'); }
  if (b.action === 'gerar_teste') {
    try { const t = await trial.requestTest(db.prepare('SELECT * FROM leads WHERE id=?').get(id)); if (!t) return back('API do painel desativada.'); db.prepare('UPDATE leads SET test_user=?, test_pass=?, test_info=? WHERE id=?').run(t.user, t.pass, t.info, id); await trial.deliverTest(id); return back('Teste gerado e enviado.'); }
    catch (e) { return back('Erro: ' + e.message); }
  }
  if (b.action === 'pausar') { db.prepare('UPDATE leads SET seq_active=0 WHERE id=?').run(id); return back('Sequência pausada.'); }
  if (b.action === 'retomar') { db.prepare("UPDATE leads SET seq_active=1, seq_next_at=datetime('now') WHERE id=?").run(id); return back('Sequência retomada.'); }
  if (b.action === 'excluir') { db.prepare('DELETE FROM message_log WHERE lead_id=?').run(id); db.prepare('DELETE FROM leads WHERE id=?').run(id); return res.redirect('/admin/leads'); }
  back('Nada feito.');
});

// ---- Planos ----
router.get('/planos', (req, res) => view(res, 'plans', { plans: db.prepare('SELECT * FROM plans ORDER BY category, sort').all(), msg: req.query.msg || null }));
router.get('/planos/novo', (req, res) => view(res, 'plan', { plan: { id: '', category: 'revenda', active: 1, highlight: 0, sort: 99 } }));
router.get('/planos/:id', (req, res) => view(res, 'plan', { plan: db.prepare('SELECT * FROM plans WHERE id=?').get(req.params.id) }));
router.post('/planos/:id', (req, res) => {
  const b = req.body;
  const slug = String(b.slug || b.name).toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
  const vals = [b.category, slug, b.name, b.credits || null, Number(String(b.price).replace(',', '.')) || 0, b.period, b.short, b.audience || null, b.features, b.highlight ? 1 : 0, Number(b.sort) || 0, b.active ? 1 : 0];
  if (b.action === 'excluir') db.prepare('DELETE FROM plans WHERE id=?').run(req.params.id);
  else if (req.params.id === 'novo') db.prepare('INSERT INTO plans(category,slug,name,credits,price,period,short,audience,features,highlight,sort,active) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)').run(...vals);
  else db.prepare('UPDATE plans SET category=?,slug=?,name=?,credits=?,price=?,period=?,short=?,audience=?,features=?,highlight=?,sort=?,active=? WHERE id=?').run(...vals, req.params.id);
  res.redirect('/admin/planos?msg=Salvo');
});

// ---- Sequência de e-mails ----
router.get('/emails', (req, res) => view(res, 'emails', { emails: db.prepare('SELECT * FROM email_sequence ORDER BY day, audience').all(), msg: req.query.msg || null }));
router.get('/emails/:id', (req, res) => view(res, 'email', { email: req.params.id === 'novo' ? { id: 'novo', day: 31, audience: 'todos', subject: '', body: '', whatsapp: '', active: 1 } : db.prepare('SELECT * FROM email_sequence WHERE id=?').get(req.params.id), msg: req.query.msg || null }));
router.post('/emails/:id', async (req, res) => {
  const b = req.body; const vals = [Number(b.day) || 1, b.audience, b.subject, b.body, b.whatsapp || null, b.active ? 1 : 0];
  let id = req.params.id;
  if (b.action === 'excluir') { db.prepare('DELETE FROM email_sequence WHERE id=?').run(id); return res.redirect('/admin/emails?msg=Excluído'); }
  if (id === 'novo') id = db.prepare('INSERT INTO email_sequence(day,audience,subject,body,whatsapp,active) VALUES(?,?,?,?,?,?)').run(...vals).lastInsertRowid;
  else db.prepare('UPDATE email_sequence SET day=?,audience=?,subject=?,body=?,whatsapp=?,active=? WHERE id=?').run(...vals, id);
  if (b.action === 'testar' && b.test_to) {
    const ok = await sendEmail({ id: null, name: 'Carlos', email: b.test_to, unsub_token: null }, b.subject, b.body, 'teste_admin');
    return res.redirect(`/admin/emails/${id}?msg=${ok ? 'E-mail de teste enviado' : 'Falha ao enviar (veja SMTP em Configurações)'}`);
  }
  res.redirect('/admin/emails?msg=Salvo');
});
router.get('/emails/:id/preview', (req, res) => {
  const e = db.prepare('SELECT * FROM email_sequence WHERE id=?').get(req.params.id);
  const { emailLayout, fill, vars } = require('./services/messaging');
  const s = getSettings(); const lead = { name: 'Carlos Exemplo', unsub_token: 'exemplo' };
  res.send(emailLayout(s, lead, fill(e.body, vars(s, lead))));
});

// ---- SEO ----
router.get('/seo', (req, res) => view(res, 'seo', { pages: db.prepare('SELECT * FROM seo ORDER BY path').all(), msg: req.query.msg || null }));
router.post('/seo', (req, res) => {
  const b = req.body; const p = String(b.path || '').trim();
  if (p.startsWith('/')) {
    if (b.action === 'excluir') db.prepare('DELETE FROM seo WHERE path=?').run(p);
    else db.prepare('INSERT INTO seo(path,title,description,h1,keywords,wa) VALUES(?,?,?,?,?,?) ON CONFLICT(path) DO UPDATE SET title=excluded.title, description=excluded.description, h1=excluded.h1, keywords=excluded.keywords, wa=excluded.wa').run(p, b.title, b.description, b.h1, b.keywords, b.wa);
  }
  res.redirect('/admin/seo?msg=Salvo');
});

// ---- Configurações ----
const SETTING_KEYS = ['site_url', 'whatsapp_number', 'support_email', 'trailer_youtube', 'trailer_title', 'popup_enabled', 'popup_title', 'popup_text', 'sites_revenda_url',
  'gtm_id', 'ga4_id', 'gads_id', 'gads_label_lead', 'gads_label_whatsapp', 'meta_pixel_id', 'google_site_verification', 'extra_head',
  'smtp_host', 'smtp_port', 'smtp_secure', 'smtp_user', 'mail_from', 'wa_provider', 'wa_url', 'wa_instance',
  'sigma_enabled', 'sigma_url', 'sigma_method', 'sigma_headers', 'sigma_body', 'sigma_path_user', 'sigma_path_pass', 'sigma_path_message',
  'test_email_subject', 'test_message', 'test_pending_message', 'seq_send_hour_start', 'seq_send_hour_end',
  'tv_instructions', 'tutorial_enabled', 'tutorial_delay_sec', 'tutorial_video_path', 'tutorial_caption', 'tutorial_email_subject', 'followup_enabled', 'followup_hours', 'followup_message'];
const SECRET_KEYS = ['smtp_pass', 'wa_token', 'wa_client_token'];
const CHECKBOXES = ['popup_enabled', 'smtp_secure', 'sigma_enabled', 'tutorial_enabled', 'followup_enabled'];

router.get('/configuracoes', (req, res) => view(res, 'settings', { msg: req.query.msg || null }));
router.post('/configuracoes', async (req, res) => {
  const b = req.body;
  for (const k of SETTING_KEYS) {
    if (CHECKBOXES.includes(k)) setSetting(k, b[k] ? '1' : '0');
    else if (k in b) setSetting(k, String(b[k]).trim());
  }
  for (const k of SECRET_KEYS) if (b[k]) setSetting(k, b[k].trim()); // vazio = mantém o atual
  if (b.new_password) {
    if (b.new_password.length < 10) return res.redirect('/admin/configuracoes?msg=' + encodeURIComponent('A nova senha precisa de 10+ caracteres'));
    setSetting('admin_password_hash', hashPassword(b.new_password));
  }
  let msg = 'Configurações salvas.';
  if (b.action === 'testar_email' && b.test_email) msg = (await sendEmail({ id: null, name: 'Teste', email: b.test_email }, 'Teste de envio Cine Pulse', '<p>Se você recebeu, o SMTP está funcionando. ✅</p>', 'teste_smtp')) ? 'E-mail de teste enviado.' : 'Falha no e-mail: veja o log no painel inicial.';
  if (b.action === 'testar_whatsapp' && b.test_whatsapp) msg = (await sendWhatsApp({ id: null, name: 'Teste', whatsapp: b.test_whatsapp }, 'Teste de envio Cine Pulse ✅', 'teste_wa')) ? 'WhatsApp de teste enviado.' : 'Falha no WhatsApp: veja o log no painel inicial.';
  if (b.action === 'testar_painel') {
    try { const t = await trial.requestTest({ name: 'Teste Site', email: 'teste@cinepulse.com.br', whatsapp: b.test_whatsapp || '5567999999999' }); msg = t ? `Painel respondeu. Usuário: ${t.user || '(não identificado)'} · Senha: ${t.pass || '(não identificada)'} · Mensagem: ${(t.info || '').slice(0, 300)}` : 'API do painel desativada.'; }
    catch (e) { msg = 'Erro no painel: ' + e.message; }
  }
  if (b.action === 'rodar_sequencia') { await scheduler.tick(); msg = 'Fila da sequência processada.'; }
  res.redirect('/admin/configuracoes?msg=' + encodeURIComponent(msg));
});

module.exports = router;
