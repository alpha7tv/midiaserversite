# Cine Pulse — Relatório SEO (22/11/2026)

## Antes / depois
Antes: não havia site (projeto novo). Depois: 20 URLs indexáveis, cada uma com title, meta description, H1, canonical, Open Graph/Twitter, breadcrumbs e JSON-LD próprios.

## Técnico implementado
- `lang="pt-BR"`, HTML semântico, 1 H1 por página, H2/H3, alt nas imagens, layout responsivo (sem rolagem lateral em 390 px).
- Canonical absoluto em todas as páginas; `noindex` em /obrigado, /descadastrar, 404 e /admin (também `X-Robots-Tag`).
- `/sitemap.xml` dinâmico (só URLs indexáveis, inclui as páginas dos planos) e `/robots.txt` com Sitemap.
- 301: barra final e maiúsculas → minúsculas sem barra; www → sem www; atalhos (/teste, /revenda, /franquia, /apontamento, /painel-iptv, /provedores, /assinar).
- JSON-LD: Organization (com contato 24h), WebSite (home) e BreadcrumbList. Sem reviews, notas ou dados inventados. Sem FAQPage (Google restringe esse rich result).
- Sem `meta keywords`.
- Desempenho: CSS único sem framework, sem fontes externas, logo em WebP (17 KB), compressão gzip, cache 7 dias de estáticos, JS `defer`.
- Rastreamento: GTM, GA4, Google Ads (conversões lead e clique no WhatsApp) e Pixel Meta configuráveis no admin; Consent Mode v2 com tudo negado até o aceite (LGPD).
- Eventos: `whatsapp_click`, `form_submit`, `generate_lead`, `popup_view`; Meta `PageView`, `Contact`, `Lead`.
- UTMs/gclid/fbclid gravados no lead.

## Mapa palavra-chave → URL
| URL | Principal | Secundárias |
|---|---|---|
| / | cine pulse iptv | serviço iptv, iptv de qualidade, revenda iptv |
| /planos | assinatura iptv | iptv preço, iptv mensalidade, iptv plano anual/semestral/trimestral, cine pulse planos |
| /teste-gratis | teste iptv grátis | iptv para smart tv, iptv com ativação rápida |
| /contato | cine pulse suporte | iptv com suporte, iptv com atendimento |
| /revenda-iptv | revenda iptv | revendedor iptv, painel iptv mensalista, painel iptv por créditos, painel iptv |
| /revenda-iptv/painel-10-creditos … painel-3000-creditos | painel iptv N créditos | painel iptv barato, painel iptv com créditos acumulativos, revenda iptv para iniciantes (10) |
| /revenda-iptv/painel-ilimitado | painel iptv ilimitado | melhor painel iptv para revenda |
| /apontamento-iptv | apontamento iptv | apontamento de servidor iptv, servidor iptv para revenda, iptv por ativo |
| /franquia-iptv | franquia iptv | franquia iptv marca própria, iptv solução white label, painel iptv personalizado |
| /iptv-para-provedores | iptv para provedor de internet | iptv para isp, iptv valor agregado provedor |
| /como-funciona-iptv | como funciona iptv | iptv precisa de internet, qual internet precisa para iptv, como escolher iptv, como escolher painel iptv, painel iptv ou franquia |
| /dispositivos | iptv para smart tv | iptv tv box android, iptv para celular, iptv como instalar, iptv samsung/lg smart tv |

As demais expressões do banco de 300 entram como variações naturais no texto dessas páginas. Termos sobre "lista de canais", "4K", "sem travar", "esportes ao vivo" e nomes de plataformas foram deixados de fora de propósito (risco de política do Google Ads e alegações não verificadas).
Volumes de busca: **não validados**. Conferir no Keyword Planner e no Search Console após 30 dias de indexação.

## Titles e descriptions
Editáveis em /admin/seo. Valores iniciais em `src/seed/seo.js`; páginas de plano geram title automático, ex.: "Painel IPTV 100 Créditos por R$ 80,00/mês | Cine Pulse".

## Pendências / precisa de aprovação
1. Link do trailer oficial no YouTube (admin → Configurações). Aviso: trailer de filme num site de IPTV pode ser lido pelo Google Ads como sugestão de acesso a conteúdo de terceiros; se um anúncio for reprovado, desligue o trailer.
2. IDs de GA4, Google Ads (+ rótulos de conversão), GTM e Pixel Meta.
3. Chave SMTP da Brevo e URL/instância/apikey da Evolution API.
4. Validar o formato de resposta do webhook do Sigma pelo botão "testar painel".
5. Cadastrar o site no Google Search Console e enviar o sitemap.
6. Não há depoimentos nem números de clientes: só adicione se forem reais.
