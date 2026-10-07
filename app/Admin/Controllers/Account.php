<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Auth;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\Db;

final class Account
{
    public static function index(): void
    {
        Kernel::view('account', [], 'Minha conta');
    }

    public static function save(): void
    {
        $u = Session::user();
        $cur = (string) ($_POST['current'] ?? '');
        $new = (string) ($_POST['new'] ?? '');
        $row = Db::one('SELECT password_hash FROM admins WHERE id = ?', [$u['id']]);
        if (!$row || !password_verify($cur, (string) $row['password_hash'])) {
            Session::flash('err', 'Senha atual incorreta.');
        } elseif (strlen($new) < 10 || !preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new)) {
            Session::flash('err', 'A nova senha precisa ter 10 ou mais caracteres, com letras e números.');
        } elseif ($new !== (string) ($_POST['confirm'] ?? '')) {
            Session::flash('err', 'A confirmação não confere.');
        } else {
            Auth::changePassword((int) $u['id'], $new);
            Audit::log('senha_alterada');
            session_regenerate_id(true);
            Session::flash('ok', 'Senha alterada.');
        }
        Kernel::redirect('/admin/conta');
    }
}
