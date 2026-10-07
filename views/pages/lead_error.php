<?php use App\Core\Settings; ?>
<section class="hero"><div class="wrap hero-in">
  <h1>Não foi possível enviar</h1>
  <p class="lead"><?= e($message) ?></p>
  <div class="hero-cta">
    <a class="btn btn-primary btn-lg" href="javascript:history.back()">Voltar e tentar de novo</a>
    <a class="btn btn-wa btn-lg" href="<?= e(Settings::whatsapp('Olá, tive um problema ao enviar o formulário do site.')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp', 20) ?> Falar no WhatsApp</a>
  </div>
</div></section>
