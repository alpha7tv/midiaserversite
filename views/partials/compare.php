<?php
/** @var list<array<string,mixed>> $plans */
use App\Core\Whmcs;
use App\Services\Catalog;

if (count($plans) < 2) { return; }
$labels = Catalog::specLabels($plans);
?>
<section class="sec" id="comparar">
  <div class="wrap">
    <h2 class="sec-title"><?= e($compareTitle ?? 'Compare os planos') ?></h2>
    <div class="table-wrap" tabindex="0" role="region" aria-label="Tabela comparativa de planos">
      <table class="cmp">
        <caption class="sr">Comparação entre os planos</caption>
        <thead>
          <tr>
            <th scope="col">Recursos</th>
            <?php foreach ($plans as $p): ?><th scope="col"><span class="cmp-name"><?= e($p['name']) ?></span><span class="cmp-price"><?= e(money((float) $p['price_month'])) ?>/mês</span></th><?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($labels as $l): ?>
          <tr>
            <th scope="row"><?= e($l) ?></th>
            <?php foreach ($plans as $p): ?><td><?= spec_cell($p['specs'][$l] ?? false) ?></td><?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th scope="row"></th>
            <?php foreach ($plans as $p): ?><td><a class="btn btn-primary btn-sm" href="<?= e(Whmcs::cart((int) $p['whmcs_pid'])) ?>" rel="nofollow" data-ev="begin_checkout" data-pid="<?= (int) $p['whmcs_pid'] ?>" data-plan="<?= e($p['name']) ?>" data-price="<?= e((string) $p['price_month']) ?>">Contratar</a></td><?php endforeach; ?>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</section>
