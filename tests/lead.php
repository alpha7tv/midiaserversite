<?php
declare(strict_types=1);
/** Teste do formulário de lead. Uso: php tests/lead.php http://127.0.0.1:8081 (banco de TESTE/sqlite; apaga leads de teste). */
require dirname(__DIR__) . '/bootstrap.php';

use App\Core\Db;

$base = rtrim($argv[1] ?? 'http://127.0.0.1:8081', '/');
$ok = 0; $fail = 0;
function t(bool $c, string $m): void { global $ok, $fail; if ($c) { $ok++; echo "  ✓ $m\n"; } else { $fail++; echo "  ✗ FALHA: $m\n"; } }

$jar = tempnam(sys_get_temp_dir(), 'cj');
function req(string $url, ?array $post, string $jar, array $hdr = []): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar, CURLOPT_TIMEOUT => 20, CURLOPT_HEADER => true, CURLOPT_HTTPHEADER => $hdr]);
    if ($post !== null) { curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post)); }
    $res = (string) curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hs = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    return [$code, substr($res, 0, $hs), substr($res, $hs)];
}
function form(string $html): array {
    $f = [];
    foreach (['csrf', 'ts', 'sig', 'page', 'interest', 'attribution'] as $k) {
        preg_match('#name="' . $k . '" value="([^"]*)"#', $html, $m);
        $f[$k] = html_entity_decode($m[1] ?? '');
    }
    return $f;
}

Db::run("DELETE FROM leads WHERE name LIKE 'Teste Auto%'");
[$c, , $html] = req($base . '/lp/streaming-radio', null, $jar);
t($c === 200 && str_contains($html, 'data-lead'), 'landing tem o formulário');
$f = form($html);
t(strlen($f['csrf']) === 32 && $f['ts'] !== '' && strlen($f['sig']) === 64, 'campos ocultos presentes (csrf, ts, sig)');
$good = array_merge($f, ['name' => 'Teste Auto Um', 'phone' => '(14) 98815-9045', 'website' => '', 'attribution' => '{"gclid":"abc123","utm_source":"google"}']);

echo "\nEnvio rápido demais (menos de 3 s):\n";
[$c] = req($base . '/lead', $good, $jar);
t($c === 400, "bloqueado com 400 (recebeu $c)");

sleep(4);
echo "\nEnvio válido:\n";
[$c, $h] = req($base . '/lead', $good, $jar);
t($c === 303 && str_contains($h, 'Location: /obrigado'), "303 para /obrigado (recebeu $c)");
$row = Db::one("SELECT * FROM leads WHERE name = 'Teste Auto Um'");
t($row !== null && $row['phone'] === '5514988159045', 'lead gravado com telefone normalizado 5514988159045');
t($row && str_contains((string) $row['attribution'], 'abc123'), 'gclid gravado na atribuição');
t($row && $row['consent_text'] !== '', 'texto de consentimento registrado');
[$c, , $html] = req($base . '/obrigado', null, $jar);
t($c === 200 && str_contains($html, 'data-lead-thanks') && str_contains($html, 'Obrigado, Teste'), 'página de obrigado dispara a conversão e usa o nome');
t(str_contains($html, 'noindex'), 'página de obrigado é noindex');

echo "\nDuplicado em 10 min não cria outro registro:\n";
req($base . '/lead', $good, $jar);
$n = (int) Db::one("SELECT COUNT(*) n FROM leads WHERE phone = '5514988159045' AND name LIKE 'Teste Auto%'")['n'];
t($n === 1, "continua 1 registro (tem $n)");

echo "\nProteções:\n";
[$c] = req($base . '/lead', ['csrf' => 'ffffffffffffffffffffffffffffffff'] + $good, $jar);
t($c === 403, "CSRF inválido => 403 (recebeu $c)");
[$c] = req($base . '/lead', ['phone' => '123'] + $good, $jar);
t($c === 422, "telefone inválido => 422 (recebeu $c)");
[$c] = req($base . '/lead', ['name' => '1'] + $good, $jar);
t($c === 422, "nome inválido => 422 (recebeu $c)");
$before = (int) Db::one('SELECT COUNT(*) n FROM leads')['n'];
[$c, $h] = req($base . '/lead', ['website' => 'http://spam.example', 'phone' => '(14) 99999-0001', 'name' => 'Teste Auto Bot'] + $good, $jar);
t($c === 303, 'campo isca: responde 303 sem avisar o bot');
t((int) Db::one('SELECT COUNT(*) n FROM leads')['n'] === $before, 'campo isca: nada gravado');
[$c] = req($base . '/lead', ['ts' => (string) (time() - 5), 'sig' => str_repeat('0', 64)] + $good, $jar);
t($c === 400, "assinatura de tempo falsa => 400 (recebeu $c)");
[$c, $h] = req($base . '/lead', null, $jar);
t($c === 302 || $c === 303, "GET em /lead volta para a home (recebeu $c)");

echo "\nLimite por IP (5/hora):\n";
$last = 0;
for ($i = 0; $i < 7; $i++) {
    $p = ['name' => 'Teste Auto Lote ' . chr(65 + $i), 'phone' => '(11) 9' . (1000 + $i) . '-0000'] + $good;
    [$last] = req($base . '/lead', $p, $jar);
}
t($last === 429, "bloqueio após o limite => 429 (recebeu $last)");

Db::run("DELETE FROM leads WHERE name LIKE 'Teste Auto%'");
echo "\nResultado: $ok OK, $fail falhas\n";
exit($fail ? 1 : 0);
