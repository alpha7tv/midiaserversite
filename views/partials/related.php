<?php
/** @var list<string> $related */
$all = require BASE_PATH . '/config/pages.php';
if (empty($related)) { return; }
?>
<section class="sec sec-tight">
  <div class="wrap">
    <h2 class="sec-title sm">Veja também</h2>
    <div class="related">
      <?php foreach ($related as $r): if (!isset($all[$r])) { continue; } ?>
      <a class="card rel" href="<?= e($r) ?>"><strong><?= e((string) ($all[$r]['crumb'] ?? $all[$r]['h1'])) ?></strong><span><?= icon('arrow', 20) ?></span></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
