<?php
declare(strict_types=1);
/**
 * Teste de fumaça. Uso: php tests/smoke.php http://127.0.0.1:8081
 * Verifica páginas, SEO, links internos, imagens OG e coerência dos links do WHMCS com o catálogo.
 */
require dirname(__DIR__) . '/bootstrap.php';

use App\Core\Db;

$base = rtrim($argv[1] ?? 'http://127.0.0.1:8081', '/');
$pages = require BASE_PATH . '/config/pages.php';
$fail = 0; $ok = 0;
function t(bool $cond, string $msg): void { global $fail, $ok; if ($cond) { $ok++; } else { $fail++; echo "  FALHA: $msg\n"; } }
function get(string $url, bool $follow = false): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => $follow, CURLOPT_TIMEOUT => 20, CURLOPT_HEADER => false]);
    $body = (string) curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $loc = (string) curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return [$code, $body, $loc];
}

$validPids = array_map('intval', array_column(Db::all('SELECT whmcs_pid FROM plans'), 'whmcs_pid'));
$titles = []; $descs = []; $internal = [];

foreach ($pages as $path => $p) {
    [$code, $html] = get($base . $path);
    echo ($code === 200 ? '✓' : '✗') . " $path ($code)\n";
    t($code === 200, "$path respondeu $code");
    if ($code !== 200) { continue; }

    preg_match('#<title>(.*?)</title>#s', $html, $m); $title = html_entity_decode($m[1] ?? '');
    t($title !== '' && mb_strlen($title) <= 70, "$path title vazio ou longo (" . mb_strlen($title) . ')');
    t(!isset($titles[$title]), "$path title duplicado: $title"); $titles[$title] = 1;
    preg_match('#<meta name="description" content="(.*?)">#s', $html, $m); $desc = html_entity_decode($m[1] ?? '');
    t($desc !== '' && mb_strlen($desc) <= 175, "$path description vazia ou longa (" . mb_strlen($desc) . ')');
    t(!isset($descs[$desc]), "$path description duplicada"); $descs[$desc] = 1;
    t(!str_contains($title . $desc, '{min}'), "$path placeholder {min} sem resolver");
    t(preg_match_all('#<h1[ >]#', $html) === 1, "$path deve ter exatamente 1 H1");
    t(str_contains($html, '<link rel="canonical"'), "$path sem canonical");
    foreach (['og:title', 'og:description', 'og:image', 'og:url', 'twitter:card'] as $k) { t(str_contains($html, $k), "$path sem $k"); }
    preg_match('#og:image" content="[^"]*/assets/img/og/([a-z0-9-]+)\.png#', $html, $m);
    t(isset($m[1]) && is_file(BASE_PATH . "/public/assets/img/og/{$m[1]}.png"), "$path imagem OG inexistente");
    if (preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $mm)) {
        foreach ($mm[1] as $j) { t(json_decode($j, true) !== null, "$path JSON-LD inválido"); }
    }
    t(!preg_match('#R\$ ?0,00#', $html), "$path mostra R$ 0,00");
    // WhatsApp: só o número oficial
    preg_match_all('#wa\.me/(\d+)#', $html, $w);
    foreach (array_unique($w[1]) as $n) { t($n === '5514988159045', "$path usa WhatsApp diferente do oficial: $n"); }
    // links do WHMCS: pid precisa existir no catálogo
    preg_match_all('#cart\.php\?a=add&(?:amp;)?pid=(\d+)#', $html, $c);
    foreach (array_unique($c[1]) as $pid) { t(in_array((int) $pid, $validPids, true), "$path usa pid inexistente no catálogo: $pid"); }
    // links internos
    preg_match_all('#href="(/[^"#?]*)#', $html, $l);
    foreach ($l[1] as $href) { $internal[$href][] = $path; }
}

echo "\nLinks internos:\n";
foreach ($internal as $href => $from) {
    if (preg_match('#^/(assets|baixar)/#', $href) || $href === '/site.webmanifest') { continue; }
    $h = rtrim($href, '/') ?: '/';
    if (isset($pages[$h])) { continue; }
    [$code] = get($base . $href);
    t($code === 200 || $code === 301 || $code === 302, "link quebrado $href ($code) em " . implode(', ', array_slice($from, 0, 3)));
}

echo "\nTécnicos:\n";
[$c, $b] = get($base . '/sitemap.xml'); t($c === 200 && str_contains($b, '<urlset'), 'sitemap.xml');
t(!str_contains($b, '/lp/'), 'sitemap não deve listar landings de campanha');
[$c, $b] = get($base . '/robots.txt'); t($c === 200 && str_contains($b, 'Sitemap:'), 'robots.txt');
[$c] = get($base . '/pagina-que-nao-existe'); t($c === 404, '404 real para página inexistente');
[$c, , $loc] = get($base . '/hospedagem-de-sites'); t($c === 301 && str_ends_with($loc, '/hospedagem'), 'redirect 301 legado /hospedagem-de-sites');
[$c, , $loc] = get($base . '/streaming-web-radio'); t($c === 301 && str_ends_with($loc, '/streaming'), 'redirect 301 legado /streaming-web-radio');
[$c] = get($base . '/baixar/midia-radio-studio'); t($c === 302, 'download do Studio redireciona');
[$c] = get($base . '/health'); t($c === 200, '/health');
foreach (['img/logo-horizontal.svg', 'img/logo-symbol.svg', 'img/favicon.svg', 'css/app.css', 'js/app.js', 'fonts/sora-700.woff2'] as $a) {
    [$c] = get($base . '/assets/' . $a); t($c === 200, "asset $a");
}
echo "\nResultado: $ok verificações OK, $fail falhas\n";
exit($fail ? 1 : 0);
