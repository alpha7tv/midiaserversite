<?php
declare(strict_types=1);

namespace App\Core;

/** CSRF por double-submit cookie + carimbo de tempo assinado (anti-bot sem JavaScript). */
final class Csrf
{
    public static function token(): string
    {
        $t = $_COOKIE['ms_csrf'] ?? '';
        if (!preg_match('/^[a-f0-9]{32}$/', (string) $t)) {
            $t = bin2hex(random_bytes(16));
            if (!headers_sent()) {
                setcookie('ms_csrf', $t, [
                    'expires' => 0, 'path' => '/', 'secure' => self::https(), 'httponly' => true, 'samesite' => 'Lax',
                ]);
            }
            $_COOKIE['ms_csrf'] = $t;
        }
        return $t;
    }

    public static function check(string $sent): bool
    {
        $c = (string) ($_COOKIE['ms_csrf'] ?? '');
        return $c !== '' && hash_equals($c, $sent);
    }

    /** @return array{0:string,1:string} [timestamp, assinatura] */
    public static function stamp(): array
    {
        $ts = (string) time();
        return [$ts, hash_hmac('sha256', 'lead|' . $ts, (string) env('APP_KEY', 'dev'))];
    }

    public static function stampOk(string $ts, string $sig, int $minSeconds = 3, int $maxSeconds = 7200): bool
    {
        if (!ctype_digit($ts) || !hash_equals(hash_hmac('sha256', 'lead|' . $ts, (string) env('APP_KEY', 'dev')), $sig)) {
            return false;
        }
        $age = time() - (int) $ts;
        return $age >= $minSeconds && $age <= $maxSeconds;
    }

    public static function https(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    }

    public static function ipHash(): string
    {
        return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . (string) env('APP_KEY', 'dev'));
    }
}
