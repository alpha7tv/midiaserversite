<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Db;

/** Busca interna: páginas, downloads e artigos (rádios entram na etapa de integração). */
final class Search
{
    /** @return array<string,list<array<string,string>>> */
    public static function run(string $q): array
    {
        $q = trim(mb_substr($q, 0, 80));
        $out = ['Páginas' => [], 'Downloads' => [], 'Artigos' => []];
        if (mb_strlen($q) < 2) {
            return $out;
        }
        $needle = mb_strtolower($q);
        $pages = require BASE_PATH . '/config/pages.php';
        foreach ($pages as $path => $p) {
            if (!empty($p['noindex']) || !empty($p['no_sitemap'])) {
                continue;
            }
            $hay = mb_strtolower(($p['title'] ?? '') . ' ' . ($p['h1'] ?? '') . ' ' . ($p['description'] ?? '') . ' ' . implode(' ', $p['kw'] ?? []));
            if (str_contains($hay, $needle)) {
                $out['Páginas'][] = ['title' => (string) ($p['crumb'] ?? $p['h1']), 'url' => $path, 'text' => (string) ($p['description'] ?? '')];
            }
        }
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
        try {
            foreach (Db::all("SELECT slug, name, description, os FROM downloads WHERE active = 1 AND (name LIKE ? ESCAPE '\\' OR description LIKE ? ESCAPE '\\') LIMIT 12", [$like, $like]) as $d) {
                $out['Downloads'][] = ['title' => (string) $d['name'], 'url' => '/downloads#' . $d['slug'], 'text' => (string) $d['description']];
            }
            foreach (Db::all("SELECT slug, title, excerpt FROM posts WHERE status = 'published' AND (title LIKE ? ESCAPE '\\' OR excerpt LIKE ? ESCAPE '\\') LIMIT 12", [$like, $like]) as $a) {
                $out['Artigos'][] = ['title' => (string) $a['title'], 'url' => '/blog/' . $a['slug'], 'text' => (string) $a['excerpt']];
            }
        } catch (\Throwable) {
        }
        return $out;
    }
}
