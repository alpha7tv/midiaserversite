<?php
declare(strict_types=1);

/**
 * Definição de todas as páginas públicas. Chave = caminho.
 * Textos comerciais sem promessas que não possamos comprovar (sem uptime, sem nº de clientes, sem avaliações).
 * Preços e planos NÃO ficam aqui: vêm do banco (tabela plans), copiados do WHMCS.
 * Placeholders em title/description: {min} = menor preço mensal do produto.
 */

$steps = [
    ['Escolha o plano', 'Compare os planos desta página e veja o que cada um inclui.'],
    ['Clique em contratar', 'Você segue para o carrinho da Área do Cliente, com o plano já selecionado.'],
    ['Crie sua conta e pague', 'Cadastre-se na Área do Cliente e pague a fatura pelos meios disponíveis, como o Pix.'],
    ['Receba o acesso', 'Confirmado o pagamento, você recebe os dados do serviço e conta com o nosso suporte.'],
];

return [

    // =====================================================================
    '/' => [
        'template' => 'home', 'og' => 'home',
        'title' => 'Mídia Server — Hospedagem, Streaming e Automação para Rádios',
        'description' => 'Hospedagem cPanel, streaming de áudio com AutoDJ, sites e automação para web rádios, conteúdos para rádios e revenda. Tudo em um só lugar.',
        'h1' => 'Tudo que sua empresa, site ou rádio precisa para ficar online',
        'lead' => 'Hospedagem, streaming, automação, sites, conteúdos e infraestrutura profissional em um só lugar.',
        'wa' => 'Olá, vim pelo site da Mídia Server e gostaria de conhecer melhor os serviços.',
        'kw' => ['mídia server', 'hospedagem e streaming', 'infraestrutura para web rádio'],
        'faq' => [
            ['Posso usar meu próprio domínio?', 'Sim. Você pode apontar um domínio seu para a hospedagem ou para o site da rádio, e também registrar domínios pela Área do Cliente.'],
            ['Como funciona a contratação?', 'Você escolhe o plano, clica em contratar e conclui o pedido na Área do Cliente. Depois de pago, os dados de acesso do serviço chegam até você.'],
            ['Como falo com o suporte?', 'Pelo WhatsApp e por tickets na Área do Cliente.'],
            ['A Mídia Server atende web rádios e rádios FM?', 'Sim. Há streaming com AutoDJ, site para a rádio, conteúdos prontos e o software de automação Mídia Rádio Studio para rádios FM, web e comunitárias.'],
        ],
    ],

    // =====================================================================
    '/hospedagem' => [
        'template' => 'product', 'product' => 'hospedagem', 'og' => 'hospedagem', 'schema' => 'Product',
        'schema_name' => 'Hospedagem de Sites cPanel',
        'title' => 'Hospedagem cPanel com NVMe e SSL grátis | Mídia Server',
        'description' => 'Hospedagem de sites com cPanel, disco NVMe, SSL grátis e transferência ilimitada. Planos a partir de {min}/mês. Contrate pela Área do Cliente.',
        'eyebrow' => 'Hospedagem de sites',
        'h1' => 'Hospedagem cPanel rápida, segura e com suporte',
        'lead' => 'Coloque seu site, loja ou blog no ar com disco NVMe, certificado SSL grátis e o painel cPanel que você já conhece.',
        'cta' => 'Ver planos de hospedagem',
        'wa' => 'Olá, gostaria de informações sobre os planos de Hospedagem.',
        'breadcrumb' => [], 'crumb' => 'Hospedagem cPanel',
        'kw' => ['hospedagem de sites', 'hospedagem cpanel', 'hospedagem com ssl grátis', 'hospedagem nvme', 'hospedagem wordpress'],
        'benefits' => [
            ['bolt', 'Disco NVMe', 'Armazenamento NVMe em todos os planos, de 10 GB a 100 GB, para páginas que carregam rápido.'],
            ['shield', 'SSL grátis', 'Certificado SSL incluso em todos os planos, para o seu site abrir com cadeado.'],
            ['server', 'Painel cPanel', 'Crie e-mails, bancos de dados e gerencie arquivos em um painel completo e fácil de usar.'],
            ['database', 'Bancos MySQL', 'De 1 banco até bancos ilimitados, conforme o plano escolhido.'],
            ['backup', 'Backup', 'Backup semanal nos planos Prata e Ouro e backup diário no plano Premium.'],
            ['headset', 'Suporte humano', 'Atendimento por WhatsApp e tickets na Área do Cliente.'],
        ],
        'faq' => [
            ['Posso usar meu próprio domínio?', 'Sim. Você pode apontar um domínio seu para a hospedagem ou registrar um novo domínio pela Área do Cliente.'],
            ['Todos os planos têm SSL?', 'Sim. O certificado SSL é grátis em todos os planos de hospedagem.'],
            ['Qual a diferença entre os planos?', 'Mudam o espaço em disco, o número de contas de e-mail e de bancos de dados e o tipo de backup. Veja a tabela comparativa nesta página.'],
            ['O que é disco NVMe?', 'É um tipo de armazenamento mais rápido que o disco tradicional, o que ajuda o site a responder com agilidade.'],
            ['Posso trazer meu site de outra hospedagem?', 'Podemos conversar sobre a migração. Fale com a gente pelo WhatsApp e explique onde seu site está hospedado hoje.'],
        ],
        'related' => ['/revenda-hospedagem', '/sites-para-radio', '/vps'],
    ],

    // =====================================================================
    '/revenda-hospedagem' => [
        'template' => 'product', 'product' => 'revenda-hospedagem', 'og' => 'revenda-hospedagem', 'schema' => 'Product',
        'schema_name' => 'Revenda de Hospedagem com WHM e cPanel',
        'title' => 'Revenda de Hospedagem com WHM e cPanel | Mídia Server',
        'description' => 'Abra sua empresa de hospedagem: revenda com painel WHM, contas cPanel, disco NVMe e marca própria. Planos a partir de {min}/mês.',
        'eyebrow' => 'Revenda de hospedagem',
        'h1' => 'Venda hospedagem com a sua própria marca',
        'lead' => 'Tenha estrutura para hospedar seus clientes e criar seus próprios planos, com painel WHM e contas cPanel.',
        'cta' => 'COMEÇAR MINHA REVENDA',
        'wa' => 'Olá, gostaria de informações sobre a Revenda de Hospedagem.',
        'crumb' => 'Revenda de hospedagem',
        'kw' => ['revenda de hospedagem', 'revenda cpanel whm', 'abrir empresa de hospedagem', 'hospedagem revenda marca própria'],
        'benefits' => [
            ['server', 'Painel WHM', 'Crie e gerencie as contas cPanel dos seus clientes em um só lugar.'],
            ['users', 'Contas cPanel', 'De 10 contas até contas ilimitadas, conforme o plano.'],
            ['tag', 'Marca própria', 'Nos planos Prata, Ouro e Premium, apresente o serviço com a sua identidade.'],
            ['bolt', 'Disco NVMe', 'De 50 GB a 500 GB de armazenamento NVMe para hospedar seus clientes.'],
            ['globe', 'Transferência ilimitada', 'Sem limite de tráfego em todos os planos de revenda.'],
            ['headset', 'Suporte', 'Atendimento por WhatsApp e tickets para você e para quem estiver começando.'],
        ],
        'faq' => [
            ['O que é o WHM?', 'O WHM (Web Host Manager) é o painel do revendedor: nele você cria, suspende e administra as contas cPanel dos seus clientes.'],
            ['Posso colocar a minha marca?', 'Os planos Prata, Ouro e Premium incluem marca própria. O plano Bronze não inclui esse recurso.'],
            ['Quantos clientes posso hospedar?', 'Depende do plano: de 10 contas cPanel no Bronze até contas ilimitadas no Premium, respeitando o espaço em disco contratado.'],
            ['Preciso ter experiência com hospedagem?', 'Ajuda, mas o cPanel e o WHM são painéis amplamente conhecidos. Se tiver dúvidas, fale com a gente pelo WhatsApp.'],
        ],
        'related' => ['/hospedagem', '/revenda-streaming'],
    ],

    // =====================================================================
    '/streaming' => [
        'template' => 'product', 'product' => 'streaming', 'og' => 'streaming', 'schema' => 'Product',
        'schema_name' => 'Streaming de Áudio para Web Rádio',
        'title' => 'Streaming de Rádio com AutoDJ e Site Incluso | Mídia Server',
        'description' => 'Streaming de áudio para web rádio com AutoDJ, ouvintes ilimitados, painel Midia Cast e site administrável incluso. A partir de {min}/mês.',
        'eyebrow' => 'Streaming de áudio',
        'h1' => 'Sua rádio não pode parar',
        'lead' => 'Tenha streaming profissional, AutoDJ e estrutura preparada para manter sua programação online, com site administrável incluso em todos os planos.',
        'cta' => 'COLOCAR MINHA RÁDIO NO AR',
        'wa' => 'Olá, vim pelo site da Mídia Server e gostaria de informações sobre Streaming para Web Rádio.',
        'crumb' => 'Streaming de áudio',
        'banner' => 'divulgue',
        'kw' => ['streaming de áudio', 'streaming para rádio', 'streaming para web rádio', 'autodj', 'servidor shoutcast icecast'],
        'benefits' => [
            ['wave', 'Ouvintes ilimitados', 'Todos os planos de streaming têm ouvintes simultâneos ilimitados.'],
            ['radio', 'AutoDJ', 'Mantenha a programação tocando mesmo sem locutor ao vivo, com 10 GB a 60 GB de espaço de AutoDJ.'],
            ['gauge', 'Qualidade de áudio', 'Transmissão em 128 kbps nos planos Bronze e Prata e 320 kbps no plano Ouro.'],
            ['globe', 'Site administrável incluso', 'Todos os planos incluem o site para a rádio, com player ao vivo e painel fácil de editar.'],
            ['panel', 'Painel Midia Cast', 'Controle seu streaming, o AutoDJ e o acervo de MP3 e campanhas pelo painel.'],
            ['phone', 'Apps e Web App', 'Web App para iOS e Android em todos os planos; aplicativo Android PREMIUM e notificação push nos planos Prata e Ouro.'],
        ],
        'faq' => [
            ['O site para a rádio está incluso?', 'Sim. Todos os planos de streaming incluem o site administrável, com player ao vivo e painel para editar logo, cores, programação e notícias.'],
            ['Quantos ouvintes posso ter?', 'Os planos têm ouvintes simultâneos ilimitados.'],
            ['O que é o AutoDJ?', 'É o recurso que toca as músicas e campanhas da rádio automaticamente a partir dos arquivos que você envia, mantendo a rádio no ar sem locutor ao vivo.'],
            ['Qual plano escolher?', 'O Bronze atende quem está começando. O Prata acrescenta conteúdos inclusos, download de programas e programetes, live nas redes sociais e aplicativos. O Ouro tem áudio em 320 kbps, mais espaço e Web TV.'],
            ['Posso transmitir ao vivo?', 'Sim. Os planos Prata e Ouro incluem live nas redes sociais, o Prata inclui câmera no estúdio (só ao vivo) e o Ouro inclui Web TV (ao vivo e gravado). Veja a tabela comparativa.'],
            ['Como divulgo minha rádio?', 'Você pode cadastrar a rádio gratuitamente no diretório Rádios do Brasil, que fica ligado ao nosso portal.'],
        ],
        'related' => ['/web-radio-completa', '/sites-para-radio', '/automacao-radio', '/revenda-streaming'],
    ],

    // =====================================================================
    '/web-radio-completa' => [
        'template' => 'product', 'product' => 'streaming', 'og' => 'web-radio-completa', 'schema' => 'Service',
        'schema_name' => 'Web Rádio Completa',
        'title' => 'Web Rádio Completa: Streaming, AutoDJ, Site e App',
        'description' => 'Tudo para criar sua rádio online: streaming, AutoDJ, site com player, painel e subdomínio grátis. Planos a partir de {min}/mês.',
        'eyebrow' => 'Web Rádio Completa',
        'h1' => 'Crie sua rádio online com tudo em um só lugar',
        'lead' => 'Streaming, AutoDJ, site com player, painel de controle e endereço grátis para começar sua web rádio rapidamente.',
        'cta' => 'COLOCAR MINHA RÁDIO NO AR',
        'wa' => 'Olá, gostaria de informações sobre a Web Rádio Completa.',
        'crumb' => 'Web Rádio Completa',
        'banner' => 'divulgue',
        'kw' => ['criar uma rádio online', 'web rádio completa', 'como montar uma web rádio', 'rádio online com site'],
        'solution' => [
            ['wave', 'Streaming', 'Seu sinal no ar para ouvintes ilimitados.'],
            ['radio', 'AutoDJ', 'Programação tocando mesmo sem locutor.'],
            ['globe', 'Site com player', 'Site administrável, com endereço grátis (suaradio.radioweb.top) ou o seu domínio.'],
            ['phone', 'Apps e Web App', 'Ouvintes no celular, com Web App para iOS e Android.'],
            ['panel', 'Painel de controle', 'Midia Cast para gerenciar tudo em um lugar.'],
        ],
        'steps_title' => 'Como montar sua rádio',
        'steps' => [
            ['Contrate o plano de streaming', 'O site administrável já vem incluso em todos os planos.'],
            ['Envie suas músicas', 'Coloque os arquivos no AutoDJ e monte a programação.'],
            ['Personalize o site', 'Troque logo, cores, banners e textos pelo painel fácil.'],
            ['Divulgue sua rádio', 'Cadastre-a no diretório Rádios do Brasil e compartilhe o link do player.'],
        ],
        'benefits' => [
            ['bolt', 'Comece rápido', 'Streaming e site juntos: você não precisa contratar cada peça separada.'],
            ['globe', 'Endereço grátis', 'O site vem com endereço no formato suaradio.radioweb.top; se preferir, use o seu domínio.'],
            ['users', 'Conteúdos nos planos Prata e Ouro', 'Esses planos incluem conteúdos e download de programas e programetes.'],
            ['headset', 'Suporte', 'Atendimento por WhatsApp e tickets na Área do Cliente.'],
        ],
        'faq' => [
            ['O que está incluso na Web Rádio Completa?', 'Streaming com AutoDJ, o site administrável com player ao vivo, o painel de controle e o endereço grátis. Os recursos variam conforme o plano de streaming escolhido.'],
            ['Posso usar meu domínio no site?', 'Sim. Você pode usar o endereço grátis ou apontar o seu próprio domínio.'],
            ['Preciso entender de informática?', 'O painel foi pensado para ser fácil. Em caso de dúvida, você conta com o suporte pelo WhatsApp.'],
            ['Posso mudar de plano depois?', 'Fale com o suporte para ajustar o plano conforme a rádio crescer.'],
        ],
        'related' => ['/streaming', '/sites-para-radio', '/conteudos-para-radio', '/automacao-radio'],
    ],

    // =====================================================================
    '/revenda-streaming' => [
        'template' => 'product', 'product' => 'revenda-streaming', 'og' => 'revenda-streaming', 'schema' => 'Product',
        'schema_name' => 'Revenda de Streaming',
        'title' => 'Revenda de Streaming para Web Rádio | Mídia Server',
        'description' => 'Transforme streaming em seu próprio negócio: revenda com contas de streaming, painel de revenda, marca própria e sub-revendas. A partir de {min}/mês.',
        'eyebrow' => 'Revenda de streaming',
        'h1' => 'Transforme streaming em seu próprio negócio',
        'lead' => 'Tenha estrutura para atender seus clientes com os planos de revenda de streaming da Mídia Server.',
        'cta' => 'COMEÇAR MINHA REVENDA DE STREAMING',
        'wa' => 'Olá, gostaria de informações sobre a Revenda de Streaming.',
        'crumb' => 'Revenda de streaming',
        'banner' => 'divulgue',
        'kw' => ['revenda de streaming', 'revender streaming de rádio', 'painel de revenda streaming', 'ser revendedor de streaming'],
        'benefits' => [
            ['users', 'Contas de streaming', 'De 5 a 60 contas de streaming para atender suas rádios clientes.'],
            ['database', 'Disco compartilhado', 'De 50 GB a 250 GB divididos entre as contas.'],
            ['panel', 'Painel de revenda', 'Gerencie suas contas em um painel próprio para o revendedor.'],
            ['tag', 'Marca própria', 'Apresente o serviço com a sua marca em todos os planos.'],
            ['network', 'Sub-revendas', 'Nos planos Prata, Ouro e Premium, de 2 a 10 sub-revendas.'],
            ['wave', 'Ouvintes ilimitados', 'Qualidade até 128 kbps, ou até 320 kbps no plano Premium.'],
        ],
        'faq' => [
            ['O que é revenda de streaming?', 'Você contrata um pacote de contas de streaming e as vende para suas próprias rádios clientes, com seus preços e a sua marca.'],
            ['Quantas contas posso criar?', 'De 5 contas no Bronze até 60 contas no Premium, dividindo o disco compartilhado do plano.'],
            ['Existem sub-revendas?', 'Sim, a partir do plano Prata: 2 no Prata, 5 no Ouro e 10 no Premium.'],
            ['Posso usar minha marca?', 'Sim, todos os planos de revenda de streaming incluem marca própria.'],
        ],
        'related' => ['/streaming', '/revenda-hospedagem', '/sites-para-radio'],
    ],

    // =====================================================================
    '/sites-para-radio' => [
        'template' => 'sites', 'product' => 'sites-para-radio', 'og' => 'sites-para-radio', 'schema' => 'Product',
        'schema_name' => 'Site Administrável para Web Rádio',
        'title' => 'Site para Web Rádio com Player ao Vivo | Mídia Server',
        'description' => 'Site profissional para sua web rádio: player ao vivo, notícias automáticas e painel fácil de editar. 15 modelos. {min}/mês ou grátis nos planos de streaming.',
        'eyebrow' => 'Sites para rádio',
        'h1' => 'O site da sua rádio, pronto e no ar',
        'lead' => 'Escolha um dos nossos modelos, com player ao vivo, notícias automáticas, programação e equipe, e edite tudo por um painel simples.',
        'cta' => 'Contratar o site',
        'wa' => 'Olá, gostaria de informações sobre os sites para rádio.',
        'crumb' => 'Sites para rádio',
        'kw' => ['site para web rádio', 'site para rádio online', 'modelos de site para rádio', 'template site rádio'],
        'benefits' => [
            ['play', 'Player ao vivo', 'O ouvinte ouve a rádio sem sair do site, vendo o programa e a música que está tocando.'],
            ['news', 'Notícias automáticas', 'O site se atualiza sozinho com notícias do seu estilo musical.'],
            ['panel', 'Painel fácil', 'Troque logo, cores, fotos, banners e textos pelo computador ou celular.'],
            ['users', 'Programação e equipe', 'Mostre quem está no ar, os próximos programas e os locutores.'],
            ['phone', 'Pedidos e mural', 'Pedidos de música, recados dos ouvintes e botão de WhatsApp para falar com o estúdio.'],
            ['shield', 'Seguro e no celular', 'Certificado de segurança, funciona no celular e fica hospedado nos nossos servidores.'],
        ],
        'faq' => [
            ['O site é grátis para quem tem streaming?', 'Sim. Todos os planos de streaming da Mídia Server incluem o site administrável sem custo adicional. Quem não tem streaming pode contratar o site por R$ 29,90 por mês.'],
            ['Posso usar meu domínio?', 'Sim. Você pode usar o endereço grátis (suaradio.radioweb.top) ou o seu domínio próprio.'],
            ['Posso trocar de modelo depois?', 'Fale com o nosso suporte para ajustar o modelo escolhido.'],
            ['Preciso de programador?', 'Não. Logo, cores, banners, textos, programação e equipe são editados pelo painel.'],
        ],
        'related' => ['/streaming', '/web-radio-completa', '/hospedagem'],
    ],

    // =====================================================================
    '/automacao-radio' => [
        'template' => 'automacao', 'product' => 'automacao-radio', 'og' => 'automacao-radio', 'schema' => 'SoftwareApplication',
        'schema_name' => 'Mídia Rádio Studio',
        'title' => 'Automação de Rádio Mídia Rádio Studio | Teste grátis',
        'description' => 'Software de automação para rádio FM, web rádio e comunitária: playlist com mixagem, cartucheira, grade, agendador e hora certa. Baixe grátis.',
        'eyebrow' => 'Automação de rádio · Windows',
        'h1' => 'Automação de Rádio Mídia Rádio Studio',
        'lead' => 'A sua rádio no ar 24 horas, sem complicação: playlist com mixagem, cartucheira de vinhetas, grade de programação, agendador, hora certa e relatórios de comerciais.',
        'cta' => 'Baixar grátis',
        'wa' => 'Olá, gostaria de informações sobre a Automação de Rádio.',
        'crumb' => 'Automação de rádio',
        'kw' => ['automação de rádio', 'software para rádio', 'automação para web rádio', 'cartucheira de vinhetas', 'hora certa zararadio'],
        'features' => [
            ['list', 'Playlist com mixagem', 'Arraste músicas e pastas direto para a playlist, com horário previsto de cada faixa e cruzamento de 0 a 12 segundos.'],
            ['keys', 'Cartucheira de vinhetas', 'Até 40 cartuchos coloridos por banco, com tecla de atalho para cada vinheta.'],
            ['calendar', 'Grade de programação', 'Monte os relógios e a grade da semana uma vez; o programa gera a playlist do dia.'],
            ['clock', 'Agendador', 'Programas gravados, blocos comerciais, hora certa e temperatura nos horários que você definir.'],
            ['time', 'Hora certa e temperatura', 'Reconhece os arquivos no padrão do ZaraRadio e toca a temperatura da sua cidade.'],
            ['chart', 'Comerciais e relatórios', 'Cadastre clientes e campanhas e gere o comprovante de veiculação em PDF, Excel ou CSV.'],
        ],
        'shots' => [
            ['principal.webp', 'Tela principal do Mídia Rádio Studio com playlist, contagem regressiva e cartucheira colorida'],
            ['grade-2.webp', 'Grade semanal de programas coloridos por horário'],
            ['agendador.webp', 'Agendador de eventos com hora certa, prefixo e bloco comercial'],
            ['relatorios.webp', 'Relatório de veiculação com data, hora, cliente e campanha'],
        ],
        'extras' => ['Backup automático diário da configuração', 'Proteção contra player travado e silêncio', 'Atualizações avisadas no próprio programa', 'Envia o nome da música para Shoutcast e Icecast', 'Funciona até 7 dias sem internet depois de ativado'],
        'requirements' => ['Windows 10 ou 11 (64 bits)', '4 GB de memória (8 GB recomendado)', 'Placa de som (uma segunda saída é opcional, para pré-escuta no fone)', 'Internet para ativar e para a temperatura', 'Formatos de áudio: MP3, WAV, WMA, FLAC, OGG, M4A e AAC'],
        'faq' => [
            ['Posso testar antes de assinar?', 'Sim. Baixe grátis e use o modo demonstração, com playlist de até 10 músicas e cartucheira de até 5 vinhetas. Ao assinar, é só colar a chave no programa e tudo é liberado, sem reinstalar.'],
            ['Como recebo a chave de licença?', 'Assim que o pagamento é confirmado, a chave chega por e-mail e fica na sua Área do Cliente.'],
            ['Funciona sem internet?', 'A internet é necessária para ativar. Depois disso o programa confere a licença em segundo plano e continua tocando por até 7 dias sem internet.'],
            ['Posso usar os arquivos de hora certa do ZaraRadio?', 'Pode. O programa reconhece os nomes HRS e MIN (e HRS_O para hora em ponto) e os arquivos de temperatura TMP.'],
            ['Troquei de computador. E agora?', 'Na Área do Cliente, abra o serviço e use a opção de trocar de computador; depois instale no novo e ative com a mesma chave.'],
            ['Serve para web rádio, FM e rádio comunitária?', 'Sim. Ele toca no computador da rádio, que pode estar ligado ao transmissor FM ou ao encoder do streaming.'],
            ['Apareceu "O Windows protegeu o computador". É seguro?', 'É o aviso padrão do Windows para programas novos. Clique em "Mais informações" e depois em "Executar assim mesmo".'],
        ],
        'related' => ['/conteudos-para-radio', '/streaming', '/downloads'],
    ],

    // =====================================================================
    '/conteudos-para-radio' => [
        'template' => 'conteudos', 'product' => 'conteudos-para-radio', 'og' => 'conteudos-para-radio', 'schema' => 'Service',
        'schema_name' => 'Conteúdos para Rádios',
        'title' => 'Conteúdo para Rádio: Programas e Programetes | Mídia Server',
        'description' => 'Programas, programetes e conteúdo de fim de semana atualizados todos os dias, direto no computador da sua rádio. A partir de {min}/mês.',
        'eyebrow' => 'Conteúdos para rádios',
        'h1' => 'Conteúdo novo direto no computador da sua rádio',
        'lead' => 'Programas, programetes e conteúdos de fim de semana atualizados automaticamente na nuvem, prontos para a sua automação.',
        'cta' => 'Ver planos de conteúdo',
        'wa' => 'Olá, gostaria de informações sobre os Conteúdos para Rádio.',
        'crumb' => 'Conteúdos para rádios',
        'kw' => ['conteúdo para rádio', 'programas para rádio', 'programetes para rádio', 'programas de rádio prontos', 'conteúdo para web rádio'],
        'how' => [
            ['Escolha o plano', 'Selecione o pacote que combina com a sua rádio e contrate pela Área do Cliente.'],
            ['Receba a nuvem', 'O acesso é criado com as pastas do seu plano já organizadas.'],
            ['Atualização diária', 'De madrugada, as novas edições substituem as antigas, com o mesmo nome de arquivo.'],
            ['Direto na automação', 'Sincronize uma pasta do computador da rádio e aponte seu software para ela.'],
        ],
        'categories' => ['Gospel', 'Popular', 'Sertanejo', 'Eletrônica', 'Flashback', 'Informação', 'Humor', 'Romântico', 'Samba e Pagode', 'Forró e Piseiro', 'Entretenimento', 'Católico', 'Esportes', 'Utilidade', 'Curiosidades', 'Tecnologia', 'Reflexão', 'Rock', 'Economia', 'Saúde', 'Horóscopo', 'Agronegócio', 'Outros'],
        'faq' => [
            ['Como os conteúdos chegam à minha rádio?', 'Pelo aplicativo de sincronização, que mantém uma pasta do computador da rádio sempre atualizada com os arquivos do seu plano. Também é possível acessar pelo navegador.'],
            ['Com que frequência são atualizados?', 'Todos os dias. De madrugada, as novas edições substituem as antigas, com o mesmo nome de arquivo, então a sua automação continua apontando para a mesma pasta.'],
            ['Qual a diferença entre programas e programetes?', 'Programas são conteúdos mais longos, em blocos. Programetes são quadros curtos, como notícias, esporte, humor, saúde e curiosidades.'],
            ['Funciona com qualquer automação?', 'Funciona com o software que você usar, bastando apontar a pasta sincronizada. Você também pode usar o Mídia Rádio Studio.'],
            ['Os planos de streaming incluem conteúdos?', 'Os planos de streaming Prata e Ouro listam conteúdos inclusos. Veja os detalhes na página de Streaming.'],
        ],
        'related' => ['/automacao-radio', '/streaming', '/radios'],
    ],

    // =====================================================================
    '/vps' => [
        'template' => 'soon', 'og' => 'vps', 'schema' => '',
        'title' => 'VPS Linux — em breve | Mídia Server',
        'description' => 'Servidores VPS Linux para sites, APIs e automações estão chegando à Mídia Server. Fale com a gente para saber quando.',
        'eyebrow' => 'VPS Linux · em breve',
        'h1' => 'VPS Linux chegando em breve',
        'lead' => 'Estamos preparando os planos de VPS. Fale com a gente pelo WhatsApp e avisamos assim que estiverem disponíveis.',
        'cta' => 'Avisar quando estiver disponível',
        'wa' => 'Olá, gostaria de saber quando os planos de VPS Linux estarão disponíveis.',
        'crumb' => 'VPS Linux',
        'kw' => ['vps linux', 'servidor vps brasil'],
        'related' => ['/hospedagem', '/revenda-hospedagem'],
    ],

    // =====================================================================
    '/radios' => [
        'template' => 'radios', 'og' => 'radios',
        'title' => 'Rádios Online: Ouça Rádios ao Vivo | Mídia Server',
        'description' => 'Ouça rádios de todo o Brasil ao vivo, grátis, e cadastre a sua rádio gratuitamente no diretório Rádios do Brasil, da Mídia Server.',
        'eyebrow' => 'Rádios online',
        'h1' => 'Ouça rádios ao vivo e divulgue a sua',
        'lead' => 'Descubra emissoras de todo o Brasil por estado, cidade e segmento. Tem uma rádio? Cadastre grátis e apareça no diretório.',
        'cta' => 'Ouvir rádios',
        'wa' => 'Olá, gostaria de informações sobre o diretório de Rádios do Brasil.',
        'crumb' => 'Rádios online',
        'kw' => ['rádios online', 'ouvir rádio ao vivo', 'rádios do brasil', 'cadastrar rádio grátis'],
        'faq' => [
            ['O cadastro de rádio é gratuito?', 'Sim. O cadastro no diretório é gratuito. Cada rádio passa por uma aprovação antes de aparecer.'],
            ['Como destaco minha rádio?', 'Há posições de destaque no diretório. Fale com a gente pelo WhatsApp para conhecer as opções.'],
        ],
        'related' => ['/radios/cadastrar-radio', '/streaming'],
    ],

    '/radios/cadastrar-radio' => [
        'template' => 'cadastrar', 'og' => 'radios',
        'title' => 'Cadastrar Rádio Grátis no Diretório | Mídia Server',
        'description' => 'Cadastre sua rádio gratuitamente no diretório Rádios do Brasil. Após a aprovação, ela aparece para ouvintes de todo o país.',
        'eyebrow' => 'Cadastro gratuito',
        'h1' => 'Cadastre sua rádio grátis',
        'lead' => 'Coloque sua emissora no diretório com página própria e player ao vivo. Todo cadastro passa por aprovação antes de ser publicado.',
        'cta' => 'Cadastrar minha rádio',
        'wa' => 'Olá, preciso de ajuda para cadastrar minha rádio.',
        'breadcrumb' => [['Rádios online', '/radios']], 'crumb' => 'Cadastrar rádio',
        'kw' => ['cadastrar rádio', 'cadastrar rádio grátis', 'divulgar rádio online'],
    ],

    // =====================================================================
    '/downloads' => [
        'template' => 'downloads', 'og' => 'downloads',
        'title' => 'Downloads para Rádio: Automação, Encoders e Utilitários',
        'description' => 'Central de downloads da Mídia Server: Mídia Rádio Studio, programas de automação, encoders para streaming e utilitários para rádios.',
        'eyebrow' => 'Central de downloads',
        'h1' => 'Downloads para rádios e streaming',
        'lead' => 'O Mídia Rádio Studio e uma seleção de programas de automação, encoders e utilitários. Os programas de terceiros levam aos sites oficiais, com a licença de cada um.',
        'crumb' => 'Downloads',
        'wa' => 'Olá, preciso de ajuda com um download.',
        'kw' => ['programas para rádio', 'download automação de rádio', 'encoder shoutcast icecast'],
    ],

    // =====================================================================
    '/blog' => [
        'template' => 'blog', 'og' => 'blog',
        'title' => 'Blog Mídia Server — Hospedagem, Streaming e Web Rádio',
        'description' => 'Artigos, tutoriais e novidades sobre hospedagem, streaming de web rádio, automação, revenda e marketing para rádios.',
        'eyebrow' => 'Blog',
        'h1' => 'Novidades e tutoriais da Mídia Server',
        'lead' => 'Conteúdo sobre hospedagem, streaming, web rádio e automação.',
        'crumb' => 'Blog',
        'wa' => 'Olá, vim pelo blog da Mídia Server.',
    ],

    '/busca' => [
        'template' => 'busca', 'og' => 'home', 'noindex' => true, 'no_sitemap' => true,
        'title' => 'Buscar no portal | Mídia Server', 'description' => 'Pesquise produtos, páginas, downloads e rádios da Mídia Server.',
        'h1' => 'Buscar no portal', 'lead' => '', 'crumb' => 'Busca', 'wa' => 'Olá, não encontrei o que procurava no site.',
    ],

    // ---------------------------------------------------------------------
    '/sobre' => [
        'template' => 'text', 'og' => 'institucional',
        'title' => 'Sobre a Mídia Server', 'description' => 'Conheça a Mídia Server: hospedagem, streaming, automação e conteúdos para rádios e empresas na internet.',
        'h1' => 'Sobre a Mídia Server', 'crumb' => 'Sobre',
        'lead' => 'Tecnologia para colocar sites e rádios no ar.',
        'wa' => 'Olá, gostaria de falar com a Mídia Server.',
        'body' => 'about',
    ],
    '/contato' => [
        'template' => 'contato', 'og' => 'institucional',
        'title' => 'Contato | Mídia Server', 'description' => 'Fale com a Mídia Server pelo WhatsApp, e-mail ou abra um ticket na Área do Cliente.',
        'h1' => 'Fale com a Mídia Server', 'crumb' => 'Contato',
        'lead' => 'Escolha o canal que preferir.', 'wa' => 'Olá, vim pelo site da Mídia Server e gostaria de falar com o atendimento.',
    ],
    '/suporte' => [
        'template' => 'suporte', 'og' => 'institucional',
        'title' => 'Suporte | Mídia Server', 'description' => 'Precisa de ajuda? Abra um ticket na Área do Cliente, fale pelo WhatsApp ou consulte a base de conhecimento.',
        'h1' => 'Suporte', 'crumb' => 'Suporte',
        'lead' => 'Estamos aqui para ajudar.', 'wa' => 'Olá, preciso de suporte com um serviço da Mídia Server.',
    ],
    '/politica-de-privacidade' => [
        'template' => 'text', 'og' => 'institucional', 'body' => 'privacy',
        'title' => 'Política de Privacidade | Mídia Server', 'description' => 'Como a Mídia Server trata dados pessoais, em conformidade com a LGPD.',
        'h1' => 'Política de Privacidade', 'crumb' => 'Política de Privacidade', 'lead' => '', 'wa' => 'Olá, tenho uma dúvida sobre privacidade.',
    ],
    '/termos-de-uso' => [
        'template' => 'text', 'og' => 'institucional', 'body' => 'terms',
        'title' => 'Termos de Uso | Mídia Server', 'description' => 'Termos de uso do site da Mídia Server.',
        'h1' => 'Termos de Uso', 'crumb' => 'Termos de Uso', 'lead' => '', 'wa' => 'Olá, tenho uma dúvida sobre os termos de uso.',
    ],
    '/politica-de-cookies' => [
        'template' => 'text', 'og' => 'institucional', 'body' => 'cookies',
        'title' => 'Política de Cookies | Mídia Server', 'description' => 'Como o site da Mídia Server usa cookies e como você controla o consentimento.',
        'h1' => 'Política de Cookies', 'crumb' => 'Política de Cookies', 'lead' => '', 'wa' => 'Olá, tenho uma dúvida sobre cookies.',
    ],
    '/lgpd' => [
        'template' => 'text', 'og' => 'institucional', 'body' => 'lgpd',
        'title' => 'LGPD e Direitos do Titular | Mídia Server', 'description' => 'Seus direitos como titular de dados e como falar com a Mídia Server sobre a LGPD.',
        'h1' => 'LGPD e direitos do titular', 'crumb' => 'LGPD', 'lead' => '', 'wa' => 'Olá, gostaria de exercer um direito previsto na LGPD.',
    ],

    '/obrigado' => [
        'template' => 'thanks', 'og' => 'home', 'noindex' => true, 'no_sitemap' => true,
        'title' => 'Obrigado | Mídia Server', 'description' => 'Recebemos o seu contato.',
        'h1' => 'Obrigado', 'crumb' => 'Obrigado', 'lead' => '', 'wa' => 'Olá, enviei o formulário no site da Mídia Server.',
    ],

    // =====================================================================
    // Landings para campanhas (Google/Meta Ads): baixa distração, fora do sitemap, noindex.
    '/lp/streaming-radio' => [
        'template' => 'lp', 'lp_of' => '/streaming', 'product' => 'streaming', 'og' => 'streaming', 'noindex' => true, 'no_sitemap' => true, 'schema' => 'Product',
        'title' => 'Streaming para Rádio com AutoDJ | Mídia Server', 'description' => 'Streaming profissional para sua rádio, com AutoDJ e site incluso. A partir de {min}/mês.',
        'h1' => 'Sua rádio não pode parar', 'lead' => 'Streaming com AutoDJ, ouvintes ilimitados e site para a sua rádio incluso em todos os planos.',
        'cta' => 'COLOCAR MINHA RÁDIO NO AR', 'wa' => 'Olá, vim pelo site da Mídia Server e gostaria de informações sobre Streaming para Web Rádio.',
    ],
    '/lp/hospedagem-cpanel' => [
        'template' => 'lp', 'lp_of' => '/hospedagem', 'product' => 'hospedagem', 'og' => 'hospedagem', 'noindex' => true, 'no_sitemap' => true, 'schema' => 'Product',
        'title' => 'Hospedagem cPanel com SSL grátis | Mídia Server', 'description' => 'Hospedagem com cPanel, NVMe e SSL grátis. A partir de {min}/mês.',
        'h1' => 'Seu site no ar com velocidade e segurança', 'lead' => 'Hospedagem cPanel com disco NVMe, SSL grátis e suporte.',
        'cta' => 'VER PLANOS', 'wa' => 'Olá, gostaria de informações sobre os planos de Hospedagem.',
    ],
    '/lp/revenda-hospedagem' => [
        'template' => 'lp', 'lp_of' => '/revenda-hospedagem', 'product' => 'revenda-hospedagem', 'og' => 'revenda-hospedagem', 'noindex' => true, 'no_sitemap' => true, 'schema' => 'Product',
        'title' => 'Revenda de Hospedagem com WHM | Mídia Server', 'description' => 'Comece sua revenda de hospedagem com painel WHM e cPanel. A partir de {min}/mês.',
        'h1' => 'Comece sua própria empresa de hospedagem', 'lead' => 'Revenda com painel WHM, contas cPanel e marca própria nos planos Prata a Premium.',
        'cta' => 'COMEÇAR MINHA REVENDA', 'wa' => 'Olá, gostaria de informações sobre a Revenda de Hospedagem.',
    ],
    '/lp/revenda-streaming' => [
        'template' => 'lp', 'lp_of' => '/revenda-streaming', 'product' => 'revenda-streaming', 'og' => 'revenda-streaming', 'noindex' => true, 'no_sitemap' => true, 'schema' => 'Product',
        'title' => 'Revenda de Streaming | Mídia Server', 'description' => 'Revenda de streaming com painel e marca própria. A partir de {min}/mês.',
        'h1' => 'Transforme streaming em seu próprio negócio', 'lead' => 'Contas de streaming, painel de revenda e marca própria para atender suas rádios clientes.',
        'cta' => 'QUERO COMEÇAR', 'wa' => 'Olá, gostaria de informações sobre a Revenda de Streaming.',
    ],
    '/lp/automacao-radio' => [
        'template' => 'lp', 'lp_of' => '/automacao-radio', 'product' => 'automacao-radio', 'og' => 'automacao-radio', 'noindex' => true, 'no_sitemap' => true, 'schema' => 'SoftwareApplication',
        'title' => 'Automação de Rádio — teste grátis | Mídia Server', 'description' => 'Mídia Rádio Studio: automação completa para rádio. Baixe grátis. {min}/mês.',
        'h1' => 'A sua rádio no ar 24 horas, sem complicação', 'lead' => 'Playlist com mixagem, cartucheira, grade de programação e hora certa. Baixe grátis e teste.',
        'cta' => 'BAIXAR GRÁTIS', 'wa' => 'Olá, gostaria de informações sobre a Automação de Rádio.',
    ],
    '/lp/conteudos-para-radio' => [
        'template' => 'lp', 'lp_of' => '/conteudos-para-radio', 'product' => 'conteudos-para-radio', 'og' => 'conteudos-para-radio', 'noindex' => true, 'no_sitemap' => true, 'schema' => 'Service',
        'title' => 'Conteúdo para Rádio | Mídia Server', 'description' => 'Programas e programetes atualizados todos os dias. A partir de {min}/mês.',
        'h1' => 'Conteúdo novo direto no computador da sua rádio', 'lead' => 'Programas, programetes e conteúdo de fim de semana, atualizados automaticamente na nuvem.',
        'cta' => 'VER PLANOS', 'wa' => 'Olá, gostaria de informações sobre os Conteúdos para Rádio.',
    ],
    '/lp/site-para-radio' => [
        'template' => 'lp', 'lp_of' => '/sites-para-radio', 'product' => 'sites-para-radio', 'og' => 'sites-para-radio', 'noindex' => true, 'no_sitemap' => true, 'schema' => 'Product',
        'title' => 'Site para Web Rádio | Mídia Server', 'description' => 'Site para rádio com player ao vivo e 15 modelos. {min}/mês, grátis nos planos de streaming.',
        'h1' => 'O site da sua rádio, pronto e no ar', 'lead' => '15 modelos, player ao vivo e painel fácil. Grátis em todos os planos de streaming.',
        'cta' => 'QUERO O MEU SITE', 'wa' => 'Olá, gostaria de informações sobre os sites para rádio.',
    ],
    '/lp/web-radio-completa' => [
        'template' => 'lp', 'lp_of' => '/web-radio-completa', 'product' => 'streaming', 'og' => 'web-radio-completa', 'noindex' => true, 'no_sitemap' => true, 'schema' => 'Service',
        'title' => 'Crie sua Rádio Online Completa | Mídia Server', 'description' => 'Streaming, AutoDJ, site com player e painel para sua rádio online. A partir de {min}/mês.',
        'h1' => 'Crie sua rádio online com tudo em um só lugar', 'lead' => 'Streaming, AutoDJ, site com player, painel de controle e endereço grátis para começar sua web rádio.',
        'cta' => 'COLOCAR MINHA RÁDIO NO AR', 'wa' => 'Olá, gostaria de informações sobre a Web Rádio Completa.',
    ],
];
