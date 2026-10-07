<h1>Visão geral</h1>
<?php foreach ($alerts as [$t, $m]): ?><div class="a-flash a-<?= e($t) ?>"><?= e($m) ?></div><?php endforeach; ?>
<div class="grid g4" style="margin-bottom:16px">
  <?php foreach ($stats as $label => $v): ?><div class="card stat"><b><?= (int) $v ?></b><span><?= e($label) ?></span></div><?php endforeach; ?>
</div>
<div class="grid g2">
  <section>
    <h2 style="margin-top:0">Últimos leads</h2>
    <div class="tw"><table><thead><tr><th>Quando</th><th>Nome</th><th>Interesse</th><th></th></tr></thead><tbody>
      <?php foreach ($leads as $l): ?><tr><td><?= e(date('d/m H:i', strtotime((string) $l['created_at']))) ?></td><td><?= e((string) $l['name']) ?></td><td><?= e((string) $l['interest']) ?></td>
        <td><a class="btn sm" href="https://wa.me/<?= e((string) $l['phone']) ?>?text=<?= rawurlencode('Olá ' . explode(' ', (string) $l['name'])[0] . ', aqui é da Mídia Server. Recebemos seu contato.') ?>" target="_blank" rel="noopener">WhatsApp</a></td></tr><?php endforeach; ?>
      <?php if (!$leads): ?><tr><td colspan="4" class="muted">Nenhum lead ainda.</td></tr><?php endif; ?>
    </tbody></table></div>
    <p><a href="/admin/leads">Ver todos os leads →</a></p>
  </section>
  <section>
    <h2 style="margin-top:0">Atalhos</h2>
    <div class="card">
      <p><a class="btn" href="/admin/planos/whmcs">Conferir preços com o WHMCS</a></p>
      <p><a class="btn sec" href="/admin/publicar/editar">Publicar novidade</a> <a class="btn sec" href="/admin/versoes/editar">Nova versão do Studio</a></p>
      <p><a class="btn sec" href="/admin/configuracoes">Configurações</a> <a class="btn sec" href="/admin/logs">Logs</a></p>
    </div>
    <h2>Últimas ações</h2>
    <div class="tw"><table><tbody>
      <?php foreach ($audit as $a): ?><tr><td><?= e(date('d/m H:i', strtotime((string) $a['created_at']))) ?></td><td><?= e((string) ($a['name'] ?? '—')) ?></td><td><?= e((string) $a['action']) ?></td></tr><?php endforeach; ?>
      <?php if (!$audit): ?><tr><td class="muted">Sem registros.</td></tr><?php endif; ?>
    </tbody></table></div>
  </section>
</div>
