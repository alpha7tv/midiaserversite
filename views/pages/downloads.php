<?php
use App\Core\Settings;

require BASE_PATH . '/views/partials/hero.php';
$own = null;
foreach (($groups['dl-automacao']['items'] ?? []) as $i) { if ($i['slug'] === 'midia-radio-studio') { $own = $i; } }
?>
<?php if ($own): ?>
<section class="sec sec-tight" id="midia-radio-studio">
  <div class="wrap">
    <div class="card dl-feature">
      <div>
        <p class="badge">Software da Mídia Server</p>
        <h2><?= e($own['name']) ?></h2>
        <p><?= e((string) $own['description']) ?></p>
        <p class="meta"><?= e((string) $own['os']) ?><?php if ($own['size_bytes']): ?> · <?= e(number_format($own['size_bytes'] / 1048576, 0, ',', '.')) ?> MB<?php endif; ?><?php if ($own['released_at']): ?> · atualizado em <?= e(date('d/m/Y', strtotime((string) $own['released_at']))) ?><?php endif; ?></p>
      </div>
      <div class="dl-actions">
        <a class="btn btn-primary btn-lg" href="/baixar/<?= e($own['slug']) ?>" data-ev="file_download" data-file="<?= e($own['name']) ?>"><?= icon('download', 20) ?> Baixar</a>
        <a class="btn btn-ghost" href="/automacao-radio">Ver recursos e planos</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php foreach ($groups as $slug => $g): ?>
<section class="sec <?= $slug === 'dl-encoders' ? 'sec-alt' : '' ?>" id="<?= e($slug) ?>">
  <div class="wrap">
    <h2 class="sec-title left"><?= e($g['name']) ?></h2>
    <?php if ($slug === 'dl-automacao'): ?><p class="sec-sub left">Programas que tocam a sua programação sozinhos: músicas, vinhetas, comerciais e hora certa.</p>
    <?php elseif ($slug === 'dl-encoders'): ?><p class="sec-sub left">Leve o som da mesa ou do microfone para o seu streaming (Shoutcast e Icecast).</p>
    <?php else: ?><p class="sec-sub left">Ferramentas para enviar, editar e organizar os áudios da rádio.</p><?php endif; ?>
    <div class="grid grid-3">
      <?php foreach ($g['items'] as $d): if ($d['slug'] === 'midia-radio-studio') { continue; } ?>
      <article class="card dl" id="<?= e($d['slug']) ?>">
        <h3><?= e($d['name']) ?></h3>
        <p class="meta"><?= e((string) $d['os']) ?></p>
        <p><?= e((string) $d['description']) ?></p>
        <p class="lic"><?= e((string) $d['license_note']) ?></p>
        <a class="btn btn-ghost btn-sm" href="<?= e((string) $d['file_url']) ?>" target="_blank" rel="noopener nofollow" data-ev="file_download" data-file="<?= e($d['name']) ?>">Baixar no site oficial <?= icon('external', 16) ?></a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>

<section class="sec sec-tight">
  <div class="wrap wrap-narrow">
    <p class="muted center">Os programas de terceiros pertencem aos seus desenvolvedores. Os links levam aos sites oficiais; confira os requisitos e a licença de cada um antes de instalar. A Mídia Server não distribui esses arquivos.</p>
  </div>
</section>
<?php
$bandTitle = 'Streaming para a sua rádio';
$bandText = 'Áudio de até 320 kbps, ouvintes ilimitados, AutoDJ e site incluso em todos os planos.';
$bandHref = '/streaming';
$bandLabel = 'Ver planos de streaming';
require BASE_PATH . '/views/partials/cta-band.php';
