const nodemailer = require('nodemailer');
const { db, getSettings } = require('../db');

const fill = (tpl, vars) => String(tpl || '').replace(/\{\{\s*(\w+)\s*\}\}/g, (_, k) => (vars[k] ?? ''));
const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

function log(leadId, channel, kind, status, error) {
  db.prepare('INSERT INTO message_log(lead_id,channel,kind,status,error) VALUES(?,?,?,?,?)').run(leadId ?? null, channel, kind, status, error ? String(error).slice(0, 500) : null);
}

function waLink(s, text) {
  return `https://wa.me/${(s.whatsapp_number || '').replace(/\D/g, '')}${text ? '?text=' + encodeURIComponent(text) : ''}`;
}

function vars(s, lead) {
  const u = s.site_url.replace(/\/$/, '');
  const first = String(lead?.name || '').trim().split(/\s+/)[0] || 'tudo bem';
  return {
    nome: first, email: lead?.email || '', whatsapp_cliente: lead?.whatsapp || '',
    site: u, whatsapp: waLink(s, 'Olá! Recebi o e-mail da Cine Pulse e quero falar com vocês.'),
    planos: u + '/planos', revenda: u + '/revenda-iptv', apontamento: u + '/apontamento-iptv', franquia: u + '/franquia-iptv',
    teste: u + '/teste-gratis', sites_revenda: s.sites_revenda_url,
  };
}

function emailLayout(s, lead, bodyHtml, preheader = '') {
  const u = s.site_url.replace(/\/$/, '');
  const unsub = lead?.unsub_token ? `${u}/descadastrar/${lead.unsub_token}` : `${u}/contato`;
  return `<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Cine Pulse</title></head>
<body style="margin:0;background:#0b0b0b;font-family:Arial,Helvetica,sans-serif;color:#e8e8e8">
<span style="display:none;max-height:0;overflow:hidden">${esc(preheader)}</span>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#0b0b0b"><tr><td align="center" style="padding:24px 12px">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#141414;border-radius:12px;overflow:hidden;border:1px solid #2a2a2a">
<tr><td align="center" style="background:#000;padding:22px;border-bottom:3px solid #e50914"><a href="${u}"><img src="${u}/img/logo.png" width="280" alt="Cine Pulse" style="display:block;max-width:280px;height:auto;border:0"></a></td></tr>
<tr><td style="padding:28px 28px 8px;font-size:16px;line-height:1.6;color:#e8e8e8">${bodyHtml}</td></tr>
<tr><td style="padding:18px 28px 26px;font-size:12px;color:#8a8a8a;border-top:1px solid #2a2a2a">
Cine Pulse · Suporte 24 horas · <a href="${waLink(s)}" style="color:#ff6a00">WhatsApp</a><br>
Você recebeu este e-mail porque pediu um teste em ${esc(u.replace(/^https?:\/\//, ''))}. <a href="${unsub}" style="color:#8a8a8a">Não quero mais receber</a>.
</td></tr></table></td></tr></table></body></html>`;
}

let transporter = null, transporterKey = '';
function getTransporter(s) {
  const key = [s.smtp_host, s.smtp_port, s.smtp_secure, s.smtp_user, s.smtp_pass].join('|');
  if (!s.smtp_host || !s.smtp_user || !s.smtp_pass) return null;
  if (key !== transporterKey) {
    transporter = nodemailer.createTransport({ host: s.smtp_host, port: Number(s.smtp_port) || 587, secure: s.smtp_secure === '1', auth: { user: s.smtp_user, pass: s.smtp_pass } });
    transporterKey = key;
  }
  return transporter;
}

async function sendEmail(lead, subject, html, kind = 'email') {
  const s = getSettings();
  const t = getTransporter(s);
  if (!t || !lead.email) { log(lead.id, 'email', kind, 'skip', !t ? 'SMTP não configurado' : 'sem e-mail'); return false; }
  const v = vars(s, lead);
  try {
    const u = s.site_url.replace(/\/$/, '');
    await t.sendMail({
      from: s.mail_from || s.smtp_user, to: lead.email, subject: fill(subject, v), html: emailLayout(s, lead, fill(html, v)),
      headers: lead.unsub_token ? { 'List-Unsubscribe': `<${u}/descadastrar/${lead.unsub_token}>` } : {},
    });
    log(lead.id, 'email', kind, 'ok');
    return true;
  } catch (e) { log(lead.id, 'email', kind, 'erro', e.message); return false; }
}

function normalizePhone(p) {
  let d = String(p || '').replace(/\D/g, '');
  if (d.length === 10 || d.length === 11) d = '55' + d;
  return d;
}

async function sendWhatsApp(lead, text, kind = 'whatsapp') {
  const s = getSettings();
  const phone = normalizePhone(lead.whatsapp);
  if (s.wa_provider === 'off' || !s.wa_url || !phone) { log(lead.id, 'whatsapp', kind, 'skip', 'API de WhatsApp não configurada'); return false; }
  const message = fill(text, vars(s, lead));
  const base = s.wa_url.replace(/\/$/, '');
  let url, body, headers = { 'Content-Type': 'application/json' };
  if (s.wa_provider === 'evolution') { url = `${base}/message/sendText/${s.wa_instance}`; headers.apikey = s.wa_token; body = { number: phone, text: message }; }
  else if (s.wa_provider === 'zapi') { url = `${base}/instances/${s.wa_instance}/token/${s.wa_token}/send-text`; if (s.wa_client_token) headers['Client-Token'] = s.wa_client_token; body = { phone, message }; }
  else { url = base; if (s.wa_token) headers.Authorization = `Bearer ${s.wa_token}`; body = { phone, number: phone, message, text: message }; }
  try {
    const r = await fetch(url, { method: 'POST', headers, body: JSON.stringify(body), signal: AbortSignal.timeout(20000) });
    if (!r.ok) throw new Error(`HTTP ${r.status}: ${(await r.text()).slice(0, 200)}`);
    log(lead.id, 'whatsapp', kind, 'ok');
    return true;
  } catch (e) { log(lead.id, 'whatsapp', kind, 'erro', e.message); return false; }
}

async function sendWhatsAppVideo(lead, videoUrl, caption, kind = 'tutorial') {
  const s = getSettings();
  const phone = normalizePhone(lead.whatsapp);
  if (s.wa_provider === 'off' || !s.wa_url || !phone) { log(lead.id, 'whatsapp', kind, 'skip', 'API de WhatsApp não configurada'); return false; }
  const text = fill(caption, vars(s, lead));
  if (s.wa_provider === 'evolution') {
    const url = `${s.wa_url.replace(/\/$/, '')}/message/sendMedia/${s.wa_instance}`;
    try {
      const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', apikey: s.wa_token },
        body: JSON.stringify({ number: phone, mediatype: 'video', mimetype: 'video/mp4', caption: text, media: videoUrl, fileName: 'tutorial-cinepulse.mp4' }), signal: AbortSignal.timeout(60000) });
      if (!r.ok) throw new Error(`HTTP ${r.status}: ${(await r.text()).slice(0, 200)}`);
      log(lead.id, 'whatsapp', kind, 'ok');
      return true;
    } catch (e) { log(lead.id, 'whatsapp', kind + '_video', 'erro', e.message); }
  }
  // outros provedores ou falha no vídeo: envia o link
  return sendWhatsApp(lead, `${text}\n\n▶️ Assista: ${videoUrl}`, kind);
}

module.exports = { sendWhatsAppVideo, fill, esc, vars, waLink, sendEmail, sendWhatsApp, normalizePhone, emailLayout, log };
