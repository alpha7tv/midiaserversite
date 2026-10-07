<?php require BASE_PATH . '/views/partials/hero.php'; ?>
<article class="sec">
  <div class="wrap wrap-narrow prose-page">
    <p class="muted"><?= e((string) ($post['cat'] ?? 'Novidades')) ?> · <?= e(date('d/m/Y', strtotime((string) $post['published_at']))) ?></p>
    <?php if (!empty($post['excerpt'])): ?><p class="lead-ex"><?= e((string) $post['excerpt']) ?></p><?php endif; ?>
    <?= $html ?>
    <p style="margin-top:28px"><a class="btn btn-ghost" href="/blog">← Voltar ao blog</a></p>
  </div>
</article>
<?php
$bandTitle = 'Gostou? Fale com a gente';
$bandText = 'Tire dúvidas sobre hospedagem, streaming, automação e conteúdos para rádios.';
$bandHref = '/contato';
$bandLabel = 'Falar com a equipe';
require BASE_PATH . '/views/partials/cta-band.php';
