<?php
declare(strict_types=1);

/**
 * Central de downloads inicial.
 * - Mídia Rádio Studio: instalador próprio (download direto no servidor de licenças).
 * - Terceiros: apenas LINK para o site oficial de cada programa (não redistribuímos binários de terceiros).
 *   Lista e descrições vêm da página de downloads dos Conteúdos para Rádios.
 */
return [
    ['cat' => 'dl-automacao', 'slug' => 'midia-radio-studio', 'name' => 'Mídia Rádio Studio', 'os' => 'Windows 10 e 11 (64 bits)',
        'description' => 'Automação completa para rádio FM, web rádio e comunitária. Baixe e use o modo demonstração (playlist de até 10 músicas e cartucheira de até 5 vinhetas); a chave de licença libera todas as funções.',
        'file_url' => '{studio_download}', 'external' => 0, 'size_bytes' => 59876808, 'released_at' => '2026-10-02 22:08:37',
        'license_note' => 'Software licenciado da Mídia Server. Demonstração gratuita.', 'own' => true],

    ['cat' => 'dl-automacao', 'slug' => 'salamandra', 'name' => 'Salamandra', 'os' => 'Windows 7, 8, 10 e 11', 'file_url' => 'https://salamandraautomacao.com/',
        'description' => 'Automação brasileira, simples e estável: programador de eventos, locução de horário, cartucheira e vinhetas sobre as músicas.', 'license_note' => 'Grátis'],
    ['cat' => 'dl-automacao', 'slug' => 'radiodj', 'name' => 'RadioDJ', 'os' => 'Windows', 'file_url' => 'https://www.radiodj.ro/',
        'description' => 'Automação completa com banco de músicas, rotações, comerciais agendados e transmissão para Shoutcast/Icecast.', 'license_note' => 'Grátis'],
    ['cat' => 'dl-automacao', 'slug' => 'zararadio', 'name' => 'ZaraRadio', 'os' => 'Windows', 'file_url' => 'http://www.zarastudio.es/',
        'description' => 'Clássico das rádios: leve, fácil, com listas, eventos agendados, vinhetas e hora certa.', 'license_note' => 'Grátis'],
    ['cat' => 'dl-automacao', 'slug' => 'playit-live', 'name' => 'PlayIt Live', 'os' => 'Windows', 'file_url' => 'https://www.playitsoftware.com/',
        'description' => 'Automação moderna com decks ao vivo, cartucheira, pastas monitoradas e grade de programação.', 'license_note' => 'Grátis'],
    ['cat' => 'dl-automacao', 'slug' => 'radioboss', 'name' => 'RadioBOSS', 'os' => 'Windows', 'file_url' => 'https://www.djsoft.net/',
        'description' => 'Automação profissional com agendador, comerciais, hora certa, efeitos e encoder integrado.', 'license_note' => 'Pago · teste grátis'],
    ['cat' => 'dl-automacao', 'slug' => 'jazler-radiostar', 'name' => 'Jazler RadioStar', 'os' => 'Windows', 'file_url' => 'https://jazler.com/',
        'description' => 'Automação profissional muito usada em rádios FM, com grade, comerciais e locução de horário.', 'license_note' => 'Pago'],
    ['cat' => 'dl-automacao', 'slug' => 'playoutone', 'name' => 'PlayoutONE', 'os' => 'Windows', 'file_url' => 'https://aiir.com/',
        'description' => 'Automação profissional para rádios, hoje parte da plataforma Aiir.', 'license_note' => 'Pago'],
    ['cat' => 'dl-automacao', 'slug' => 'omniplayer', 'name' => 'OmniPlayer', 'os' => 'Windows', 'file_url' => 'https://www.mibroadcastservices.nl/products/omniplayer/',
        'description' => 'Automação profissional da M&I Broadcast Services para emissoras de todos os tamanhos.', 'license_note' => 'Pago'],
    ['cat' => 'dl-automacao', 'slug' => 'myriad-playout', 'name' => 'Myriad Playout', 'os' => 'Windows', 'file_url' => 'https://www.broadcastradio.com/myriad-6-playout',
        'description' => 'Automação profissional da Broadcast Radio para rádios FM e web.', 'license_note' => 'Pago'],
    ['cat' => 'dl-automacao', 'slug' => 'proppfrexx-onair', 'name' => 'ProppFrexx ONAIR', 'os' => 'Windows', 'file_url' => 'https://www.radio42.com/proppfrexx/',
        'description' => 'Automação e playout profissional, com muitos recursos para programação ao vivo.', 'license_note' => 'Pago'],
    ['cat' => 'dl-automacao', 'slug' => 'rivendell', 'name' => 'Rivendell', 'os' => 'Linux', 'file_url' => 'https://www.rivendellaudio.org/',
        'description' => 'Sistema de automação completo e aberto para emissoras que usam Linux.', 'license_note' => 'Grátis · código aberto'],
    ['cat' => 'dl-automacao', 'slug' => 'enco-dad', 'name' => 'ENCO DAD', 'os' => 'Windows', 'file_url' => 'https://www.enco.com/products/dad',
        'description' => 'Sistema de automação profissional da ENCO para emissoras de rádio.', 'license_note' => 'Pago'],
    ['cat' => 'dl-automacao', 'slug' => 'djb-radio', 'name' => 'DJB Radio', 'os' => 'Windows', 'file_url' => 'https://www.djbradio.com/',
        'description' => 'Automação para rádios FM e web, com grade, comerciais e ao vivo.', 'license_note' => 'Pago'],

    ['cat' => 'dl-encoders', 'slug' => 'butt', 'name' => 'BUTT', 'os' => 'Windows, Mac e Linux', 'file_url' => 'https://danielnoethen.de/butt/',
        'description' => 'Encoder simples para transmitir ao vivo da mesa de som para o streaming (Shoutcast e Icecast).', 'license_note' => 'Grátis · código aberto'],
    ['cat' => 'dl-encoders', 'slug' => 'mixxx', 'name' => 'Mixxx', 'os' => 'Windows, Mac e Linux', 'file_url' => 'https://mixxx.org/',
        'description' => 'Software de DJ com transmissão ao vivo integrada: mixe e transmita direto para a sua rádio.', 'license_note' => 'Grátis · código aberto'],
    ['cat' => 'dl-encoders', 'slug' => 'rocket-broadcaster', 'name' => 'Rocket Broadcaster', 'os' => 'Windows', 'file_url' => 'https://www.rocketbroadcaster.com/',
        'description' => 'Encoder moderno para transmitir ao vivo com boa qualidade de áudio.', 'license_note' => 'Grátis (edição básica)'],
    ['cat' => 'dl-encoders', 'slug' => 'winamp', 'name' => 'Winamp', 'os' => 'Windows', 'file_url' => 'https://winamp.com/',
        'description' => 'Player clássico; com o plugin DSP do Shoutcast, transmite para o seu streaming.', 'license_note' => 'Grátis'],

    ['cat' => 'dl-utilitarios', 'slug' => 'filezilla', 'name' => 'FileZilla', 'os' => 'Windows, Mac e Linux', 'file_url' => 'https://filezilla-project.org/',
        'description' => 'Envie músicas e vinhetas para o AutoDJ pelo FTP.', 'license_note' => 'Grátis'],
    ['cat' => 'dl-utilitarios', 'slug' => 'audacity', 'name' => 'Audacity', 'os' => 'Windows, Mac e Linux', 'file_url' => 'https://www.audacityteam.org/',
        'description' => 'Edite vinhetas, cortes e locuções: corte, normalize e exporte em MP3.', 'license_note' => 'Grátis · código aberto'],
    ['cat' => 'dl-utilitarios', 'slug' => 'mp3tag', 'name' => 'Mp3tag', 'os' => 'Windows e Mac', 'file_url' => 'https://www.mp3tag.de/en/',
        'description' => 'Organize artista e título das músicas para aparecerem certos no player e no app.', 'license_note' => 'Grátis'],
];
