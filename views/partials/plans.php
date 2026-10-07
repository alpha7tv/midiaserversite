<?php
/** @var list<array<string,mixed>> $plans */
use App\Core\Whmcs;

$limit = $limit ?? 9;
?>
<section class="sec sec-alt" id="planos">
  <div class="wrap">
    <h2 class="sec-title"><?= e($plansTitle ?? 'Escolha o seu plano') ?></h2>
    <?php if (!empty($plansSub)): ?><p class="sec-sub"><?= e($plansSub) ?></p><?php endif; ?>
    <?php if (!$plans): ?>
      <p class="sec-sub">Os planos estão sendo atualizados. Fale com a gente pelo WhatsApp.</p>
    <?php else: ?>
    <div class="plans plans-<?= count($plans) ?>">
      <?php foreach ($plans as $p):
          $shown = 0; $total = 0; $items = [];
          foreach ($p['specs'] as $label => $v) {
              if ($v === false) { continue; }
              $total++;
              if ($shown < $limit) { $items[] = [$label, $v]; $shown++; }
          }
          $more = $total - $shown;
      ?>
      <article class="plan<?= !empty($p['highlight']) ? ' plan-hl' : '' ?>">
        <h3><?= e($p['name']) ?></h3>
        <p class="price"><?= e(money((float) $p['price_month'])) ?><span>/mês</span></p>
        <?php if (!empty($p['includes_site'])): ?><p class="plan-tag"><?= icon('globe', 16) ?> Site administrável incluso</p><?php endif; ?>
        <ul class="plan-list">
          <?php foreach ($items as [$label, $v]): ?>
          <li><?= icon('check', 18, 'i ok') ?><span><?php if ($v === true): ?><?= e($label) ?><?php else: ?><?= e($label) ?>: <strong><?= e((string) $v) ?></strong><?php endif; ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php if ($more > 0): ?><p class="plan-more"><a href="#comparar">+ <?= $more ?> recursos na tabela</a></p><?php endif; ?>
        <a class="btn btn-primary btn-block" href="<?= e(Whmcs::cart((int) $p['whmcs_pid'])) ?>" rel="nofollow"
           data-ev="begin_checkout" data-pid="<?= (int) $p['whmcs_pid'] ?>" data-plan="<?= e($p['name']) ?>" data-price="<?= e((string) $p['price_month']) ?>">Contratar</a>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
