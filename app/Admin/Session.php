<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Db;

/** Sessão do painel: cookie Secure/HttpOnly/SameSite=Strict, ID regenerado no login, expira por inatividade. */
final class Session
{
    private const IDLE = 7200;

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $dir = BASE_PATH . '/storage/sessions';
        if (!is_dir($dir)) {
            @mkdir($dir, 0770, true);
        }
        session_save_path($dir);
        session_name('ms_admin');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.gc_maxlifetime', (string) self::IDLE);
        session_set_cookie_params(['lifetime' => 0, 'path' => '/admin', 'secure' => Csrf::https(), 'httponly' => true, 'samesite' => 'Strict']);
        session_start();
        if (isset($_SESSION['last']) && time() - (int) $_SESSION['last'] > self::IDLE) {
            self::logout();
            session_start();
        }
        $_SESSION['last'] = time();
    }

    /** @return array<string,mixed>|null */
    public static function user(): ?array
    {
        $id = (int) ($_SESSION['uid'] ?? 0);
        if ($id < 1) {
            return null;
        }
        static $u = null;
        return $u ??= Db::one('SELECT id, name, email, role FROM admins WHERE id = ?', [$id]);
    }

    public static function login(int $id): void
    {
        session_regenerate_id(true);
        $_SESSION['uid'] = $id;
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
        Db::run('UPDATE admins SET last_login_at = ? WHERE id = ?', [date('Y-m-d H:i:s'), $id]);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function csrf(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
    }

    public static function csrfOk(string $sent): bool
    {
        return isset($_SESSION['csrf']) && hash_equals((string) $_SESSION['csrf'], $sent);
    }

    public static function flash(string $type, string $msg): void
    {
        $_SESSION['flash'][] = [$type, $msg];
    }

    /** @return list<array{0:string,1:string}> */
    public static function takeFlash(): array
    {
        $f = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $f;
    }
}
