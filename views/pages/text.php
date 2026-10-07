<?php
require BASE_PATH . '/views/partials/hero.php';
$body = BASE_PATH . '/views/legal/' . preg_replace('/[^a-z]/', '', (string) $page['body']) . '.php';
?>
<section class="sec">
  <div class="wrap wrap-narrow prose-page">
    <?php if (is_file($body)) { require $body; } ?>
  </div>
</section>
