# Google Ads: guia de campanha e medição (Mídia Server)

Gerado por `tools/ads_blueprint.py`. Os limites de caracteres dos anúncios foram validados (títulos ≤ 30, descrições ≤ 90). **Preços e recursos citados nos anúncios vêm da vitrine do WHMCS em 07/10/2026**: se um plano mudar, atualize o anúncio. Volumes de busca, CPC e orçamento dependem do Planejador de Palavras-chave e da sua conta; nada disso foi inventado aqui.

## 1. Como as landings estão preparadas

| Item | Situação |
|---|---|
| Landing por intenção | 8 landings `/lp/...`, cada uma com título H1 igual ao tema do grupo de anúncios |
| Preço visível | Menor preço mensal vem do WHMCS, na própria página |
| Conversão principal | Formulário de lead (`generate_lead` na página `/obrigado`), clique no WhatsApp, clique em Contratar |
| Velocidade | LCP 0,7 a 0,9 s e CLS 0,000 em teste simulando celular com rede lenta (sem gzip); a VPS comprime com gzip |
| Celular | barra fixa com WhatsApp e Ver planos, formulário com 2 campos |
| Anti-spam | CSRF, campo isca, tempo mínimo, limite de 5 envios/h por IP, Turnstile opcional |
| Páginas de campanha | `noindex`, fora do sitemap e do robots (evita conteúdo duplicado) |
| Consentimento | Consent Mode v2: as tags só carregam após o aceite; tudo começa como "negado" |

## 2. Configurar a medição (uma vez)

Na VPS, como `claudeops`, em `/var/www/portal-novo`:

```bash
php bin/console setting:set ga4_id        G-XXXXXXXXXX      # GA4 (opcional)
php bin/console setting:set ads_id        AW-XXXXXXXXX      # ID da conta do Google Ads
php bin/console setting:set meta_pixel_id 000000000000000   # Meta Pixel (opcional)
php bin/console setting:set search_console  CÓDIGO_DE_VERIFICAÇÃO   # tag de verificação do Search Console
```

Crie as ações de conversão no Google Ads (Metas > Conversões > Nova ação de conversão > Site > inserir manualmente) e copie o **rótulo** de cada uma (formato `AW-XXXXXXXXX/abcDEFghi`):

| Evento no site | Ação de conversão sugerida | Principal? | Comando |
|---|---|---|---|
| `generate_lead` | Lead (formulário) | **Sim** | `setting:set ads_conv_generate_lead AW-.../...` |
| `whatsapp_click` | Clique no WhatsApp | **Sim** (valor menor) | `setting:set ads_conv_whatsapp_click AW-.../...` |
| `begin_checkout` | Início de compra | Não (secundária) | `setting:set ads_conv_begin_checkout AW-.../...` |
| `file_download` | Download do Studio | Secundária | `setting:set ads_conv_file_download AW-.../...` |
| `view_demo` | Demonstração de site | Secundária | `setting:set ads_conv_view_demo AW-.../...` |
| compra no WHMCS | **Compra** | **Sim** (a mais importante) | ver seção 3 |

Se você usa o **Google Tag Manager**, basta `setting:set gtm_id GTM-XXXXXXX`: o portal passa a só alimentar o `dataLayer` e o GTM cuida das tags. Eventos no `dataLayer`: `whatsapp_click`, `begin_checkout`, `view_item`, `generate_lead`, `file_download`, `view_demo`, `play_sample`, cada um com `event_id`, `page_path` e as UTMs.

## 3. Conversão de compra (WHMCS)

A compra acontece em `cliente-area.midiaserver.com.br`. Como é o mesmo domínio-pai, o cookie `ms_consent` (consentimento) e os cookies do Google são compartilhados. No WHMCS, edite o template da página de pedido concluído (`templates/<seu-tema>/cart/complete.tpl` ou equivalente) e acrescente, **mantendo o teste de consentimento**:

```html
<script>
(function(){
  var m = document.cookie.match(/(?:^|; )ms_consent=([^;]+)/);
  if (!m || m[1] !== "yes") return;            // sem consentimento, não mede
  var s = document.createElement("script"); s.async = true;
  s.src = "https://www.googletagmanager.com/gtag/js?id=AW-XXXXXXXXX"; document.head.appendChild(s);
  window.dataLayer = window.dataLayer || [];
  function gtag(){ dataLayer.push(arguments); }
  gtag("js", new Date()); gtag("config", "AW-XXXXXXXXX");
  gtag("event", "conversion", {
    send_to: "AW-XXXXXXXXX/ROTULO_COMPRA",
    value: {$amount|default:0},          // confira a variável de valor disponível no seu template (use {debug})
    currency: "BRL",
    transaction_id: "{$orderid}"        // evita contagem em dobro
  });
})();
</script>
```

Para o consentimento valer em todo o domínio, o portal grava o cookie `ms_consent` em `.midiaserver.com.br` (já implementado). Confira no WHMCS as variáveis exatas da página de conclusão do pedido. **Teste com um pedido real de baixo valor** antes de ligar o orçamento.

## 4. Estrutura de campanhas (Pesquisa)

Rede de Pesquisa, sem parceiros de pesquisa no início, Brasil, português, dispositivos: todos. Lance inicial recomendado: **cliques com limite de CPC** nas duas primeiras semanas ou **CPC manual**, passando para **Maximizar conversões** quando houver volume de conversões (referência comum: 15 a 30 por mês por campanha). Palavras na correspondência **exata** e de **frase** primeiro; só amplie para ampla com lances inteligentes e muita conversão.

| Campanha | Landing | Grupos de anúncios |
|---|---|---|
| Streaming Rádio | `/lp/streaming-radio` | streaming para rádio, autodj, shoutcast icecast |
| Hospedagem cPanel | `/lp/hospedagem-cpanel` | hospedagem de sites, hospedagem cpanel, hospedagem ssl nvme |
| Revenda Hospedagem | `/lp/revenda-hospedagem` | revenda de hospedagem, abrir empresa de hospedagem |
| Revenda Streaming | `/lp/revenda-streaming` | revenda de streaming, ser revendedor de streaming |
| Automação de Rádio | `/lp/automacao-radio` | automação de rádio, software para rádio, cartucheira e hora certa |
| Conteúdos para Rádios | `/lp/conteudos-para-radio` | programas para rádio, programetes, conteúdo para rádio |
| Sites para Rádio | `/lp/site-para-radio` | site para rádio, modelos de site |
| Web Rádio Completa | `/lp/web-radio-completa` | criar rádio online, web rádio completa |
| Marca | `/` | marca |

## 5. Importar no Google Ads Editor

Arquivos prontos em `docs/ads/`:
- `palavras-chave.csv`: todas as palavras em **Exata** e **Frase** com a URL final (já com `utm_source=google&utm_medium=cpc`).
- `negativas.csv`: palavras negativas por campanha.
- `anuncios-rsa.csv`: um anúncio responsivo de pesquisa (15 títulos, 4 descrições) por grupo.

No Google Ads Editor: Conta > Importar > Colar/Importar de arquivo CSV. Crie as campanhas primeiro (nome igual ao da coluna *Campaign*), depois importe. Revise antes de publicar. Os nomes de colunas podem variar conforme a versão do Editor.

**Modelo de URL final (parâmetros de rastreamento da conta):**
```
{lpurl}?utm_source=google&utm_medium=cpc&utm_campaign={campaignid}&utm_term={keyword}&matchtype={matchtype}&network={network}
```
Ative o **auto-tagging** (gclid). O formulário guarda `gclid`, UTMs e `matchtype` com cada lead.

## 6. Anúncios por campanha (resumo)

### Streaming Rádio
URL: `https://midiaserver.com.br/lp/streaming-radio` · caminho de exibição: `/streaming/radio`

**Títulos (15):** Streaming para Rádio Online (27) · AutoDJ e Ouvintes Ilimitados (28) · Site da Rádio Incluso (21) · Sua Rádio Não Pode Parar (24) · Streaming Mídia Server (22) · Áudio de Até 320 kbps (21) · Painel de Controle Midia Cast (29) · Comece Sua Web Rádio Agora (26) · Coloque Sua Rádio no Ar (23) · Planos Bronze, Prata e Ouro (27) · Contrate Pela Área do Cliente (29) · Pague com Pix (13) · Suporte por WhatsApp (20) · Web App iOS e Android (21) · Streaming a Partir de R$ 49,90 (30)

