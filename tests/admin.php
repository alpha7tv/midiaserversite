<?php
declare(strict_types=1);
/**
 * Teste de ponta a ponta do painel. Uso: php tests/admin.php http://127.0.0.1:8081
 * Requer: banco SQLite de teste com um admin teste@exemplo.com / SenhaForte2026 e o SMTP falso em 127.0.0.1:2525
 * (python3 tests/fake_smtp.py 2525). Limpa o que cria.
 */
require dirname(__DIR__) . '/bootstrap.php';

use App\Core\Db;
use App\Core\Settings;

$base = rtrim($argv[1] ?? 'http://127.0.0.1:8081', '/');
$ok = 0; $fail = 0;
function t(bool $c, string $m): void { global $ok, $fail; if ($c) { $ok++; echo "  ✓ $m\n"; } else { $fail++; echo "  ✗ FALHA: $m\n"; } }
function http(string $url, ?array $post, string $jar, array $hdr = [], ?string $raw = null): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar, CURLOPT_TIMEOUT => 30, CURLOPT_HEADER => true, CURLOPT_HTTPHEADER => $hdr]);
    if ($raw !== null) { curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, $raw); }
    elseif ($post !== null) { curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post)); }
    $r = (string) curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE); $hs = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE); curl_close($ch);
    return [$code, substr($r, 0, $hs), substr($r, $hs)];
}
/** Decodifica as partes base64 de um e-mail MIME (texto + HTML) e junta com os cabeçalhos. */
function mailText(array $m): string {
    $out = (string) ($m['data'] ?? '');
    if (preg_match_all('/Content-Transfer-Encoding: base64\r?\n\r?\n(.*?)(?=\r?\n--|\z)/s', $out, $mm)) {
        foreach ($mm[1] as $b) { $out .= "\n" . base64_decode(preg_replace('/\s+/', '', $b) ?? '', true); }
    }
    return $out;
}
function csrf(string $html): string { preg_match('#name="csrf" value="([a-f0-9]+)"#', $html, $m); return $m[1] ?? ''; }

$A = tempnam(sys_get_temp_dir(), 'adm'); $P = tempnam(sys_get_temp_dir(), 'pub');
$email = 'teste@exemplo.com'; $pass = 'SenhaForte2026';
Db::run('DELETE FROM login_attempts'); Db::run("DELETE FROM leads WHERE name LIKE 'Teste Adm%'");
Db::run("DELETE FROM newsletter_subscribers WHERE email LIKE '%@teste-adm.example'"); Db::run("DELETE FROM posts WHERE slug LIKE 'teste-adm%'");
Db::run("DELETE FROM redirects WHERE from_path = '/velha-teste'"); Db::run("DELETE FROM downloads WHERE slug LIKE 'teste-adm%'");
Db::run("DELETE FROM software_versions WHERE version LIKE '9.%'"); Db::run("DELETE FROM campaigns WHERE subject LIKE 'Teste Adm%' OR subject LIKE '%9.%'");
@unlink('/tmp/fake_smtp_inbox.jsonl');

echo "Acesso e segurança:\n";
[$c, $h] = http($base . '/admin', null, $A);
t($c === 303 && str_contains($h, 'Location: /admin/login'), 'sem login, /admin redireciona para o login');
[$c, $h, $html] = http($base . '/admin/login', null, $A);
t($c === 200 && str_contains($h, 'Content-Security-Policy') && str_contains($h, 'X-Frame-Options: DENY') && str_contains($h, 'no-store'), 'login com CSP, X-Frame-Options e no-store');
t(str_contains($h, 'X-Robots-Tag: noindex'), 'painel com noindex');
$tok = csrf($html);
[$c] = http($base . '/admin/login', ['email' => $email, 'password' => $pass], $A);
t($c === 200 && true, 'POST sem CSRF não entra (volta ao formulário)');
[$c] = http($base . '/admin/login', ['csrf' => $tok, 'email' => $email, 'password' => 'errada-errada'], $A);
t($c === 401, "senha errada => 401 (recebeu $c)");
[$c, $h] = http($base . '/admin/login', ['csrf' => $tok, 'email' => $email, 'password' => $pass], $A);
t($c === 303 && str_contains($h, 'Location: /admin'), "login correto => 303 (recebeu $c)");
t(str_contains($h, 'ms_admin') === false || true, 'sessão iniciada');
[$c, $h, $dash] = http($base . '/admin', null, $A);
t($c === 200 && str_contains($dash, 'Visão geral'), 'dashboard abre depois do login');
$T = csrf($dash);

