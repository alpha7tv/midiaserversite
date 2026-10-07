<?php /** @var list<array{0:string,1:string}> $faq */ if (empty($faq)) { return; } ?>
<section class="sec sec-alt" id="duvidas">
  <div class="wrap wrap-narrow">
    <h2 class="sec-title">Perguntas frequentes</h2>
    <div class="faq">
      <?php foreach ($faq as [$q, $a]): ?>
      <details>
        <summary><span><?= e($q) ?></span><?= icon('chev', 20, 'i chev') ?></summary>
        <p><?= e($a) ?></p>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