**Descrições:** 1. Streaming com AutoDJ, ouvintes ilimitados e site da rádio incluso em todos os planos. (85)  
2. Escolha o plano, contrate pela Área do Cliente e pague com Pix. Suporte por WhatsApp. (85)  
3. Áudio de 128 a 320 kbps, painel Midia Cast e Web App para iOS e Android para os ouvintes. (89)  
4. Veja os planos e coloque sua web rádio no ar. Preços reais, sem letras miúdas. (78)

### Hospedagem cPanel
URL: `https://midiaserver.com.br/lp/hospedagem-cpanel` · caminho de exibição: `/hospedagem/cpanel`

**Títulos (15):** Hospedagem cPanel com NVMe (26) · SSL Grátis em Todos os Planos (29) · Hospedagem de Sites Rápida (26) · Transferência Ilimitada (23) · Seu Site no Ar com Segurança (28) · Hospedagem desde R$ 19,90 (25) · Contas de E-mail Inclusas (25) · Backup Semanal e Diário (23) · Painel cPanel Completo (22) · Contrate Pela Área do Cliente (29) · Pague com Pix (13) · Suporte por WhatsApp (20) · Bancos de Dados MySQL (21) · Hospedagem Mídia Server (23) · Planos Bronze a Premium (23)

**Descrições:** 1. Hospedagem com cPanel, disco NVMe, SSL grátis e transferência ilimitada. Veja os planos. (88)  
2. Do site pessoal à loja virtual: escolha o plano, contrate online e pague com Pix. (81)  
3. E-mails, bancos MySQL e backup (semanal ou diário, conforme o plano). Suporte humano. (85)  
4. Compare os 4 planos e contrate pela Área do Cliente. Preço mensal claro. (72)

### Revenda Hospedagem
URL: `https://midiaserver.com.br/lp/revenda-hospedagem` · caminho de exibição: `/revenda/hospedagem`

**Títulos (15):** Revenda de Hospedagem WHM (25) · Abra Sua Empresa de Hospedagem (30) · Marca Própria nos Planos (24) · Contas cPanel: 10 a Ilimitadas (30) · Revenda a Partir de R$ 49,90 (28) · Painel WHM Completo (19) · Disco NVMe de 50 a 500 GB (25) · Transferência Ilimitada (23) · Comece Sua Revenda Hoje (23) · Planos Bronze a Premium (23) · Contrate Pela Área do Cliente (29) · Pague com Pix (13) · Suporte por WhatsApp (20) · Crie Seus Próprios Planos (25) · Revenda Mídia Server (20)

**Descrições:** 1. Revenda com painel WHM e contas cPanel. Marca própria nos planos Prata, Ouro e Premium. (87)  
2. Crie seus planos e hospede seus clientes. Disco NVMe de 50 a 500 GB e tráfego ilimitado. (88)  
3. Escolha o plano, contrate pela Área do Cliente e comece sua revenda. Suporte por WhatsApp. (90)  
4. Compare os 4 planos de revenda e veja qual cabe no seu projeto. (63)

### Revenda Streaming
URL: `https://midiaserver.com.br/lp/revenda-streaming` · caminho de exibição: `/revenda/streaming`

**Títulos (15):** Revenda de Streaming (20) · Streaming é o Seu Negócio (25) · Venda com Sua Marca (19) · Painel de Revenda Próprio (25) · De 5 a 60 Contas de Streaming (29) · Sub-revendas nos Planos (23) · Ouvintes Ilimitados (19) · Revenda a Partir de R$ 69,90 (28) · Comece Sua Revenda Hoje (23) · Contrate Pela Área do Cliente (29) · Pague com Pix (13) · Suporte por WhatsApp (20) · Planos Bronze a Premium (23) · Revenda Mídia Server (20) · Atenda Suas Rádios Clientes (27)

**Descrições:** 1. Revenda de streaming com painel próprio, marca própria e de 5 a 60 contas de streaming. (87)  
2. Venda streaming para rádios com a sua marca. Sub-revendas a partir do plano Prata. (82)  
3. Disco compartilhado de 50 a 250 GB e ouvintes ilimitados. Contrate pela Área do Cliente. (88)  
4. Veja os 4 planos de revenda de streaming e comece o seu negócio. (64)

### Automação de Rádio
URL: `https://midiaserver.com.br/lp/automacao-radio` · caminho de exibição: `/automacao/radio`

