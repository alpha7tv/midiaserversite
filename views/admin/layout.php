<?php
use App\Admin\Session;

$u = Session::user();
$cur = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '');
$nav = [
    ['/admin', 'Visão geral'],
    ['/admin/leads', 'Leads'],
    ['/admin/planos', 'Planos e preços'],
    ['/admin/configuracoes', 'Configurações'],
    ['/admin/downloads', 'Downloads'],
    ['/admin/versoes', 'Versões do Studio'],
    ['/admin/publicar', 'Publicar novidade'],
    ['/admin/newsletter', 'Newsletter'],
    ['/admin/redirects', 'Redirects 301'],
    ['/admin/logs', 'Logs e auditoria'],
    ['/admin/conta', 'Minha conta'],
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title ?? 'Painel') ?> · Painel Mídia Server</title>
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('/assets/admin/admin.css') ?>">
</head>
<body>
<?php if ($u): ?>
<header class="a-top">
  <button class="a-burger" type="button" aria-label="Menu" data-toggle="menu">☰</button>
  <a class="a-brand" href="/admin"><img src="/assets/img/logo-horizontal-dark.svg" alt="Mídia Server" height="30"> <span>Painel</span></a>
  <span class="a-user"><?= e((string) $u['name']) ?>
    <form method="post" action="/admin/sair" class="inline"><?= csrf_field() ?><button class="a-link" type="submit">Sair</button></form>
  </span>
</header>
<div class="a-wrap">
  <nav class="a-nav" id="a-nav" aria-label="Painel">
    <?php foreach ($nav as [$href, $label]): $active = $href === '/admin' ? $cur === '/admin' : str_starts_with($cur, $href); ?>
    <a href="<?= e($href) ?>" <?= $active ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
    <a href="/" target="_blank" rel="noopener" class="a-ext">Ver o site ↗</a>
  </nav>
  <main class="a-main">
    <?php foreach (Session::takeFlash() as [$t, $m]): ?><div class="a-flash a-<?= e($t) ?>" role="status"><?= e($m) ?></div><?php endforeach; ?>
    <?= $content ?>
  </main>
</div>
<?php else: ?>
<main class="a-login-wrap"><?= $content ?></main>
<?php endif; ?>
<script src="<?= asset('/assets/admin/admin.js') ?>" defer></script>
</body>
</html>
