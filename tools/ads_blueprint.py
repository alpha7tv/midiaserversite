#!/usr/bin/env python3
"""Gera docs/GOOGLE-ADS.md e os CSV de importação (Google Ads Editor). Valida limites de caracteres dos anúncios."""
import csv, os
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SITE = 'https://midiaserver.com.br'

# (campanha, landing, [(grupo, [palavras exatas/frase])], headlines, descriptions, path1, path2)
C = [
 ('Streaming Rádio', '/lp/streaming-radio', [
   ('streaming para rádio', ['streaming para rádio', 'streaming de áudio para rádio', 'streaming rádio online', 'streaming para web rádio', 'servidor de streaming para rádio', 'plano de streaming para rádio', 'streaming de rádio preço', 'contratar streaming para rádio', 'streaming para rádio fm e web']),
   ('autodj', ['autodj', 'autodj para rádio', 'auto dj web rádio', 'servidor autodj', 'autodj shoutcast', 'autodj icecast', 'hospedagem autodj']),
   ('shoutcast icecast', ['servidor shoutcast', 'servidor icecast', 'hospedagem shoutcast', 'streaming shoutcast', 'streaming icecast', 'shoutcast brasil', 'icecast hospedagem']),
 ], ['Streaming para Rádio Online', 'AutoDJ e Ouvintes Ilimitados', 'Site da Rádio Incluso', 'Sua Rádio Não Pode Parar', 'Streaming Mídia Server', 'Áudio de Até 320 kbps', 'Painel de Controle Midia Cast', 'Comece Sua Web Rádio Agora', 'Coloque Sua Rádio no Ar', 'Planos Bronze, Prata e Ouro', 'Contrate Pela Área do Cliente', 'Pague com Pix', 'Suporte por WhatsApp', 'Web App iOS e Android', 'Streaming a Partir de R$ 49,90'],
   ['Streaming com AutoDJ, ouvintes ilimitados e site da rádio incluso em todos os planos.', 'Escolha o plano, contrate pela Área do Cliente e pague com Pix. Suporte por WhatsApp.', 'Áudio de 128 a 320 kbps, painel Midia Cast e Web App para iOS e Android para os ouvintes.', 'Veja os planos e coloque sua web rádio no ar. Preços reais, sem letras miúdas.'], 'streaming', 'radio'),
 ('Hospedagem cPanel', '/lp/hospedagem-cpanel', [
   ('hospedagem de sites', ['hospedagem de sites', 'hospedagem de site', 'hospedagem de site profissional', 'plano de hospedagem de sites', 'contratar hospedagem de site', 'hospedagem para site institucional', 'hospedagem para loja virtual']),
   ('hospedagem cpanel', ['hospedagem cpanel', 'hospedagem com cpanel', 'hospedagem de sites cpanel', 'plano de hospedagem cpanel', 'hospedagem cpanel brasil', 'cpanel hospedagem preço']),
   ('hospedagem ssl nvme', ['hospedagem com ssl grátis', 'hospedagem nvme', 'hospedagem ssd nvme', 'hospedagem com certificado ssl', 'hospedagem wordpress', 'hospedagem wordpress cpanel']),
 ], ['Hospedagem cPanel com NVMe', 'SSL Grátis em Todos os Planos', 'Hospedagem de Sites Rápida', 'Transferência Ilimitada', 'Seu Site no Ar com Segurança', 'Hospedagem desde R$ 19,90', 'Contas de E-mail Inclusas', 'Backup Semanal e Diário', 'Painel cPanel Completo', 'Contrate Pela Área do Cliente', 'Pague com Pix', 'Suporte por WhatsApp', 'Bancos de Dados MySQL', 'Hospedagem Mídia Server', 'Planos Bronze a Premium'],
   ['Hospedagem com cPanel, disco NVMe, SSL grátis e transferência ilimitada. Veja os planos.', 'Do site pessoal à loja virtual: escolha o plano, contrate online e pague com Pix.', 'E-mails, bancos MySQL e backup (semanal ou diário, conforme o plano). Suporte humano.', 'Compare os 4 planos e contrate pela Área do Cliente. Preço mensal claro.'], 'hospedagem', 'cpanel'),
 ('Revenda Hospedagem', '/lp/revenda-hospedagem', [
   ('revenda de hospedagem', ['revenda de hospedagem', 'revenda de hospedagem cpanel', 'plano de revenda de hospedagem', 'revenda de hospedagem whm', 'revenda de hospedagem com marca própria', 'contratar revenda de hospedagem']),
   ('abrir empresa de hospedagem', ['abrir empresa de hospedagem', 'como ser revendedor de hospedagem', 'revender hospedagem', 'montar empresa de hospedagem', 'revenda cpanel whm']),
 ], ['Revenda de Hospedagem WHM', 'Abra Sua Empresa de Hospedagem', 'Marca Própria nos Planos', 'Contas cPanel: 10 a Ilimitadas', 'Revenda a Partir de R$ 49,90', 'Painel WHM Completo', 'Disco NVMe de 50 a 500 GB', 'Transferência Ilimitada', 'Comece Sua Revenda Hoje', 'Planos Bronze a Premium', 'Contrate Pela Área do Cliente', 'Pague com Pix', 'Suporte por WhatsApp', 'Crie Seus Próprios Planos', 'Revenda Mídia Server'],
   ['Revenda com painel WHM e contas cPanel. Marca própria nos planos Prata, Ouro e Premium.', 'Crie seus planos e hospede seus clientes. Disco NVMe de 50 a 500 GB e tráfego ilimitado.', 'Escolha o plano, contrate pela Área do Cliente e comece sua revenda. Suporte por WhatsApp.', 'Compare os 4 planos de revenda e veja qual cabe no seu projeto.'], 'revenda', 'hospedagem'),
 ('Revenda Streaming', '/lp/revenda-streaming', [
   ('revenda de streaming', ['revenda de streaming', 'revenda de streaming de rádio', 'revenda de streaming de áudio', 'plano de revenda de streaming', 'revender streaming para rádio', 'revenda de streaming marca própria']),
   ('ser revendedor de streaming', ['ser revendedor de streaming', 'como revender streaming', 'montar empresa de streaming', 'painel de revenda de streaming']),
 ], ['Revenda de Streaming', 'Streaming é o Seu Negócio', 'Venda com Sua Marca', 'Painel de Revenda Próprio', 'De 5 a 60 Contas de Streaming', 'Sub-revendas nos Planos', 'Ouvintes Ilimitados', 'Revenda a Partir de R$ 69,90', 'Comece Sua Revenda Hoje', 'Contrate Pela Área do Cliente', 'Pague com Pix', 'Suporte por WhatsApp', 'Planos Bronze a Premium', 'Revenda Mídia Server', 'Atenda Suas Rádios Clientes'],
   ['Revenda de streaming com painel próprio, marca própria e de 5 a 60 contas de streaming.', 'Venda streaming para rádios com a sua marca. Sub-revendas a partir do plano Prata.', 'Disco compartilhado de 50 a 250 GB e ouvintes ilimitados. Contrate pela Área do Cliente.', 'Veja os 4 planos de revenda de streaming e comece o seu negócio.'], 'revenda', 'streaming'),
 ('Automação de Rádio', '/lp/automacao-radio', [
   ('automação de rádio', ['automação de rádio', 'automação para rádio', 'automação de rádio fm', 'automação para web rádio', 'automação para rádio comunitária', 'software de automação de rádio', 'sistema de automação para rádio']),
   ('software para rádio', ['software para rádio', 'programa para rádio', 'programa para rádio fm', 'programa para web rádio', 'programa de automação de rádio', 'software de playout rádio']),
   ('cartucheira e hora certa', ['cartucheira de vinhetas', 'cartucheira para rádio', 'hora certa para rádio', 'programa hora certa rádio', 'grade de programação rádio', 'playlist com mixagem rádio']),
 ], ['Automação de Rádio Windows', 'Mídia Rádio Studio', 'Baixe Grátis e Teste', 'Playlist com Mixagem', 'Cartucheira de Vinhetas', 'Grade de Programação', 'Hora Certa e Temperatura', 'Relatório de Comerciais', 'Automação a R$ 59,90/Mês', 'Para FM, Web e Comunitária', 'Compatível com ZaraRadio', 'Suporte no WhatsApp', 'Rádio no Ar 24 Horas', 'Teste Sem Cartão', 'Licença para 1 Computador'],
   ['Automação completa para rádio FM, web e comunitária: playlist, cartucheira e grade.', 'Baixe grátis e teste no seu computador com Windows 10 ou 11. Assine quando quiser.', 'Hora certa compatível com ZaraRadio, relatórios em PDF e Excel e backup diário.', 'Plano mensal ou anual com desconto. Pague com Pix pela Área do Cliente.'], 'automacao', 'radio'),
 ('Conteúdos para Rádios', '/lp/conteudos-para-radio', [
   ('programas para rádio', ['programas para rádio', 'programas para rádio fm', 'programas para web rádio', 'programas de rádio prontos', 'programas gravados para rádio', 'programas gospel para rádio', 'programas sertanejos para rádio']),
   ('programetes', ['programetes para rádio', 'programete para rádio', 'quadros para rádio', 'programetes de notícias para rádio', 'programetes de humor para rádio']),
   ('conteúdo para rádio', ['conteúdo para rádio', 'conteúdo para web rádio', 'conteúdo para rádio fm', 'conteúdo para rádio comunitária', 'conteúdo para rádio fim de semana']),
 ], ['Conteúdo para Rádio', 'Programas e Programetes', 'Atualizados Todos os Dias', 'Direto no Computador da Rádio', 'Conteúdo Gospel e Secular', 'Fim de Semana Incluso', 'Sincronização Automática', 'Planos a Partir de R$ 89,90', 'Mais de 450 Conteúdos', 'Ouça Amostras Antes', 'Para FM e Web Rádio', 'Contrate Pela Área do Cliente', 'Pague com Pix', 'Suporte por WhatsApp', 'Conteúdos Mídia Server'],
   ['Programas, programetes e conteúdo de fim de semana atualizados todos os dias na sua rádio.', 'O aplicativo sincroniza uma pasta do computador da rádio. Aponte a sua automação e pronto.', 'Mais de 450 conteúdos em 23 categorias. Ouça amostras e escolha o pacote ideal.', 'Pacotes Básico, Intermediário e Profissional. Contrate pela Área do Cliente.'], 'conteudos', 'radio'),
 ('Sites para Rádio', '/lp/site-para-radio', [
   ('site para rádio', ['site para rádio', 'site para web rádio', 'site para rádio online', 'site para rádio com player', 'criar site para rádio', 'site profissional para rádio']),
   ('modelos de site', ['modelos de site para rádio', 'template site rádio', 'template para web rádio', 'tema para rádio online', 'site de rádio pronto']),
 ], ['Site para Web Rádio', '15 Modelos de Site', 'Player ao Vivo no Site', 'Notícias Automáticas', 'Painel Fácil de Editar', 'Site a Partir de R$ 29,90', 'Grátis nos Planos de Streaming', 'Programação e Equipe', 'Funciona no Celular', 'Veja as Demonstrações', 'Contrate Pela Área do Cliente', 'Pague com Pix', 'Suporte por WhatsApp', 'Site Mídia Server', 'Seu Site de Rádio no Ar'],
   ['Site profissional para sua web rádio: player ao vivo, notícias automáticas e painel fácil.', 'Escolha entre 15 modelos e veja as demonstrações. Grátis em todos os planos de streaming.', 'Sem programador: troque logo, cores, banners, textos, programação e equipe pelo painel.', 'Contrate pela Área do Cliente e receba o acesso assim que o pagamento for confirmado.'], 'site', 'radio'),
 ('Web Rádio Completa', '/lp/web-radio-completa', [
   ('criar rádio online', ['criar rádio online', 'como criar uma rádio online', 'criar uma web rádio', 'como montar uma web rádio', 'montar rádio online', 'abrir uma rádio online']),
   ('web rádio completa', ['web rádio completa', 'pacote web rádio', 'rádio online com site', 'streaming e site para rádio']),
 ], ['Crie Sua Rádio Online', 'Streaming + AutoDJ + Site', 'Web Rádio Completa', 'Endereço Grátis Para o Site', 'Tudo em um Só Lugar', 'Player e Painel de Controle', 'Comece a Partir de R$ 49,90', 'Ouvintes Ilimitados', 'Site Administrável Incluso', 'Coloque Sua Rádio no Ar', 'Contrate Pela Área do Cliente', 'Pague com Pix', 'Suporte por WhatsApp', 'Apps e Web App Inclusos', 'Web Rádio Mídia Server'],
   ['Streaming, AutoDJ, site com player e painel para sua rádio online. Tudo em um só lugar.', 'O site vem incluso em todos os planos de streaming, com endereço grátis ou o seu domínio.', 'Comece rápido: contrate pela Área do Cliente, envie as músicas e personalize o site.', 'Planos Bronze, Prata e Ouro. Pague com Pix e conte com o suporte por WhatsApp.'], 'web-radio', 'completa'),
]
NEG = ['grátis para sempre', 'gratuita', 'emprego', 'vaga', 'curso', 'apostila', 'pdf', 'torrent', 'crack', 'pirata', 'apk', 'baixar música', 'letra', 'cifra', 'rádio ao vivo', 'ouvir rádio', 'rádio fm ao vivo', 'frequência', 'antena', 'transmissor', 'concurso', 'wikipedia', 'o que é', 'reclame aqui', 'login', 'webmail', 'revenda de produtos', 'imobiliária', 'hospedagem de hotel', 'hotel', 'pousada', 'hospedagem airbnb', 'iptv', 'lista iptv', 'canais iptv', 'jogos', 'servidor minecraft', 'servidor de jogos']
BRAND = [('Marca', '/', [('marca', ['mídia server', 'midia server', 'midiaserver', 'mídia server hospedagem', 'mídia server streaming', 'midia server rádio', 'mídia rádio studio', 'midia radio studio'])],
  ['Mídia Server Oficial', 'Hospedagem e Streaming', 'Automação para Rádios', 'Conteúdos para Rádios', 'Sites para Web Rádio', 'Revenda de Hospedagem', 'Revenda de Streaming', 'Área do Cliente', 'Contrate Online', 'Pague com Pix', 'Suporte por WhatsApp', 'Tudo para Sua Rádio', 'Hospedagem cPanel', 'Streaming com AutoDJ', 'Veja Todos os Serviços'],
  ['Hospedagem, streaming, automação, sites e conteúdos para rádios em um só lugar.', 'Contrate pela Área do Cliente, pague com Pix e conte com suporte por WhatsApp.', 'Conheça os planos de hospedagem cPanel, streaming com AutoDJ e o Mídia Rádio Studio.', 'Revenda de hospedagem e de streaming com marca própria. Veja os planos.'], 'midiaserver', 'oficial')]

