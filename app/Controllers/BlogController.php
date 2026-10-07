<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Db;
use App\Core\Markup;
use App\Core\View;

final class BlogController
{
    public static function show(string $slug): void
    {
        $p = Db::one("SELECT p.*, c.name AS cat FROM posts p LEFT JOIN categories c ON c.id = p.category_id WHERE p.slug = ? AND p.status = 'published'", [$slug]);
        if (!$p) {
            PageController::notFound();
            return;
        }
        $cover = (string) ($p['og_image'] ?: $p['cover']);
        $page = [
            'path' => '/blog/' . $p['slug'], 'template' => 'post', 'og' => 'blog', 'og_url' => preg_match('#^https://#', $cover) ? $cover : '',
            'og_type' => 'article', 'title' => mb_substr((string) $p['title'], 0, 62) . ' | Mídia Server',
            'description' => mb_substr(trim((string) ($p['excerpt'] ?: strip_tags(Markup::toHtml((string) $p['body'])))), 0, 160),
            'h1' => (string) $p['title'], 'crumb' => (string) $p['title'], 'breadcrumb' => [['Blog', '/blog']],
            'article' => ['title' => (string) $p['title'], 'published' => date('c', strtotime((string) $p['published_at'])), 'modified' => date('c', strtotime((string) $p['published_at']))],
            'wa' => 'Olá, li o artigo "' . $p['title'] . '" no site da Mídia Server.',
        ];
        $body = View::render('pages/post', ['page' => $page, 'post' => $p, 'html' => Markup::toHtml((string) $p['body'])]);
        echo View::render('layout', ['page' => $page, 'content' => $body]);
    }
}
