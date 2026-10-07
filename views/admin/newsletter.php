<h1>Newsletter</h1>
<?php if (!$smtp): ?><div class="a-flash a-warn">SMTP não configurado: o formulário não aparece no site e nenhum e-mail é enviado. Configure em <a href="/admin/configuracoes">Configurações</a>.</div><?php endif; ?>
<div class="grid g4" style="margin-bottom:14px">
  <div class="card stat"><b><?= (int) $counts['confirmed'] ?></b><span>confirmados (recebem)</span></div>
  <div class="card stat"><b><?= (int) $counts['pending'] ?></b><span>aguardando confirmação</span></div>
  <div class="card stat"><b><?= (int) $counts['unsubscribed'] ?></b><span>cancelaram</span></div>
</div>
<p class="muted small">Só recebe quem clicou no link de confirmação do e-mail. Cada e-mail tem link de cancelamento.</p>
<h2>Campanhas</h2>
<p class="row"><a class="btn" href="/admin/newsletter/campanha">Nova campanha</a>
<form method="post" action="/admin/newsletter/lote" class="inline"><?= csrf_field() ?><button class="btn sec" type="submit">Enviar próximo lote (25)</button></form></p>
<div class="tw"><table>
  <thead><tr><th>Assunto</th><th>Origem</th><th>Status</th><th>Enviados</th><th>Falhas</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($campaigns as $c): $pill = ['draft' => ['wn', 'rascunho'], 'sending' => ['wn', 'enviando'], 'sent' => ['ok', 'enviada']][$c['status']] ?? ['', $c['status']]; ?>
  <tr><td><strong><?= e((string) $c['subject']) ?></strong></td><td class="small"><?= e((string) $c['source']) ?></td><td><span class="pill <?= $pill[0] ?>"><?= e($pill[1]) ?></span></td>
  <td><?= (int) $c['sent_count'] ?>/<?= (int) $c['total_count'] ?></td><td><?= (int) $c['failed_count'] ?></td>
  <td><a class="btn sm sec" href="/admin/newsletter/campanha?id=<?= (int) $c['id'] ?>"><?= $c['status'] === 'draft' ? 'Revisar e enviar' : 'Ver' ?></a></td></tr>
  <?php endforeach; ?>
  <?php if (!$campaigns): ?><tr><td colspan="6" class="muted">Nenhuma campanha.</td></tr><?php endif; ?>
  </tbody>
</table></div>
<h2>Inscritos</h2>
<form class="row" method="get" action="/admin/newsletter" style="margin-bottom:10px"><input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar e-mail" style="max-width:300px"><button class="btn sec" type="submit">Buscar</button><a class="btn sec" href="/admin/newsletter/exportar">Exportar CSV</a></form>
<div class="tw"><table>
  <thead><tr><th>E-mail</th><th>Status</th><th>Inscrito em</th><th>Confirmado em</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($subs as $s): ?>
  <tr><td><?= e((string) $s['email']) ?></td><td><?= $s['status'] === 'confirmed' ? '<span class="pill ok">confirmado</span>' : ($s['status'] === 'pending' ? '<span class="pill wn">pendente</span>' : '<span class="pill no">cancelou</span>') ?></td>
  <td class="small"><?= e(date('d/m/Y H:i', strtotime((string) $s['created_at']))) ?></td><td class="small"><?= $s['confirmed_at'] ? e(date('d/m/Y H:i', strtotime((string) $s['confirmed_at']))) : '—' ?></td>
  <td><form method="post" action="/admin/newsletter/remover" data-confirm="Remover definitivamente este inscrito?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $s['id'] ?>"><button class="btn sm dan" type="submit">Remover</button></form></td></tr>
  <?php endforeach; ?>
  <?php if (!$subs): ?><tr><td colspan="5" class="muted">Nenhum inscrito.</td></tr><?php endif; ?>
  </tbody>
</table></div>
