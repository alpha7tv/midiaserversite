<h1>Configurações</h1>
<p class="muted">Tudo o que o site usa, sem mexer em código. Campos em branco desligam o recurso. Segredos ficam criptografados no banco.</p>
<form method="post" action="/admin/configuracoes">
  <?= csrf_field() ?>
  <?php foreach ($schema as $group => $fields): ?>
  <section class="card">
    <h2 style="margin-top:0"><?= e($group) ?></h2>
    <?php foreach ($fields as [$key, $label, $type, $help]): $isSecret = $type === 'secret'; $val = $isSecret ? '' : \App\Core\Settings::get($key); ?>
    <label for="f_<?= e($key) ?>"><?= e($label) ?><?php if ($help): ?><span class="hint"><?= e($help) ?></span><?php endif; ?></label>
    <?php if (str_starts_with($type, 'select:')): ?>
      <select id="f_<?= e($key) ?>" name="<?= e($key) ?>">
        <?php foreach (explode(',', substr($type, 7)) as $o): [$v, $t] = explode('=', $o, 2); ?>
        <option value="<?= e($v) ?>" <?= $val === $v ? 'selected' : '' ?>><?= e($t) ?></option>
        <?php endforeach; ?>
      </select>
    <?php elseif ($isSecret): $has = !empty($stored[$key]); ?>
      <input id="f_<?= e($key) ?>" name="<?= e($key) ?>" type="password" autocomplete="new-password" placeholder="<?= $has ? '•••••••• (definido; digite para trocar)' : 'não definido' ?>">
      <?php if ($has): ?><label class="chk"><input type="checkbox" name="clear_<?= e($key) ?>" value="1"> Remover este segredo</label><?php endif; ?>
    <?php else: ?>
      <input id="f_<?= e($key) ?>" name="<?= e($key) ?>" type="<?= $type === 'email' ? 'email' : 'text' ?>" value="<?= e($val) ?>" autocomplete="off" maxlength="300">
    <?php endif; ?>
    <?php endforeach; ?>
  </section>
  <?php endforeach; ?>
  <div class="row"><button class="btn" type="submit">Salvar configurações</button></div>
</form>
<form method="post" action="/admin/configuracoes/teste-smtp" style="margin-top:14px">
  <?= csrf_field() ?>
  <button class="btn sec" type="submit">Enviar e-mail de teste para mim</button>
  <span class="muted small"> Salve as configurações antes de testar.</span>
</form>