errs = []
for name, lp, groups, H, D, p1, p2 in C + BRAND:
    for h in H:
        if len(h) > 30: errs.append(f'[{name}] título {len(h)}>30: {h}')
    for d in D:
        if len(d) > 90: errs.append(f'[{name}] descrição {len(d)}>90: {d}')
    if len(H) < 3 or len(D) < 2: errs.append(f'[{name}] poucos ativos')
    if len(p1) > 15 or len(p2) > 15: errs.append(f'[{name}] path > 15')
if errs:
    print('\n'.join(errs)); raise SystemExit(1)

out = os.path.join(ROOT, 'docs/ads'); os.makedirs(out, exist_ok=True)
with open(os.path.join(out, 'palavras-chave.csv'), 'w', newline='', encoding='utf-8-sig') as f:
    w = csv.writer(f); w.writerow(['Campaign', 'Ad Group', 'Keyword', 'Criterion Type', 'Final URL'])
    n = 0
    for name, lp, groups, *_ in C + BRAND:
        for g, kws in groups:
            for k in kws:
                for mt in ('Exact', 'Phrase'):
                    w.writerow([name, g, k, mt, SITE + lp + '?utm_source=google&utm_medium=cpc']); n += 2 if False else 1
with open(os.path.join(out, 'negativas.csv'), 'w', newline='', encoding='utf-8-sig') as f:
    w = csv.writer(f); w.writerow(['Campaign', 'Keyword', 'Criterion Type'])
    for name, *_ in C:
        for k in NEG: w.writerow([name, k, 'Negative Phrase'])
