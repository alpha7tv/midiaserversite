<h1>Versões do Mídia Rádio Studio</h1>
<p class="muted">A versão mais recente <strong>publicada</strong> aparece em <a href="/automacao-radio#versoes" target="_blank" rel="noopener">/automacao-radio</a> com novidades e correções. Nada é avisado a clientes sem você marcar.</p>
<div class="card">
  <strong>Webhook do software:</strong> <code>POST <?= e(app_url('/api/v1/studio/version')) ?></code>
  <p class="small muted" style="margin:6px 0 0">Cabeçalho <code>X-Signature: sha256=&lt;HMAC-SHA256 do corpo com o segredo&gt;</code>. Corpo JSON: <code>{"version":"1.4.2","released_at":"2026-10-08","notes":"…","fixes":"…","download_url":"https://…"}</code>. A versão chega como <strong>rascunho</strong> para você aprovar aqui. Segredo: <?= $secret ? '<span class="pill ok">configurado</span>' : '<span class="pill no">não configurado (defina em Configurações > Integrações)</span>' ?></p>
</div>
<p><a class="btn" href="/admin/versoes/editar">Nova versão</a></p>
<div class="tw"><table>
  <thead><tr><th>Versão</th><th>Data</th><th>Novidades</th><th>Status</th><th>Avisos</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $v): ?>
  <tr>
    <td><strong>v<?= e((string) $v['version']) ?></strong></td>
    <td><?= $v['released_at'] ? e(date('d/m/Y', strtotime((string) $v['released_at']))) : '—' ?></td>
    <td class="small"><?= e(mb_substr((string) $v['notes'], 0, 90)) ?></td>
    <td><?= $v['published'] ? '<span class="pill ok">publicada</span>' : '<span class="pill wn">rascunho</span>' ?></td>
    <td class="small"><?= $v['notify_newsletter'] ? 'newsletter ' : '' ?><?= $v['notify_push'] ? 'push' : '' ?><?= !$v['notify_newsletter'] && !$v['notify_push'] ? '—' : '' ?></td>
    <td><a class="btn sm sec" href="/admin/versoes/editar?id=<?= (int) $v['id'] ?>">Editar</a></td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="muted">Nenhuma versão cadastrada.</td></tr><?php endif; ?>
  </tbody>
</table></div>
