<?php
use App\Core\Settings;

$ctaHref = Settings::whatsapp((string) $page['wa']);
require BASE_PATH . '/views/partials/hero.php';
?>
<section class="sec">
  <div class="wrap wrap-narrow">
    <div class="card soon">
      <h2>O que estamos preparando</h2>
      <p>Servidores virtuais Linux para sites, APIs e automações. Quando os planos forem publicados, eles aparecerão aqui, com preços e recursos reais e contratação pela Área do Cliente.</p>
      <p>Enquanto isso, a <a href="/hospedagem">hospedagem cPanel</a> atende sites e lojas, e você pode falar com a gente para entender qual solução cabe no seu projeto.</p>
    </div>
  </div>
</section>
<?php
$related = $page['related'] ?? [];
require BASE_PATH . '/views/partials/related.php';
