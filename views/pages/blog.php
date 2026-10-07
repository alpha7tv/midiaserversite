<?php
require BASE_PATH . '/views/partials/hero.php';
$cats = ['Hospedagem', 'Streaming', 'Web Rádio', 'Automação', 'Marketing para Rádio', 'Tutoriais', 'Tecnologia', 'Novidades Mídia Server'];
?>
<section class="sec">
  <div class="wrap">
    <ul class="chips"><?php foreach ($cats as $c): ?><li><?= e($c) ?></li><?php endforeach; ?></ul>
    <?php if (empty($posts)): ?>
      <p class="sec-sub">Os primeiros artigos estão a caminho. Enquanto isso, conheça <a href="/streaming">o streaming</a>, a <a href="/automacao-radio">automação de rádio</a> e os <a href="/conteudos-para-radio">conteúdos para rádios</a>.</p>
    <?php else: ?>
    <div class="grid grid-3">
      <?php foreach ($posts as $p): ?>
      <article class="card post"><h2><a href="/blog/<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h2><p><?= e((string) $p['excerpt']) ?></p></article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
