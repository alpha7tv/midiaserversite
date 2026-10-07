<?php
use App\Core\Csrf;
use App\Core\Settings;

$wa = Settings::whatsapp((string) $page['wa']);
$src = $src ?? [];
[$ts, $sig] = Csrf::stamp();
$interest = (string) ($src['schema_name'] ?? $src['h1'] ?? $page['h1']);
$tsKey = Settings::get('turnstile_site_key');
?>
<section class="hero hero-lp">
  <div class="wrap hero-in hero-grid">
    <div class="hero-main">
      <h1><?= e((string) $page['h1']) ?></h1>
      <p class="lead"><?= e((string) $page['lead']) ?></p>
      <?php if (!empty($min)): ?><p class="from">A partir de <strong><?= e(money((float) $min)) ?></strong><span>/mês</span></p><?php endif; ?>
      <ul class="lp-points">
        <li><?= icon('check', 18, 'i ok') ?> Preços e planos iguais aos da Área do Cliente</li>
        <li><?= icon('check', 18, 'i ok') ?> Pague pela fatura, inclusive com Pix</li>
        <li><?= icon('check', 18, 'i ok') ?> Suporte por WhatsApp e tickets</li>
      </ul>
      <div class="hero-cta">
        <a class="btn btn-primary btn-lg" href="#planos" data-ev="cta_click" data-where="lp-hero"><?= e((string) $page['cta']) ?> <?= icon('arrow', 20) ?></a>
        <a class="btn btn-ghost-dark btn-lg" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="lp-hero"><?= icon('whatsapp', 20) ?> Falar no WhatsApp</a>
      </div>
    </div>
    <form class="lead-card" id="proposta" method="post" action="/lead" data-lead>
      <h2>Receba uma proposta no WhatsApp</h2>
      <p class="lead-sub">Deixe seu contato e nossa equipe fala com você.</p>
      <input type="hidden" name="csrf" value="<?= e(Csrf::token()) ?>">
      <input type="hidden" name="ts" value="<?= e($ts) ?>"><input type="hidden" name="sig" value="<?= e($sig) ?>">
      <input type="hidden" name="page" value="<?= e((string) $page['path']) ?>">
      <input type="hidden" name="interest" value="<?= e($interest) ?>">
      <input type="hidden" name="attribution" value="">
      <label class="fld"><span>Seu nome</span><input name="name" required minlength="2" maxlength="80" autocomplete="name" placeholder="Como podemos te chamar?"></label>
      <label class="fld"><span>WhatsApp com DDD</span><input name="phone" type="tel" inputmode="tel" required maxlength="20" autocomplete="tel" placeholder="(14) 98815-9045"></label>
      <div class="hp" aria-hidden="true"><label>Não preencha <input name="website" tabindex="-1" autocomplete="off"></label></div>
      <?php if ($tsKey !== ''): ?><div class="cf-turnstile" data-sitekey="<?= e($tsKey) ?>"></div><script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script><?php endif; ?>
      <button class="btn btn-primary btn-block btn-lg" type="submit">Quero receber contato</button>
      <p class="lead-note">Ao enviar, você concorda em receber contato da Mídia Server por WhatsApp e com a <a href="/politica-de-privacidade" target="_blank" rel="noopener">Política de Privacidade</a>.</p>
    </form>
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
?>
<div class="sticky-cta" role="region" aria-label="Contratar">
  <a class="btn btn-wa" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="lp-barra"><?= icon('whatsapp', 20) ?> WhatsApp</a>
  <a class="btn btn-primary" href="#planos" data-ev="cta_click" data-where="lp-barra">Ver planos</a>
</div>
