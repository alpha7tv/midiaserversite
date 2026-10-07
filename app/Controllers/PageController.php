<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Db;
use App\Core\Logger;
use App\Core\Settings;
use App\Core\View;
use App\Services\Catalog;

final class PageController
{
    /** @param array<string,mixed> $page */
    public static function show(string $path, array $page): void
    {
        $page['path'] = $path;

        $plans = [];
        $min = null;
        if (!empty($page['product'])) {
            $plans = Catalog::plans((string) $page['product']);
            $min = Catalog::minPrice((string) $page['product']);
        }
        $page['plans'] = $plans;
        $minTxt = $min !== null ? money($min) : '';
        foreach (['title', 'description'] as $k) {
            if (isset($page[$k])) {
                $page[$k] = trim(str_replace('{min}', $minTxt, (string) $page[$k]));
            }
        }
        // sem preço disponível, remove o trecho "A partir de /mês"
        if ($minTxt === '') {
            foreach (['title', 'description'] as $k) {
                $page[$k] = trim((string) preg_replace('/\s*A partir de\s*\/mês\.?/iu', '', (string) ($page[$k] ?? '')));
            }
        }
        $page['min_price'] = $min;

        $tpl = (string) ($page['template'] ?? 'text');
        $data = ['page' => $page, 'plans' => $plans, 'min' => $min];
        $data += self::extra($tpl, $page);

        $body = View::render('pages/' . $tpl, $data);
        $layout = $tpl === 'lp' ? 'layout-lp' : 'layout';
        echo View::render($layout, ['page' => $page, 'content' => $body]);
    }

    /** @return array<string,mixed> */
    private static function extra(string $tpl, array $page): array
    {
        try {
            if ($tpl === 'home') {
                $mins = [];
                foreach (['hospedagem', 'revenda-hospedagem', 'streaming', 'revenda-streaming', 'sites-para-radio', 'automacao-radio', 'conteudos-para-radio'] as $slug) {
                    $mins[$slug] = Catalog::minPrice($slug);
                }
                return ['mins' => $mins];
            }
            if ($tpl === 'lp') {
                $pages = require BASE_PATH . '/config/pages.php';
                $src = $pages[(string) ($page['lp_of'] ?? '')] ?? [];
                return ['src' => $src];
            }
            if ($tpl === 'busca') {
                return ['results' => \App\Services\Search::run((string) ($_GET['q'] ?? '')), 'q' => trim((string) ($_GET['q'] ?? ''))];
            }
            if ($tpl === 'downloads') {
                $rows = Db::all(
                    'SELECT d.*, c.slug AS cat_slug, c.name AS cat_name, c.sort_order AS cat_order
                     FROM downloads d LEFT JOIN categories c ON c.id = d.category_id
                     WHERE d.active = 1 ORDER BY c.sort_order, d.external, d.name'
                );
                $groups = [];
                foreach ($rows as $r) {
                    $groups[(string) $r['cat_slug']]['name'] = (string) $r['cat_name'];
                    $groups[(string) $r['cat_slug']]['items'][] = $r;
                }
                return ['groups' => $groups];
            }
            if ($tpl === 'automacao') {
                $v = Db::one("SELECT * FROM software_versions WHERE software_slug = 'midia-radio-studio' AND published = 1 ORDER BY released_at DESC, id DESC LIMIT 1");
                $all = Db::all("SELECT * FROM software_versions WHERE software_slug = 'midia-radio-studio' AND published = 1 ORDER BY released_at DESC, id DESC LIMIT 10");
                return ['latest' => $v, 'versions' => $all];
            }
            if ($tpl === 'sites') {
                return ['models' => require BASE_PATH . '/config/site_models.php'];
            }
            if ($tpl === 'blog') {
                return ['posts' => Db::all("SELECT slug, title, excerpt, cover, published_at FROM posts WHERE status = 'published' ORDER BY published_at DESC LIMIT 30")];
            }
        } catch (\Throwable $e) {
            Logger::write('app', 'extra_failed', ['tpl' => $tpl, 'error' => $e->getMessage()]);
        }
        return [];
    }

    public static function notFound(): void
    {
        http_response_code(404);
        $page = [
            'path' => '/404', 'template' => 'notfound', 'og' => 'home', 'noindex' => true,
            'title' => 'Página não encontrada | Mídia Server', 'description' => 'A página que você procura não foi encontrada.',
            'h1' => 'Página não encontrada', 'crumb' => 'Página não encontrada', 'wa' => 'Olá, não encontrei uma página no site da Mídia Server.',
        ];
        $body = View::render('pages/notfound', ['page' => $page]);
        echo View::render('layout', ['page' => $page, 'content' => $body]);
    }
}
