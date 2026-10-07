<?php
use App\Core\Settings;

$ctaHref = Settings::get('radios_url');
require BASE_PATH . '/views/partials/hero.php';
$dir = rtrim(Settings::get('radios_url'), '/');
?>
<section class="sec">
  <div class="wrap">
    <div class="grid grid-3">
      <a class="card path" href="<?= e($dir) ?>/" target="_blank" rel="noopener" data-ev="cta_click" data-where="radios-ouvir">
        <span class="feat-ic"><?= icon('play', 26) ?></span><h3>Ouvir rádios</h3>
        <p>Pesquise por nome, estado, cidade ou segmento e ouça ao vivo, com player moderno e página de cada emissora.</p>
        <span class="more">Abrir o diretório <?= icon('arrow', 18) ?></span>
      </a>
      <a class="card path" href="<?= e($dir) ?>/radios/mais-tocadas" target="_blank" rel="noopener" data-ev="cta_click" data-where="radios-mais-tocadas">
        <span class="feat-ic"><?= icon('chart', 26) ?></span><h3>Mais tocadas</h3>
        <p>O ranking usa apenas audições reais e não pode ser comprado.</p>
        <span class="more">Ver ranking <?= icon('arrow', 18) ?></span>
      </a>
      <a class="card path" href="/radios/cadastrar-radio" data-ev="cta_click" data-where="radios-cadastrar">
        <span class="feat-ic"><?= icon('radio', 26) ?></span><h3>Cadastrar minha rádio</h3>
        <p>Cadastro gratuito, com página própria, player ao vivo, logo, descrição e redes sociais.</p>
        <span class="more">Cadastrar grátis <?= icon('arrow', 18) ?></span>
      </a>
    </div>
  </div>
</section>

<section class="sec sec-alt" id="destaques">
  <div class="wrap">
    <h2 class="sec-title">Destaque a sua rádio</h2>
    <p class="sec-sub">Além do cadastro gratuito, existem posições de destaque no diretório. Posições contratadas são sempre identificadas como PATROCINADO, e o ranking de mais tocadas não pode ser comprado.</p>
    <p class="center"><a class="btn btn-primary" href="<?= e($dir) ?>/promover-radio" target="_blank" rel="noopener" data-ev="cta_click" data-where="radios-destaque">Conhecer as opções de destaque <?= icon('external', 18) ?></a></p>
  </div>
</section>
<?php
$faq = $page['faq'];
require BASE_PATH . '/views/partials/faq.php';
$related = $page['related'];
require BASE_PATH . '/views/partials/related.php';
