<?php
/** @var array $page */
use App\Core\Settings;

$wa = Settings::whatsapp((string) ($page['wa'] ?? ''));
$ctaHref = $ctaHref ?? '#planos';
?>
<?php $side = !empty($plans) && empty($noSide) && ($page['template'] ?? '') !== 'lp'; ?>
<section class="hero">
  <div class="wrap hero-in<?= $side ? ' hero-grid' : '' ?>">
    <div class="hero-main">
    <?php if (!empty($page['breadcrumb']) || ($page['path'] ?? '/') !== '/'): ?>
    <nav class="crumbs" aria-label="Você está em">
      <a href="/">Início</a>
      <?php foreach (($page['breadcrumb'] ?? []) as [$n, $u]): ?><span aria-hidden="true">/</span><a href="<?= e($u) ?>"><?= e($n) ?></a><?php endforeach; ?>
      <span aria-hidden="true">/</span><span aria-current="page"><?= e((string) ($page['crumb'] ?? $page['h1'])) ?></span>
    </nav>
    <?php endif; ?>
    <?php if (!empty($page['eyebrow'])): ?><p class="eyebrow"><?= e($page['eyebrow']) ?></p><?php endif; ?>
    <h1><?= e((string) $page['h1']) ?></h1>
    <?php if (!empty($page['lead'])): ?><p class="lead"><?= e($page['lead']) ?></p><?php endif; ?>
    <?php if (isset($min) && $min !== null && !empty($page['product'])): ?>
      <p class="from">A partir de <strong><?= e(money((float) $min)) ?></strong><span>/mês</span></p>
    <?php endif; ?>
    <div class="hero-cta">
      <?php if (!empty($page['cta'])): ?><a class="btn btn-primary btn-lg" href="<?= e($ctaHref) ?>" data-ev="cta_click" data-where="hero"><?= e($page['cta']) ?> <?= icon('arrow', 20) ?></a><?php endif; ?>
      <a class="btn btn-ghost-dark btn-lg" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="hero"><?= icon('whatsapp', 20) ?> Falar no WhatsApp</a>
    </div>
    <?= $heroExtra ?? '' ?>
    </div>
    <?php if ($side): ?>
    <aside class="hero-side" aria-label="Planos e preços">
      <p class="hs-title">Planos e preços</p>
      <ul>
        <?php foreach ($plans as $pl): ?>
        <li>
          <span class="hs-name"><?= e((string) $pl['name']) ?></span>
          <span class="hs-price"><?= e(money((float) $pl['price_month'])) ?><small>/mês</small></span>
          <a class="btn btn-light btn-sm" href="<?= e(\App\Core\Whmcs::cart((int) $pl['whmcs_pid'])) ?>" rel="nofollow" data-ev="begin_checkout" data-where="hero-side" data-pid="<?= (int) $pl['whmcs_pid'] ?>" data-plan="<?= e((string) $pl['name']) ?>" data-price="<?= e((string) $pl['price_month']) ?>">Contratar</a>
        </li>
        <?php endforeach; ?>
      </ul>
      <p class="hs-note">Você finaliza o pedido na Área do Cliente.</p>
    </aside>
    <?php endif; ?>
  </div>
</section>