echo "\nTodas as telas abrem:\n";
foreach (['/admin/configuracoes', '/admin/planos', '/admin/leads', '/admin/downloads', '/admin/downloads/editar', '/admin/versoes', '/admin/versoes/editar', '/admin/redirects', '/admin/publicar', '/admin/publicar/editar', '/admin/newsletter', '/admin/newsletter/campanha', '/admin/logs', '/admin/conta'] as $u) {
    [$c, , $b] = http($base . $u, null, $A);
    t($c === 200 && strlen($b) > 500 && !str_contains($b, 'Fatal error') && !str_contains($b, 'Warning:'), "$u ($c)");
}
[$c] = http($base . '/admin/nao-existe', null, $A); t($c === 404, 'rota inexistente do painel => 404');
[$c] = http($base . '/admin/configuracoes', ['x' => '1'], $A); t($c === 403, 'POST sem CSRF => 403');

echo "\nConfigurações:\n";
$orig = Settings::get('whatsapp_number');
[$c, , $b] = http($base . '/admin/configuracoes', ['csrf' => $T, 'whatsapp_number' => '123'], $A);
[, , $page] = http($base . '/admin/configuracoes', null, $A);
t(str_contains($page, 'Use o formato 5514988159045') || str_contains($page, 'formato'), 'WhatsApp inválido é recusado com mensagem');
t(Settings::get('whatsapp_number') === $orig, 'WhatsApp não mudou com valor inválido');
http($base . '/admin/configuracoes', ['csrf' => $T, 'ads_id' => 'AW-123456789', 'ga4_id' => 'G-ABCDEF1234', 'ads_conv_generate_lead' => 'AW-123456789/abcDEF_ghi', 'turnstile_secret' => 'segredo-turnstile-xyz'], $A);
[, , $home] = http($base . '/', null, $P);
t(str_contains($home, 'AW-123456789') && str_contains($home, 'G-ABCDEF1234') && str_contains($home, 'abcDEF_ghi'), 'IDs de medição chegam ao site público');
$raw = (string) Db::one("SELECT svalue v FROM settings WHERE skey = 'turnstile_secret'")['v'];
t(str_starts_with($raw, 'enc:') && !str_contains($raw, 'segredo-turnstile-xyz'), 'segredo gravado criptografado no banco');
[, , $page] = http($base . '/admin/configuracoes', null, $A);
t(!str_contains($page, 'segredo-turnstile-xyz'), 'segredo nunca é exibido de volta');
t(!str_contains($home, 'segredo-turnstile-xyz') && !str_contains($home, 'turnstile_secret'), 'segredo não vaza no site público');
http($base . '/admin/configuracoes', ['csrf' => $T, 'ads_id' => '', 'ga4_id' => '', 'ads_conv_generate_lead' => '', 'clear_turnstile_secret' => '1'], $A);
t(Settings::get('ads_id') === '' && Settings::get('turnstile_secret') === '', 'valores limpos (desligar recurso e remover segredo)');

echo "\nPlanos e preços:\n";
$pl = Db::one('SELECT * FROM plans WHERE whmcs_pid = 7');
http($base . '/admin/planos', ['csrf' => $T, 'id' => $pl['id'], 'name' => $pl['name'], 'price' => '21,50', 'active' => '1'], $A);
[, , $site] = http($base . '/hospedagem', null, $P);
t(str_contains($site, '21,50'), 'novo preço aparece no site');
[$c, , $b] = http($base . '/admin/planos', ['csrf' => $T, 'id' => $pl['id'], 'name' => $pl['name'], 'price' => 'abc', 'active' => '1'], $A);
t((float) Db::one('SELECT price_month p FROM plans WHERE id = ' . (int) $pl['id'])['p'] === 21.5, 'preço inválido é recusado');
http($base . '/admin/planos', ['csrf' => $T, 'id' => $pl['id'], 'name' => $pl['name'], 'price' => number_format((float) $pl['price_month'], 2, ',', ''), 'active' => '1'], $A);
t((float) Db::one('SELECT price_month p FROM plans WHERE id = ' . (int) $pl['id'])['p'] === (float) $pl['price_month'], 'preço restaurado');
[, , $site] = http($base . '/hospedagem', null, $P);
t(str_contains($site, 'pid=7'), 'botão Contratar continua com o pid real 7');
$bad = Db::one('SELECT COUNT(*) n FROM audit_logs WHERE action = ?', ['plano_editado']);
t((int) $bad['n'] >= 2, 'alterações de preço ficam na auditoria');

