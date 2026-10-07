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
php tests/smoke.php http://127.0.0.1:8081
```

## Instalação na VPS (staging)
Veja `deploy/install.sh`. Roda como usuário comum, sem sudo, e só mexe no vhost do domínio de teste.

## Etapas
1. **Entregue:** estrutura, design system, logo, home, páginas de produto com links reais do WHMCS, SEO base, schema, sitemap, robots, WhatsApp, LGPD, landings de Ads, downloads, busca.
2. **Próxima:** painel administrativo, blog, webhook de versões do Studio, newsletter e Web Push.
3. **Depois:** integração do cadastro de rádios, destaques, Pixel/Conversions API, redirects 301 a partir do Search Console, testes finais e publicação.

## Pendências conhecidas
- Plano de VPS Linux ainda não existe no WHMCS (`/vps` fica "em breve").
- Demonstrações dos modelos de site usam o padrão `demo{n}.164-68-121-142.sslip.io` (configurável em `settings.demo_pattern`); validar com o time.
- Textos legais (privacidade, termos, cookies, LGPD) são um modelo base: revisar com assessoria jurídica e incluir razão social e CNPJ.
