<?php
use App\Core\Settings;
use App\Core\Seo;
use App\Core\Whmcs;

$gtm = Settings::get('gtm_id');
$wa = Settings::whatsapp((string) ($page['wa'] ?? 'Olá, vim pelo site da Mídia Server.'));
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0b1424">
<?= Seo::head($page) ?>

<link rel="icon" href="<?= asset('/assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="preload" href="/assets/fonts/sora-700.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= asset('/assets/css/app.css') ?>">
<script>window.dataLayer=window.dataLayer||[];window.MS={gtm:<?= json_encode($gtm) ?>,page:<?= json_encode((string) ($page['path'] ?? '/')) ?>};</script>
</head>
<body class="lp">
<?php require BASE_PATH . '/views/partials/icons.php'; ?>
<header class="hdr hdr-lp">
  <div class="wrap hdr-in">
    <a class="brand" href="/" aria-label="Mídia Server"><img src="/assets/img/logo-horizontal.svg" width="188" height="48" alt="Mídia Server" class="brand-light"><img src="/assets/img/logo-horizontal-dark.svg" width="188" height="48" alt="" class="brand-dark" aria-hidden="true"></a>
    <a class="btn btn-wa btn-sm" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="lp-topo"><?= icon('whatsapp', 18) ?> <span>Falar no WhatsApp</span></a>
  </div>
</header>
<main id="conteudo"><?= $content ?></main>
<footer class="ftr ftr-lp"><div class="wrap ftr-bottom"><span>© <?= date('Y') ?> Mídia Server</span><span class="ftr-legal"><a href="/politica-de-privacidade">Privacidade</a><a href="/termos-de-uso">Termos de uso</a></span></div></footer>
<a class="wa-float" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="flutuante" aria-label="Falar no WhatsApp"><?= icon('whatsapp', 28) ?></a>
<div class="consent" id="consent" hidden role="dialog" aria-label="Cookies">
  <p>Usamos cookies para medir o desempenho do site e de anúncios. <a href="/politica-de-cookies">Saiba mais</a>.</p>
  <div class="consent-actions"><button type="button" class="btn btn-ghost btn-sm" data-consent="no">Recusar</button><button type="button" class="btn btn-primary btn-sm" data-consent="yes">Aceitar</button></div>
</div>
<script src="<?= asset('/assets/js/app.js') ?>" defer></script>
</body>
</html>
