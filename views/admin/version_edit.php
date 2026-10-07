<h1><?= $v ? 'Editar versão' : 'Nova versão do Mídia Rádio Studio' ?></h1>
<form class="card" method="post" action="/admin/versoes/editar" style="max-width:760px">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) ($v['id'] ?? 0) ?>">
  <div class="grid g2"><div><label for="version">Versão</label><input id="version" name="version" type="text" required placeholder="1.4.2" value="<?= e((string) ($v['version'] ?? '')) ?>"></div>
  <div><label for="released_at">Data de lançamento</label><input id="released_at" name="released_at" type="date" value="<?= e(substr((string) ($v['released_at'] ?? date('Y-m-d')), 0, 10)) ?>"></div></div>
  <label for="notes">Novidades<span class="hint">Texto simples. Use "- " no início da linha para fazer lista.</span></label><textarea id="notes" name="notes"><?= e((string) ($v['notes'] ?? '')) ?></textarea>
  <label for="fixes">Correções</label><textarea id="fixes" name="fixes"><?= e((string) ($v['fixes'] ?? '')) ?></textarea>
  <label for="download_url">Link do instalador desta versão<span class="hint">Opcional. Se vazio, o botão usa o instalador padrão de Configurações.</span></label><input id="download_url" name="download_url" type="text" value="<?= e((string) ($v['download_url'] ?? '')) ?>">
  <h2>O que fazer com esta versão</h2>
  <label class="chk"><input type="checkbox" name="published" value="1" <?= !empty($v['published']) ? 'checked' : '' ?>> <span>Publicar no site (aparece em Automação de Rádio)</span></label>
  <label class="chk"><input type="checkbox" name="notify_newsletter" value="1" <?= !empty($v['notify_newsletter']) ? 'checked' : '' ?>> <span>Preparar newsletter<span class="hint">Cria um rascunho em Newsletter para você revisar. Não envia sozinho.</span></span></label>
  <label class="chk"><input type="checkbox" name="notify_push" value="1" <?= !empty($v['notify_push']) ? 'checked' : '' ?>> <span>Enviar Web Push<span class="hint">Registrado; o envio é ativado quando as chaves VAPID estiverem configuradas.</span></span></label>
  <p class="row" style="margin-top:14px"><button class="btn" type="submit">Salvar</button><a class="btn sec" href="/admin/versoes">Cancelar</a></p>
</form>
