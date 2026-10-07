<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\Db;

final class Redirects
{
    public static function index(): void
    {
        Kernel::view('redirects', ['rows' => Db::all('SELECT * FROM redirects ORDER BY id DESC')], 'Redirects 301');
    }

    public static function save(): void
    {
        $from = '/' . trim(trim((string) ($_POST['from_path'] ?? '')), '/');
        $to = trim((string) ($_POST['to_path'] ?? ''));
        $code = (int) ($_POST['code'] ?? 301) === 302 ? 302 : 301;
        if ($from === '/' || !preg_match('#^/[\w\-./%]*$#', $from) || str_starts_with($from, '/admin') || str_starts_with($from, '/assets')) {
            Session::flash('err', 'Endereço de origem inválido. Use um caminho como /pagina-antiga.');
        } elseif (!preg_match('~^(/[\w\-./%?=&#]*|https://[\w.-]+(/\S*)?)$~', $to) || $to === $from) {
            Session::flash('err', 'Destino inválido. Use um caminho (/nova-pagina) ou um endereço https://.');
        } elseif (Db::one('SELECT id FROM redirects WHERE from_path = ?', [$from])) {
            Session::flash('err', 'Já existe um redirect para esse endereço.');
        } else {
            Db::run('INSERT INTO redirects (from_path, to_path, code) VALUES (?,?,?)', [$from, $to, $code]);
            Audit::log('redirect_criado', 'redirects', ['de' => $from, 'para' => $to, 'codigo' => $code]);
            Session::flash('ok', "Redirect criado: $from → $to");
        }
        Kernel::redirect('/admin/redirects');
    }

    public static function delete(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (Db::run('DELETE FROM redirects WHERE id = ?', [$id])) {
            Audit::log('redirect_excluido', 'redirects#' . $id);
            Session::flash('ok', 'Redirect excluído.');
        }
        Kernel::redirect('/admin/redirects');
    }
}
