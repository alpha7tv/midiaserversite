# Cine Pulse — site + painel admin

Node 22 (SQLite nativo) + Express + EJS + Nodemailer. Sem build.

- Rodar local: `npm install && ADMIN_PASSWORD=algumasenha npm start` → http://localhost:3000 e /admin
- Produção (VPS): `bash deploy/install.sh check` (só inspeciona) e `bash deploy/install.sh install` (Docker em 127.0.0.1 + vhost nginx + certbot). Dados ficam no volume `cinepulse-data`.
- Backup do banco: `docker cp cinepulse-web:/app/data/cinepulse.db ./backup.db`

Admin: leads (testes e contatos, exportar CSV), planos, sequência de 30 e-mails (+ WhatsApp no dia 30), SEO por página, configurações (trailer, popup, GTM/GA4/Ads/Pixel, SMTP Brevo, Evolution API, webhook Sigma).

Fluxo do teste: formulário → webhook do painel Sigma → usuário/senha enviados por e-mail e WhatsApp → sequência de 1 e-mail/dia (9h–20h, Brasília) até o cliente ser marcado como "convertido" ou se descadastrar.
