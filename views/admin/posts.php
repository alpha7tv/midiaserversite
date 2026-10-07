<h1>Publicar novidade</h1>
<p class="muted">Promoções, novos produtos, atualizações, softwares, versões, conteúdos e notícias. Em cada publicação <strong>você escolhe</strong> onde comunicar.</p>
<p><a class="btn" href="/admin/publicar/editar">Nova publicação</a></p>
<div class="tw"><table>
  <thead><tr><th>Título</th><th>Tipo</th><th>Categoria</th><th>Status</th><th>Comunicação</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $p): ?>
  <tr>
    <td><strong><?= e((string) $p['title']) ?></strong><br><span class="muted small"><?= $p['status'] === 'published' ? '<a href="/blog/' . e((string) $p['slug']) . '" target="_blank" rel="noopener">/blog/' . e((string) $p['slug']) . '</a>' : 'rascunho' ?></span></td>
    <td><?= e($kinds[$p['kind'] ?? ''] ?? '—') ?></td>
    <td><?= e((string) ($p['cat'] ?? '—')) ?></td>
    <td><?= $p['status'] === 'published' ? '<span class="pill ok">no site</span>' : '<span class="pill wn">rascunho</span>' ?></td>
    <td class="small"><?= !empty($p['notify_newsletter']) ? 'newsletter ' : '' ?><?= !empty($p['notify_push']) ? 'push' : '' ?><?= empty($p['notify_newsletter']) && empty($p['notify_push']) ? '—' : '' ?></td>
    <td><a class="btn sm sec" href="/admin/publicar/editar?id=<?= (int) $p['id'] ?>">Editar</a></td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="muted">Nenhuma publicação ainda.</td></tr><?php endif; ?>
  </tbody>
</table></div>
