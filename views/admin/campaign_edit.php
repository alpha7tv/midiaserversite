<?php $locked = $c && $c['status'] !== 'draft'; ?>
<h1><?= $c ? 'Campanha' : 'Nova campanha' ?></h1>
<form class="card" method="post" action="/admin/newsletter/campanha" style="max-width:820px">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) ($c['id'] ?? 0) ?>">
  <label for="subject">Assunto</label><input id="subject" name="subject" type="text" required maxlength="200" value="<?= e((string) ($c['subject'] ?? '')) ?>" <?= $locked ? 'readonly' : '' ?>>
  <label for="body">Texto<span class="hint">Parágrafos separados por linha em branco. <code>## Título</code>, <code>**negrito**</code>, <code>- lista</code>, <code>[texto](https://link)</code>. O cabeçalho com a logo e o rodapé com o link de cancelamento entram automaticamente.</span></label>
  <textarea id="body" name="body" required style="min-height:260px" <?= $locked ? 'readonly' : '' ?>><?= e((string) ($c['body_text'] ?? '')) ?></textarea>
  <?php if (!$locked): ?><p class="row"><button class="btn" type="submit">Salvar rascunho</button><a class="btn sec" href="/admin/newsletter">Voltar</a></p><?php else: ?><p class="muted">Campanha <?= e((string) $c['status']) ?>: não pode mais ser editada. <a href="/admin/newsletter">Voltar</a></p><?php endif; ?>
</form>
<?php if ($c && !$locked): ?>
<div class="card" style="max-width:820px">
  <h2 style="margin-top:0">Prévia</h2>
  <iframe title="Prévia" sandbox="" style="width:100%;height:420px;border:1px solid var(--line);border-radius:10px" srcdoc="<?= e(\App\Services\NewsletterSender::wrap((string) $c['body_html'], str_repeat('0', 48))) ?>"></iframe>
  <form method="post" action="/admin/newsletter/teste" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $c['id'] ?>"><button class="btn sec" type="submit">Enviar teste para mim</button></form>
  <form method="post" action="/admin/newsletter/enviar" class="inline" data-confirm="Enviar esta campanha para TODOS os inscritos confirmados? Não dá para cancelar depois de iniciar."><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $c['id'] ?>"><button class="btn" type="submit">Enviar para os inscritos confirmados</button></form>
</div>
<?php endif; ?>
