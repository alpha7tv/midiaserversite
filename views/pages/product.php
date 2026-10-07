<?php
use App\Core\Settings;

require BASE_PATH . '/views/partials/hero.php';

// bloco "o que vem junto" (Web Rádio Completa)
if (!empty($page['solution'])): ?>
<section class="sec sec-tight">
  <div class="wrap">
    <h2 class="sec-title">Tudo que a sua rádio recebe</h2>
    <div class="solution">
      <?php foreach ($page['solution'] as [$ic, $t, $d]): ?>
      <div class="sol-item"><span class="feat-ic"><?= icon($ic, 24) ?></span><div><strong><?= e($t) ?></strong><span><?= e($d) ?></span></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif;

$items = $page['benefits'];
$title = !empty($page['solution']) ? 'Por que começar por aqui' : 'O que você leva';
require BASE_PATH . '/views/partials/benefits.php';

if (($page['banner'] ?? '') === 'divulgue') {
    require BASE_PATH . '/views/partials/divulgue.php';
}

$plansTitle = 'Escolha o seu plano';
$plansSub = 'Preços mensais. Você finaliza o pedido na Área do Cliente.';
require BASE_PATH . '/views/partials/plans.php';
require BASE_PATH . '/views/partials/compare.php';

$steps = $page['steps'] ?? [
    ['Escolha o plano', 'Compare os planos desta página e veja o que cada um inclui.'],
    ['Clique em contratar', 'Você segue para o carrinho da Área do Cliente, com o plano já selecionado.'],
    ['Crie sua conta e pague', 'Cadastre-se na Área do Cliente e pague a fatura pelos meios disponíveis, como o Pix.'],
    ['Receba o acesso', 'Confirmado o pagamento, você recebe os dados do serviço e conta com o nosso suporte.'],
];
$stepsTitle = $page['steps_title'] ?? 'Como contratar';
require BASE_PATH . '/views/partials/steps.php';

$faq = $page['faq'] ?? [];
require BASE_PATH . '/views/partials/faq.php';

$bandTitle = (string) ($page['band_title'] ?? 'Pronto para começar?');
$bandText = 'Escolha o plano ideal ou fale com a nossa equipe pelo WhatsApp.';
$bandHref = '#planos';
$bandLabel = (string) ($page['cta'] ?? 'Ver planos');
require BASE_PATH . '/views/partials/cta-band.php';

$related = $page['related'] ?? [];
require BASE_PATH . '/views/partials/related.php';