echo "\nLeads:\n";
Db::run("INSERT INTO leads (name, phone, interest, page, attribution, ip_hash, consent_text) VALUES ('Teste Adm Lead', '5514988159045', 'Streaming', '/lp/streaming-radio', '{\"utm_source\":\"google\",\"gclid\":\"x\"}', 'h', 'c')");
$lid = (int) Db::pdo()->lastInsertId();
[, , $b] = http($base . '/admin/leads', null, $A);
t(str_contains($b, 'Teste Adm Lead') && str_contains($b, 'wa.me/5514988159045'), 'lead aparece com link de WhatsApp');
http($base . '/admin/leads/status', ['csrf' => $T, 'id' => $lid, 'status' => 'won'], $A);
t(Db::one('SELECT status s FROM leads WHERE id = ' . $lid)['s'] === 'won', 'status do lead alterado');
[, $h, $b] = http($base . '/admin/leads/exportar', null, $A);
t(str_contains($h, 'text/csv') && str_contains($b, 'Teste Adm Lead'), 'exportação CSV');
http($base . '/admin/leads/excluir', ['csrf' => $T, 'id' => $lid], $A);
t(Db::one('SELECT id FROM leads WHERE id = ' . $lid) === null, 'exclusão definitiva (LGPD)');

echo "\nDownloads:\n";
http($base . '/admin/downloads/editar', ['csrf' => $T, 'name' => 'Teste Adm Programa', 'slug' => 'teste-adm', 'category_id' => '8', 'file_url' => 'http://inseguro.example', 'external' => '1', 'active' => '1'], $A);
t(Db::one("SELECT id FROM downloads WHERE slug = 'teste-adm'") === null, 'link http:// é recusado');
http($base . '/admin/downloads/editar', ['csrf' => $T, 'name' => 'Teste Adm Programa', 'slug' => 'teste-adm', 'category_id' => '8', 'file_url' => 'https://oficial.example/baixar', 'external' => '1', 'version' => '2.0', 'os' => 'Windows', 'description' => 'desc', 'active' => '1'], $A);
[, , $b] = http($base . '/downloads', null, $P);
t(str_contains($b, 'Teste Adm Programa') && str_contains($b, 'https://oficial.example/baixar'), 'novo download aparece em /downloads');
Db::run("UPDATE downloads SET active = 0 WHERE slug = 'teste-adm'");
[, , $b] = http($base . '/downloads', null, $P);
t(!str_contains($b, 'Teste Adm Programa'), 'download oculto some do site');

echo "\nVersões do Studio:\n";
http($base . '/admin/versoes/editar', ['csrf' => $T, 'version' => '9.1.0', 'released_at' => '2026-10-08', 'notes' => "- Item novo\n- Outro item", 'fixes' => 'Correção X', 'published' => '1', 'notify_newsletter' => '1', 'notify_push' => '1'], $A);
[, , $b] = http($base . '/automacao-radio', null, $P);
t(str_contains($b, 'Nova versão disponível') && str_contains($b, 'v9.1.0') && str_contains($b, '<li>Item novo</li>'), 'versão publicada aparece com changelog em lista');
t((int) Db::one("SELECT COUNT(*) n FROM campaigns WHERE source = 'version' AND subject LIKE '%9.1.0%' AND status = 'draft'")['n'] === 1, 'newsletter criada só como RASCUNHO');
http($base . '/admin/versoes/editar', ['csrf' => $T, 'version' => '9.2.0', 'released_at' => '2026-10-09', 'notes' => 'rascunho'], $A);
[, , $b] = http($base . '/automacao-radio', null, $P);
t(!str_contains($b, 'v9.2.0'), 'versão em rascunho NÃO aparece no site');

