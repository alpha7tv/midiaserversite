<h1>Logs e auditoria</h1>
<h2>Ações no painel (últimas 100)</h2>
<div class="tw"><table>
  <thead><tr><th>Quando</th><th>Quem</th><th>Ação</th><th>Item</th><th>Detalhes</th></tr></thead>
  <tbody>
  <?php foreach ($audit as $a): ?><tr><td class="small"><?= e(date('d/m H:i', strtotime((string) $a['created_at']))) ?></td><td><?= e((string) ($a['name'] ?? '—')) ?></td><td><?= e((string) $a['action']) ?></td><td class="small"><?= e((string) $a['entity']) ?></td><td class="small"><code><?= e(mb_substr((string) $a['payload'], 0, 160)) ?></code></td></tr><?php endforeach; ?>
  <?php if (!$audit): ?><tr><td colspan="5" class="muted">Sem registros.</td></tr><?php endif; ?>
  </tbody>
</table></div>
<h2>Integrações (webhook do Studio)</h2>
<div class="tw"><table><thead><tr><th>Quando</th><th>Origem</th><th>Nível</th><th>Mensagem</th></tr></thead><tbody>
  <?php foreach ($integ as $i): ?><tr><td class="small"><?= e(date('d/m H:i', strtotime((string) $i['created_at']))) ?></td><td><?= e((string) $i['source']) ?></td><td><?= e((string) $i['level']) ?></td><td><?= e((string) $i['message']) ?> <code class="small"><?= e((string) $i['payload']) ?></code></td></tr><?php endforeach; ?>
  <?php if (!$integ): ?><tr><td colspan="4" class="muted">Sem registros.</td></tr><?php endif; ?>
</tbody></table></div>
<h2>Arquivos de log</h2>
<p class="row"><?php foreach ($files as $k => $label): ?><a class="btn sm <?= $k === $key ? '' : 'sec' ?>" href="/admin/logs?arquivo=<?= e($k) ?>"><?= e($label) ?></a><?php endforeach; ?></p>
<pre class="card" style="overflow:auto;max-height:420px;font-size:12.5px;white-space:pre-wrap"><?= $tail !== '' ? e($tail) : 'Arquivo vazio ou inexistente.' ?></pre>
