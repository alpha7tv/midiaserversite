<?php
use App\Core\Settings;

$ctaHref = rtrim(Settings::get('radios_url'), '/') . '/conta/criar';
require BASE_PATH . '/views/partials/hero.php';

$steps = [
    ['Crie sua conta', 'Informe seus dados e confirme que é responsável pela emissora ou tem autorização para cadastrá-la.'],
    ['Cadastre a rádio', 'Envie nome, logo, descrição, cidade, estado, gênero, link do streaming, site e redes sociais.'],
    ['Aguarde a aprovação', 'Seu cadastro fica AGUARDANDO APROVAÇÃO. Depois de aprovado, a rádio aparece no diretório.'],
];
$stepsTitle = 'Como funciona';
require BASE_PATH . '/views/partials/steps.php';
?>
<section class="sec sec-alt">
  <div class="wrap wrap-narrow">
    <div class="card">
      <h2>O que você precisa ter em mãos</h2>
      <ul class="checks one">
        <li><?= icon('check', 18, 'i ok') ?> Nome da rádio, cidade, estado e país</li>
        <li><?= icon('check', 18, 'i ok') ?> Logo da rádio</li>
        <li><?= icon('check', 18, 'i ok') ?> Link do streaming para o player ao vivo</li>
        <li><?= icon('check', 18, 'i ok') ?> Site, Facebook, Instagram, TikTok, YouTube e WhatsApp (opcionais)</li>
      </ul>
      <p><a class="btn btn-primary btn-lg" href="<?= e($ctaHref) ?>" target="_blank" rel="noopener" data-ev="cta_click" data-where="cadastrar-radio">Cadastrar minha rádio grátis <?= icon('external', 18) ?></a></p>
      <p class="muted">O cadastro é feito no diretório Rádios do Brasil, da Mídia Server. O formulário próprio deste portal será integrado na próxima etapa.</p>
    </div>
  </div>
</section>
