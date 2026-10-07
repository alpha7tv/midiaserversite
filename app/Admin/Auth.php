<?php
declare(strict_types=1);

namespace App\Admin;

use App\Core\Csrf;
use App\Core\Db;

/** Autenticação com limite de tentativas (por IP e por e-mail) e senha em Argon2id. */
final class Auth
{
    private const WINDOW = 900;      // 15 min
    private const MAX_IP = 10;
    private const MAX_EMAIL = 5;

    public static function blocked(string $email): bool
    {
        $since = date('Y-m-d H:i:s', time() - self::WINDOW);
        $ip = Db::one('SELECT COUNT(*) n FROM login_attempts WHERE ip_hash = ? AND success = 0 AND created_at > ?', [Csrf::ipHash(), $since]);
        $em = Db::one('SELECT COUNT(*) n FROM login_attempts WHERE email_hash = ? AND success = 0 AND created_at > ?', [self::eh($email), $since]);
        return (int) ($ip['n'] ?? 0) >= self::MAX_IP || (int) ($em['n'] ?? 0) >= self::MAX_EMAIL;
    }

    /** @return array<string,mixed>|null */
    public static function attempt(string $email, string $password): ?array
    {
        $row = Db::one('SELECT id, password_hash FROM admins WHERE email = ?', [mb_strtolower(trim($email))]);
        // verificação com hash falso quando o e-mail não existe (tempo de resposta semelhante)
        $hash = $row['password_hash'] ?? '$argon2id$v=19$m=65536,t=4,p=1$ZHVtbXlzYWx0ZHVtbXk$0000000000000000000000000000000000000000000';
        $ok = $row && password_verify($password, (string) $hash);
        Db::run('INSERT INTO login_attempts (ip_hash, email_hash, success) VALUES (?,?,?)', [Csrf::ipHash(), self::eh($email), $ok ? 1 : 0]);
        if (!$ok) {
            return null;
        }
        if (password_needs_rehash((string) $row['password_hash'], PASSWORD_ARGON2ID)) {
            Db::run('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_ARGON2ID), $row['id']]);
        }
        return $row;
    }

    public static function changePassword(int $id, string $new): void
    {
        Db::run('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_ARGON2ID), $id]);
    }

    private static function eh(string $email): string
    {
        return hash('sha256', mb_strtolower(trim($email)) . '|' . (string) env('APP_KEY'));
    }
}
