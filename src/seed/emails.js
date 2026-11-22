// Sequência de 30 e-mails (1 por dia) para quem pediu teste e ainda não fechou.
// Variáveis disponíveis: {{nome}} {{site}} {{whatsapp}} {{planos}} {{revenda}} {{apontamento}} {{franquia}} {{teste}} {{sites_revenda}}
// audience: todos | cliente | revenda   (o dia 30 tem uma versão para cada público)
const btn = (href, label) =>
  `<p style="text-align:center;margin:28px 0"><a href="${href}" style="background:linear-gradient(90deg,#e50914,#ff6a00);color:#fff;text-decoration:none;font-weight:700;padding:14px 28px;border-radius:8px;display:inline-block">${label}</a></p>`;
const precos = `<table role="presentation" width="100%" style="border-collapse:collapse;margin:16px 0;font-size:15px">
<tr><td style="padding:10px;border-bottom:1px solid #333">Mensal</td><td style="padding:10px;border-bottom:1px solid #333;text-align:right"><b>R$ 19,90</b></td></tr>
<tr><td style="padding:10px;border-bottom:1px solid #333">Trimestral</td><td style="padding:10px;border-bottom:1px solid #333;text-align:right"><b>R$ 45,90</b></td></tr>
<tr><td style="padding:10px;border-bottom:1px solid #333">Semestral</td><td style="padding:10px;border-bottom:1px solid #333;text-align:right"><b>R$ 69,90</b></td></tr>
<tr><td style="padding:10px">Anual</td><td style="padding:10px;text-align:right"><b>R$ 119,90</b></td></tr></table>`;
const revendaTab = `<table role="presentation" width="100%" style="border-collapse:collapse;margin:16px 0;font-size:15px">
<tr><td style="padding:8px;border-bottom:1px solid #333">10 créditos</td><td style="padding:8px;border-bottom:1px solid #333;text-align:right"><b>R$ 50,00/mês</b></td></tr>
<tr><td style="padding:8px;border-bottom:1px solid #333">100 créditos</td><td style="padding:8px;border-bottom:1px solid #333;text-align:right"><b>R$ 80,00/mês</b></td></tr>
<tr><td style="padding:8px;border-bottom:1px solid #333">300 créditos</td><td style="padding:8px;border-bottom:1px solid #333;text-align:right"><b>R$ 200,00/mês</b></td></tr>
<tr><td style="padding:8px;border-bottom:1px solid #333">500 créditos</td><td style="padding:8px;border-bottom:1px solid #333;text-align:right"><b>R$ 250,00/mês</b></td></tr>
<tr><td style="padding:8px;border-bottom:1px solid #333">1000 créditos</td><td style="padding:8px;border-bottom:1px solid #333;text-align:right"><b>R$ 600,00/mês</b></td></tr>
<tr><td style="padding:8px;border-bottom:1px solid #333">3000 créditos</td><td style="padding:8px;border-bottom:1px solid #333;text-align:right"><b>R$ 1.000,00/mês</b></td></tr>
<tr><td style="padding:8px">Ilimitado</td><td style="padding:8px;text-align:right"><b>R$ 1.200,00/mês</b></td></tr></table>`;
const p = (...t) => t.map((x) => `<p>${x}</p>`).join('\n');

