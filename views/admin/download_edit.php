<h1><?= $d ? 'Editar download' : 'Novo download' ?></h1>
<form class="card" method="post" action="/admin/downloads/editar" style="max-width:720px">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) ($d['id'] ?? 0) ?>">
  <label for="name">Nome</label><input id="name" name="name" type="text" required maxlength="190" value="<?= e((string) ($d['name'] ?? '')) ?>">
  <label for="slug">Endereço curto (slug)<span class="hint">Deixe em branco para gerar a partir do nome. É o que vai em /baixar/slug.</span></label><input id="slug" name="slug" type="text" maxlength="120" value="<?= e((string) ($d['slug'] ?? '')) ?>">
  <label for="category_id">Categoria</label>
  <select id="category_id" name="category_id"><?php foreach ($cats as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) ($d['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e((string) $c['name']) ?></option><?php endforeach; ?></select>
  <div class="grid g2"><div><label for="version">Versão</label><input id="version" name="version" type="text" maxlength="40" value="<?= e((string) ($d['version'] ?? '')) ?>"></div>
  <div><label for="os">Sistema operacional</label><input id="os" name="os" type="text" maxlength="120" value="<?= e((string) ($d['os'] ?? '')) ?>"></div></div>
  <label for="description">Descrição</label><textarea id="description" name="description" maxlength="2000"><?= e((string) ($d['description'] ?? '')) ?></textarea>
  <label for="file_url">Link do arquivo ou do site oficial<span class="hint">Precisa começar com https://. Para o Mídia Rádio Studio, deixe em branco (usa o link de Configurações).</span></label><input id="file_url" name="file_url" type="text" maxlength="400" value="<?= e((string) ($d['file_url'] ?? '')) ?>">
  <label class="chk"><input type="checkbox" name="external" value="1" <?= !isset($d) || !empty($d['external']) ? 'checked' : '' ?>> Este link é de um site de terceiros (mostra "Baixar no site oficial")</label>
  <div class="grid g2"><div><label for="size_mb">Tamanho (MB)</label><input id="size_mb" name="size_mb" type="text" inputmode="decimal" value="<?= !empty($d['size_bytes']) ? e(number_format($d['size_bytes'] / 1048576, 1, ',', '')) : '' ?>"></div>
  <div><label for="released_at">Data de atualização</label><input id="released_at" name="released_at" type="date" value="<?= !empty($d['released_at']) ? e(substr((string) $d['released_at'], 0, 10)) : '' ?>"></div></div>
  <label for="license_note">Licença<span class="hint">Ex.: Grátis · código aberto, Pago, Software licenciado da Mídia Server.</span></label><input id="license_note" name="license_note" type="text" maxlength="300" value="<?= e((string) ($d['license_note'] ?? '')) ?>">
  <label class="chk"><input type="checkbox" name="active" value="1" <?= !isset($d) || !empty($d['active']) ? 'checked' : '' ?>> Visível no site</label>
  <p class="row" style="margin-top:14px"><button class="btn" type="submit">Salvar</button><a class="btn sec" href="/admin/downloads">Cancelar</a></p>
</form>
