<?php
use App\Controllers\LeadController;
use App\Core\Settings;

$ty = LeadController::thanksData();
$nome = (string) ($ty['n'] ?? '');
$interest = (string) ($ty['i'] ?? 'os serviços da Mídia Server');
$msg = 'Olá, sou ' . ($nome !== '' ? $nome : 'cliente') . ' e acabei de enviar o formulário no site da Mídia Server. Tenho interesse em ' . $interest . '.';
$wa = Settings::whatsapp($msg);
?>
<section class="hero hero-lp" data-lead-thanks="<?= e((string) ($ty['p'] ?? 'lp')) ?>">
  <div class="wrap hero-in">
    <p class="eyebrow">Recebemos o seu contato</p>
    <h1><?= $nome !== '' ? e('Obrigado, ' . explode(' ', $nome)[0] . '!') : 'Obrigado!' ?></h1>
    <p class="lead">Nossa equipe vai falar com você pelo WhatsApp. Se preferir, comece a conversa agora.</p>
    <div class="hero-cta">
      <a class="btn btn-wa btn-lg" id="wa-go" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="obrigado"><?= icon('whatsapp', 20) ?> Continuar no WhatsApp</a>
      <a class="btn btn-ghost-dark btn-lg" href="<?= e((string) ($ty['p'] ?? '/')) ?>">Voltar</a>
    </div>
  </div>
</section>
