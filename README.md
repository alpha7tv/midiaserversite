# Portal Mídia Server

Portal comercial único da Mídia Server: hospedagem, revenda, streaming, web rádio, sites para rádio,
automação (Mídia Rádio Studio), conteúdos para rádios, downloads, diretório de rádios e blog.
A contratação é feita no **WHMCS** (`cliente-area.midiaserver.com.br`); este portal apresenta os produtos e
leva cada botão ao carrinho correto.

## Pilha
PHP 8.1+ (sem framework), MySQL/MariaDB, nginx + PHP-FPM. Nenhuma dependência do Composer. Sem build de front-end.

## Regras do projeto
- **Planos e preços**: ficam na tabela `plans`, copiados da vitrine do WHMCS (`database/seeds/catalog.php`). O `whmcs_pid` é o ID real do WHMCS; o portal nunca inventa IDs.
- **WhatsApp oficial** único em `settings.whatsapp_number` (`5514988159045`).
- Sem depoimentos, nem números não comprovados (anos, uptime, nº de clientes).
- Não altera WHMCS, Studio, Conteúdos, Nuvem nem Rádios do Brasil: apenas integra.

## Estrutura
```
public/            index.php (front controller), assets/ (css, js, fonts, img, og)
app/Core/          Env, Db, Settings, Whmcs, Seo, View, Logger
app/Services/      Catalog, Search
app/Controllers/   PageController
config/            pages.php (conteúdo e SEO de cada página), menu.php, site_models.php
views/             layout, partials, pages, legal
database/          Schema.php (MySQL/SQLite), seeds/ (catálogo e downloads)
bin/console        migrate | seed | admin:create | sitemap
deploy/            install.sh, update.sh, backup.sh, nginx.vhost.tpl
tools/             make_brand.py (logo), make_og.py (imagens de compartilhamento), shot.js (capturas)
tests/             smoke.php (teste de fumaça), router.php (servidor local)
```

## Desenvolvimento local
```bash
cp .env.example .env            # DB_DRIVER=sqlite, APP_URL=http://127.0.0.1:8081
php bin/console migrate && php bin/console seed
php -S 127.0.0.1:8081 -t public tests/router.php
php tests/smoke.php http://127.0.0.1:8081   # páginas, SEO, links, WHMCS, landings, assets
php tests/lead.php  http://127.0.0.1:8081   # formulário de lead e anti-spam
node tools/perf.js http://127.0.0.1:8081/   # LCP/CLS em perfil de celular
```

## Instalação na VPS (staging)
Veja `deploy/install.sh`. Roda como usuário comum, sem sudo, e só mexe no vhost do domínio de teste.

## Painel administrativo (/admin)
Login com e-mail e senha (Argon2id), sessão Secure/HttpOnly/SameSite=Strict, CSRF em todo POST, limite de tentativas, auditoria de ações.
Telas: visão geral, leads (status, CSV, exclusão LGPD), planos e preços (com conferência contra a vitrine do WHMCS e aplicação **manual**),
configurações (WhatsApp, Google Ads/GA4/GTM/Pixel, Turnstile, integrações, SMTP; segredos criptografados), downloads, versões do Studio
(webhook assinado `POST /api/v1/studio/version`, a versão chega como rascunho), publicar novidade (site / newsletter / Web Push, sempre por escolha),
newsletter (dupla confirmação, campanhas, envio em lotes), redirects 301, logs e minha conta.

Criar o administrador (na VPS, a senha não aparece nem fica no histórico):
```bash
read -rs P; printf '%s' "$P" | php bin/console admin:create "Seu Nome" voce@dominio.com; unset P
```
Envio agendado da newsletter e backup do banco (crontab do `claudeops`):
```
*/5 * * * * cd /var/www/portal-novo && php bin/console newsletter:send >> storage/logs/newsletter-cron.log 2>&1
30 3 * * *  bash /var/www/portal-novo/deploy/backup.sh >> /home/claudeops/backups-portal.log 2>&1
```
Testes: `php tests/admin.php http://127.0.0.1:8081` (precisa do SMTP falso: `python3 tests/fake_smtp.py 2525`).

## Google Ads e medição
Guia completo em `docs/GOOGLE-ADS.md` (estrutura de campanhas, anúncios, conversões e a conversão de compra no WHMCS) e arquivos
para o Google Ads Editor em `docs/ads/` (palavras-chave, negativas, anúncios). Regerar: `python3 tools/ads_blueprint.py`.
Configuração (IDs públicos, via console): `php bin/console setting:set ads_id AW-XXXX` (e `ga4_id`, `gtm_id`, `meta_pixel_id`,
`ads_conv_generate_lead`, `ads_conv_whatsapp_click`, ...). Leads das landings: `php bin/console leads:list`.

## Assets
Depois de alterar `public/assets/css/app.css` ou `js/app.js`, rode `bash tools/build_assets.sh` (gera os `.min` e o hash conferido pelo teste).

## Etapas
1. **Entregue:** estrutura, design system, logo, home, páginas de produto com links reais do WHMCS, SEO base, schema, sitemap, robots, WhatsApp, LGPD, landings de Ads, downloads, busca.
2. **Entregue:** painel administrativo, blog, webhook de versões do Studio, newsletter com dupla confirmação, leads e landings de Ads.
3. **Próxima:** envio de Web Push (chaves VAPID), formulário próprio de cadastro de rádios com moderação, destaques, Conversions API (servidor), webhook de compra do WHMCS, 2FA do painel.

## Pendências conhecidas
- Plano de VPS Linux ainda não existe no WHMCS (`/vps` fica "em breve").
- Demonstrações dos modelos de site usam o padrão `demo{n}.164-68-121-142.sslip.io` (configurável em `settings.demo_pattern`); validar com o time.
- Textos legais (privacidade, termos, cookies, LGPD) são um modelo base: revisar com assessoria jurídica e incluir razão social e CNPJ.
