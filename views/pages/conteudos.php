<?php
use App\Core\Settings;

$ctaHref = '#planos';
$amostras = require BASE_PATH . '/config/conteudos_amostras.php';
$base = rtrim(Settings::get('conteudos_url'), '/');
$heroExtra = '<p class="hero-note"><a href="#amostras" style="color:#fff">Ouça amostras dos programas ↓</a></p>';
require BASE_PATH . '/views/partials/hero.php';

$steps = $page['how'];
$stepsTitle = 'Sua programação sempre em dia, sem trabalho manual';
$stepsId = 'como-funciona';
require BASE_PATH . '/views/partials/steps.php';
?>
<section class="sec" id="amostras">
  <div class="wrap">
    <h2 class="sec-title">Ouça amostras dos programas</h2>
    <p class="sec-sub">Aperte o play e ouça um trecho de cada programa. Os arquivos completos chegam à sua rádio pelo plano.</p>
    <div class="samples">
      <?php foreach ($amostras as $a): ?>
      <article class="sample" data-sample>
        <img src="<?= e($base) ?>/capa/<?= e($a['slug']) ?>" width="64" height="64" loading="lazy" decoding="async" alt="Capa do programa <?= e($a['nome']) ?>">
        <div class="sample-info">
          <h3><?= e($a['nome']) ?></h3>
          <p><?= e($a['cat']) ?> · <?= e($a['tipo']) ?></p>
          <div class="sample-bar" aria-hidden="true"><i></i></div>
        </div>
        <button type="button" class="sample-play" data-sample-btn aria-label="Ouvir amostra de <?= e($a['nome']) ?>" data-name="<?= e($a['nome']) ?>"><?= icon('play', 22) ?></button>
        <audio preload="none" data-src="<?= e($base) ?>/demo/<?= (int) $a['demo'] ?>"></audio>
      </article>
      <?php endforeach; ?>
    </div>
    <p class="center"><a class="btn btn-ghost" href="<?= e($base) ?>/programas" target="_blank" rel="noopener" data-ev="cta_click" data-where="amostras-catalogo">Ver todos os programas e ouvir mais <?= icon('external', 18) ?></a></p>
  </div>
</section>

<section class="sec sec-alt" id="categorias">
  <div class="wrap">
    <h2 class="sec-title">Conteúdo para todos os estilos</h2>
    <p class="sec-sub">Programas, programetes e conteúdo de fim de semana organizados em <?= count($page['categories']) ?> categorias. O catálogo é atualizado pela nossa equipe.</p>
    <ul class="chips">
      <?php foreach ($page['categories'] as $c): ?><li><?= e($c) ?></li><?php endforeach; ?>
    </ul>
    <p class="center"><a class="btn btn-ghost" href="<?= e(Settings::get('conteudos_url')) ?>" target="_blank" rel="noopener" data-ev="cta_click" data-where="catalogo-conteudos">Ver o catálogo completo <?= icon('external', 18) ?></a></p>
  </div>
</section>

<section class="sec sec-tight">
  <div class="wrap">
    <div class="promo promo-static">
      <span class="promo-ic"><?= icon('cloud', 34) ?></span>
      <span class="promo-txt"><strong>Conteúdo novo direto no computador da sua rádio</strong><span>O aplicativo de sincronização mantém uma pasta do computador sempre atualizada. Aponte a sua automação para ela e pronto.</span></span>
    </div>
  </div>
</section>
<?php
$plansTitle = 'Planos de conteúdo';
$plansSub = 'Escolha o pacote que combina com a sua rádio.';
$limit = 10;
require BASE_PATH . '/views/partials/plans.php';
$compareTitle = 'Compare os pacotes';
require BASE_PATH . '/views/partials/compare.php';
$faq = $page['faq'];
require BASE_PATH . '/views/partials/faq.php';
$bandTitle = 'Conteúdo novo todos os dias na sua rádio';
$bandText = 'Escolha o pacote ou fale com a nossa equipe.';
$bandHref = '#planos';
$bandLabel = 'Ver planos';
require BASE_PATH . '/views/partials/cta-band.php';
$related = $page['related'];
require BASE_PATH . '/views/partials/related.php';
