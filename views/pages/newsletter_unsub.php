<section class="hero"><div class="wrap hero-in">
  <h1>Cancelar inscrição</h1>
  <p class="lead">Confirme para parar de receber a newsletter da Mídia Server.</p>
  <form method="post" action="/newsletter/sair" class="hero-cta"><input type="hidden" name="t" value="<?= e($token) ?>"><button class="btn btn-primary btn-lg" type="submit">Sim, cancelar inscrição</button><a class="btn btn-ghost-dark btn-lg" href="/">Manter inscrição</a></form>
</div></section>
