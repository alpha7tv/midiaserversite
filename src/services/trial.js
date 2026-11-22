// Geração automática de teste via webhook do painel (Sigma "chatbot") + envio por e-mail e WhatsApp.
const { db, getSettings } = require('../db');
const { fill, esc, sendEmail, sendWhatsApp, normalizePhone, log } = require('./messaging');

const getPath = (obj, p) => (p ? p.split('.').reduce((o, k) => (o == null ? undefined : o[k]), obj) : undefined);
const jsonEsc = (v) => JSON.stringify(String(v ?? '')).slice(1, -1);

function parseCreds(text) {
  const t = String(text || '');
  const user = t.match(/(?:usu[aá]rio|user(?:name)?|login)\s*[:=]\s*\*?\s*([^\s*]+)/i);
  const pass = t.match(/(?:senha|pass(?:word)?)\s*[:=]\s*\*?\s*([^\s*]+)/i);
  return { user: user?.[1] || null, pass: pass?.[1] || null };
}

async function requestTest(lead) {
  const s = getSettings();
  if (s.sigma_enabled !== '1' || !s.sigma_url) return null;
  const v = { nome: lead.name || '', email: lead.email || '', whatsapp: normalizePhone(lead.whatsapp) };
  const body = String(s.sigma_body || '').replace(/\{\{\s*(\w+)\s*\}\}/g, (_, k) => jsonEsc(v[k]));
  let headers = {};
  try { headers = JSON.parse(s.sigma_headers || '{}'); } catch { headers = { 'Content-Type': 'application/json' }; }
  const r = await fetch(s.sigma_url, { method: s.sigma_method || 'POST', headers, body: s.sigma_method === 'GET' ? undefined : body, signal: AbortSignal.timeout(25000) });
  const raw = await r.text();
  if (!r.ok) throw new Error(`Painel respondeu HTTP ${r.status}: ${raw.slice(0, 200)}`);
  let data; try { data = JSON.parse(raw); } catch { data = null; }
  let message = data ? getPath(data, s.sigma_path_message) : raw;
  if (!message && data) message = getPath(data, 'replies.0.message') || getPath(data, 'data.0.message') || getPath(data, 'message') || getPath(data, 'reply');
  let user = data ? getPath(data, s.sigma_path_user) : null;
  let pass = data ? getPath(data, s.sigma_path_pass) : null;
  if ((!user || !pass) && message) { const c = parseCreds(message); user = user || c.user; pass = pass || c.pass; }
  if (!user && !message) throw new Error('Resposta do painel sem dados de acesso: ' + raw.slice(0, 200));
  return { user: user || '', pass: pass || '', info: typeof message === 'string' ? message : '' };
}

function startSequence(leadId) {
  // primeiro e-mail da sequência 24h depois do teste
  db.prepare("UPDATE leads SET seq_active=1, seq_day=0, seq_next_at=datetime('now','+1 day') WHERE id=? AND consent=1 AND status NOT IN ('convertido','descadastrado')").run(leadId);
}

async function deliverTest(leadId) {
  const s = getSettings();
  const lead = db.prepare('SELECT * FROM leads WHERE id=?').get(leadId);
  if (!lead) return;
  const dados = lead.test_info && lead.test_info.trim() ? lead.test_info.trim() : `Usuário: ${lead.test_user || ''}\nSenha: ${lead.test_pass || ''}`;
  const v = { nome: (lead.name || '').split(' ')[0], usuario: lead.test_user || '', senha: lead.test_pass || '', info: lead.test_info || '', dados };
  const text = fill(s.test_message, v);
  const html = `<p>${esc(text).replace(/\n/g, '<br>')}</p><p style="text-align:center;margin:28px 0"><a href="{{whatsapp}}" style="background:linear-gradient(90deg,#e50914,#ff6a00);color:#fff;text-decoration:none;font-weight:700;padding:14px 28px;border-radius:8px;display:inline-block">Preciso de ajuda para instalar</a></p>`;
  await Promise.all([sendEmail(lead, s.test_email_subject, html, 'teste'), sendWhatsApp(lead, text, 'teste')]);
  db.prepare("UPDATE leads SET status='teste_enviado', test_sent_at=datetime('now') WHERE id=?").run(leadId);
  startSequence(leadId);
}

async function processNewTrial(leadId) {
  const s = getSettings();
  const lead = db.prepare('SELECT * FROM leads WHERE id=?').get(leadId);
  try {
    const t = await requestTest(lead);
    if (t) {
      db.prepare('UPDATE leads SET test_user=?, test_pass=?, test_info=? WHERE id=?').run(t.user, t.pass, t.info, leadId);
      log(leadId, 'painel', 'teste', 'ok');
      return deliverTest(leadId);
    }
  } catch (e) { log(leadId, 'painel', 'teste', 'erro', e.message); }
  // sem API ou falhou: avisa o cliente e deixa pendente no admin
  db.prepare("UPDATE leads SET status='aguardando_teste' WHERE id=?").run(leadId);
  await sendWhatsApp(lead, fill(s.test_pending_message, { nome: (lead.name || '').split(' ')[0] }), 'teste_pendente');
  startSequence(leadId);
}

module.exports = { processNewTrial, deliverTest, requestTest, parseCreds };
