<?php
declare(strict_types=1);

/**
 * Catálogo inicial, copiado da vitrine pública do WHMCS (coletada em 07/10/2026).
 * Os pids são os IDs reais do WHMCS. Preços: R$ por mês. Nada aqui é inventado.
 * Valores true/false = recurso incluso / não incluso. Atualize pelo painel quando o WHMCS mudar.
 */
return [
    'categories' => [
        ['slug' => 'hospedagem',   'name' => 'Hospedagem',          'type' => 'product', 'sort_order' => 1],
        ['slug' => 'streaming',    'name' => 'Streaming e Web Rádio', 'type' => 'product', 'sort_order' => 2],
        ['slug' => 'automacao',    'name' => 'Automação de Rádios', 'type' => 'product', 'sort_order' => 3],
        ['slug' => 'conteudos',    'name' => 'Conteúdos para Rádios', 'type' => 'product', 'sort_order' => 4],
        ['slug' => 'revenda',      'name' => 'Revenda',             'type' => 'product', 'sort_order' => 5],
        ['slug' => 'dl-automacao', 'name' => 'Automação de Rádio',  'type' => 'download', 'sort_order' => 1],
        ['slug' => 'dl-encoders',  'name' => 'Encoders',            'type' => 'download', 'sort_order' => 2],
        ['slug' => 'dl-utilitarios', 'name' => 'Utilitários',       'type' => 'download', 'sort_order' => 3],
    ],

    'products' => [
        ['slug' => 'hospedagem',          'name' => 'Hospedagem de Sites cPanel', 'cat' => 'hospedagem', 'whmcs_group' => 'hospedagem-de-sites-cpanel', 'sort_order' => 1],
        ['slug' => 'revenda-hospedagem',  'name' => 'Revenda de Hospedagem',      'cat' => 'revenda',    'whmcs_group' => 'revenda-de-hospedagem',      'sort_order' => 2],
        ['slug' => 'streaming',           'name' => 'Streaming de Áudio',         'cat' => 'streaming',  'whmcs_group' => 'streaming-de-audio',         'sort_order' => 3],
        ['slug' => 'revenda-streaming',   'name' => 'Revenda de Streaming',       'cat' => 'revenda',    'whmcs_group' => 'revenda-streaming',          'sort_order' => 4],
        ['slug' => 'sites-para-radio',    'name' => 'Sites para Rádio',           'cat' => 'streaming',  'whmcs_group' => 'sites-para-radio',           'sort_order' => 5],
        ['slug' => 'automacao-radio',     'name' => 'Mídia Rádio Studio',         'cat' => 'automacao',  'whmcs_group' => 'automacao-para-radios',      'sort_order' => 6],
        ['slug' => 'conteudos-para-radio', 'name' => 'Conteúdos para Rádios',     'cat' => 'conteudos',  'whmcs_group' => 'conteudos-para-radios',      'sort_order' => 7],
    ],

    'plans' => [
        // ---------- Hospedagem cPanel ----------
        ['product' => 'hospedagem', 'pid' => 7,  'name' => 'Hospedagem Bronze',  'price' => 19.90, 'highlight' => 0, 'sort' => 1, 'specs' => [
            'Espaço em disco NVMe' => '10 GB', 'Transferência' => 'Ilimitada', 'Contas de e-mail' => '5',
            'Bancos de dados MySQL' => '1', 'Certificado SSL' => 'Grátis', 'Backup' => false, 'Painel' => 'cPanel']],
        ['product' => 'hospedagem', 'pid' => 8,  'name' => 'Hospedagem Prata',   'price' => 24.90, 'highlight' => 1, 'sort' => 2, 'specs' => [
            'Espaço em disco NVMe' => '25 GB', 'Transferência' => 'Ilimitada', 'Contas de e-mail' => '20',
            'Bancos de dados MySQL' => '5', 'Certificado SSL' => 'Grátis', 'Backup' => 'Semanal', 'Painel' => 'cPanel']],
        ['product' => 'hospedagem', 'pid' => 9,  'name' => 'Hospedagem Ouro',    'price' => 29.90, 'highlight' => 0, 'sort' => 3, 'specs' => [
            'Espaço em disco NVMe' => '50 GB', 'Transferência' => 'Ilimitada', 'Contas de e-mail' => 'Ilimitadas',
            'Bancos de dados MySQL' => '15', 'Certificado SSL' => 'Grátis', 'Backup' => 'Semanal', 'Painel' => 'cPanel']],
        ['product' => 'hospedagem', 'pid' => 10, 'name' => 'Hospedagem Premium', 'price' => 34.90, 'highlight' => 0, 'sort' => 4, 'specs' => [
            'Espaço em disco NVMe' => '100 GB', 'Transferência' => 'Ilimitada', 'Contas de e-mail' => 'Ilimitadas',
            'Bancos de dados MySQL' => 'Ilimitados', 'Certificado SSL' => 'Grátis', 'Backup' => 'Diário', 'Painel' => 'cPanel']],

        // ---------- Revenda de hospedagem ----------
        ['product' => 'revenda-hospedagem', 'pid' => 11, 'name' => 'Revenda Bronze',  'price' => 49.90,  'highlight' => 0, 'sort' => 1, 'specs' => [
            'Espaço em disco NVMe' => '50 GB', 'Contas cPanel' => '10', 'Painel WHM' => true, 'Marca própria' => false, 'Transferência' => 'Ilimitada']],
        ['product' => 'revenda-hospedagem', 'pid' => 12, 'name' => 'Revenda Prata',   'price' => 79.90,  'highlight' => 1, 'sort' => 2, 'specs' => [
            'Espaço em disco NVMe' => '100 GB', 'Contas cPanel' => '25', 'Painel WHM' => true, 'Marca própria' => true, 'Transferência' => 'Ilimitada']],
        ['product' => 'revenda-hospedagem', 'pid' => 13, 'name' => 'Revenda Ouro',    'price' => 119.90, 'highlight' => 0, 'sort' => 3, 'specs' => [
            'Espaço em disco NVMe' => '250 GB', 'Contas cPanel' => '60', 'Painel WHM' => true, 'Marca própria' => true, 'Transferência' => 'Ilimitada']],
        ['product' => 'revenda-hospedagem', 'pid' => 14, 'name' => 'Revenda Premium', 'price' => 149.90, 'highlight' => 0, 'sort' => 4, 'specs' => [
            'Espaço em disco NVMe' => '500 GB', 'Contas cPanel' => 'Ilimitadas', 'Painel WHM' => true, 'Marca própria' => true, 'Transferência' => 'Ilimitada']],

        // ---------- Streaming de áudio (todos incluem site administrável) ----------
        ['product' => 'streaming', 'pid' => 1, 'name' => 'Plano Bronze', 'price' => 49.90,  'highlight' => 0, 'sort' => 1, 'includes_site' => 1, 'specs' => [
            'Qualidade de transmissão' => '128 kbps', 'Ouvintes simultâneos' => 'Ilimitados', 'Espaço de AutoDJ' => '10 GB',
            'Painel de controle' => 'Midia Cast', 'Acervo MP3 e campanhas' => true, 'Músicas no AutoDJ' => true,
            'Site administrável' => true, 'Web App iOS e Android' => true,
            'Conteúdos inclusos' => false, 'Download de programas e programetes' => false,
            'Câmera no estúdio (só ao vivo)' => false, 'Web TV (ao vivo e gravado)' => false,
            'Live nas redes sociais' => false, 'Aplicativo Android PREMIUM' => false, 'Aplicativo com notificação push' => false]],
        ['product' => 'streaming', 'pid' => 2, 'name' => 'Plano Prata',  'price' => 69.90,  'highlight' => 1, 'sort' => 2, 'includes_site' => 1, 'specs' => [
            'Qualidade de transmissão' => '128 kbps', 'Ouvintes simultâneos' => 'Ilimitados', 'Espaço de AutoDJ' => '30 GB',
            'Painel de controle' => 'Midia Cast', 'Acervo MP3 e campanhas' => true, 'Músicas no AutoDJ' => true,
            'Site administrável' => true, 'Web App iOS e Android' => true,
            'Conteúdos inclusos' => true, 'Download de programas e programetes' => true,
            'Câmera no estúdio (só ao vivo)' => true, 'Web TV (ao vivo e gravado)' => false,
            'Live nas redes sociais' => true, 'Aplicativo Android PREMIUM' => true, 'Aplicativo com notificação push' => true]],
        ['product' => 'streaming', 'pid' => 3, 'name' => 'Plano Ouro',   'price' => 129.90, 'highlight' => 0, 'sort' => 3, 'includes_site' => 1, 'specs' => [
            'Qualidade de transmissão' => '320 kbps', 'Ouvintes simultâneos' => 'Ilimitados', 'Espaço de AutoDJ' => '60 GB',
            'Painel de controle' => 'Midia Cast', 'Acervo MP3 e campanhas' => true, 'Músicas no AutoDJ' => true,
            'Site administrável' => true, 'Web App iOS e Android' => true,
            'Conteúdos inclusos' => true, 'Download de programas e programetes' => true,
            'Câmera no estúdio (só ao vivo)' => false, 'Web TV (ao vivo e gravado)' => true,
            'Live nas redes sociais' => true, 'Aplicativo Android PREMIUM' => true, 'Aplicativo com notificação push' => true]],

        // ---------- Revenda de streaming ----------
        ['product' => 'revenda-streaming', 'pid' => 15, 'name' => 'Revenda Streaming Bronze',  'price' => 69.90,  'highlight' => 0, 'sort' => 1, 'specs' => [
            'Contas de streaming' => '5', 'Disco compartilhado' => '50 GB', 'Sub-revendas' => false,
            'Ouvintes' => 'Ilimitados', 'Qualidade' => 'Até 128 kbps', 'Painel de revenda' => true, 'Marca própria' => true]],
        ['product' => 'revenda-streaming', 'pid' => 16, 'name' => 'Revenda Streaming Prata',   'price' => 99.90,  'highlight' => 1, 'sort' => 2, 'specs' => [
            'Contas de streaming' => '10', 'Disco compartilhado' => '80 GB', 'Sub-revendas' => '2',
            'Ouvintes' => 'Ilimitados', 'Qualidade' => 'Até 128 kbps', 'Painel de revenda' => true, 'Marca própria' => true]],
        ['product' => 'revenda-streaming', 'pid' => 17, 'name' => 'Revenda Streaming Ouro',    'price' => 149.90, 'highlight' => 0, 'sort' => 3, 'specs' => [
            'Contas de streaming' => '30', 'Disco compartilhado' => '150 GB', 'Sub-revendas' => '5',
            'Ouvintes' => 'Ilimitados', 'Qualidade' => 'Até 128 kbps', 'Painel de revenda' => true, 'Marca própria' => true]],
        ['product' => 'revenda-streaming', 'pid' => 18, 'name' => 'Revenda Streaming Premium', 'price' => 199.90, 'highlight' => 0, 'sort' => 4, 'specs' => [
            'Contas de streaming' => '60', 'Disco compartilhado' => '250 GB', 'Sub-revendas' => '10',
            'Ouvintes' => 'Ilimitados', 'Qualidade' => 'Até 320 kbps', 'Painel de revenda' => true, 'Marca própria' => true]],

        // ---------- Conteúdos para rádios ----------
        ['product' => 'conteudos-para-radio', 'pid' => 4, 'name' => 'Pacote Básico',        'price' => 89.90,  'highlight' => 0, 'sort' => 1, 'specs' => [
            'Espaço na nuvem' => '10 GB', 'Programetes (notícias, esporte, humor, saúde, curiosidades e mais)' => true,
            'Programas de segunda a sexta' => false, 'Programas e conteúdo de fim de semana' => false,
            'Programas divididos em blocos' => false, 'Novos conteúdos incluídos automaticamente' => false,
            'Atualização automática todos os dias' => true, 'Aplicativo de sincronização + acesso pelo navegador' => true,
            'Suporte' => 'WhatsApp']],
        ['product' => 'conteudos-para-radio', 'pid' => 5, 'name' => 'Pacote Intermediário', 'price' => 129.90, 'highlight' => 1, 'sort' => 2, 'specs' => [
            'Espaço na nuvem' => '20 GB', 'Programetes (notícias, esporte, humor, saúde, curiosidades e mais)' => true,
            'Programas de segunda a sexta' => true, 'Programas e conteúdo de fim de semana' => false,
            'Programas divididos em blocos' => true, 'Novos conteúdos incluídos automaticamente' => false,
            'Atualização automática todos os dias' => true, 'Aplicativo de sincronização + acesso pelo navegador' => true,
            'Suporte' => 'WhatsApp']],
        ['product' => 'conteudos-para-radio', 'pid' => 6, 'name' => 'Pacote Profissional',  'price' => 149.90, 'highlight' => 0, 'sort' => 3, 'specs' => [
            'Espaço na nuvem' => '40 GB', 'Programetes (notícias, esporte, humor, saúde, curiosidades e mais)' => true,
            'Programas de segunda a sexta' => true, 'Programas e conteúdo de fim de semana' => true,
            'Programas divididos em blocos' => true, 'Novos conteúdos incluídos automaticamente' => true,
            'Atualização automática todos os dias' => true, 'Aplicativo de sincronização + acesso pelo navegador' => true,
            'Suporte' => 'Prioritário']],

        // ---------- Sites para rádio ----------
        ['product' => 'sites-para-radio', 'pid' => 19, 'name' => 'Site Administrável', 'price' => 29.90, 'highlight' => 1, 'sort' => 1, 'specs' => [
            'Endereço grátis' => 'suaradio.radioweb.top', 'Player ao vivo com a música tocando' => true,
            'Notícias automáticas do seu estilo' => true, 'Painel fácil para editar tudo' => true,
            'Programação, equipe, eventos e mural' => true, 'Modelos para escolher' => '15',
            'Incluso nos planos de streaming' => true]],

        // ---------- Mídia Rádio Studio ----------
        ['product' => 'automacao-radio', 'pid' => 20, 'name' => 'Mídia Rádio Studio — Licença 1 PC', 'price' => 59.90, 'price_year' => 575.04, 'highlight' => 1, 'sort' => 1, 'specs' => [
            'Licença para' => '1 computador', 'Todas as funções liberadas' => true, 'Atualizações e suporte no WhatsApp' => true,
            'Plano anual' => 'R$ 575,04 (20% de desconto)']],
    ],
];
