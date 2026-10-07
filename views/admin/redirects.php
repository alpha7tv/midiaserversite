<h1>Redirects 301</h1>
<p class="muted">Use quando uma página antiga foi movida, para preservar a posição no Google. Nunca redirecione tudo para a Home: aponte cada URL para a página equivalente.</p>
<form class="card row" method="post" action="/admin/redirects" style="align-items:flex-end">
  <?= csrf_field() ?>
  <div style="flex:1;min-width:200px"><label for="from_path" style="margin-top:0">De (caminho antigo)</label><input id="from_path" name="from_path" type="text" required placeholder="/pagina-antiga"></div>
  <div style="flex:1;min-width:200px"><label for="to_path" style="margin-top:0">Para</label><input id="to_path" name="to_path" type="text" required placeholder="/streaming"></div>
  <div><label for="code" style="margin-top:0">Tipo</label><select id="code" name="code"><option value="301">301 permanente</option><option value="302">302 temporário</option></select></div>
  <button class="btn" type="submit">Criar</button>
</form>
<div class="tw"><table>
  <thead><tr><th>De</th><th>Para</th><th>Tipo</th><th>Acessos</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
  <tr><td><code><?= e((string) $r['from_path']) ?></code></td><td><code><?= e((string) $r['to_path']) ?></code></td><td><?= (int) $r['code'] ?></td><td><?= (int) $r['hits'] ?></td>
  <td><form method="post" action="/admin/redirects/excluir" data-confirm="Excluir este redirect?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn sm dan" type="submit">Excluir</button></form></td></tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="5" class="muted">Nenhum redirect cadastrado. Os redirects legados do site antigo já estão ativos no código.</td></tr><?php endif; ?>
  </tbody>
</table></div>
