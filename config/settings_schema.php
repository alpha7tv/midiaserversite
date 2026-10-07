<?php
declare(strict_types=1);

/**
 * Campos editáveis em /admin/configuracoes.
 * type: text | url | email | secret | select | tel
 * pattern: regex que o valor precisa cumprir (vazio sempre é aceito, desliga o recurso).
 */
return [
    'Contato e marca' => [
        ['whatsapp_number', 'WhatsApp oficial', 'tel', 'Somente números com DDI e DDD. Exemplo: 5514988159045. Usado em todos os botões do site.', '/^55[1-9][1-9]9?\d{8}$/', 'Use o formato 5514988159045.'],
        ['support_email', 'E-mail de contato', 'email', 'Aparece no rodapé, em Contato e nas políticas.', '', ''],
        ['site_name', 'Nome da empresa', 'text', '', '', ''],
        ['youtube_studio', 'Canal do YouTube do Studio', 'url', 'Link dos tutoriais do Mídia Rádio Studio.', '', ''],
    ],
    'Google Ads, Analytics e Meta' => [
        ['gtm_id', 'Google Tag Manager (ID)', 'text', 'Formato GTM-XXXXXXX. Se preencher, o GTM controla as tags e o site só alimenta o dataLayer.', '/^GTM-[A-Z0-9]{4,10}$/', 'Use o formato GTM-XXXXXXX.'],
        ['ga4_id', 'Google Analytics 4 (ID de métricas)', 'text', 'Formato G-XXXXXXXXXX. Não precisa se usar o GTM.', '/^G-[A-Z0-9]{6,14}$/', 'Use o formato G-XXXXXXXXXX.'],
        ['ads_id', 'Google Ads (ID da conta)', 'text', 'Formato AW-XXXXXXXXX.', '/^AW-\d{6,12}$/', 'Use o formato AW-123456789.'],
        ['ads_conv_generate_lead', 'Conversão: lead (formulário)', 'text', 'Rótulo AW-XXXXXXXXX/abcDEFghi. Disparada na página /obrigado.', '/^AW-\d{6,12}\/[\w-]{6,40}$/', 'Use o formato AW-123456789/abcDEFghi.'],
        ['ads_conv_whatsapp_click', 'Conversão: clique no WhatsApp', 'text', 'Mesmo formato do rótulo.', '/^AW-\d{6,12}\/[\w-]{6,40}$/', 'Use o formato AW-123456789/abcDEFghi.'],
        ['ads_conv_begin_checkout', 'Conversão: clique em Contratar', 'text', 'Mesmo formato do rótulo.', '/^AW-\d{6,12}\/[\w-]{6,40}$/', 'Use o formato AW-123456789/abcDEFghi.'],
        ['ads_conv_file_download', 'Conversão: download', 'text', 'Mesmo formato do rótulo.', '/^AW-\d{6,12}\/[\w-]{6,40}$/', 'Use o formato AW-123456789/abcDEFghi.'],
        ['ads_conv_view_demo', 'Conversão: demonstração de site', 'text', 'Mesmo formato do rótulo.', '/^AW-\d{6,12}\/[\w-]{6,40}$/', 'Use o formato AW-123456789/abcDEFghi.'],
        ['meta_pixel_id', 'Meta Pixel (ID)', 'text', 'Somente números.', '/^\d{8,20}$/', 'O ID do Pixel tem só números.'],
        ['search_console', 'Google Search Console (código de verificação)', 'text', 'Valor do atributo content da meta tag google-site-verification.', '/^[\w-]{20,80}$/', 'Cole apenas o código, sem a tag.'],
    ],
    'Anti-spam (Cloudflare Turnstile)' => [
        ['turnstile_site_key', 'Chave do site (pública)', 'text', 'Se preencher junto com a chave secreta, os formulários passam a exigir o Turnstile.', '/^[\w-]{10,60}$/', 'Chave inválida.'],
        ['turnstile_secret', 'Chave secreta', 'secret', 'Guardada criptografada. Deixe em branco para manter a atual.', '', ''],
    ],
    'Integrações' => [
        ['whmcs_base', 'Endereço do WHMCS', 'url', 'Sem barra no final. Os botões Contratar usam este endereço.', '/^https:\/\/[\w.-]+(\/[\w.\/-]*)?$/', 'Use https:// e sem barra no final.'],
        ['conteudos_url', 'Site de Conteúdos', 'url', 'Origem das amostras de áudio e capas.', '/^https:\/\/[\w.-]+\/?$/', 'Use https://.'],
        ['radios_url', 'Diretório Rádios do Brasil', 'url', '', '/^https:\/\/[\w.-]+\/?$/', 'Use https://.'],
        ['studio_download', 'Instalador do Mídia Rádio Studio', 'url', 'Link direto do .exe.', '/^https:\/\/\S+$/', 'Use https://.'],
        ['demo_pattern', 'Endereço das demonstrações dos modelos de site', 'text', 'Use {n} no lugar do número do modelo. Deixe vazio para esconder os botões Ver demonstração.', '/^https?:\/\/\S*\{n\}\S*$/', 'Inclua {n} no endereço.'],
        ['lead_webhook_url', 'Webhook de novos leads', 'url', 'Receberá um POST JSON a cada lead (para n8n, Zapier, WhatsApp automático). Opcional.', '/^https:\/\/\S+$/', 'Use https://.'],
        ['studio_webhook_secret', 'Segredo do webhook de versões do Studio', 'secret', 'Assina as chamadas em POST /api/v1/studio/version. Guardado criptografado.', '', ''],
    ],
    'E-mail (SMTP) para newsletter' => [
        ['smtp_host', 'Servidor SMTP', 'text', 'Ex.: smtp.seuprovedor.com. Deixe vazio para desligar a newsletter.', '/^[\w.-]+$/', 'Host inválido.'],
        ['smtp_port', 'Porta', 'text', '587 (STARTTLS) ou 465 (SSL).', '/^\d{2,5}$/', 'Porta inválida.'],
        ['smtp_secure', 'Segurança', 'select:tls=STARTTLS (587),ssl=SSL (465),none=Nenhuma (somente testes)', '', '', ''],
        ['smtp_user', 'Usuário', 'text', '', '', ''],
        ['smtp_pass', 'Senha', 'secret', 'Guardada criptografada. Deixe em branco para manter a atual.', '', ''],
        ['smtp_from_email', 'E-mail remetente', 'email', 'Use um endereço do seu domínio, com SPF e DKIM do provedor configurados.', '', ''],
        ['smtp_from_name', 'Nome do remetente', 'text', '', '', ''],
        ['newsletter_footer', 'Texto do rodapé da newsletter', 'text', 'Aparece em todo e-mail, junto do link de descadastro.', '', ''],
    ],
];
