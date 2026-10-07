<?php /** @var list<array{0:string,1:string}> $steps */ ?>
<section class="sec" id="<?= e($stepsId ?? 'como-contratar') ?>">
  <div class="wrap">
    <h2 class="sec-title"><?= e($stepsTitle ?? 'Como contratar') ?></h2>
    <ol class="steps">
      <?php foreach ($steps as $i => [$t, $d]): ?>
      <li><span class="step-n"><?= $i + 1 ?></span><h3><?= e($t) ?></h3><p><?= e($d) ?></p></li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
