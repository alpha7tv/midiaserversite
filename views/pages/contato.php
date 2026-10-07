<?php
use App\Core\Settings;
use App\Core\Whmcs;

require BASE_PATH . '/views/partials/hero.php';
?>
<section class="sec">
  <div class="wrap">
    <div class="grid grid-4">
      <a class="card path" href="<?= e(Settings::whatsapp((string) $page['wa'])) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="contato">
        <span class="feat-ic"><?= icon('whatsapp', 26) ?></span><h3>WhatsApp</h3><p>O jeito mais rápido de falar com a gente.</p><span class="more">Chamar agora <?= icon('arrow', 18) ?></span>
      </a>
      <a class="card path" href="mailto:<?= e(Settings::get('support_email')) ?>" data-ev="click_email">
        <span class="feat-ic"><?= icon('mail', 26) ?></span><h3>E-mail</h3><p><?= e(Settings::get('support_email')) ?></p><span class="more">Enviar e-mail <?= icon('arrow', 18) ?></span>
      </a>
      <a class="card path" href="<?= e(Whmcs::ticket()) ?>" data-ev="cta_click" data-where="contato-ticket">
        <span class="feat-ic"><?= icon('headset', 26) ?></span><h3>Ticket de suporte</h3><p>Para clientes: abra um chamado na Área do Cliente.</p><span class="more">Abrir ticket <?= icon('arrow', 18) ?></span>
      </a>
      <a class="card path" href="<?= e(Whmcs::login()) ?>" data-ev="click_area_cliente">
        <span class="feat-ic"><?= icon('users', 26) ?></span><h3>Área do Cliente</h3><p>Faturas, serviços e dados da sua conta.</p><span class="more">Entrar <?= icon('arrow', 18) ?></span>
      </a>
    </div>
  </div>
</section>
