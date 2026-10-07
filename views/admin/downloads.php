<h1>Downloads</h1>
<p class="muted">Programas de terceiros devem apontar para o <strong>site oficial</strong> do desenvolvedor (marque "externo"). Só hospedamos arquivos próprios.</p>
<p><a class="btn" href="/admin/downloads/editar">Novo download</a></p>
<div class="tw"><table>
  <thead><tr><th>Nome</th><th>Categoria</th><th>Versão</th><th>Tipo</th><th>Baixados</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $d): ?>
  <tr>
    <td><strong><?= e((string) $d['name']) ?></strong><br><span class="muted small"><?= e((string) $d['os']) ?></span></td>
    <td><?= e((string) $d['cat']) ?></td>
    <td><?= e((string) ($d['version'] ?: '—')) ?></td>
    <td><?= $d['external'] ? '<span class="pill">link oficial</span>' : '<span class="pill ok">arquivo próprio</span>' ?></td>
    <td><?= (int) $d['download_count'] ?></td>
    <td><?= $d['active'] ? '<span class="pill ok">ativo</span>' : '<span class="pill no">oculto</span>' ?></td>
    <td><a class="btn sm sec" href="/admin/downloads/editar?id=<?= (int) $d['id'] ?>">Editar</a></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
