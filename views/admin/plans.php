<h1>Planos e preços</h1>
<p class="muted">Os botões <strong>Contratar</strong> usam o <strong>ID do WHMCS (pid)</strong> de cada plano. O ID não é editável aqui, para ninguém apontar o botão para o produto errado. Os preços exibidos no site vêm desta tela.</p>
<p><a class="btn" href="/admin/planos/whmcs">Conferir preços com o WHMCS</a></p>
<?php foreach ($groups as $product => $plans): ?>
<h2><?= e($product) ?></h2>
<div class="tw"><table>
  <thead><tr><th>pid</th><th>Nome</th><th>Preço/mês (R$)</th><th>Destaque</th><th>Ativo</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($plans as $p): $fid = 'f' . (int) $p['id']; ?>
  <tr id="p<?= (int) $p['id'] ?>">
    <td><a href="<?= e(\App\Core\Whmcs::cart((int) $p['whmcs_pid'])) ?>" target="_blank" rel="noopener"><?= (int) $p['whmcs_pid'] ?></a></td>
    <td><input form="<?= $fid ?>" type="text" name="name" value="<?= e((string) $p['name']) ?>" maxlength="120" required></td>
    <td style="width:130px"><input form="<?= $fid ?>" type="text" inputmode="decimal" name="price" value="<?= e(number_format((float) $p['price_month'], 2, ',', '')) ?>" required></td>
    <td><input form="<?= $fid ?>" type="checkbox" name="highlight" value="1" <?= $p['highlight'] ? 'checked' : '' ?>></td>
    <td><input form="<?= $fid ?>" type="checkbox" name="active" value="1" <?= $p['active'] ? 'checked' : '' ?>></td>
    <td><button form="<?= $fid ?>" class="btn sm" type="submit">Salvar</button></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php foreach ($plans as $p): ?><form id="f<?= (int) $p['id'] ?>" method="post" action="/admin/planos"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"></form><?php endforeach; ?>
<?php endforeach; ?>
