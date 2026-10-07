<form class="a-login" method="post" action="/admin/login" autocomplete="on">
  <img src="/assets/img/logo-horizontal.svg" alt="Mídia Server" height="38">
  <h1 style="text-align:center;font-size:1.2rem">Painel administrativo</h1>
  <?php if ($error): ?><div class="a-flash a-err" role="alert"><?= e($error) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <label for="email">E-mail</label>
  <input id="email" name="email" type="email" required autocomplete="username" value="<?= old('email') ?>">
  <label for="password">Senha</label>
  <input id="password" name="password" type="password" required autocomplete="current-password">
  <p style="margin-top:16px"><button class="btn" style="width:100%" type="submit">Entrar</button></p>
</form>
