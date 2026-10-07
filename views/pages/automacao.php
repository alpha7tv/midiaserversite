<?php
use App\Core\Settings;
use App\Core\Whmcs;

$studio = !empty($plans) ? $plans[0] : null;
$pid = $studio ? (int) $studio['whmcs_pid'] : 20;
$monthly = $studio ? (float) $studio['price_month'] : 0.0;
$yearly = $studio && $studio['price_year'] ? (float) $studio['price_year'] : 0.0;

$ctaHref = '/baixar/midia-radio-studio';
$heroExtra = '<p class="hero-note">Teste grátis, sem cartão · Windows 10 e 11 · Suporte em português no WhatsApp</p>';
require BASE_PATH . '/views/partials/hero.php';
?>
<section class="sec sec-tight">
  <div class="wrap">
    <figure class="shot shot-hero">
      <img src="/assets/img/studio/principal.webp" width="1200" height="720" alt="<?= e($page['shots'][0][1]) ?>" decoding="async" fetchpriority="high">
    </figure>
  </div>
</section>

<section class="sec" id="recursos">
  <div class="wrap">
    <h2 class="sec-title">Feito para quem opera rádio de verdade</h2>
    <p class="sec-sub">Do locutor ao vivo à madrugada no automático: cada função foi pensada para ser rápida de aprender e confiável para ficar no ar.</p>
    <div class="grid grid-3">
      <?php foreach ($page['features'] as [$ic, $t, $d]): ?>
      <article class="card feat"><span class="feat-ic"><?= icon($ic, 26) ?></span><h3><?= e($t) ?></h3><p><?= e($d) ?></p></article>
      <?php endforeach; ?>
    </div>
    <ul class="checks">
      <?php foreach ($page['extras'] as $x): ?><li><?= icon('check', 18, 'i ok') ?> <?= e($x) ?></li><?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="sec sec-alt" id="telas">
  <div class="wrap">
    <h2 class="sec-title">Veja o programa por dentro</h2>
    <div class="shots">
      <?php foreach (array_slice($page['shots'], 1) as [$f, $alt]): ?>
      <figure class="shot"><img src="/assets/img/studio/<?= e($f) ?>" width="1000" height="600" loading="lazy" decoding="async" alt="<?= e($alt) ?>"></figure>
      <?php endforeach; ?>
    </div>
    <?php $yt = Settings::get('youtube_studio'); if ($yt !== ''): ?>
    <p class="center"><a class="btn btn-ghost" href="<?= e($yt) ?>" target="_blank" rel="noopener" data-ev="cta_click" data-where="youtube-studio">Ver tutoriais no YouTube <?= icon('external', 18) ?></a></p>
    <?php endif; ?>
  </div>
</section>

<section class="sec" id="planos">
  <div class="wrap">
    <h2 class="sec-title">Todas as funções. Um preço simples.</h2>
    <p class="sec-sub">Licença para 1 computador, com atualizações e suporte inclusos. Sem taxa de instalação.</p>
    <div class="plans plans-3">
      <article class="plan">
        <h3>Demonstração</h3>
        <p class="price">Grátis</p>
        <ul class="plan-list">
          <li><?= icon('check', 18, 'i ok') ?><span>Playlist com até 10 músicas</span></li>
          <li><?= icon('check', 18, 'i ok') ?><span>Cartucheira com até 5 vinhetas</span></li>
          <li><?= icon('check', 18, 'i ok') ?><span>Conheça a tela e o som antes de assinar</span></li>
        </ul>
        <a class="btn btn-ghost btn-block" href="/baixar/midia-radio-studio" data-ev="file_download" data-file="Mídia Rádio Studio">Baixar grátis</a>
      </article>
      <article class="plan plan-hl">
        <h3>Mensal</h3>
        <p class="price"><?= $monthly ? e(money($monthly)) : '' ?><span>/mês</span></p>
        <ul class="plan-list">
          <li><?= icon('check', 18, 'i ok') ?><span>Todas as funções liberadas</span></li>
          <li><?= icon('check', 18, 'i ok') ?><span>1 computador</span></li>
          <li><?= icon('check', 18, 'i ok') ?><span>Atualizações e suporte no WhatsApp</span></li>
        </ul>
        <a class="btn btn-primary btn-block" href="<?= e(Whmcs::cart($pid, 'monthly')) ?>" rel="nofollow" data-ev="begin_checkout" data-pid="<?= $pid ?>" data-plan="Mídia Rádio Studio mensal" data-price="<?= e((string) $monthly) ?>">Assinar mensal</a>
      </article>
      <article class="plan">
        <h3>Anual</h3>
        <p class="price"><?= $yearly ? e(money($yearly)) : '' ?><span>/ano</span></p>
        <?php if ($yearly && $monthly): ?><p class="plan-tag">Equivale a <?= e(money($yearly / 12)) ?>/mês · economia de <?= e(money($monthly * 12 - $yearly)) ?></p><?php endif; ?>
        <ul class="plan-list">
          <li><?= icon('check', 18, 'i ok') ?><span>Todas as funções liberadas</span></li>
          <li><?= icon('check', 18, 'i ok') ?><span>1 computador</span></li>
          <li><?= icon('check', 18, 'i ok') ?><span>Um pagamento por ano</span></li>
        </ul>
        <a class="btn btn-primary btn-block" href="<?= e(Whmcs::cart($pid, 'annually')) ?>" rel="nofollow" data-ev="begin_checkout" data-pid="<?= $pid ?>" data-plan="Mídia Rádio Studio anual" data-price="<?= e((string) $yearly) ?>">Assinar anual</a>
      </article>
    </div>
    <p class="center muted">Precisa de mais computadores ou de uma rede de emissoras? <a href="<?= e(Settings::whatsapp('Olá, quero saber sobre o Mídia Rádio Studio para mais de um computador.')) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="studio-multi">Fale com a gente</a>.</p>
  </div>
