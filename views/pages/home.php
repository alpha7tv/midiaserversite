<?php
use App\Core\Settings;
use App\Core\Whmcs;

$wa = Settings::whatsapp((string) $page['wa']);
$cards = [
    ['hospedagem', '/hospedagem', 'server', 'Hospedagem de sites', 'Hospedagem cPanel com disco NVMe, SSL grátis e transferência ilimitada.'],
    ['revenda-hospedagem', '/revenda-hospedagem', 'tag', 'Revenda de hospedagem', 'Crie seus planos e hospede seus clientes com painel WHM e marca própria.'],
    ['streaming', '/streaming', 'wave', 'Streaming de áudio', 'Streaming com AutoDJ, ouvintes ilimitados e site administrável incluso.'],
    ['streaming', '/web-radio-completa', 'radio', 'Web rádio completa', 'Streaming, AutoDJ, site com player, painel e subdomínio grátis.'],
    ['revenda-streaming', '/revenda-streaming', 'network', 'Revenda de streaming', 'Monte sua operação e venda streaming com a sua marca.'],
    ['sites-para-radio', '/sites-para-radio', 'globe', 'Sites para rádio', '15 modelos com player ao vivo e painel fácil de editar.'],
    ['automacao-radio', '/automacao-radio', 'panel', 'Automação de rádio', 'Mídia Rádio Studio: playlist, cartucheira, grade e hora certa.'],
    ['conteudos-para-radio', '/conteudos-para-radio', 'cloud', 'Conteúdos para rádios', 'Programas e programetes atualizados todos os dias, direto na sua rádio.'],
];
?>
<section class="hero hero-home">
  <div class="wrap hero-in hero-grid">
    <div>
      <p class="eyebrow">Hospedagem · Streaming · Automação</p>
      <h1><?= e($page['h1']) ?></h1>
      <p class="lead"><?= e($page['lead']) ?></p>
      <div class="hero-cta">
        <a class="btn btn-primary btn-lg" href="#solucoes" data-ev="cta_click" data-where="hero-solucoes">Conhecer soluções <?= icon('arrow', 20) ?></a>
        <a class="btn btn-ghost-dark btn-lg" href="#caminhos" data-ev="cta_click" data-where="hero-planos">Ver planos</a>
      </div>
      <div class="hero-cta hero-cta-2">
        <a class="btn btn-wa" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="hero"><?= icon('whatsapp', 20) ?> Falar no WhatsApp</a>
        <a class="btn btn-link-dark" href="<?= e(Whmcs::login()) ?>" data-ev="click_area_cliente">Área do Cliente <?= icon('arrow', 18) ?></a>
      </div>
    </div>
    <div class="hero-visual" aria-hidden="true">
      <div class="hv hv-1"><?= icon('server', 28) ?><span>Hospedagem</span></div>
      <div class="hv hv-2"><?= icon('wave', 28) ?><span>Streaming</span></div>
      <div class="hv hv-3"><?= icon('panel', 28) ?><span>Automação</span></div>
      <div class="hv hv-4"><?= icon('cloud', 28) ?><span>Conteúdos</span></div>
    </div>
  </div>
</section>

<section class="sec" id="caminhos">
  <div class="wrap">
    <h2 class="sec-title">Por onde você quer começar?</h2>
    <div class="grid grid-3">
      <a class="card path" href="/hospedagem" data-ev="cta_click" data-where="caminho-site">
        <span class="feat-ic"><?= icon('server', 26) ?></span>
        <h3>Tenho um site ou empresa</h3>
        <p>Hospedagem cPanel com NVMe e SSL grátis.<?php if (!empty($mins['hospedagem'])): ?> A partir de <strong><?= e(money($mins['hospedagem'])) ?>/mês</strong>.<?php endif; ?></p>
        <span class="more">Ver hospedagem <?= icon('arrow', 18) ?></span>
      </a>
      <a class="card path" href="/streaming" data-ev="cta_click" data-where="caminho-radio">
        <span class="feat-ic"><?= icon('radio', 26) ?></span>
        <h3>Tenho ou quero uma rádio</h3>
        <p>Streaming com AutoDJ, site incluso e ouvintes ilimitados.<?php if (!empty($mins['streaming'])): ?> A partir de <strong><?= e(money($mins['streaming'])) ?>/mês</strong>.<?php endif; ?></p>
        <span class="more">Ver streaming <?= icon('arrow', 18) ?></span>
      </a>
      <a class="card path" href="/revenda-hospedagem" data-ev="cta_click" data-where="caminho-revenda">
        <span class="feat-ic"><?= icon('tag', 26) ?></span>
        <h3>Quero revender e empreender</h3>
        <p>Revenda de hospedagem ou de streaming, com marca própria.<?php if (!empty($mins['revenda-hospedagem'])): ?> A partir de <strong><?= e(money($mins['revenda-hospedagem'])) ?>/mês</strong>.<?php endif; ?></p>
        <span class="more">Ver revendas <?= icon('arrow', 18) ?></span>
      </a>
    </div>
  </div>
</section>

<section class="sec sec-alt" id="solucoes">
  <div class="wrap">
    <h2 class="sec-title">Soluções completas para o seu projeto online</h2>
    <p class="sec-sub">Tudo na mesma Área do Cliente: contrate, pague e acompanhe os seus serviços em um só lugar.</p>
    <div class="grid grid-4">
      <?php foreach ($cards as [$slug, $href, $ic, $t, $d]): $m = $mins[$slug] ?? null; ?>
      <a class="card sol" href="<?= e($href) ?>" data-ev="cta_click" data-where="solucao">
        <span class="feat-ic"><?= icon($ic, 26) ?></span>
        <h3><?= e($t) ?></h3>
        <p><?= e($d) ?></p>
        <?php if ($m): ?><span class="sol-price">a partir de <strong><?= e(money($m)) ?></strong>/mês</span><?php endif; ?>
        <span class="more">Saiba mais <?= icon('arrow', 18) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
    <p class="note-line"><?= icon('server', 18) ?> <a href="/vps">VPS Linux</a> — em breve na Mídia Server.</p>
  </div>
</section>

<?php require BASE_PATH . '/views/partials/divulgue.php'; ?>

<?php
$items = [
    ['users', 'Uma conta para tudo', 'Hospedagem, streaming, automação e conteúdos contratados e gerenciados pela mesma Área do Cliente.'],
    ['bolt', 'Contratação online', 'Escolha o plano, siga para o carrinho e pague pela fatura, inclusive com Pix.'],
    ['headset', 'Suporte humano', 'Atendimento por WhatsApp e tickets na Área do Cliente.'],
    ['radio', 'Feito para quem tem rádio', 'Streaming, site, automação, conteúdos e divulgação no diretório de rádios, tudo conectado.'],
];
$title = 'Por que a Mídia Server';
require BASE_PATH . '/views/partials/benefits.php';

$faq = $page['faq'];
require BASE_PATH . '/views/partials/faq.php';

$bandTitle = 'Pronto para colocar seu projeto no ar?';
$bandText = 'Fale com o nosso time e encontre o plano ideal para hospedagem, streaming ou automação.';
$bandHref = '#solucoes';
$bandLabel = 'Conhecer soluções';
require BASE_PATH . '/views/partials/cta-band.php';
