<?php
use App\Core\Settings;
use App\Core\Whmcs;

require BASE_PATH . '/views/partials/hero.php';
?>
<section class="sec">
  <div class="wrap">
    <div class="grid grid-3">
      <a class="card path" href="<?= e(Whmcs::ticket()) ?>" data-ev="cta_click" data-where="suporte-ticket">
        <span class="feat-ic"><?= icon('headset', 26) ?></span><h3>Abrir um ticket</h3><p>Acompanhe o atendimento pela Área do Cliente.</p><span class="more">Abrir ticket <?= icon('arrow', 18) ?></span>
      </a>
      <a class="card path" href="<?= e(Settings::whatsapp((string) $page['wa'])) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="suporte">
        <span class="feat-ic"><?= icon('whatsapp', 26) ?></span><h3>WhatsApp</h3><p>Converse com o nosso time.</p><span class="more">Chamar agora <?= icon('arrow', 18) ?></span>
      </a>
      <a class="card path" href="<?= e(Whmcs::base()) ?>/index.php?rp=/knowledgebase" data-ev="cta_click" data-where="suporte-kb">
        <span class="feat-ic"><?= icon('help', 26) ?></span><h3>Base de conhecimento</h3><p>Artigos e tutoriais para resolver dúvidas comuns.</p><span class="more">Consultar <?= icon('arrow', 18) ?></span>
      </a>
    </div>
  </div>
</section>
<section class="sec sec-alt">
  <div class="wrap wrap-narrow">
    <h2 class="sec-title">Antes de abrir o chamado</h2>
    <ul class="checks one">
      <li><?= icon('check', 18, 'i ok') ?> Informe o serviço (domínio, rádio ou licença) e o e-mail da conta.</li>
      <li><?= icon('check', 18, 'i ok') ?> Descreva o que aconteceu e, se possível, anexe um print.</li>
      <li><?= icon('check', 18, 'i ok') ?> Para o Mídia Rádio Studio, informe a versão do programa e a mensagem exibida.</li>
    </ul>
  </div>
</section>
