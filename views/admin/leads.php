<h1>Leads <span class="muted" style="font-size:1rem">(<?= (int) $total ?>)</span></h1>
<form class="row" method="get" action="/admin/leads" style="margin-bottom:12px">
  <select name="status" style="width:auto"><option value="">Todos os status</option><?php foreach ($status as $k => $v): ?><option value="<?= e($k) ?>" <?= $st === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select>
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar nome, WhatsApp ou interesse" style="max-width:320px">
  <button class="btn sec" type="submit">Filtrar</button>
  <a class="btn sec" href="/admin/leads/exportar">Exportar CSV</a>
</form>
<div class="tw"><table>
  <thead><tr><th>Quando</th><th>Nome</th><th>WhatsApp</th><th>Interesse</th><th>Origem</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): $a = $r['attr']; $first = explode(' ', (string) $r['name'])[0]; ?>
  <tr>
    <td><?= e(date('d/m/Y H:i', strtotime((string) $r['created_at']))) ?></td>
    <td><?= e((string) $r['name']) ?></td>
    <td><a href="https://wa.me/<?= e((string) $r['phone']) ?>?text=<?= rawurlencode('Olá ' . $first . ', aqui é da Mídia Server. Recebemos seu contato sobre ' . $r['interest'] . '.') ?>" target="_blank" rel="noopener"><?= e((string) $r['phone']) ?></a></td>
    <td><?= e((string) $r['interest']) ?><br><span class="muted small"><?= e((string) $r['page']) ?></span></td>
    <td class="small"><?= e(trim(($a['utm_source'] ?? '') . ' ' . ($a['utm_campaign'] ?? ''))) ?: '—' ?><?php if (!empty($a['utm_term'])): ?><br><span class="muted">termo: <?= e($a['utm_term']) ?></span><?php endif; ?><?php if (!empty($a['gclid'])): ?><br><span class="pill">gclid</span><?php endif; ?></td>
    <td>
      <form method="post" action="/admin/leads/status" class="row"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><input type="hidden" name="back" value="<?= e($st) ?>">
        <select name="status" style="width:auto"><?php foreach ($status as $k => $v): ?><option value="<?= e($k) ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select>
        <button class="btn sm sec" type="submit">OK</button></form>
    </td>
    <td><form method="post" action="/admin/leads/excluir" data-confirm="Excluir definitivamente este lead? Não dá para desfazer."><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn sm dan" type="submit">Excluir</button></form></td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="7" class="muted">Nenhum lead encontrado.</td></tr><?php endif; ?>
  </tbody>
</table></div>
<?php $pages = (int) ceil($total / $per); if ($pages > 1): ?>
<p class="row" style="margin-top:12px"><?php for ($i = 1; $i <= $pages; $i++): ?><a class="btn sm <?= $i === $page ? '' : 'sec' ?>" href="/admin/leads?<?= e(http_build_query(['status' => $st, 'q' => $q, 'p' => $i])) ?>"><?= $i ?></a><?php endfor; ?></p>
<?php endif; ?>
