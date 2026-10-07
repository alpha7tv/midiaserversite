<?php
use App\Core\Settings;
use App\Core\Whmcs;

$menu = require BASE_PATH . '/config/menu.php';
$cur = (string) ($page['path'] ?? '/');
$wa = Settings::whatsapp('Olá, vim pelo site da Mídia Server e gostaria de conhecer melhor os serviços.');
?>
<div class="topbar">
  <div class="wrap topbar-in">
    <span class="topbar-msg">Atendimento por WhatsApp e tickets</span>
    <span class="topbar-links">
      <a href="mailto:<?= e(Settings::get('support_email')) ?>"><?= icon('mail', 16) ?> <?= e(Settings::get('support_email')) ?></a>
      <a href="<?= e(Whmcs::register()) ?>" data-ev="click_cadastro">Criar conta</a>
    </span>
  </div>
</div>
<header class="hdr" id="top">
  <div class="wrap hdr-in">
    <a class="brand" href="/" aria-label="Mídia Server — página inicial">
      <img src="/assets/img/logo-horizontal.svg" width="188" height="48" alt="Mídia Server" class="brand-light">
      <img src="/assets/img/logo-horizontal-dark.svg" width="188" height="48" alt="" class="brand-dark" aria-hidden="true">
    </a>
    <nav class="nav" id="nav" aria-label="Principal">
      <ul class="nav-list">
        <li class="nav-home"><a href="/" <?= $cur === '/' ? 'aria-current="page"' : '' ?>>Home</a></li>
        <?php foreach ($menu as $g): ?>
        <li class="has-sub">
          <button type="button" class="nav-btn" aria-expanded="false"><?= e($g['label']) ?> <?= icon('chev', 16, 'i chev') ?></button>
          <ul class="sub">
            <?php foreach ($g['items'] as [$label, $href]): $h = menu_href($href); ?>
            <li><a href="<?= e($h) ?>"><?= e($label) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <?php endforeach; ?>
      </ul>
      <div class="nav-cta">
        <a class="btn btn-ghost btn-sm" href="<?= e(Whmcs::login()) ?>" data-ev="click_area_cliente">Área do Cliente</a>
        <a class="btn btn-wa btn-sm" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="menu"><?= icon('whatsapp', 18) ?> <span>WhatsApp</span></a>
      </div>
    </nav>
    <div class="hdr-actions">
      <a class="icon-btn" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="menu-mobile" aria-label="WhatsApp"><?= icon('whatsapp', 22) ?></a>
      <button type="button" class="icon-btn burger" id="burger" aria-controls="nav" aria-expanded="false" aria-label="Abrir menu"><?= icon('menu', 24) ?></button>
    </div>
  </div>
</header>
