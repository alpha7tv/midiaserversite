<?php
declare(strict_types=1);

/** Menu principal. 'whmcs' => chave tratada em runtime (links reais do WHMCS). */
return [
    ['label' => 'Hospedagem', 'items' => [
        ['Hospedagem cPanel', '/hospedagem'],
        ['Revenda de Hospedagem', '/revenda-hospedagem'],
        ['VPS', '/vps'],
        ['Domínios', 'whmcs:domain'],
    ]],
    ['label' => 'Streaming', 'items' => [
        ['Streaming de Áudio', '/streaming'],
        ['Web Rádio Completa', '/web-radio-completa'],
        ['Revenda de Streaming', '/revenda-streaming'],
        ['Sites para Web Rádio', '/sites-para-radio'],
    ]],
    ['label' => 'Automação', 'items' => [
        ['Software de Automação', '/automacao-radio'],
        ['Recursos', '/automacao-radio#recursos'],
        ['Planos', '/automacao-radio#planos'],
        ['Atualizações', '/automacao-radio#versoes'],
        ['Download / Demonstração', '/automacao-radio#download'],
    ]],
    ['label' => 'Conteúdos', 'items' => [
        ['Ouvir amostras', '/conteudos-para-radio#amostras'],
        ['Programas', '/conteudos-para-radio#categorias'],
        ['Programetes', '/conteudos-para-radio#categorias'],
        ['Conteúdo diário', '/conteudos-para-radio#como-funciona'],
        ['Fim de semana', '/conteudos-para-radio#planos'],
        ['Como funciona', '/conteudos-para-radio#como-funciona'],
        ['Planos', '/conteudos-para-radio#planos'],
    ]],
    ['label' => 'Rádios online', 'items' => [
        ['Ouvir rádios', '/radios'],
        ['Cadastrar minha rádio', '/radios/cadastrar-radio'],
        ['Rádios em destaque', '/radios#destaques'],
    ]],
    ['label' => 'Downloads', 'items' => [
        ['Automação', '/downloads#dl-automacao'],
        ['Encoders', '/downloads#dl-encoders'],
        ['Mídia Rádio Studio', '/downloads#midia-radio-studio'],
        ['Ferramentas para rádio', '/downloads#dl-utilitarios'],
    ]],
    ['label' => 'Revenda', 'items' => [
        ['Revenda de Hospedagem', '/revenda-hospedagem'],
        ['Revenda de Streaming', '/revenda-streaming'],
    ]],
    ['label' => 'Empresa', 'items' => [
        ['Sobre', '/sobre'],
        ['Blog / Novidades', '/blog'],
        ['Contato', '/contato'],
        ['Suporte', '/suporte'],
    ]],
];
