<?php
require BASE_PATH . '/views/partials/hero.php';
?>
<section class="sec">
  <div class="wrap wrap-narrow">
    <form class="search" action="/busca" method="get" role="search">
      <label class="sr" for="q">Buscar</label>
      <input id="q" name="q" type="search" value="<?= e($q) ?>" minlength="2" maxlength="80" placeholder="Digite o que procura" autocomplete="off">
      <button class="btn btn-primary" type="submit">Buscar</button>
    </form>
    <?php $any = false; foreach ($results as $group => $rows): if (!$rows) { continue; } $any = true; ?>
    <h2 class="sm"><?= e($group) ?></h2>
    <ul class="results">
      <?php foreach ($rows as $r): ?><li><a href="<?= e($r['url']) ?>"><strong><?= e($r['title']) ?></strong></a><span><?= e($r['text']) ?></span></li><?php endforeach; ?>
    </ul>
    <?php endforeach; ?>
    <?php if ($q !== '' && !$any): ?><p>Nada encontrado para “<?= e($q) ?>”. Tente outra palavra ou <a href="/contato">fale com a gente</a>.</p><?php endif; ?>
  </div>
</section>