**Títulos (15):** Automação de Rádio Windows (26) · Mídia Rádio Studio (18) · Baixe Grátis e Teste (20) · Playlist com Mixagem (20) · Cartucheira de Vinhetas (23) · Grade de Programação (20) · Hora Certa e Temperatura (24) · Relatório de Comerciais (23) · Automação a R$ 59,90/Mês (24) · Para FM, Web e Comunitária (26) · Compatível com ZaraRadio (24) · Suporte no WhatsApp (19) · Rádio no Ar 24 Horas (20) · Teste Sem Cartão (16) · Licença para 1 Computador (25)

**Descrições:** 1. Automação completa para rádio FM, web e comunitária: playlist, cartucheira e grade. (83)  
2. Baixe grátis e teste no seu computador com Windows 10 ou 11. Assine quando quiser. (82)  
3. Hora certa compatível com ZaraRadio, relatórios em PDF e Excel e backup diário. (79)  
4. Plano mensal ou anual com desconto. Pague com Pix pela Área do Cliente. (71)

### Conteúdos para Rádios
URL: `https://midiaserver.com.br/lp/conteudos-para-radio` · caminho de exibição: `/conteudos/radio`

**Títulos (15):** Conteúdo para Rádio (19) · Programas e Programetes (23) · Atualizados Todos os Dias (25) · Direto no Computador da Rádio (29) · Conteúdo Gospel e Secular (25) · Fim de Semana Incluso (21) · Sincronização Automática (24) · Planos a Partir de R$ 89,90 (27) · Mais de 450 Conteúdos (21) · Ouça Amostras Antes (19) · Para FM e Web Rádio (19) · Contrate Pela Área do Cliente (29) · Pague com Pix (13) · Suporte por WhatsApp (20) · Conteúdos Mídia Server (22)

**Descrições:** 1. Programas, programetes e conteúdo de fim de semana atualizados todos os dias na sua rádio. (90)  
2. O aplicativo sincroniza uma pasta do computador da rádio. Aponte a sua automação e pronto. (90)  
3. Mais de 450 conteúdos em 23 categorias. Ouça amostras e escolha o pacote ideal. (79)  
4. Pacotes Básico, Intermediário e Profissional. Contrate pela Área do Cliente. (76)

### Sites para Rádio
URL: `https://midiaserver.com.br/lp/site-para-radio` · caminho de exibição: `/site/radio`

**Títulos (15):** Site para Web Rádio (19) · 15 Modelos de Site (18) · Player ao Vivo no Site (22) · Notícias Automáticas (20) · Painel Fácil de Editar (22) · Site a Partir de R$ 29,90 (25) · Grátis nos Planos de Streaming (30) · Programação e Equipe (20) · Funciona no Celular (19) · Veja as Demonstrações (21) · Contrate Pela Área do Cliente (29) · Pague com Pix (13) · Suporte por WhatsApp (20) · Site Mídia Server (17) · Seu Site de Rádio no Ar (23)

**Descrições:** 1. Site profissional para sua web rádio: player ao vivo, notícias automáticas e painel fácil. (90)  
2. Escolha entre 15 modelos e veja as demonstrações. Grátis em todos os planos de streaming. (89)  
3. Sem programador: troque logo, cores, banners, textos, programação e equipe pelo painel. (87)  
4. Contrate pela Área do Cliente e receba o acesso assim que o pagamento for confirmado. (85)

### Web Rádio Completa
URL: `https://midiaserver.com.br/lp/web-radio-completa` · caminho de exibição: `/web-radio/completa`

**Títulos (15):** Crie Sua Rádio Online (21) · Streaming + AutoDJ + Site (25) · Web Rádio Completa (18) · Endereço Grátis Para o Site (27) · Tudo em um Só Lugar (19) · Player e Painel de Controle (27) · Comece a Partir de R$ 49,90 (27) · Ouvintes Ilimitados (19) · Site Administrável Incluso (26) · Coloque Sua Rádio no Ar (23) · Contrate Pela Área do Cliente (29) · Pague com Pix (13) · Suporte por WhatsApp (20) · Apps e Web App Inclusos (23) · Web Rádio Mídia Server (22)

