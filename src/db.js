const path = require('path');
const fs = require('fs');
const crypto = require('crypto');
const { DatabaseSync } = require('node:sqlite');

const DATA_DIR = process.env.DATA_DIR || path.join(__dirname, '..', 'data');
fs.mkdirSync(DATA_DIR, { recursive: true });
const db = new DatabaseSync(path.join(DATA_DIR, 'cinepulse.db'));
db.exec('PRAGMA journal_mode = WAL; PRAGMA foreign_keys = ON;');

db.exec(`
CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT);
CREATE TABLE IF NOT EXISTS plans (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  category TEXT NOT NULL, slug TEXT NOT NULL, name TEXT NOT NULL,
  credits TEXT, price REAL NOT NULL, period TEXT, short TEXT, audience TEXT,
  features TEXT, highlight INTEGER DEFAULT 0, sort INTEGER DEFAULT 0, active INTEGER DEFAULT 1,
  UNIQUE(category, slug)
);
CREATE TABLE IF NOT EXISTS leads (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  created_at TEXT DEFAULT (datetime('now')),
  name TEXT, email TEXT, whatsapp TEXT,
  type TEXT DEFAULT 'cliente', source TEXT, message TEXT,
  status TEXT DEFAULT 'novo',
  test_user TEXT, test_pass TEXT, test_info TEXT, test_sent_at TEXT,
  seq_day INTEGER DEFAULT 0, seq_next_at TEXT, seq_active INTEGER DEFAULT 0,
  unsub_token TEXT, consent INTEGER DEFAULT 0, utm TEXT, ip TEXT, notes TEXT
);
CREATE INDEX IF NOT EXISTS idx_leads_email ON leads(email);
CREATE INDEX IF NOT EXISTS idx_leads_seq ON leads(seq_active, seq_next_at);
CREATE TABLE IF NOT EXISTS email_sequence (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  day INTEGER NOT NULL, audience TEXT DEFAULT 'todos',
  subject TEXT NOT NULL, body TEXT NOT NULL, whatsapp TEXT, active INTEGER DEFAULT 1
);
CREATE TABLE IF NOT EXISTS message_log (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  lead_id INTEGER, channel TEXT, kind TEXT, status TEXT, error TEXT,
  created_at TEXT DEFAULT (datetime('now'))
);
CREATE TABLE IF NOT EXISTS seo (path TEXT PRIMARY KEY, title TEXT, description TEXT, h1 TEXT, keywords TEXT, wa TEXT);
`);

const DEFAULT_SETTINGS = {
  site_url: process.env.SITE_URL || 'https://cinepulse.com.br',
  site_name: 'Cine Pulse',
  whatsapp_number: '5567996971038',
  support_email: 'contato@cinepulse.com.br',
  trailer_youtube: '',
  trailer_title: 'Trailer oficial',
  popup_enabled: '1',
  popup_title: 'Apontamento para o seu painel Sigma',
  popup_text: 'Aponte o servidor Cine Pulse para o seu painel a partir de R$ 250,00 mensais. DNS exclusiva e suporte 24h direto com o dono.',
  sites_revenda_url: 'https://sites.midiaserver.com.br/',
  // Rastreamento
  gtm_id: '', ga4_id: '', gads_id: '', gads_label_lead: '', gads_label_whatsapp: '', meta_pixel_id: '',
  google_site_verification: '', extra_head: '',
  // E-mail
  smtp_host: 'smtp-relay.brevo.com', smtp_port: '587', smtp_secure: '0', smtp_user: '550089001@smtp-brevo.com', smtp_pass: '', mail_from: 'Cine Pulse <contato@cinepulse.com.br>',
  // WhatsApp API
  wa_provider: 'evolution', wa_url: '', wa_token: '', wa_instance: '', wa_client_token: '',
  // Teste automático (API do painel)
  // Webhook "chatbot" do painel Sigma (formato AutoResponder). Ajustável no admin.
  sigma_enabled: '1', sigma_url: process.env.SIGMA_URL || '', sigma_method: 'POST',
  sigma_headers: '{"Content-Type":"application/json"}',
  sigma_body: '{"appPackageName":"com.whatsapp","messengerPackageName":"com.whatsapp","query":{"sender":"{{whatsapp}}","message":"teste","isGroup":false,"groupParticipant":"","ruleId":1,"isTestMessage":false}}',
  sigma_path_user: '', sigma_path_pass: '', sigma_path_message: 'replies.0.message',
  test_email_subject: '🎬 Seu teste Cine Pulse chegou, {{nome}}!',
  test_message: 'Olá {{nome}}! Seu teste Cine Pulse está liberado 🎬\n\n{{dados}}\n\nPrecisa de ajuda para instalar? Responda esta mensagem. Suporte 24h.',
  test_pending_message: 'Olá {{nome}}! Recebemos seu pedido de teste na Cine Pulse 🎬 Em instantes nossa equipe envia seus dados de acesso por aqui.',
  seq_send_hour_start: '9', seq_send_hour_end: '20',
  admin_password_hash: '',
};