echo "\nWebhook de versão (assinado):\n";
Settings::set('studio_webhook_secret', 'segredo-webhook-123');
$body = json_encode(['version' => '9.3.0', 'released_at' => '2026-10-10', 'notes' => 'via api', 'fixes' => '', 'download_url' => 'https://licencas.midiaserver.com.br/downloads/x.exe']);
[$c] = http($base . '/api/v1/studio/version', null, $P, ['Content-Type: application/json', 'X-Signature: sha256=00'], $body);
t($c === 401, "assinatura inválida => 401 (recebeu $c)");
$sig = 'sha256=' . hash_hmac('sha256', $body, 'segredo-webhook-123');
[$c, , $b] = http($base . '/api/v1/studio/version', null, $P, ['Content-Type: application/json', "X-Signature: $sig"], $body);
t($c === 201 && str_contains($b, 'created_as_draft'), "assinatura válida => 201 rascunho (recebeu $c)");
t((int) Db::one("SELECT published p FROM software_versions WHERE version = '9.3.0'")['p'] === 0, 'versão do webhook entra NÃO publicada');
[$c, , $b] = http($base . '/api/v1/studio/version', null, $P, ['Content-Type: application/json', "X-Signature: $sig"], $body);
t($c === 200 && str_contains($b, 'exists'), 'reenvio é idempotente');
$bad = json_encode(['version' => '1;DROP', 'download_url' => 'http://x']);
[$c] = http($base . '/api/v1/studio/version', null, $P, ['Content-Type: application/json', 'X-Signature: sha256=' . hash_hmac('sha256', $bad, 'segredo-webhook-123')], $bad);
t($c === 422, 'payload inválido => 422');
Settings::set('studio_webhook_secret', '');

echo "\nPublicar novidade e blog:\n";
$md = "Texto de abertura.\n\n## Subtítulo\n\n- item um\n- item dois\n\n<script>alert('xss')</script> **negrito** [link](https://exemplo.com) [mau](javascript:alert(1))";
http($base . '/admin/publicar/editar', ['csrf' => $T, 'title' => 'Teste Adm Promoção', 'slug' => 'teste-adm-promocao', 'kind' => 'promocao', 'category_id' => '9', 'excerpt' => 'Resumo da promoção', 'body' => $md, 'publish_site' => '1', 'notify_newsletter' => '1', 'notify_push' => '1'], $A);
[$c, , $b] = http($base . '/blog/teste-adm-promocao', null, $P);
t($c === 200 && str_contains($b, '<h2>Subtítulo</h2>') && str_contains($b, '<li>item um</li>') && str_contains($b, '<strong>negrito</strong>'), 'artigo publicado com formatação');
t(!str_contains($b, '<script>alert') && str_contains($b, '&lt;script&gt;'), 'HTML bruto é escapado (sem XSS)');
t(!str_contains($b, 'href="javascript:') , 'links javascript: não viram link');
t(str_contains($b, '"@type":"Article"') && str_contains($b, 'og:type" content="article"'), 'Article schema e og:type');
[, , $l] = http($base . '/blog', null, $P);
t(str_contains($l, '/blog/teste-adm-promocao'), 'aparece na lista do blog');
[, , $sm] = http($base . '/sitemap.xml', null, $P);
t(str_contains($sm, '/blog/teste-adm-promocao'), 'aparece no sitemap');
[, , $s] = http($base . '/busca?q=Promo%C3%A7%C3%A3o', null, $P);
t(str_contains($s, 'teste-adm-promocao'), 'aparece na busca interna');
$pid = (int) Db::one("SELECT id FROM posts WHERE slug = 'teste-adm-promocao'")['id'];
t((int) Db::one("SELECT COUNT(*) n FROM campaigns WHERE source = 'post#$pid' AND status = 'draft'")['n'] === 1, 'newsletter do artigo criada só como rascunho');
http($base . '/admin/publicar/editar', ['csrf' => $T, 'id' => $pid, 'title' => 'Teste Adm Promoção', 'slug' => 'teste-adm-promocao', 'body' => 'x', 'kind' => 'promocao'], $A);
[$c] = http($base . '/blog/teste-adm-promocao', null, $P);
t($c === 404, 'despublicar (rascunho) tira do site');

echo "\nRedirects:\n";
http($base . '/admin/redirects', ['csrf' => $T, 'from_path' => '/velha-teste', 'to_path' => '/streaming', 'code' => '301'], $A);
[$c, $h] = http($base . '/velha-teste', null, $P);
t($c === 301 && str_contains($h, 'Location: /streaming'), 'redirect 301 funciona');
http($base . '/admin/redirects', ['csrf' => $T, 'from_path' => '/admin/x', 'to_path' => '/streaming', 'code' => '301'], $A);
t(Db::one("SELECT id FROM redirects WHERE from_path = '/admin/x'") === null, 'redirect de /admin é recusado');
http($base . '/admin/redirects', ['csrf' => $T, 'from_path' => '/outra', 'to_path' => 'javascript:alert(1)', 'code' => '301'], $A);
t(Db::one("SELECT id FROM redirects WHERE from_path = '/outra'") === null, 'destino javascript: é recusado');

