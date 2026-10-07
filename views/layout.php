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
<link rel="icon" href="<?= asset('/assets/img/favicon-32.png') ?>" sizes="32x32" type="image/png">
<link rel="apple-touch-icon" href="<?= asset('/assets/img/apple-touch-icon.png') ?>">
<link rel="manifest" href="/site.webmanifest">
<link rel="preload" href="/assets/fonts/sora-700.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/assets/fonts/inter-400.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= asset_min('/assets/css/app.css') ?>">
<script>window.dataLayer=window.dataLayer||[];window.MS=Object.assign(<?= json_encode(Settings::tracking(), JSON_UNESCAPED_SLASHES) ?>,{page:<?= json_encode((string) ($page['path'] ?? '/')) ?>});</script>
</head>
<body>
<a class="skip" href="#conteudo">Ir para o conteúdo</a>
<?php require BASE_PATH . '/views/partials/icons.php'; ?>
<?php require BASE_PATH . '/views/partials/header.php'; ?>
<main id="conteudo">
<?= $content ?>
</main>
<?php require BASE_PATH . '/views/partials/footer.php'; ?>

<a class="wa-float" href="<?= e($wa) ?>" target="_blank" rel="noopener" data-ev="whatsapp_click" data-where="flutuante" aria-label="Falar no WhatsApp"><?= icon('whatsapp', 28) ?></a>

<div class="consent" id="consent" hidden role="dialog" aria-label="Cookies">
  <p>Usamos cookies para medir o desempenho do site e de anúncios. Você pode aceitar ou recusar. <a href="/politica-de-cookies">Saiba mais</a>.</p>
  <div class="consent-actions"><button type="button" class="btn btn-ghost btn-sm" data-consent="no">Recusar</button><button type="button" class="btn btn-primary btn-sm" data-consent="yes">Aceitar</button></div>
</div>
<script src="<?= asset_min('/assets/js/app.js') ?>" defer></script>
</body>
</html>