function getSettings() {
  const rows = db.prepare('SELECT key, value FROM settings').all();
  const s = { ...DEFAULT_SETTINGS };
  for (const r of rows) s[r.key] = r.value;
  return s;
}
function setSetting(key, value) {
  db.prepare('INSERT INTO settings(key,value) VALUES(?,?) ON CONFLICT(key) DO UPDATE SET value=excluded.value').run(key, String(value ?? ''));
}

function hashPassword(pw) {
  const salt = crypto.randomBytes(16).toString('hex');
  return salt + ':' + crypto.scryptSync(pw, salt, 64).toString('hex');
}
function checkPassword(pw, stored) {
  if (!stored) return false;
  const [salt, hash] = stored.split(':');
  const h = crypto.scryptSync(pw, salt, 64);
  return crypto.timingSafeEqual(h, Buffer.from(hash, 'hex'));
}

function seed() {
  if (!db.prepare('SELECT COUNT(*) c FROM plans').get().c) {
    const ins = db.prepare('INSERT INTO plans(category,slug,name,credits,price,period,short,audience,features,highlight,sort) VALUES(?,?,?,?,?,?,?,?,?,?,?)');
    for (const p of require('./seed/plans')) ins.run(p.category, p.slug, p.name, p.credits || null, p.price, p.period, p.short, p.audience || null, p.features, p.highlight, p.sort);
  }
  if (!db.prepare('SELECT COUNT(*) c FROM email_sequence').get().c) {
    const ins = db.prepare('INSERT INTO email_sequence(day,audience,subject,body,whatsapp) VALUES(?,?,?,?,?)');
    for (const e of require('./seed/emails')) ins.run(e.day, e.audience, e.subject, e.body, e.whatsapp || null);
  }
  const seo = require('./seed/seo');
  const insSeo = db.prepare('INSERT OR IGNORE INTO seo(path,title,description,h1,keywords,wa) VALUES(?,?,?,?,?,?)');
  for (const [p, v] of Object.entries(seo)) insSeo.run(p, v.title, v.description, v.h1, [v.primary, ...v.secondary].filter(Boolean).join(', '), v.wa);
  const s = getSettings();
  if (!s.admin_password_hash) {
    const pw = process.env.ADMIN_PASSWORD || crypto.randomBytes(9).toString('base64url');
    setSetting('admin_password_hash', hashPassword(pw));
    if (!process.env.ADMIN_PASSWORD) console.log(`\n*** Senha inicial do admin: ${pw} (troque em /admin/configuracoes) ***\n`);
  }
  if (process.env.SITE_URL && !db.prepare("SELECT 1 FROM settings WHERE key='site_url'").get()) setSetting('site_url', process.env.SITE_URL);
}
seed();

module.exports = { db, getSettings, setSetting, hashPassword, checkPassword };
