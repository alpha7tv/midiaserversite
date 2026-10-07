<h1>Minha conta</h1>
<form class="card" method="post" action="/admin/conta" style="max-width:460px" autocomplete="off">
  <?= csrf_field() ?>
  <p class="muted"><?= e((string) \App\Admin\Session::user()['email']) ?></p>
  <label for="current">Senha atual</label><input id="current" name="current" type="password" required autocomplete="current-password">
  <label for="new">Nova senha<span class="hint">Mínimo 10 caracteres, com letras e números.</span></label><input id="new" name="new" type="password" required minlength="10" autocomplete="new-password">
  <label for="confirm">Confirmar nova senha</label><input id="confirm" name="confirm" type="password" required minlength="10" autocomplete="new-password">
  <p style="margin-top:14px"><button class="btn" type="submit">Alterar senha</button></p>
</form>
<p class="muted small">Novos administradores são criados na VPS: <code>read -rs P; printf '%s' "$P" | php bin/console admin:create "Nome" email@dominio.com</code></p>
