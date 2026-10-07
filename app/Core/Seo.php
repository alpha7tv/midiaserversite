<?php
declare(strict_types=1);

namespace App\Core;

/** Metatags, Open Graph, Twitter Cards, JSON-LD e sitemap. */
final class Seo
{
    /** @param array<string,mixed> $page */
    public static function head(array $page): string
    {
        $title = (string) ($page['title'] ?? Settings::get('site_name'));
        $desc = (string) ($page['description'] ?? '');
        $path = (string) ($page['path'] ?? '/');
        $canonical = app_url($path === '/' ? '' : $path);
        $robots = (!empty($page['noindex']) || env('APP_ENV', 'production') !== 'production') ? 'noindex, nofollow' : 'index, follow, max-image-preview:large';
        $ogImage = !empty($page['og_url']) ? (string) $page['og_url'] : app_url('/assets/img/og/' . ($page['og'] ?? 'home') . '.png');
        $type = (string) ($page['og_type'] ?? 'website');

        $h = [];
        $h[] = '<title>' . e($title) . '</title>';
        $h[] = '<meta name="description" content="' . e($desc) . '">';
        $h[] = '<meta name="robots" content="' . $robots . '">';
        $h[] = '<link rel="canonical" href="' . e($canonical) . '">';
        $h[] = '<meta property="og:site_name" content="' . e(Settings::get('site_name')) . '">';
        $h[] = '<meta property="og:locale" content="pt_BR">';
        $h[] = '<meta property="og:type" content="' . e($type) . '">';
        $h[] = '<meta property="og:title" content="' . e($title) . '">';
        $h[] = '<meta property="og:description" content="' . e($desc) . '">';
        $h[] = '<meta property="og:url" content="' . e($canonical) . '">';
        $h[] = '<meta property="og:image" content="' . e($ogImage) . '">';
        $h[] = '<meta property="og:image:width" content="1200">';
        $h[] = '<meta property="og:image:height" content="630">';
        $h[] = '<meta property="og:image:alt" content="' . e((string) ($page['og_alt'] ?? $title)) . '">';
        $h[] = '<meta name="twitter:card" content="summary_large_image">';
        $h[] = '<meta name="twitter:title" content="' . e($title) . '">';
        $h[] = '<meta name="twitter:description" content="' . e($desc) . '">';
        $h[] = '<meta name="twitter:image" content="' . e($ogImage) . '">';
        $verify = Settings::get('search_console');
        if ($verify !== '') {
            $h[] = '<meta name="google-site-verification" content="' . e($verify) . '">';
        }
        foreach (self::schemas($page, $canonical) as $s) {
            $h[] = '<script type="application/ld+json">' . json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
        }
        return implode("\n", $h);
    }

    /** @return list<array<string,mixed>> */
    private static function schemas(array $page, string $canonical): array
    {
        $out = [];
        $site = app_url('');
        $path = (string) ($page['path'] ?? '/');

        if ($path === '/') {
            $out[] = [
                '@context' => 'https://schema.org', '@type' => 'Organization',
                'name' => Settings::get('site_name'), 'url' => $site,
                'logo' => app_url('/assets/img/logo-symbol.svg'),
                'email' => Settings::get('support_email'),
                'contactPoint' => [[
                    '@type' => 'ContactPoint', 'contactType' => 'customer support',
                    'url' => Settings::whatsapp(), 'availableLanguage' => 'pt-BR',
                ]],
            ];
            $out[] = [
                '@context' => 'https://schema.org', '@type' => 'WebSite',
                'name' => Settings::get('site_name'), 'url' => $site, 'inLanguage' => 'pt-BR',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => app_url('/busca') . '?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ];
        }

        // Breadcrumb
        $crumbs = $page['breadcrumb'] ?? [];
        if ($path !== '/') {
            $items = [['name' => 'Início', 'url' => $site]];
            foreach ($crumbs as [$n, $u]) {
                $items[] = ['name' => $n, 'url' => app_url($u)];
            }
            $items[] = ['name' => (string) ($page['crumb'] ?? $page['h1'] ?? $page['title']), 'url' => $canonical];
            $list = [];
            foreach ($items as $i => $it) {
                $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $it['name'], 'item' => $it['url']];
            }
            $out[] = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list];
        }

