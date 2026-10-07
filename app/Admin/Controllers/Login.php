<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Audit;
use App\Admin\Auth;
use App\Admin\Kernel;
use App\Admin\Session;
use App\Core\View;

final class Login
{
    public static function handle(string $method): void
    {
        if (Session::user()) {
            Kernel::redirect('/admin');
        }
        $error = '';
        if ($method === 'POST') {
            $email = (string) ($_POST['email'] ?? '');
            if (!Session::csrfOk((string) ($_POST['csrf'] ?? ''))) {
                $error = 'Sessão expirada. Tente novamente.';
            } elseif (Auth::blocked($email)) {
                http_response_code(429);
                $error = 'Muitas tentativas. Aguarde 15 minutos.';
            } elseif ($row = Auth::attempt($email, (string) ($_POST['password'] ?? ''))) {
                Session::login((int) $row['id']);
                Audit::log('login');
                Kernel::redirect('/admin');
            } else {
                http_response_code(401);
                $error = 'E-mail ou senha incorretos.';
                Audit::log('login_falhou', null, ['email_tail' => substr($email, -6)]);
            }
        }
        echo View::render('admin/layout', ['title' => 'Entrar', 'content' => View::render('admin/login', ['error' => $error])]);
    }
}
