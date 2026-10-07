<h1><?= $p ? 'Editar publicação' : 'Nova publicação' ?></h1>
<form class="card" method="post" action="/admin/publicar/editar" style="max-width:820px">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) ($p['id'] ?? 0) ?>">
  <div class="grid g2">
    <div><label for="kind">Tipo</label><select id="kind" name="kind"><?php foreach ($kinds as $k => $v): ?><option value="<?= e($k) ?>" <?= ($p['kind'] ?? 'noticia') === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select></div>
    <div><label for="category_id">Categoria do blog</label><select id="category_id" name="category_id"><?php foreach ($cats as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) ($p['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e((string) $c['name']) ?></option><?php endforeach; ?></select></div>
  </div>
  <label for="title">Título</label><input id="title" name="title" type="text" required maxlength="190" value="<?= e((string) ($p['title'] ?? '')) ?>">
  <label for="slug">Endereço (slug)<span class="hint">Em branco: gerado do título. Fica em /blog/slug.</span></label><input id="slug" name="slug" type="text" maxlength="100" value="<?= e((string) ($p['slug'] ?? '')) ?>">
  <label for="excerpt">Resumo<span class="hint">Até 300 caracteres. Vira a descrição para Google e redes sociais.</span></label><textarea id="excerpt" name="excerpt" maxlength="300" style="min-height:70px"><?= e((string) ($p['excerpt'] ?? '')) ?></textarea>
  <label for="body">Texto<span class="hint">Parágrafos separados por linha em branco. <code>## Título</code>, <code>**negrito**</code>, <code>- item de lista</code>, <code>[texto](https://link)</code>.</span></label><textarea id="body" name="body" required style="min-height:260px"><?= e((string) ($p['body'] ?? '')) ?></textarea>
  <label for="og_image">Imagem de compartilhamento (opcional)<span class="hint">Link https:// de uma imagem 1200x630. Em branco usa a imagem padrão do blog.</span></label><input id="og_image" name="og_image" type="text" maxlength="400" value="<?= e((string) ($p['og_image'] ?? '')) ?>">
  <h2>Onde comunicar</h2>
  <label class="chk"><input type="checkbox" name="publish_site" value="1" <?= ($p['status'] ?? '') === 'published' ? 'checked' : '' ?>> <span>Publicar no site (blog)</span></label>
  <label class="chk"><input type="checkbox" name="notify_newsletter" value="1" <?= !empty($p['notify_newsletter']) ? 'checked' : '' ?>> <span>Enviar newsletter<span class="hint">Cria um rascunho em Newsletter para você revisar e enviar<?= $smtp ? '' : ' (configure o SMTP antes de enviar)' ?>. Só vai para quem confirmou a inscrição.</span></span></label>
  <label class="chk"><input type="checkbox" name="notify_push" value="1" <?= !empty($p['notify_push']) ? 'checked' : '' ?>> <span>Enviar Web Push<span class="hint">Registrado agora; o envio é ativado quando as chaves VAPID forem configuradas. Só vai para quem aceitou receber.</span></span></label>
  <p class="row" style="margin-top:14px"><button class="btn" type="submit">Salvar</button><a class="btn sec" href="/admin/publicar">Cancelar</a></p>
</form>