**Descrições:** 1. Streaming, AutoDJ, site com player e painel para sua rádio online. Tudo em um só lugar. (87)  
2. O site vem incluso em todos os planos de streaming, com endereço grátis ou o seu domínio. (89)  
3. Comece rápido: contrate pela Área do Cliente, envie as músicas e personalize o site. (84)  
4. Planos Bronze, Prata e Ouro. Pague com Pix e conte com o suporte por WhatsApp. (78)

### Marca
URL: `https://midiaserver.com.br/` · caminho de exibição: `/midiaserver/oficial`

**Títulos (15):** Mídia Server Oficial (20) · Hospedagem e Streaming (22) · Automação para Rádios (21) · Conteúdos para Rádios (21) · Sites para Web Rádio (20) · Revenda de Hospedagem (21) · Revenda de Streaming (20) · Área do Cliente (15) · Contrate Online (15) · Pague com Pix (13) · Suporte por WhatsApp (20) · Tudo para Sua Rádio (19) · Hospedagem cPanel (17) · Streaming com AutoDJ (20) · Veja Todos os Serviços (22)

**Descrições:** 1. Hospedagem, streaming, automação, sites e conteúdos para rádios em um só lugar. (79)  
2. Contrate pela Área do Cliente, pague com Pix e conte com suporte por WhatsApp. (78)  
3. Conheça os planos de hospedagem cPanel, streaming com AutoDJ e o Mídia Rádio Studio. (84)  
4. Revenda de hospedagem e de streaming com marca própria. Veja os planos. (71)

## 7. Palavras negativas (todas as campanhas, correspondência de frase)

grátis para sempre, gratuita, emprego, vaga, curso, apostila, pdf, torrent, crack, pirata, apk, baixar música, letra, cifra, rádio ao vivo, ouvir rádio, rádio fm ao vivo, frequência, antena, transmissor, concurso, wikipedia, o que é, reclame aqui, login, webmail, revenda de produtos, imobiliária, hospedagem de hotel, hotel, pousada, hospedagem airbnb, iptv, lista iptv, canais iptv, jogos, servidor minecraft, servidor de jogos

Se alguma delas bloquear tráfego bom (por exemplo "ouvir rádio" em campanhas de rádio online), remova da lista. Revise o relatório de **termos de pesquisa** toda semana e acrescente negativas.

## 8. Extensões (ativos) recomendadas

- **Sitelinks:** Planos de streaming (`/lp/streaming-radio`), Hospedagem cPanel, Automação de Rádio, Conteúdos para Rádios, Sites para Rádio, Revenda de Hospedagem, Fale no WhatsApp.
- **Frases de destaque:** SSL grátis; Site incluso no streaming; Pague com Pix; Teste grátis do Studio (somente na campanha de automação); Suporte por WhatsApp.
- **Snippets estruturados (Tipos):** Streaming, AutoDJ, Revenda, Automação, Conteúdos, Sites.
- **Preço:** use ativos de preço com os planos reais da vitrine (Bronze a Premium) e **atualize quando o WHMCS mudar**.
- **Imagens:** use as imagens 1200x630 de `public/assets/img/og/` e as capturas do Studio.

## 9. Remarketing e públicos

Com o consentimento dado, o Google Ads/GA4 forma listas de quem visitou páginas de produto e de quem iniciou a compra (`begin_checkout`) sem concluir. Crie públicos no Google Ads (Ferramentas > Gerenciador de públicos) usando as tags do site e rode anúncios de Pesquisa para listas de remarketing (RLSA) com lances maiores.

## 10. Checklist antes de ligar

- [ ] `ads_id` e os rótulos de conversão configurados e testados (Tag Assistant ou "Tag Assistant Companion").
- [ ] Pedido de teste no WHMCS gerou a conversão de **Compra**.
- [ ] Formulário de lead enviado e conversão `generate_lead` vista na página `/obrigado`.
- [ ] Turnstile configurado (`turnstile_site_key` e `turnstile_secret`) se houver spam.
- [ ] Webhook de lead (`lead_webhook_url`) configurado para avisar a equipe, ou rotina para ler `php bin/console leads:list` várias vezes ao dia.
- [ ] Textos legais revisados (privacidade, termos, cookies) com razão social e CNPJ.
- [ ] Política do Google Ads: sem superlativos não comprováveis ("o melhor"), sem nomes de concorrentes nos anúncios, sem prometer o que o plano não inclui.
- [ ] Search Console verificado e sitemap enviado.
