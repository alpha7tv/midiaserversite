<h1>Conferir com o WHMCS</h1>
<p class="muted">Lê a vitrine pública do WHMCS e compara com o que o site mostra. <strong>Nada é alterado sem você clicar em Aplicar.</strong></p>
<?php if (!$remote_ok): ?><div class="a-flash a-err">Não consegui ler a vitrine do WHMCS agora. Confira o endereço em Configurações e tente de novo.</div><?php endif; ?>
<div class="tw"><table>
  <thead><tr><th>pid</th><th>Plano no site</th><th>Preço no site</th><th>No WHMCS</th><th>Situação</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): $pl = $r['plan']; $rm = $r['remote']; $st = $r['status']; ?>
  <tr>
    <td><?= (int) $pl['whmcs_pid'] ?></td>
    <td><?= e((string) $pl['name']) ?></td>
    <td><?= e(money((float) $pl['price_month'])) ?></td>
    <td><?= $rm ? e(($rm['name'] ?: '—') . ' · ' . ($rm['price'] !== null ? money((float) $rm['price']) : 's/ preço') . ($rm['cycle'] !== '' ? ' ' . $rm['cycle'] : '')) : '—' ?></td>
    <td><?php if ($st === 'ok'): ?><span class="pill ok">igual</span><?php elseif ($st === 'preco'): ?><span class="pill no">preço diferente</span><?php elseif ($st === 'nome'): ?><span class="pill wn">nome diferente</span><?php elseif ($st === 'ausente'): ?><span class="pill wn">não encontrado na vitrine</span><?php else: ?><span class="pill wn">sem preço</span><?php endif; ?></td>
    <td><?php if (in_array($st, ['preco', 'nome'], true)): ?>
      <form method="post" action="/admin/planos/aplicar" data-confirm="Aplicar o preço e o nome do WHMCS ao plano <?= (int) $pl['whmcs_pid'] ?>?"><?= csrf_field() ?><input type="hidden" name="pid" value="<?= (int) $pl['whmcs_pid'] ?>"><button class="btn sm" type="submit">Aplicar do WHMCS</button></form>
    <?php endif; ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php if ($missing_local): ?>
<h2>Produtos no WHMCS que não estão no site</h2>
<div class="tw"><table><thead><tr><th>pid</th><th>Nome</th><th>Preço</th><th>Grupo</th></tr></thead><tbody>
<?php foreach ($missing_local as $m): ?><tr><td><?= (int) $m['pid'] ?></td><td><?= e($m['name']) ?></td><td><?= $m['price'] !== null ? e(money((float) $m['price'])) : '—' ?></td><td><?= e($m['group']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<p class="muted small">Para exibir um produto novo, é preciso incluí-lo no catálogo com seus recursos (peça na próxima atualização do portal).</p>
<?php endif; ?>
<p style="margin-top:14px"><a class="btn sec" href="/admin/planos">Voltar aos planos</a></p>