</section>

<section class="sec sec-alt" id="versoes">
  <div class="wrap wrap-narrow">
    <h2 class="sec-title">Versões e atualizações</h2>
    <?php if (!empty($latest)): ?>
    <div class="card version">
      <p class="badge">Nova versão disponível</p>
      <h3>v<?= e((string) $latest['version']) ?> <small><?= !empty($latest['released_at']) ? e(date('d/m/Y', strtotime((string) $latest['released_at']))) : '' ?></small></h3>
      <?php if (!empty($latest['notes'])): ?><h4>Novidades</h4><div class="prose"><?= \App\Core\Markup::toHtml((string) $latest['notes']) ?></div><?php endif; ?>
      <?php if (!empty($latest['fixes'])): ?><h4>Correções</h4><div class="prose"><?= \App\Core\Markup::toHtml((string) $latest['fixes']) ?></div><?php endif; ?>
      <p><a class="btn btn-primary" href="/baixar/midia-radio-studio" data-ev="file_download" data-file="Mídia Rádio Studio v<?= e((string) $latest['version']) ?>"><?= icon('download', 20) ?> Baixar / atualizar</a></p>
    </div>
    <?php if (count($versions) > 1): ?>
    <h3 class="sm">Histórico</h3>
    <ul class="changelog">
      <?php foreach (array_slice($versions, 1) as $v): ?><li><strong>v<?= e((string) $v['version']) ?></strong> <?= !empty($v['released_at']) ? '· ' . e(date('d/m/Y', strtotime((string) $v['released_at']))) : '' ?><?php if (!empty($v['notes'])): ?><br><?= nl2br(e((string) $v['notes'])) ?><?php endif; ?></li><?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <?php else: ?>
    <p class="sec-sub">As atualizações do Mídia Rádio Studio são avisadas dentro do próprio programa. Quando uma nova versão for publicada, ela aparece aqui com as novidades e correções.</p>
    <?php endif; ?>
  </div>
</section>

<section class="sec" id="download">
  <div class="wrap">
    <div class="grid grid-2">
      <div>
        <h2 class="sec-title left">Baixe e teste hoje</h2>
        <p>Instalador para Windows 10 e 11. Abra e use a demonstração na hora. Para liberar todas as funções, é só colar a chave de licença no programa, sem reinstalar.</p>
        <p><a class="btn btn-primary btn-lg" href="/baixar/midia-radio-studio" data-ev="file_download" data-file="Mídia Rádio Studio"><?= icon('download', 20) ?> Baixar Mídia Rádio Studio</a></p>
        <p class="muted">Instalador de aproximadamente 57 MB. Software licenciado; o conteúdo tocado é de responsabilidade da emissora.</p>
      </div>
      <div class="card">
        <h3>Requisitos</h3>
        <ul class="checks one">
          <?php foreach ($page['requirements'] as $r): ?><li><?= icon('check', 18, 'i ok') ?> <?= e($r) ?></li><?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>
<?php
$faq = $page['faq'];
require BASE_PATH . '/views/partials/faq.php';
$bandTitle = 'Coloque a sua rádio no automático hoje';
$bandText = 'Baixe grátis, teste no seu computador e assine quando estiver pronto.';
$bandHref = '/baixar/midia-radio-studio';
$bandLabel = 'Baixar grátis';
require BASE_PATH . '/views/partials/cta-band.php';
$related = $page['related'];
require BASE_PATH . '/views/partials/related.php';
