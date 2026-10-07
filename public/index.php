<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\PageController;
use App\Core\Db;
use App\Core\Seo;
use App\Core\Settings;

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
if (env('APP_ENV', 'production') !== 'production') {
    header('X-Robots-Tag: noindex, nofollow');
}

$uri = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
$uri = '/' . trim(rawurldecode($uri), '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// URLs legadas (links que existiam no site antigo) => 301 para a URL nova
$legacy = [
    '/hospedagem-de-sites' => '/hospedagem', '/streaming-web-radio' => '/streaming',
    '/webradio-completa' => '/web-radio-completa', '/sites-para-web-radio' => '/sites-para-radio',
    '/revenda-de-hospedagem' => '/revenda-hospedagem', '/vps-linux' => '/vps',
    '/privacidade' => '/politica-de-privacidade', '/termos' => '/termos-de-uso',
    '/area-do-cliente' => null,
];
if (array_key_exists($uri, $legacy)) {
    header('Location: ' . ($legacy[$uri] ?? \App\Core\Whmcs::clientArea()), true, 301);
    exit;
}
// redirects cadastrados no painel
try {
    $r = Db::one('SELECT to_path, code FROM redirects WHERE from_path = ?', [$uri]);
    if ($r) {
        Db::run('UPDATE redirects SET hits = hits + 1 WHERE from_path = ?', [$uri]);
        header('Location: ' . $r['to_path'], true, (int) $r['code']);
        exit;
    }
} catch (\Throwable) {
}

// arquivos técnicos
if ($uri === '/robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    if (env('APP_ENV', 'production') !== 'production') {
        echo "User-agent: *\nDisallow: /\n";
        exit;
    }
    echo "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /api/\nDisallow: /lp/\nDisallow: /busca\n\nSitemap: " . app_url('/sitemap.xml') . "\n";
    exit;
}
if ($uri === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    echo Seo::sitemapXml();
    exit;
}
if ($uri === '/health') {
    header('Content-Type: application/json');
    try {
        Db::one('SELECT 1 AS ok');
        echo json_encode(['status' => 'ok']);
    } catch (\Throwable) {
        http_response_code(503);
        echo json_encode(['status' => 'db_error']);
    }
    exit;
}

// download com contagem
if (preg_match('#^/baixar/([a-z0-9-]+)$#', $uri, $m)) {
    $d = Db::one('SELECT * FROM downloads WHERE slug = ? AND active = 1', [$m[1]]);
    if (!$d) {
        PageController::notFound();
        exit;
    }
    $url = $d['file_url'] ?: Settings::get('studio_download');
    Db::run('UPDATE downloads SET download_count = download_count + 1 WHERE id = ?', [$d['id']]);
    Db::run('INSERT INTO downloads_log (download_id, ip_hash) VALUES (?, ?)', [$d['id'], hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . (string) env('APP_KEY'))]);
    header('Location: ' . $url, true, 302);
    exit;
}

if ($uri === '/lead' && $method !== 'POST') {
    header('Location: /', true, 302);
    exit;
}
if ($uri === '/lead' && $method === 'POST') {
    \App\Controllers\LeadController::store();
    exit;
}

$pages = require BASE_PATH . '/config/pages.php';
if ($method !== 'GET' && $method !== 'HEAD') {
    http_response_code(405);
    header('Allow: GET, HEAD');
    exit;
}
if (isset($pages[$uri])) {
    PageController::show($uri, $pages[$uri]);
    exit;
}
PageController::notFound();
