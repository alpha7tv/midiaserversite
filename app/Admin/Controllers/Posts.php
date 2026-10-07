<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\Db;
use App\Core\Markup;

/** "Publicar novidade": você escolhe onde comunicar (site, newsletter, Web Push). Nada dispara sozinho. */
final class Posts
{
    public const KINDS = ['promocao' => 'Promoção', 'produto' => 'Novo produto', 'atualizacao' => 'Atualização', 'software' => 'Novo software', 'versao' => 'Nova versão', 'conteudo' => 'Novo conteúdo', 'noticia' => 'Notícia'];

    public static function index(): void
    {
        $rows = Db::all('SELECT p.*, c.name AS cat FROM posts p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC LIMIT 200');
        Kernel::view('posts', ['rows' => $rows, 'kinds' => self::KINDS], 'Publicar novidade');
    }

    public static function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $p = $id ? Db::one('SELECT * FROM posts WHERE id = ?', [$id]) : null;
        $cats = Db::all("SELECT id, name FROM categories WHERE type = 'post' ORDER BY sort_order");
        Kernel::view('post_edit', ['p' => $p, 'cats' => $cats, 'kinds' => self::KINDS, 'smtp' => \App\Core\Mailer::configured()], $p ? 'Editar publicação' : 'Nova publicação');
    }

    public static function save(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $in = static fn (string $k, int $max = 100000): string => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);
        $title = $in('title', 190);
        $body = $in('body');
        $img = $in('og_image', 400);
        $back = '/admin/publicar/editar' . ($id ? '?id=' . $id : '');
        if ($title === '' || $body === '') {
            Session::flash('err', 'Título e texto são obrigatórios.');
            Kernel::redirect($back);
        }
        if ($img !== '' && !preg_match('#^https://\S+$#', $img)) {
            Session::flash('err', 'A imagem precisa ser um link https://.');
            Kernel::redirect($back);
        }
        $slug = Markup::slug($in('slug', 100) !== '' ? $in('slug', 100) : $title);
        $dupe = Db::one('SELECT id FROM posts WHERE slug = ? AND id <> ?', [$slug, $id]);
        if ($dupe) {
            $slug .= '-' . date('ymd');
        }
        $kind = isset(self::KINDS[$in('kind', 20)]) ? $in('kind', 20) : 'noticia';
        $publish = !empty($_POST['publish_site']);
        $wantNews = !empty($_POST['notify_newsletter']);
        $wantPush = empty($_POST['notify_push']) ? 0 : 1;
        $prev = $id ? Db::one('SELECT status, published_at FROM posts WHERE id = ?', [$id]) : null;
        $pubAt = $publish ? ($prev && $prev['status'] === 'published' ? $prev['published_at'] : date('Y-m-d H:i:s')) : null;
        $vals = [$slug, $title, $in('excerpt', 300), $body, (int) $in('category_id', 6) ?: null, $img ?: null, $img ?: null, $pubAt, $publish ? 'published' : 'draft', $kind, $wantNews ? 1 : 0, $wantPush];
        if ($id) {
            Db::run('UPDATE posts SET slug=?, title=?, excerpt=?, body=?, category_id=?, cover=?, og_image=?, published_at=?, status=?, kind=?, notify_newsletter=?, notify_push=? WHERE id=?', [...$vals, $id]);
        } else {
            Db::run('INSERT INTO posts (slug, title, excerpt, body, category_id, cover, og_image, published_at, status, kind, notify_newsletter, notify_push) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', $vals);
            $id = (int) Db::pdo()->lastInsertId();
        }
        Audit::log('publicacao_salva', 'posts#' . $id, ['titulo' => $title, 'site' => $publish, 'newsletter' => $wantNews, 'push' => (bool) $wantPush]);
        $msg = $publish ? 'Publicado no site: ' . app_url('/blog/' . $slug) : 'Salvo como rascunho (não aparece no site).';
        if ($wantNews) {
            if (!Db::one('SELECT id FROM campaigns WHERE source = ?', ['post#' . $id])) {
                $md = $body . ($publish ? "\n\n[Ler no site](" . app_url('/blog/' . $slug) . ')' : '');
                Db::run('INSERT INTO campaigns (subject, body_html, body_text, source, status, created_by) VALUES (?,?,?,?,?,?)', [$title, Markup::toHtml($md), $md, 'post#' . $id, 'draft', Session::user()['id'] ?? null]);
                $msg .= ' Criei um rascunho de newsletter: revise e envie em Newsletter.';
            } else {
                $msg .= ' A newsletter desta publicação já existe em Newsletter.';
            }
        }
        if ($wantPush) {
            $msg .= ' Web Push registrado: o envio será ativado quando as chaves VAPID forem configuradas.';
        }
        Session::flash('ok', $msg);
        Kernel::redirect('/admin/publicar');
    }
}
