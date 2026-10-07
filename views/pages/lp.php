<?php
use App\Core\Settings;
use App\Core\Whmcs;

$wa = Settings::whatsapp((string) $page['wa']);
$src = $src ?? [];
?>
<section class="hero hero-lp">
  <div class="wrap hero-in">
    <h1><?= e((string) $page['h1']) ?></h1>
    <p class="lead"><?= e((string) $page['lead']) ?></p>
    <?php if (!empty($min)): ?><p class="from">A partir de <strong><?= e(money((float) $min)) ?></strong><span>/mês</span></p><?php endif; ?>
    <div class="hero-cta">
      <a class="btn btn-primary btn-lg" href="#planos" data-ev="cta_click" data-where="lp-hero"><?= e((string) $page['cta']) ?> <?= icon('arrow', 20) ?></a>
      <a class="btn btn-ghost-dark btn-lg" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="lp-hero"><?= icon('whatsapp', 20) ?> Falar no WhatsApp</a>
    </div>
  </div>
</section>
<?php
$items = array_slice($src['benefits'] ?? $src['features'] ?? [], 0, 3);
if ($items) { $title = ''; require BASE_PATH . '/views/partials/benefits.php'; }
$plansTitle = 'Escolha o plano';
require BASE_PATH . '/views/partials/plans.php';
$faq = array_slice($src['faq'] ?? [], 0, 4);
require BASE_PATH . '/views/partials/faq.php';
$bandTitle = (string) $page['h1'];
$bandText = 'Fale com a gente se tiver qualquer dúvida antes de contratar.';
$bandHref = '#planos';
$bandLabel = (string) $page['cta'];
require BASE_PATH . '/views/partials/cta-band.php';
