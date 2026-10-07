<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\Db;
use App\Core\Markup;

final class Downloads
{
    public static function index(): void
    {
        $rows = Db::all('SELECT d.*, c.name AS cat FROM downloads d LEFT JOIN categories c ON c.id = d.category_id ORDER BY c.sort_order, d.external, d.name');
        Kernel::view('downloads', ['rows' => $rows], 'Downloads');
    }

    public static function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $d = $id ? Db::one('SELECT * FROM downloads WHERE id = ?', [$id]) : null;
        $cats = Db::all("SELECT id, name FROM categories WHERE type = 'download' ORDER BY sort_order");
        Kernel::view('download_edit', ['d' => $d, 'cats' => $cats], $d ? 'Editar download' : 'Novo download');
    }

    public static function save(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $in = static fn (string $k, int $max = 300): string => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);
        $name = $in('name', 190);
        $url = $in('file_url', 400);
        $external = empty($_POST['external']) ? 0 : 1;
        $err = '';
        if ($name === '') {
            $err = 'Informe o nome.';
        } elseif ($url !== '' && !preg_match('#^https://\S+$#', $url)) {
            $err = 'O link do arquivo precisa começar com https://.';
        } elseif ($external && $url === '') {
            $err = 'Download externo precisa de um link.';
        }
        $size = $in('size_mb', 10) === '' ? null : (int) round((float) str_replace(',', '.', $in('size_mb', 10)) * 1048576);
        $date = $in('released_at', 10);
        $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date . ' 00:00:00' : null;
        if ($err) {
            Session::flash('err', $err);
            Kernel::redirect('/admin/downloads/editar' . ($id ? '?id=' . $id : ''));
        }
        $slug = $in('slug', 120) !== '' ? Markup::slug($in('slug', 120)) : Markup::slug($name);
        $vals = [(int) $in('category_id', 6) ?: null, $slug, $name, $in('version', 40) ?: null, $in('description', 2000), $in('os', 120) ?: null, $size, $url ?: null, $in('license_note', 300) ?: null, $external, $date, empty($_POST['active']) ? 0 : 1];
        if ($id) {
            Db::run('UPDATE downloads SET category_id=?, slug=?, name=?, version=?, description=?, os=?, size_bytes=?, file_url=?, license_note=?, external=?, released_at=?, active=? WHERE id=?', [...$vals, $id]);
        } else {
            if (Db::one('SELECT id FROM downloads WHERE slug = ?', [$slug])) {
                $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
                $vals[1] = $slug;
            }
            Db::run('INSERT INTO downloads (category_id, slug, name, version, description, os, size_bytes, file_url, license_note, external, released_at, active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', $vals);
            $id = (int) Db::pdo()->lastInsertId();
        }
        Audit::log('download_salvo', 'downloads#' . $id, ['nome' => $name]);
        Session::flash('ok', 'Download salvo.');
        Kernel::redirect('/admin/downloads');
    }
}