with open(os.path.join(out, 'anuncios-rsa.csv'), 'w', newline='', encoding='utf-8-sig') as f:
    cols = ['Campaign', 'Ad Group', 'Ad type'] + [f'Headline {i}' for i in range(1, 16)] + [f'Description {i}' for i in range(1, 5)] + ['Path 1', 'Path 2', 'Final URL']
    w = csv.writer(f); w.writerow(cols)
    for name, lp, groups, H, D, p1, p2 in C + BRAND:
        for g, _ in groups:
            w.writerow([name, g, 'Responsive search ad'] + H + [''] * (15 - len(H)) + D + [''] * (4 - len(D)) + [p1, p2, SITE + lp + '?utm_source=google&utm_medium=cpc'])

# ---------- guia em Markdown ----------
L = []
A = L.append
A('# Google Ads: guia de campanha e medição (Mídia Server)\n')
A('Gerado por `tools/ads_blueprint.py`. Os limites de caracteres dos anúncios foram validados (títulos ≤ 30, descrições ≤ 90). **Preços e recursos citados nos anúncios vêm da vitrine do WHMCS em 07/10/2026**: se um plano mudar, atualize o anúncio. Volumes de busca, CPC e orçamento dependem do Planejador de Palavras-chave e da sua conta; nada disso foi inventado aqui.\n')
A('## 1. Como as landings estão preparadas\n')
A('| Item | Situação |\n|---|---|\n| Landing por intenção | 8 landings `/lp/...`, cada uma com título H1 igual ao tema do grupo de anúncios |\n| Preço visível | Menor preço mensal vem do WHMCS, na própria página |\n| Conversão principal | Formulário de lead (`generate_lead` na página `/obrigado`), clique no WhatsApp, clique em Contratar |\n| Velocidade | LCP 0,7 a 0,9 s e CLS 0,000 em teste simulando celular com rede lenta (sem gzip); a VPS comprime com gzip |\n| Celular | barra fixa com WhatsApp e Ver planos, formulário com 2 campos |\n| Anti-spam | CSRF, campo isca, tempo mínimo, limite de 5 envios/h por IP, Turnstile opcional |\n| Páginas de campanha | `noindex`, fora do sitemap e do robots (evita conteúdo duplicado) |\n| Consentimento | Consent Mode v2: as tags só carregam após o aceite; tudo começa como "negado" |\n')
A('## 2. Configurar a medição (uma vez)\n')
A('Na VPS, como `claudeops`, em `/var/www/portal-novo`:\n')
A('```bash\nphp bin/console setting:set ga4_id        G-XXXXXXXXXX      # GA4 (opcional)\nphp bin/console setting:set ads_id        AW-XXXXXXXXX      # ID da conta do Google Ads\nphp bin/console setting:set meta_pixel_id 000000000000000   # Meta Pixel (opcional)\nphp bin/console setting:set search_console  CÓDIGO_DE_VERIFICAÇÃO   # tag de verificação do Search Console\n```\n')
A('Crie as ações de conversão no Google Ads (Metas > Conversões > Nova ação de conversão > Site > inserir manualmente) e copie o **rótulo** de cada uma (formato `AW-XXXXXXXXX/abcDEFghi`):\n')
A('| Evento no site | Ação de conversão sugerida | Principal? | Comando |\n|---|---|---|---|\n| `generate_lead` | Lead (formulário) | **Sim** | `setting:set ads_conv_generate_lead AW-.../...` |\n| `whatsapp_click` | Clique no WhatsApp | **Sim** (valor menor) | `setting:set ads_conv_whatsapp_click AW-.../...` |\n| `begin_checkout` | Início de compra | Não (secundária) | `setting:set ads_conv_begin_checkout AW-.../...` |\n| `file_download` | Download do Studio | Secundária | `setting:set ads_conv_file_download AW-.../...` |\n| `view_demo` | Demonstração de site | Secundária | `setting:set ads_conv_view_demo AW-.../...` |\n| compra no WHMCS | **Compra** | **Sim** (a mais importante) | ver seção 3 |\n')
A('Se você usa o **Google Tag Manager**, basta `setting:set gtm_id GTM-XXXXXXX`: o portal passa a só alimentar o `dataLayer` e o GTM cuida das tags. Eventos no `dataLayer`: `whatsapp_click`, `begin_checkout`, `view_item`, `generate_lead`, `file_download`, `view_demo`, `play_sample`, cada um com `event_id`, `page_path` e as UTMs.\n')
A('## 3. Conversão de compra (WHMCS)\n')
A('A compra acontece em `cliente-area.midiaserver.com.br`. Como é o mesmo domínio-pai, o cookie `ms_consent` (consentimento) e os cookies do Google são compartilhados. No WHMCS, edite o template da página de pedido concluído (`templates/<seu-tema>/cart/complete.tpl` ou equivalente) e acrescente, **mantendo o teste de consentimento**:\n')
A('```html\n<script>\n(function(){\n  var m = document.cookie.match(/(?:^|; )ms_consent=([^;]+)/);\n  if (!m || m[1] !== "yes") return;            // sem consentimento, não mede\n  var s = document.createElement("script"); s.async = true;\n  s.src = "https://www.googletagmanager.com/gtag/js?id=AW-XXXXXXXXX"; document.head.appendChild(s);\n  window.dataLayer = window.dataLayer || [];\n  function gtag(){ dataLayer.push(arguments); }\n  gtag("js", new Date()); gtag("config", "AW-XXXXXXXXX");\n  gtag("event", "conversion", {\n    send_to: "AW-XXXXXXXXX/ROTULO_COMPRA",\n    value: {$amount|default:0},          // confira a variável de valor disponível no seu template (use {debug})\n    currency: "BRL",\n    transaction_id: "{$orderid}"        // evita contagem em dobro\n  });\n})();\n</script>\n```\n')
A('Para o consentimento valer em todo o domínio, o portal grava o cookie `ms_consent` em `.midiaserver.com.br` (já implementado). Confira no WHMCS as variáveis exatas da página de conclusão do pedido. **Teste com um pedido real de baixo valor** antes de ligar o orçamento.\n')
A('## 4. Estrutura de campanhas (Pesquisa)\n')
A('Rede de Pesquisa, sem parceiros de pesquisa no início, Brasil, português, dispositivos: todos. Lance inicial recomendado: **cliques com limite de CPC** nas duas primeiras semanas ou **CPC manual**, passando para **Maximizar conversões** quando houver volume de conversões (referência comum: 15 a 30 por mês por campanha). Palavras na correspondência **exata** e de **frase** primeiro; só amplie para ampla com lances inteligentes e muita conversão.\n')
A('| Campanha | Landing | Grupos de anúncios |\n|---|---|---|')
for name, lp, groups, *_ in C + BRAND:
    A(f'| {name} | `{lp}` | {", ".join(g for g, _ in groups)} |')
