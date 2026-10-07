<?php
use App\Core\Settings;
use App\Core\Whmcs;

$ctaHref = '#modelos';
$pid = !empty($plans) ? (int) $plans[0]['whmcs_pid'] : 19;
$price = !empty($plans) ? (float) $plans[0]['price_month'] : 0.0;
require BASE_PATH . '/views/partials/hero.php';

$items = $page['benefits'];
$title = 'Um site completo para a sua rádio';
require BASE_PATH . '/views/partials/benefits.php';
$demo = Settings::get('demo_pattern');
?>
<section class="sec sec-alt" id="modelos">
  <div class="wrap">
    <h2 class="sec-title">Escolha o modelo e veja a demonstração</h2>
    <p class="sec-sub">Abra o site de exemplo funcionando. Depois de contratar, você troca as cores, as fotos e os textos pelos da sua rádio.</p>
    <div class="models">
      <?php foreach ($models as $m): $url = str_replace('{n}', (string) $m['n'], $demo); ?>
      <article class="model" style="--c:<?= e($m['cor']) ?>">
        <div class="model-img">
          <img src="/assets/img/modelos/<?= (int) $m['n'] ?>.webp" width="640" height="480" loading="lazy" decoding="async" alt="Modelo de site para rádio <?= e($m['nome']) ?>, estilo <?= e($m['estilo']) ?>">
          <span class="model-n">Modelo <?= (int) $m['n'] ?></span>
        </div>
        <div class="model-body">
          <h3><?= e($m['nome']) ?></h3>
          <p><?= e($m['estilo']) ?></p>
          <div class="model-cta">
            <?php if ($demo !== ''): ?><a class="btn btn-ghost btn-sm" href="<?= e($url) ?>" target="_blank" rel="noopener" data-ev="view_demo" data-model="<?= e($m['nome']) ?>">Ver demonstração</a><?php endif; ?>
            <a class="btn btn-primary btn-sm" href="<?= e(Whmcs::cart($pid)) ?>" rel="nofollow" data-ev="begin_checkout" data-pid="<?= $pid ?>" data-plan="Site Administrável — <?= e($m['nome']) ?>" data-price="<?= e((string) $price) ?>">Contratar</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="sec sec-tight">
  <div class="wrap">
    <div class="promo promo-static">
      <span class="promo-ic"><?= icon('wave', 34) ?></span>
      <span class="promo-txt"><strong>Grátis nos planos de streaming</strong><span>Todos os planos de streaming da Mídia Server incluem o site administrável sem custo adicional.</span></span>
      <a class="promo-go" href="/streaming" data-ev="cta_click" data-where="site-streaming">Ver planos de streaming <?= icon('arrow', 20) ?></a>
    </div>
  </div>
</section>
<?php
$plansTitle = 'Aluguel do site';
$plansSub = 'Para quem ainda não tem streaming da Mídia Server.';
require BASE_PATH . '/views/partials/plans.php';

$steps = [
    ['Escolha o modelo', 'Veja as demonstrações e escolha o visual que combina com a sua rádio.'],
    ['Contrate', 'Pague pela Área do Cliente, ou use o site grátis se a sua rádio tem streaming da Mídia Server.'],
    ['Receba o acesso', 'Assim que o pagamento é confirmado, o site é criado e você recebe o endereço e a senha do painel.'],
    ['Personalize', 'Coloque sua logo, suas fotos e seus textos. Depois é só divulgar.'],
];
$stepsTitle = 'Seu site no ar em poucos passos';
require BASE_PATH . '/views/partials/steps.php';
$faq = $page['faq'];
require BASE_PATH . '/views/partials/faq.php';
$bandTitle = 'O site da sua rádio, pronto e no ar';
$bandText = 'Escolha um modelo ou fale com a gente para tirar dúvidas.';
$bandHref = '#modelos';
$bandLabel = 'Ver modelos';
require BASE_PATH . '/views/partials/cta-band.php';
$related = $page['related'];
require BASE_PATH . '/views/partials/related.php';
