// Envia 1 e-mail por dia da sequência para leads que testaram e não fecharam.
const { db, getSettings } = require('../db');
const { sendEmail, sendWhatsApp } = require('./messaging');

function brHour() {
  return Number(new Intl.DateTimeFormat('pt-BR', { timeZone: 'America/Sao_Paulo', hour: '2-digit', hour12: false }).format(new Date()));
}
const audienceOf = (type) => (type === 'cliente' ? 'cliente' : 'revenda');

let running = false;
async function tick() {
  if (running) return; running = true;
  try {
    const s = getSettings();
    const h = brHour();
    if (h < Number(s.seq_send_hour_start || 9) || h >= Number(s.seq_send_hour_end || 20)) return;
    const due = db.prepare("SELECT * FROM leads WHERE seq_active=1 AND seq_next_at <= datetime('now') AND status NOT IN ('convertido','descadastrado') AND email IS NOT NULL LIMIT 50").all();
    const maxDay = db.prepare('SELECT MAX(day) m FROM email_sequence WHERE active=1').get().m || 0;
    for (const lead of due) {
      const day = lead.seq_day + 1;
      if (day > maxDay) { db.prepare('UPDATE leads SET seq_active=0 WHERE id=?').run(lead.id); continue; }
      const aud = audienceOf(lead.type);
      const mail = db.prepare("SELECT * FROM email_sequence WHERE day=? AND active=1 AND audience IN ('todos', ?) ORDER BY audience='todos' LIMIT 1").get(day, aud);
      if (mail) {
        await sendEmail(lead, mail.subject, mail.body, `seq_dia_${day}`);
        if (mail.whatsapp) await sendWhatsApp(lead, mail.whatsapp, `seq_dia_${day}`);
      }
      db.prepare("UPDATE leads SET seq_day=?, seq_next_at=datetime('now','+1 day'), seq_active=? WHERE id=?").run(day, day >= maxDay ? 0 : 1, lead.id);
    }
  } catch (e) { console.error('scheduler', e); }
  finally { running = false; }
}

let jobsRunning = false;
async function jobsTick() {
  if (jobsRunning) return; jobsRunning = true;
  try { await require('./trial').runJobs(); } catch (e) { console.error('jobs', e); } finally { jobsRunning = false; }
}
function start() { setInterval(tick, 10 * 60 * 1000); setTimeout(tick, 15000); setInterval(jobsTick, 10 * 1000); }
module.exports = { start, tick, jobsTick };