A('\n## 5. Importar no Google Ads Editor\n')
A('Arquivos prontos em `docs/ads/`:\n- `palavras-chave.csv`: todas as palavras em **Exata** e **Frase** com a URL final (já com `utm_source=google&utm_medium=cpc`).\n- `negativas.csv`: palavras negativas por campanha.\n- `anuncios-rsa.csv`: um anúncio responsivo de pesquisa (15 títulos, 4 descrições) por grupo.\n\nNo Google Ads Editor: Conta > Importar > Colar/Importar de arquivo CSV. Crie as campanhas primeiro (nome igual ao da coluna *Campaign*), depois importe. Revise antes de publicar. Os nomes de colunas podem variar conforme a versão do Editor.\n')
A('**Modelo de URL final (parâmetros de rastreamento da conta):**\n```\n{lpurl}?utm_source=google&utm_medium=cpc&utm_campaign={campaignid}&utm_term={keyword}&matchtype={matchtype}&network={network}\n```\nAtive o **auto-tagging** (gclid). O formulário guarda `gclid`, UTMs e `matchtype` com cada lead.\n')
A('## 6. Anúncios por campanha (resumo)\n')
for name, lp, groups, H, D, p1, p2 in C + BRAND:
    A(f'### {name}\nURL: `{SITE}{lp}` · caminho de exibição: `/{p1}/{p2}`\n')
    A('**Títulos (' + str(len(H)) + '):** ' + ' · '.join(f'{h} ({len(h)})' for h in H) + '\n')
    A('**Descrições:** ' + '  \n'.join(f'{i+1}. {d} ({len(d)})' for i, d in enumerate(D)) + '\n')
