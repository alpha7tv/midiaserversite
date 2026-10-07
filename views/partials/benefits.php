<?php /** @var list<array{0:string,1:string,2:string}> $items */ ?>
<section class="sec">
  <div class="wrap">
    <?php if (!empty($title)): ?><h2 class="sec-title"><?= e($title) ?></h2><?php endif; ?>
    <?php if (!empty($sub)): ?><p class="sec-sub"><?= e($sub) ?></p><?php endif; ?>
    <div class="grid <?= count($items) === 4 ? 'grid-4' : 'grid-3' ?>">
      <?php foreach ($items as [$ic, $t, $d]): ?>
      <article class="card feat">
        <span class="feat-ic"><?= icon($ic, 26) ?></span>
        <h3><?= e($t) ?></h3>
        <p><?= e($d) ?></p>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
