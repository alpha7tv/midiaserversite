<?php
use App\Core\Settings;

$wa = Settings::whatsapp((string) ($page['wa'] ?? ''));
?>
<section class="band">
  <div class="wrap band-in">
    <div>
      <h2><?= e($bandTitle ?? 'Pronto para começar?') ?></h2>
      <p><?= e($bandText ?? 'Escolha o plano ideal ou fale com a nossa equipe pelo WhatsApp.') ?></p>
    </div>
    <div class="band-cta">
      <?php if (!empty($bandHref)): ?><a class="btn btn-light btn-lg" href="<?= e($bandHref) ?>" data-ev="cta_click" data-where="faixa"><?= e($bandLabel ?? 'Ver planos') ?></a><?php endif; ?>
      <a class="btn btn-ghost-dark btn-lg" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="faixa"><?= icon('whatsapp', 20) ?> WhatsApp</a>
    </div>
  </div>
</section>