A('## 7. Palavras negativas (todas as campanhas, correspondência de frase)\n')
A(', '.join(NEG) + '\n')
A('Se alguma delas bloquear tráfego bom (por exemplo "ouvir rádio" em campanhas de rádio online), remova da lista. Revise o relatório de **termos de pesquisa** toda semana e acrescente negativas.\n')
A('## 8. Extensões (ativos) recomendadas\n')
A('- **Sitelinks:** Planos de streaming (`/lp/streaming-radio`), Hospedagem cPanel, Automação de Rádio, Conteúdos para Rádios, Sites para Rádio, Revenda de Hospedagem, Fale no WhatsApp.\n- **Frases de destaque:** SSL grátis; Site incluso no streaming; Pague com Pix; Teste grátis do Studio (somente na campanha de automação); Suporte por WhatsApp.\n- **Snippets estruturados (Tipos):** Streaming, AutoDJ, Revenda, Automação, Conteúdos, Sites.\n- **Preço:** use ativos de preço com os planos reais da vitrine (Bronze a Premium) e **atualize quando o WHMCS mudar**.\n- **Imagens:** use as imagens 1200x630 de `public/assets/img/og/` e as capturas do Studio.\n')
A('## 9. Remarketing e públicos\n')
A('Com o consentimento dado, o Google Ads/GA4 forma listas de quem visitou páginas de produto e de quem iniciou a compra (`begin_checkout`) sem concluir. Crie públicos no Google Ads (Ferramentas > Gerenciador de públicos) usando as tags do site e rode anúncios de Pesquisa para listas de remarketing (RLSA) com lances maiores.\n')
A('## 10. Checklist antes de ligar\n')
A('- [ ] `ads_id` e os rótulos de conversão configurados e testados (Tag Assistant ou "Tag Assistant Companion").\n- [ ] Pedido de teste no WHMCS gerou a conversão de **Compra**.\n- [ ] Formulário de lead enviado e conversão `generate_lead` vista na página `/obrigado`.\n- [ ] Turnstile configurado (`turnstile_site_key` e `turnstile_secret`) se houver spam.\n- [ ] Webhook de lead (`lead_webhook_url`) configurado para avisar a equipe, ou rotina para ler `php bin/console leads:list` várias vezes ao dia.\n- [ ] Textos legais revisados (privacidade, termos, cookies) com razão social e CNPJ.\n- [ ] Política do Google Ads: sem superlativos não comprováveis ("o melhor"), sem nomes de concorrentes nos anúncios, sem prometer o que o plano não inclui.\n- [ ] Search Console verificado e sitemap enviado.\n')
open(os.path.join(ROOT, 'docs/GOOGLE-ADS.md'), 'w', encoding='utf-8').write('\n'.join(L))
kw = sum(len(k) for *_, groups, H, D, p1, p2 in [(c[0], c[1], c[2], c[3], c[4], c[5], c[6]) for c in C + BRAND] for g, k in groups)
print(f'OK: {len(C)+len(BRAND)} campanhas, {sum(len(c[2]) for c in C+BRAND)} grupos, {kw} palavras (x2 correspondências), {len(NEG)} negativas')