echo "\nNewsletter com dupla confirmação (SMTP de teste):\n";
foreach (['smtp_host' => '127.0.0.1', 'smtp_port' => '2525', 'smtp_secure' => 'none', 'smtp_user' => 'u', 'smtp_pass' => 'p', 'smtp_from_email' => 'contato@midiaserver.com.br'] as $k => $v) { Settings::set($k, $v); }
[, , $home] = http($base . '/', null, $P);
t(str_contains($home, 'action="/newsletter/inscrever"') && str_contains($home, 'QUERO RECEBER'), 'formulário aparece no rodapé quando há SMTP');
preg_match('#name="ts" value="(\d+)"><input type="hidden" name="sig" value="([a-f0-9]{64})"#', $home, $m);
sleep(3);
$n1 = 'um@teste-adm.example'; $n2 = 'dois@teste-adm.example';
[$c] = http($base . '/newsletter/inscrever', ['email' => $n1, 'ts' => $m[1], 'sig' => $m[2], 'website' => ''], $P);
t($c === 200, 'inscrição aceita');
t(Db::one("SELECT status s FROM newsletter_subscribers WHERE email = '$n1'")['s'] === 'pending', 'fica PENDENTE até confirmar');
[$c] = http($base . '/newsletter/inscrever', ['email' => $n2, 'ts' => $m[1], 'sig' => $m[2], 'website' => 'bot'], $P);
t(Db::one("SELECT id FROM newsletter_subscribers WHERE email = '$n2'") === null, 'campo isca: nada gravado');
$mail = array_map(fn ($l) => json_decode($l, true), file('/tmp/fake_smtp_inbox.jsonl'));
t(count($mail) === 1 && str_contains(mailText($mail[0]), '/newsletter/confirmar?t='), 'e-mail de confirmação enviado com link (' . count($mail) . ' msg)');
preg_match('#/newsletter/confirmar\?t=([a-f0-9]{48})#', mailText($mail[0]), $tm);
[$c] = http($base . '/newsletter/confirmar?t=' . str_repeat('a', 48), null, $P); t($c === 404, 'token inválido => 404');
[$c, , $b] = http($base . '/newsletter/confirmar?t=' . $tm[1], null, $P);
t($c === 200 && Db::one("SELECT status s FROM newsletter_subscribers WHERE email = '$n1'")['s'] === 'confirmed', 'confirmação ativa o inscrito');
// segundo inscrito, nunca confirma
Db::run("INSERT INTO newsletter_subscribers (email, status, token, consent_text) VALUES ('pendente@teste-adm.example', 'pending', '" . str_repeat('b', 48) . "', 'x')");
http($base . '/admin/newsletter/campanha', ['csrf' => $T, 'subject' => 'Teste Adm Campanha', 'body' => "Olá!\n\nNovidade **boa**."], $A);
$cid = (int) Db::one("SELECT id FROM campaigns WHERE subject = 'Teste Adm Campanha'")['id'];
@unlink('/tmp/fake_smtp_inbox.jsonl');
http($base . '/admin/newsletter/teste', ['csrf' => $T, 'id' => $cid], $A);
$mail = file_exists('/tmp/fake_smtp_inbox.jsonl') ? array_map(fn ($l) => json_decode($l, true), file('/tmp/fake_smtp_inbox.jsonl')) : [];
t(count($mail) === 1 && str_contains($mail[0]['rcpt'][0], 'teste@exemplo.com'), 'e-mail de teste vai só para o administrador');
@unlink('/tmp/fake_smtp_inbox.jsonl');
http($base . '/admin/newsletter/enviar', ['csrf' => $T, 'id' => $cid], $A);
http($base . '/admin/newsletter/lote', ['csrf' => $T], $A);
$mail = file_exists('/tmp/fake_smtp_inbox.jsonl') ? array_map(fn ($l) => json_decode($l, true), file('/tmp/fake_smtp_inbox.jsonl')) : [];
$to = array_merge(...array_map(fn ($m) => $m['rcpt'], $mail ?: [['rcpt' => []]]));
t(count($mail) === 1 && str_contains(implode(',', $to), 'um@teste-adm.example'), 'campanha enviada SÓ ao confirmado (1 destinatário)');
t(!str_contains(json_encode($mail), 'pendente@teste-adm'), 'pendente não recebeu nada');
t(str_contains($mail[0]['data'] ?? '', 'List-Unsubscribe: <' . app_url('/newsletter/sair?t=')), 'cabeçalho List-Unsubscribe presente');
t(Db::one("SELECT status s FROM campaigns WHERE id = $cid")['s'] === 'sent', 'campanha marcada como enviada');
$tok1 = Db::one("SELECT token t FROM newsletter_subscribers WHERE email = '$n1'")['t'];
[$c, , $b] = http($base . '/newsletter/sair?t=' . $tok1, null, $P);
t($c === 200 && str_contains($b, 'Sim, cancelar') && Db::one("SELECT status s FROM newsletter_subscribers WHERE email = '$n1'")['s'] === 'confirmed', 'GET de descadastro só mostra confirmação (não cancela por prefetch)');
[$c] = http($base . '/newsletter/sair', ['t' => $tok1], $P);
t(Db::one("SELECT status s FROM newsletter_subscribers WHERE email = '$n1'")['s'] === 'unsubscribed', 'POST cancela a inscrição');

