// Geração automática de teste via webhook do painel (Sigma "chatbot") + envio por e-mail e WhatsApp.
const { db, getSettings } = require('../db');
const { fill, esc, sendEmail, sendWhatsApp, sendWhatsAppVideo, normalizePhone, log } = require('./messaging');

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
  const text = fill(s.test_message, v) + (s.tv_instructions ? '\n\n' + fill(s.tv_instructions, v) : '');
  const html = `<p>${esc(text).replace(/\n/g, '<br>')}</p><p style="text-align:center;margin:28px 0"><a href="{{whatsapp}}" style="background:linear-gradient(90deg,#e50914,#ff6a00);color:#fff;text-decoration:none;font-weight:700;padding:14px 28px;border-radius:8px;display:inline-block">Preciso de ajuda para instalar</a></p>`;
  await Promise.all([sendEmail(lead, s.test_email_subject, html, 'teste'), sendWhatsApp(lead, text, 'teste')]);
  db.prepare("UPDATE leads SET status='teste_enviado', test_sent_at=datetime('now') WHERE id=?").run(leadId);
  scheduleAfterTest(leadId, s);
  startSequence(leadId);
}

function addJob(leadId, kind, seconds) {
  db.prepare('DELETE FROM jobs WHERE lead_id=? AND kind=? AND done=0').run(leadId, kind);
  db.prepare(`INSERT INTO jobs(lead_id,kind,run_at) VALUES(?,?,datetime('now','+${Math.max(0, Math.round(seconds))} seconds'))`).run(leadId, kind);
}
function scheduleAfterTest(leadId, s) {
  if (s.tutorial_enabled === '1') addJob(leadId, 'tutorial', Number(s.tutorial_delay_sec) || 30);
  if (s.followup_enabled === '1') addJob(leadId, 'followup', (Number(s.followup_hours) || 4) * 3600);
}

// Executa tarefas agendadas (tutorial 30s depois, acompanhamento 4h depois)
async function runJobs() {
  const due = db.prepare("SELECT * FROM jobs WHERE done=0 AND run_at <= datetime('now') ORDER BY run_at LIMIT 20").all();
  for (const j of due) {
    db.prepare('UPDATE jobs SET done=1 WHERE id=?').run(j.id);
    const lead = db.prepare('SELECT * FROM leads WHERE id=?').get(j.lead_id);
    if (!lead || lead.status === 'descadastrado') continue;
    const s = getSettings();
    const base = s.site_url.replace(/\/$/, '');
    const v = { nome: (lead.name || '').split(' ')[0] };
    try {
      if (j.kind === 'tutorial') {
        const videoUrl = base + (s.tutorial_video_path || '/video/tutorial-media-station.mp4');
        const html = `<p>Olá, ${esc(v.nome)}!</p><p>Preparamos um vídeo rápido mostrando como instalar a Cine Pulse na <b>Smart TV LG ou Samsung</b>:</p>
<ol><li>Baixe na loja da TV o aplicativo <b>Media Station</b></li><li>Abra o app e clique em <b>Start Parameter</b></li><li>Digite o endereço <b>appcinevs.site</b></li><li>Entre com o usuário e a senha do seu teste</li></ol>
<p style="text-align:center;margin:24px 0"><a href="${base}/tutorial-smart-tv"><img src="${base}/video/tutorial-poster.jpg" width="480" alt="Assistir ao tutorial" style="max-width:100%;border-radius:8px;border:0"></a></p>
<p style="text-align:center;margin:20px 0"><a href="${base}/tutorial-smart-tv" style="background:linear-gradient(90deg,#e50914,#ff6a00);color:#fff;text-decoration:none;font-weight:700;padding:14px 28px;border-radius:8px;display:inline-block">▶ Assistir ao tutorial</a></p>`;
        await Promise.all([sendWhatsAppVideo(lead, videoUrl, s.tutorial_caption, 'tutorial'), sendEmail(lead, s.tutorial_email_subject, html, 'tutorial')]);
      } else if (j.kind === 'followup') {
        if (lead.status === 'convertido') continue;
        const h = Number(new Intl.DateTimeFormat('pt-BR', { timeZone: 'America/Sao_Paulo', hour: '2-digit', hour12: false }).format(new Date()));
        if (h < 8 || h >= 22) { db.prepare("UPDATE jobs SET done=0, run_at=datetime('now','+30 minutes') WHERE id=?").run(j.id); continue; } // não chama de madrugada
        await sendWhatsApp(lead, fill(s.followup_message, v), 'acompanhamento_4h');
      }
    } catch (e) { log(lead.id, 'job', j.kind, 'erro', e.message); }
  }
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

module.exports = { processNewTrial, deliverTest, requestTest, parseCreds, runJobs };