        // Produto/Serviço/Software
        $plans = $page['plans'] ?? [];
        $kind = (string) ($page['schema'] ?? '');
        if ($kind !== '' && $plans) {
            $offers = [];
            foreach ($plans as $p) {
                $offers[] = [
                    '@type' => 'Offer', 'name' => $p['name'], 'priceCurrency' => 'BRL',
                    'price' => number_format((float) $p['price_month'], 2, '.', ''),
                    'url' => Whmcs::cart((int) $p['whmcs_pid']),
                    'availability' => 'https://schema.org/InStock',
                ];
            }
            $base = [
                '@context' => 'https://schema.org',
                'name' => (string) ($page['schema_name'] ?? $page['h1'] ?? $page['title']),
                'description' => (string) $page['description'],
                'url' => $canonical,
                'brand' => ['@type' => 'Brand', 'name' => Settings::get('site_name')],
            ];
            if ($kind === 'SoftwareApplication') {
                $base += ['@type' => 'SoftwareApplication', 'applicationCategory' => 'MultimediaApplication',
                    'operatingSystem' => 'Windows 10, Windows 11', 'offers' => $offers];
            } elseif ($kind === 'Service') {
                $base += ['@type' => 'Service', 'provider' => ['@type' => 'Organization', 'name' => Settings::get('site_name'), 'url' => $site],
                    'areaServed' => 'BR', 'offers' => $offers];
            } else {
                $base += ['@type' => 'Product', 'offers' => $offers];
            }
            $out[] = $base;
        }

        if (!empty($page['article'])) {
            $a = $page['article'];
            $out[] = [
                '@context' => 'https://schema.org', '@type' => 'Article', 'headline' => (string) $a['title'],
                'description' => (string) $page['description'], 'mainEntityOfPage' => $canonical,
                'datePublished' => (string) $a['published'], 'dateModified' => (string) ($a['modified'] ?? $a['published']),
                'image' => !empty($page['og_url']) ? (string) $page['og_url'] : app_url('/assets/img/og/blog.png'),
                'author' => ['@type' => 'Organization', 'name' => Settings::get('site_name')],
                'publisher' => ['@type' => 'Organization', 'name' => Settings::get('site_name'), 'logo' => ['@type' => 'ImageObject', 'url' => app_url('/assets/img/icon-512.png')]],
            ];
        }

        if (!empty($page['faq'])) {
            $qa = [];
            foreach ($page['faq'] as [$q, $a]) {
                $qa[] = ['@type' => 'Question', 'name' => $q, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a]];
            }
            $out[] = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $qa];
        }
        return $out;
    }

    public static function sitemapXml(): string
    {
        $pages = require BASE_PATH . '/config/pages.php';
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        $today = date('Y-m-d');
        foreach ($pages as $path => $p) {
            if (!empty($p['noindex']) || !empty($p['no_sitemap'])) {
                continue;
            }
            $prio = $path === '/' ? '1.0' : (!empty($p['product']) ? '0.9' : '0.6');
            $xml[] = '  <url><loc>' . e(app_url($path === '/' ? '' : $path)) . '</loc><lastmod>' . $today . '</lastmod><priority>' . $prio . '</priority></url>';
        }
        try {
            foreach (Db::all("SELECT slug, published_at FROM posts WHERE status = 'published' ORDER BY published_at DESC LIMIT 2000") as $r) {
                $xml[] = '  <url><loc>' . e(app_url('/blog/' . $r['slug'])) . '</loc><lastmod>' . e(substr((string) $r['published_at'], 0, 10)) . '</lastmod><priority>0.5</priority></url>';
            }
        } catch (\Throwable) {
        }
        $xml[] = '</urlset>';
        return implode("\n", $xml) . "\n";
    }
}