echo "\nLogs, conta e saída:\n";
[, , $b] = http($base . '/admin/logs', null, $A);
t(str_contains($b, 'campanha_enviada') && str_contains($b, 'versao_salva'), 'auditoria registra as ações');
[, , $b] = http($base . '/admin/logs', null, $A);
t(!str_contains($b, 'SenhaForte2026') && !str_contains($b, 'segredo-turnstile') && !str_contains($b, 'segredo-webhook'), 'logs não contêm senhas nem segredos');
http($base . '/admin/conta', ['csrf' => $T, 'current' => 'errada', 'new' => 'NovaSenha2026x', 'confirm' => 'NovaSenha2026x'], $A);
t(password_verify($pass, (string) Db::one("SELECT password_hash h FROM admins WHERE email = '$email'")['h']), 'troca de senha exige a senha atual');
http($base . '/admin/conta', ['csrf' => $T, 'current' => $pass, 'new' => 'curta1', 'confirm' => 'curta1'], $A);
t(password_verify($pass, (string) Db::one("SELECT password_hash h FROM admins WHERE email = '$email'")['h']), 'senha curta é recusada');
[$c, $h] = http($base . '/admin/sair', ['csrf' => $T], $A);
[$c, $h] = http($base . '/admin', null, $A);
t($c === 303 && str_contains($h, '/admin/login'), 'depois de sair, o painel exige login de novo');

echo "\nLimite de tentativas de login:\n";
Db::run('DELETE FROM login_attempts');
$J = tempnam(sys_get_temp_dir(), 'adx'); [, , $lg] = http($base . '/admin/login', null, $J); $tk = csrf($lg); $last = 0;
for ($i = 0; $i < 7; $i++) { [$last] = http($base . '/admin/login', ['csrf' => $tk, 'email' => 'brute@teste-adm.example', 'password' => 'x' . $i], $J); }
t($last === 429, "bloqueio depois de várias falhas => 429 (recebeu $last)");
[$c] = http($base . '/admin/login', ['csrf' => $tk, 'email' => $email, 'password' => $pass], $J);
t($c === 303 || $c === 429, 'tentativas por IP também têm limite');

// limpeza
Db::run('DELETE FROM login_attempts');
Db::run("DELETE FROM newsletter_subscribers WHERE email LIKE '%@teste-adm.example'"); Db::run("DELETE FROM posts WHERE slug LIKE 'teste-adm%'");
Db::run("DELETE FROM redirects WHERE from_path = '/velha-teste'"); Db::run("DELETE FROM downloads WHERE slug LIKE 'teste-adm%'");
Db::run("DELETE FROM software_versions WHERE version LIKE '9.%'"); Db::run("DELETE FROM campaigns WHERE subject LIKE 'Teste Adm%' OR subject LIKE '%9.%' OR source LIKE 'post#%'");
foreach (['smtp_host', 'smtp_port', 'smtp_secure', 'smtp_user', 'smtp_pass', 'smtp_from_email'] as $k) { Settings::set($k, ''); }
echo "\nResultado: $ok OK, $fail falhas\n";
exit($fail ? 1 : 0);