module.exports = [
  { day: 1, audience: 'todos', subject: '🎬 {{nome}}, seu teste Cine Pulse começou. Não perca essas horas!',
    body: p('Olá, {{nome}}!', 'Seu acesso de teste já foi enviado por e-mail e WhatsApp. Aproveite agora: abra no aparelho que você mais usa (Smart TV, TV Box ou celular) e veja a diferença de ter um serviço com suporte de verdade.', 'Travou em alguma etapa da instalação? Chame a gente no WhatsApp. O suporte da Cine Pulse funciona <b>24 horas</b>.') + btn('{{whatsapp}}', 'Falar com o suporte agora') },
  { day: 2, audience: 'todos', subject: '⚡ 3 minutos: é o tempo para deixar a Cine Pulse pronta na sua TV',
    body: p('{{nome}}, a maioria das pessoas que não aproveita o teste é porque não conseguiu instalar. Vamos resolver isso hoje.', '1. Escolha o aparelho: Smart TV, TV Box, Fire Stick, celular ou computador.<br>2. Instale o aplicativo indicado pelo nosso suporte.<br>3. Digite o usuário e a senha que você recebeu.', 'Pronto. Se algo não funcionar, nossa equipe instala com você pelo WhatsApp.') + btn('{{whatsapp}}', 'Quero ajuda para instalar') },
  { day: 3, audience: 'todos', subject: '💰 Menos que um lanche por mês: veja os planos Cine Pulse',
    body: p('{{nome}}, gostou do teste? Continue com a Cine Pulse pagando pouco:') + precos + p('Todos os planos incluem suporte 24 horas e funcionam nos principais aparelhos.') + btn('{{planos}}', 'Ver planos e assinar') },
  { day: 4, audience: 'todos', subject: '🕒 3 da manhã e deu problema? A gente responde.',
    body: p('Sabe aquele serviço que some quando você mais precisa? Aqui é diferente.', 'Na Cine Pulse o suporte é <b>24 horas</b>, com gente de verdade do outro lado. Seja cliente ou revendedor, você fala com quem resolve.') + btn('{{whatsapp}}', 'Testar o suporte agora') },
  { day: 5, audience: 'todos', subject: '🔥 {{nome}}, e se a Cine Pulse virasse a sua renda?',
    body: p('Hoje tem gente que começou revendendo para amigos e família e fez da Cine Pulse a sua <b>renda principal</b>, com qualidade e dedicação.', 'O caminho é simples: você contrata um painel de créditos, ativa seus clientes e cobra o valor que definir. Os créditos são <b>acumulativos</b>, então o que não vender este mês continua com você.', 'Planos a partir de <b>R$ 50,00 por mês</b> com 10 créditos.') + btn('{{revenda}}', 'Quero ser revendedor') },
  { day: 6, audience: 'todos', subject: '🚫 Chega de dor de cabeça com DNS',
    body: p('Muitos revendedores estão perdendo clientes por causa de DNS que cai, muda ou é compartilhada com centenas de pessoas.', 'Na Cine Pulse usamos um sistema novo e integrado justamente para isso não acontecer. E mais: <b>ao contratar qualquer plano de revenda, você ganha de imediato uma DNS exclusiva</b> só sua.') + btn('{{revenda}}', 'Conhecer os planos de revenda') },
  { day: 7, audience: 'todos', subject: '⏳ {{nome}}, uma semana depois: ainda dá tempo',
    body: p('Já faz uma semana desde o seu teste. Se você ainda não decidiu, aqui vai um resumo rápido:') + precos + p('Sem taxa de instalação e com suporte 24 horas.') + btn('{{planos}}', 'Garantir meu plano') },
  { day: 8, audience: 'todos', subject: '💸 Trabalhar de casa, no celular, com renda todo mês',
    body: p('Revender Cine Pulse não exige loja, estoque físico nem equipamento caro. Você vende pelo WhatsApp, ativa pelo painel e acompanha tudo de onde estiver.', 'Quem leva a sério, com qualidade e dedicação no atendimento, constrói uma carteira de clientes que paga todo mês.') + revendaTab + btn('{{revenda}}', 'Começar minha revenda') },
  { day: 9, audience: 'todos', subject: '📦 Créditos que não somem: entenda o acumulativo',
    body: p('Em muitos painéis, crédito que não é usado no mês é perdido. Na Cine Pulse não.', 'Seus créditos são <b>acumulativos</b>: mesmo que você não venda todos, eles continuam no seu saldo para o mês seguinte. Você investe com segurança e cresce no seu ritmo.') + btn('{{revenda}}', 'Ver planos com créditos acumulativos') },
  { day: 10, audience: 'todos', subject: '🤝 Aqui você fala direto com o dono. Sem intermediário.',
    body: p('Muita gente tem medo de pegar revenda de outro revendedor e ficar sem suporte quando a coisa aperta. Entendemos isso.', 'Na Cine Pulse, o revendedor trata <b>diretamente com o dono</b>. Sem atravessador, sem telefone sem fio e com suporte 24 horas.') + btn('{{whatsapp}}', 'Falar com o dono agora') },
  { day: 11, audience: 'todos', subject: '📅 Um ano inteiro por R$ 119,90. Faça a conta.',
    body: p('{{nome}}, o plano anual sai por <b>R$ 119,90</b>, menos de R$ 10,00 por mês.', 'É o jeito mais econômico de continuar com a Cine Pulse e esquecer renovação por 12 meses.') + btn('{{planos}}', 'Quero o plano anual') },
  { day: 12, audience: 'todos', subject: '🖥️ Já tem painel Sigma? Aponte para o nosso servidor',
    body: p('Se você já tem seu próprio painel Sigma, não precisa recomeçar do zero.', 'Com o <b>apontamento Cine Pulse</b> você conecta o nosso servidor ao seu painel e continua atendendo seus clientes com a sua estrutura:', 'Até 600 ativos: <b>R$ 250,00/mês</b><br>Até 3000 ativos: <b>R$ 500,00/mês</b><br>Sem limite de ativos: <b>R$ 650,00/mês</b>') + btn('{{apontamento}}', 'Ver apontamento') },
  { day: 13, audience: 'todos', subject: '🏗️ Tem muitos clientes? Monte o SEU servidor',
    body: p('Para quem já tem uma ampla carteira de clientes e quer ser independente, a Cine Pulse <b>monta servidores</b> sob medida.', 'Você deixa de depender de terceiros e passa a controlar a própria operação, com o nosso suporte técnico ao lado.') + btn('{{whatsapp}}', 'Quero meu servidor') },
  { day: 14, audience: 'todos', subject: '🌐 Revendedor sem site perde venda. Resolva isso hoje.',
    body: p('Cliente confia mais em quem tem site profissional. Se você já revende, a Cine Pulse cria um <b>site profissional</b> para a sua revenda.', 'Veja os modelos disponíveis na nossa loja de amostras e escolha o seu.') + btn('{{sites_revenda}}', 'Ver modelos de site') },
  { day: 15, audience: 'todos', subject: '👑 Sua marca, seu app, sua DNS: conheça a Franquia',
    body: p('Quer ir além da revenda? A <b>Franquia Cine Pulse</b> entrega:', '• Painel personalizado com a sua marca<br>• DNS exclusiva<br>• Aplicativo personalizado<br>• Licença para apps personalizados em Smart TV LG e Samsung<br>• Créditos ilimitados', 'Tudo por <b>R$ 650,00 por mês</b>.') + btn('{{franquia}}', 'Quero minha franquia') },
  { day: 16, audience: 'todos', subject: '❓ As 4 dúvidas que todo mundo tem (respondidas)',
    body: p('<b>Precisa de internet boa?</b> Uma conexão estável de banda larga ou fibra já atende bem.', '<b>Funciona na minha TV?</b> Funciona em Smart TV, TV Box, celular e computador. Nosso suporte confirma o seu modelo.', '<b>Tem fidelidade?</b> Não. Você escolhe o período que quer pagar.', '<b>E se der problema?</b> Suporte 24 horas pelo WhatsApp.') + btn('{{whatsapp}}', 'Tirar outra dúvida') },
  { day: 17, audience: 'todos', subject: '📺 Imagem que não para no melhor momento',
    body: p('Nada pior do que a tela travar na hora mais importante do filme.', 'A Cine Pulse investe em servidores e monitoramento para manter a transmissão estável, e nosso suporte acompanha qualquer instabilidade de perto.') + btn('{{planos}}', 'Assinar a Cine Pulse') },
  { day: 18, audience: 'todos', subject: '🎯 {{nome}}, 3 meses por R$ 45,90',
    body: p('Ainda em dúvida se vale a pena? Comece pelo trimestral: <b>R$ 45,90</b> por três meses de Cine Pulse com suporte 24 horas.') + btn('{{planos}}', 'Assinar trimestral') },
  { day: 19, audience: 'todos', subject: '🚀 Com R$ 50 você já começa a revender',
    body: p('O plano de entrada da revenda Cine Pulse tem <b>10 créditos por R$ 50,00 por mês</b>.', 'Venda para 10 clientes e veja o retorno. Como os créditos são acumulativos, nada se perde. E você já ganha sua DNS exclusiva na contratação.') + btn('{{revenda}}', 'Começar com 10 créditos') },
  { day: 20, audience: 'todos', subject: '🛡️ Medo de revenda? Leia isto antes de desistir',
    body: p('Muita gente já pegou revenda, o fornecedor sumiu e os clientes ficaram na mão. Por isso a Cine Pulse trabalha diferente:', '✔ Você trata direto com o dono<br>✔ Suporte 24 horas<br>✔ DNS exclusiva para cada revendedor<br>✔ Sistema novo integrado, sem problemas de DNS<br>✔ Créditos acumulativos') + btn('{{whatsapp}}', 'Conversar com o dono') },
  { day: 21, audience: 'todos', subject: '🍿 Seis meses de Cine Pulse por R$ 69,90',
    body: p('O plano semestral é o favorito de quem quer economia sem pagar o ano todo: <b>R$ 69,90</b> por seis meses.') + precos + btn('{{planos}}', 'Quero o semestral') },
  { day: 22, audience: 'todos', subject: '📲 Como os revendedores conseguem os primeiros clientes',
    body: p('Os revendedores que crescem fazem o básico bem feito:', '1. Começam com família, amigos e vizinhos.<br>2. Usam o WhatsApp e o status para divulgar.<br>3. Oferecem teste e atendem rápido.<br>4. Cuidam de quem já é cliente para gerar indicação.', 'Com a Cine Pulse você tem o suporte 24 horas para atender bem desde o primeiro cliente.') + btn('{{revenda}}', 'Ver planos de revenda') },
  { day: 23, audience: 'todos', subject: '📺 Seu aplicativo na Smart TV LG e Samsung com a SUA marca',
    body: p('Na Franquia Cine Pulse você recebe licença para aplicativos personalizados em Smart TV LG e Samsung.', 'Seu cliente abre a TV e vê a sua marca. Isso gera confiança e fideliza.') + btn('{{franquia}}', 'Conhecer a franquia') },
  { day: 24, audience: 'todos', subject: '⏰ {{nome}}, você ainda está sem plano',
    body: p('Passamos para lembrar: seu teste já terminou e você ainda não escolheu um plano.', 'Escolha o seu em menos de 2 minutos e fale com a gente pelo WhatsApp para ativar.') + precos + btn('{{planos}}', 'Escolher meu plano') },
  { day: 25, audience: 'todos', subject: '♾️ Créditos ilimitados: venda sem olhar o saldo',
    body: p('Para quem já tem volume, o <b>painel ilimitado</b> sai por <b>R$ 1.200,00 por mês</b>, e na franquia os créditos ilimitados vêm junto com a sua marca por R$ 650,00.', 'Escolha o modelo que combina com o seu momento.') + btn('{{revenda}}', 'Comparar planos') },
  { day: 26, audience: 'todos', subject: '💬 Responda este e-mail. Um humano vai ler.',
    body: p('{{nome}}, queremos saber: o que faltou para você fechar com a Cine Pulse?', 'Preço, instalação, dúvida sobre revenda? Responda este e-mail ou chame no WhatsApp. A resposta vem de uma pessoa, não de robô.') + btn('{{whatsapp}}', 'Falar no WhatsApp') },
  { day: 27, audience: 'todos', subject: '⚠️ Estamos quase encerrando o seu acompanhamento',
    body: p('Faltam poucos dias para encerrarmos esta série de mensagens.', 'Se você ainda pensa em assinar ou revender, este é o melhor momento para falar com a gente.') + btn('{{whatsapp}}', 'Quero falar agora') },
  { day: 28, audience: 'todos', subject: '📊 Revenda Cine Pulse: tudo em um só e-mail',
    body: p('Resumo para você decidir:') + revendaTab + p('✔ Créditos acumulativos<br>✔ DNS exclusiva imediata<br>✔ Direto com o dono<br>✔ Suporte 24 horas<br>✔ Site profissional para sua revenda (opcional)') + btn('{{revenda}}', 'Escolher meu painel') },
  { day: 29, audience: 'todos', subject: '🎁 Amanhã chega uma oferta que só você vai receber',
    body: p('{{nome}}, fique de olho na sua caixa de entrada amanhã.', 'Preparamos uma condição especial que não aparece no site. Ela vai valer por pouco tempo.') },
  { day: 30, audience: 'cliente', subject: '🎁 {{nome}}, 30 dias de Cine Pulse por apenas R$ 12,90',
    body: p('Como prometido, aqui está a sua oferta exclusiva:', '<span style="font-size:22px"><b>Primeiro mês por apenas R$ 12,90</b></span>', 'Teste e conheça por 30 dias todos os nossos serviços, com suporte 24 horas. Depois, você escolhe o plano que preferir.') + btn('{{whatsapp}}', 'Quero por R$ 12,90'),
    whatsapp: 'Olá {{nome}}! Aqui é da Cine Pulse 🎬 Preparamos uma oferta só para você: o primeiro mês por apenas R$ 12,90 para conhecer nossos serviços por 30 dias, com suporte 24h. Responda QUERO para ativar.' },
  { day: 30, audience: 'revenda', subject: '🎁 {{nome}}, 10 créditos por R$ 20,00 para conhecer o painel',
    body: p('Como prometido, aqui está a sua oferta exclusiva de revendedor:', '<span style="font-size:22px"><b>10 créditos por apenas R$ 20,00 no primeiro mês</b></span>', 'Conheça o painel Cine Pulse por dentro. A partir do segundo mês, para manter o painel ativo, o plano mínimo é de 10 créditos por R$ 50,00 mensais.', 'Lembre-se: os créditos são acumulativos. Mesmo que você não venda todos, eles continuam com você.') + btn('{{whatsapp}}', 'Quero 10 créditos por R$ 20'),
    whatsapp: 'Olá {{nome}}! Aqui é da Cine Pulse 🎬 Oferta exclusiva para revendedor: 10 créditos por apenas R$ 20,00 no primeiro mês para conhecer o painel. Depois o plano mínimo é 10 créditos por R$ 50,00/mês, e os créditos são acumulativos. Responda QUERO para liberar.' },
];
